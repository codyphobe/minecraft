<?php

/*
 * @product: Minecraft Account Class
 * @description: Provides a set of useful functions to intergrate Minecraft within your projects.
 * @author: Nathaniel Blackburn
 * @version: 1.0
 * @revised: 25/02/2012 @ 22:43 PM (UST)
 * @support: support@nblackburn.me
 * @website: http://nblackburn.me
*/

class minecraft {

    public $account;

    private function request($website) {
        $request = curl_init();
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_URL, $website);
        return curl_exec($request);
        curl_close($request);
    }

    public function signin($username, $password, $version) {
        $request = $this->request('https://login.minecraft.net/?user='.$username.'&password='.$password.'&version='.$version);
        $response = explode(':', $request);
        if ($request != 'Old version' && $request != 'Bad login') {
            $this->account = array(
                'current_version' => $response[0],
                'correct_username' => $response[2],
                'session_token' => $response[3],
                'premium_account' => $this->is_premium($username),
                'custom_skin' => $this->custom_skin($username),
                'request_timestamp' => date("dmYhms", mktime(date(h), date(m), date(s), date(m), date(d), date(y)))
            );
            return true;
        } else {
            return false;
        }
    }

    public function is_premium($username) {
        return $this->request('https://www.minecraft.net/haspaid.jsp?user='.$username);
    }

    public function custom_skin($username) {
        $headers = get_headers('https://s3.amazonaws.com/MinecraftSkins/'.$username.'.png');
        if ($headers[7] == 'Content-Type: application/octet-stream') {
            return 'https://s3.amazonaws.com/MinecraftSkins/'.$username.'.png';
        } else {
            return false;
        }
    }

    public function keep_alive($username, $session) {
        $request = $this->request('https://login.minecraft.net/session?name='.$username.'&session='.$session);
        return null;
    }

    public function join_server($username, $session, $server) {
        $request = $this->request('http://session.minecraft.net/game/joinserver.jsp?user='.$username.'&sessionId='.$session.'&serverId='.$server);
        if ($request != 'Bad login') {
            return true;
        } else {
            return false;
        }
    }

    public function check_server($username, $server) {
        $request = $this->request('http://session.minecraft.net/game/checkserver.jsp?user='.$username.'&serverId='.$server);
        if ($request == 'YES') {
            return true;
        } else {
            return false;
        }
    }

}

?>