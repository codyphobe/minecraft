<?php

    /*
     * @product:     Minecraft Class
     * @description: Intergrate Minecraft within your own projects.
     * @author:      Nathaniel Blackburn
     * @version:     2.0
     * @license:     http://creativecommons.org/licenses/by/3.0/legalcode
    */

class Minecraft {

    public $account = false;
    protected $_lastError = false;

    /**
     * Send a curl request
     * Note that the CURLOPT_SSL_VERIFYPEER option is set to false to reduce the error probability
     *
     * @param  string $url        The url to send the request to
     * @param  array  $parameters An array of parameters to add to the url (using GET)
     * @return mixed
     */
    protected function _request($url, array $parameters) {
        $request = curl_init();
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
        if ($parameters != null) {
            curl_setopt($request, CURLOPT_URL, $url . '?' . http_build_query($parameters, null, '&'));
        } else {
            curl_setopt($request, CURLOPT_URL, $url);
        }
        $ret = curl_exec($request);
        curl_close($request);

        return $ret;
    }

    /**
     * Signin trough login.minecraft.net
     * If the function return false, the getLastError() method
     * will return the error which caused the login to fail
     *
     * @param  string  $username The username (should be an email since the mojang account migration)
     * @param  string  $password The corresponding password
     * @param  int     $version  The launcher version (12 for the current version on 2014-01-07)
     * @return bool
     */
    public function signin($username, $password, $version = 12) {
        $error_msg = [
            'Account migrated, use e-mail as username.',
            'Old version',
            'Bad login',
            'Bad request'
        ];
        $parameters = array('user' => $username, 'password' => $password, 'version' => $version);
        $request = $this->_request('https://login.minecraft.net/', $parameters);
        $response = explode(':', $request);

        if (in_array($request, $error_msg)) {
            $this->account = false;
            $this->_lastError = $request;
            return false;
        }

        $this->account = array(
            'current_version' => $response[0],
            'correct_username' => $response[2],
            'session_token' => $response[3],
            'premium_account' => $this->isPremium($response[2]),
            'player_skin' => $this->getSkinUrl($response[2]),
            'request_timestamp' => date("dmYhms")
        );

        $this->_lastError = false;

        return true;
    }

    /**
     * Let you know if the user own a premium minecraft version
     *
     * @param  string $username The username to check (if ommited, will use the last user of a successfull signin call)
     * @return bool
     */
    public function isPremium($username = false) {
        if($username === false) {
            $username = $this->getUsername();
        }

        $parameters = array('user' => $username);
        return $this->_request('https://minecraft.net/haspaid.jsp', $parameters) == 'true';
    }

    /**
     * This function firstly checks the user specified has a premium account,
     * then returns an url to the skin file for that user if a custom skin was found.
     *
     * @param  string $username The username to get skin from (if ommited, will use the
     *                              last user of a successfull signin call)
     * @return string / false
     */
    public function getSkinUrl($username = false) {
        if($username === false) {
            $username = $this->getUsername();
        }

        if ($this->isPremium($username)) {
            $headers = get_headers('http://s3.amazonaws.com/MinecraftSkins/' . $username . '.png');
            if ($headers[7] == 'Content-Type: image/png' || $headers[7] == 'Content-Type: application/octet-stream') {
                return 'https://s3.amazonaws.com/MinecraftSkins/' . $username . '.png';
            }
            return 'https://s3.amazonaws.com/MinecraftSkins/char.png';
        }
        return false;
    }

    /**
     * This function is used to keep the user's current session alive, this command needs to be
     * sent to the Minecraft servers every 600 ticks (60 seconds) otherwise the user is signed out.
     *
     * @param  string $session  The session token
     * @param  string $username The username to keep alive (if ommited, will use the last user
     *                              of a successfull signin call)
     * @return '--' (Strange return value, wonder if it's normal...)
     */
    public function keepAlive($session = false, $username = false) {
        if($session === false && $username === false) {
            $username = $this->getUsername();
            // No need to check if the signin method has been called since the getUsername already does it
            $session = $this->account['session_token'];
        }

        $parameters = array('name' => $username, 'session' => $session);
        return $this->_request('https://login.minecraft.net/session', $parameters);
    }

    /**
     * This function renders the specified player's skin.
     * Please note when rendering the full body, the image width is half the size of the image height.
     * You can include this function directly inside a img tag by using the following example...
     *
     * <img src="data:image/png;base64,<?= base64_encode($minecraft->renderSkin('nblackburn', 'head', 100)) ?>">
     *
     * @param  string  $render_type Enum of 'head', 'body'
     * @param  int     $size        Size of the generated skin
     * @param  string  $username    The username to get the skin from (if ommited, will use
     *                                  the last user of a successfull signin call)
     * @return binary/false
     */
    public function renderSkin($render_type, $size, $username = false) {
        if($username === false) {
            $username = $this->getUsername();
        }

        if (in_array($render_type, array('head', 'body'))) {
            ob_start();
            if ($render_type == 'head') {
                $canvas = imagecreatetruecolor($size, $size);
                $image = imagecreatefrompng($this->getSkinUrl($username));
                imagecopyresampled($canvas, $image, 0, 0, 8, 8, $size, $size, 8, 8);

                imagepng($canvas);
                return ob_get_clean();
            }

            if($render_type == 'body') {
                $scale = $size / 16;
                $canvas = imagecreatetruecolor(16*$scale, 32*$scale);
                $image = imagecreatefrompng($this->getSkinUrl($username));
                imagealphablending($canvas, false);
                imagesavealpha($canvas,true);
                $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
                imagefilledrectangle($canvas, 0, 0, 16*$scale, 32*$scale, $transparent);
                imagecopyresized  ($canvas, $image, 4*$scale,  0*$scale,  8,  8,  8*$scale, 8*$scale,  8,  8);
                imagecopyresized  ($canvas, $image, 4*$scale,  8*$scale,  20, 20, 8*$scale, 12*$scale, 8,  12);
                imagecopyresized  ($canvas, $image, 0*$scale,  8*$scale,  44, 20, 4*$scale, 12*$scale, 4,  12);
                imagecopyresampled($canvas, $image, 12*$scale, 8*$scale,  47, 20, 4*$scale, 12*$scale, -4, 12);
                imagecopyresized  ($canvas, $image, 4*$scale,  20*$scale, 4,  20, 4*$scale, 12*$scale, 4,  12);
                imagecopyresampled($canvas, $image, 8*$scale,  20*$scale, 7,  20, 4*$scale, 12*$scale, -4, 12);

                imagepng($canvas);
                return ob_get_clean();
            }
        }

        return false;
    }

    public function getUsername() {
        if($this->account) {
            return $this->account['correct_username'];
        }

        throw new Exception('You should use the signin method successfully before or precise the username');
    }

    public function getLastError() {
        return $this->_lastError;
    }

}
