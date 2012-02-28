# Minecraft Class

This class was developed to provide a set of functions to integrate Minecraft within your projects. Under no circumstances
are you permitted to use this for malicious purposes.

**Example Usage**

```<?php
    include('class.minecraft.php');
    $minecraft = new minecraft();
    if ($minecraft->login('username', 'password', '12') == true) {
        // todo: Make some magic happen.
    }
?>```

**Functions**

```$minecraft->login('username', 'password', 'version');```

This function is used to login to your Minecraft account, taking 3 parameters: username, password and the version of the minecraft
launcher which currently defaults to **12**. This returns an **array** with details on that users account on success and **false**
otherwise.

```$minecraft->is_premium('username');```

This function determines if the user specified has a premium account or not. It returns **true** if a premium account was found
and **false** otherwise.