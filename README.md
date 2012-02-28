# Minecraft Class

This class was developed to provide a set of functions to integrate Minecraft within your projects. Under no circumstances
are you permitted to use this for malicious purposes.

**Example Usage**

```php
<?php
    include('class.minecraft.php');
    $minecraft = new minecraft();
    if ($minecraft->login('username', 'password', '12') == true) {
        foreach($minecraft->account as $field => $value) {
            echo('<strong>'.$field.'</strong>:&nbsp;'.$value.'<br>');
        }
    }
?>
```

**Functions**

```php
$minecraft->login('username', 'password', 'version');
```

This function is used to login to your Minecraft account, taking 3 parameters: username, password and the version of the minecraft
launcher which currently defaults to **12**. This returns **true** if the user was successfully authenticated and **false**
otherwise. An array is also set upon successful login which contains the following...

* current_version - The current version of the games resources.
* correct_username - A correctly formatted username.
* session_token - The current session used to login to the Minecraft client.
* premium_account - If the account specified is of premium status.
* custom_skin - The url to the users custom skin, if applicable.
* request_timestamp - the timestamp for the request which is formatted as **DDMMYYYYHHMMSS** in local server time.

This array can be called using the following function...

```php
$minecraft->account();
```

```php
$minecraft->is_premium('username');
```

This function determines if the user specified has a premium account or not. It takes a single parameter of **username** and
returns **true** if a premium account was detected and **false** otherwise.

```php
$minecraft->custom_skin('username');
```

This function checks if the user specified has a custom skin. It takes a single parameter of **username** and either returns
the url to the users skin or **false** if not custom skin was found.

```php
$minecraft->keep_alive('username', 'session');
```

This function is used to keep the users current session alive, this command needs sending to the Minecraft servers every 600
ticks (60 seconds) otherwise the user is signed out. It takes 2 parameters, the **username** and **session** returned from signing
in to your account, this can be obtained with the following code...

```php
$minecraft->account['session_token'];
```

The function returns **null** as the Minecraft server doesnt appear to throw back any exceptions or acknowledgement of the request.