# Minecraft Class

This class was developed to provide a set of functions to integrate Minecraft within your projects. Under no circumstances
are you permitted to use this for malicious purposes.

#### Example Usage

```php
<?php
    include('class.minecraft.php');
	if ($minecraft->signin('nblackburn', 'rachel19', '12') == true) {
		foreach($minecraft->account as $field => $value) {
			echo($field.'->'.$value.'<br>');
		}
	} else {
		echo('Error!');
	}
?>
```

---

#### Login

```php
$minecraft->login('username', 'password', 'version');
```

This function is used to login to your Minecraft account, taking 3 parameters: username, password and the version of the minecraft
launcher which currently defaults to **12**. This returns **true** if the user was successfully authenticated and **false**
otherwise. An array is also set upon successful login which contains the following...

* **current_version**: The current version of the games resources.
* **correct_username**: A correctly formatted username.
* **session_token**: The current session used to login to the Minecraft client.
* **premium_account**: If the account specified is of premium status.
* **player_skin**: The url to the users custom skin, if applicable.
* **request_timestamp**: the timestamp for the request which is formatted as **DDMMYYYYHHMMSS** in local server time.

```php
$minecraft->account();
```

This array can be called using the function above once the user has logged in to their account otherwise **null** will be returned
when calling this function.

---

#### Is Premium

```php
$minecraft->is_premium('username');
```

This function determines if the user specified has a premium account or not. It takes a single parameter of **username** and
returns **true** if a premium account was detected and **false** otherwise.

---

#### Get Skin

```php
$minecraft->get_skin('username');
```

This function firstly checks the user specified has a premium account, then returns a url to the skin file for that user if
a custom skin was found. It takes a single parameter which is the **username** of the user you which to get the skin file of.

---

#### Keep Alive

```php
$minecraft->keep_alive('username', 'session');
```

This function is used to keep the users current session alive, this command needs sending to the Minecraft servers every 600
ticks (60 seconds) otherwise the user is signed out. It takes 2 parameters, the **username** and **session** returned from signing
in to your account, this can be obtained with the following code...

---

#### Account

```php
$minecraft->account['session_token'];
```

The function returns **null** as the Minecraft server doesnt appear to throw back any exceptions or acknowledgement of the request.

---

#### Render Skin

```php
$minecraft->render_skin($username, $render_type, $size);
```

This function renders the specified player's skin. It takes 3 parameters, the first being the **username** of the player whos skin you wish to render,
the **render_type** which can either be **head** OR **body** and the **size** you would like the rendered image to be. Please note when rendering
the full body, the image width is half the size of the image height. You can include this function directly inside a **img** tag by using the following
example...

```html
<img src="<?php echo($minecraft->render_skin('nblackburn', 'head', 100)) ?>" width="100" height="100">
```