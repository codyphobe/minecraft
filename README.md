# Minecraft Class

This class was developed to provide a set of functions to integrate Minecraft within your projects.
Under no circumstances are you permitted to use this for malicious purposes.

#### Example Usage

```php
require 'class.minecraft.php';
if ($minecraft->signin('username', 'password')) {
    foreach($minecraft->account as $field => $value) {
        echo $field . '->' . $value . '<br>';
    }
} else {
    echo $minecraft->getLastError();
}
```

---

#### Login

```php
$minecraft->signin('username', 'password', 'version');
```

This function is used to login to your Minecraft account, taking 3 parameters:
username (or email since Mojang account migration), password and the version of the minecraft launcher (optional)
which is currently **12** by defaults. This returns **true** if the user was successfully authenticated and **false**
otherwise. An array is also set upon successful login which contains the following...

* **current_version**: The current version of the games resources.
* **correct_username**: A correctly formatted username.
* **session_token**: The current session used to login to the Minecraft client.
* **premium_account**: If the account specified is of premium status.
* **player_skin**: The url to the users custom skin, if applicable.
* **request_timestamp**: the timestamp for the request which is formatted as **DDMMYYYYHHMMSS** in local server time.

If the authentication fail, you can use the ```getLastError()``` method to see why.

---

#### Is Premium

```php
$minecraft->isPremium('username');
```

This function determines if the user specified has a premium account or not.
It takes a single parameter of **username** (optional if already used successfully the signin method)
and returns **true** if a premium account was detected and **false** otherwise.

---

#### Get Skin Url

```php
$minecraft->getSkinUrl('username');
```

This function firstly checks the user specified has a premium account,
then returns an url to the skin file for that user if a custom skin was found.
It takes a single parameter which is the **username** of the user you which to
get the skin file of (optional if already used successfully the signin method).

---

#### Keep Alive

```php
$minecraft->keepAlive('session', 'username');
```

This function is used to keep the user's current session alive,
this command needs to be sent to the Minecraft servers every 600
ticks (60 seconds) otherwise the user is signed out. It takes 2 parameters,
the **username** and **session** returned from signing into the account
(optionals if already used successfully the signin method),
this can be obtained with the code above...

---

#### Account

```php
$minecraft->account['session_token'];
```

This variable is **false** if you didn't sign in correctly.

---

#### Render Skin

```php
$minecraft->renderSkin($render_type, $size, $username);
```

This function renders the specified player's skin. It takes 3 parameters, the first being the **username** of the player who's skin you wish to render,
the **render_type** which can either be **head** OR **body** and the **size**
you would like the rendered image to be.
Please note when rendering the full body, the image width is half the size of the image height.
You can include this function directly inside a **img** tag by using the following
example...

```html
<img src="<?= $minecraft->render_skin('nblackburn', 'head', 100) ?>">
```

---

#### Get Username

```php
$minecraft->getUsername();
```

This function return the username of the last successfully signed in user.

If no user has been successfully signed in, this function will throw an **exception**.

#### Get Last Error

```php
$minecraft->getLastError();
```

This function can be called after an unsuccessfull call  of signin to know why it failed.
