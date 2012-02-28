# Minecraft Class

This class was developed to provide a set of functions to integrate Minecraft within your projects. Under no circumstances
are you permitted to use this for malicious purposes.

**Example Usage**

`<?php
    include('class.minecraft.php');
    $minecraft = new minecraft();
    if ($minecraft->login('username', 'password', '12') == true) {
        // todo: Make some magic happen.
    }
?>`

**Functions**

`$minecraft->login('username', 'password', 'version');`

This function is used to login to your Minecraft account, taking 3 parameters: username, password and the version of the minecraft
launcher which currently defaults to 12. This returns an array with details such as the url to the users skin file, if that account
is premium (paid), the current login token, the latest version of the games resources, the correctly formatted username and a timestamp
of the request which can be used in cookies to check if the session needs keeping alive or not. The timestamp format is one i have
developed and is formatted as the following: **DDMMYYYHHMMSS** in local server time.
This file was modified by JetBrains PhpStorm (PhpStorm) PS-114.158 for binding GitHub repository