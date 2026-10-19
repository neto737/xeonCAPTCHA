 
# xeonCAPTCHA

A simple CAPTCHA system written in PHP.

[![Latest Stable Version](https://poser.pugx.org/neto737/xeoncaptcha/version)](https://packagist.org/packages/neto737/xeoncaptcha) [![Total Downloads](https://poser.pugx.org/neto737/xeoncaptcha/downloads)](https://packagist.org/packages/neto737/xeoncaptcha) [![Latest Unstable Version](https://poser.pugx.org/neto737/xeoncaptcha/v/unstable)](//packagist.org/packages/neto737/xeoncaptcha) [![License](https://poser.pugx.org/neto737/xeoncaptcha/license)](https://packagist.org/packages/neto737/xeoncaptcha)

## Requirements
- PHP 7.4 or later with:
  - GD (with FreeType support)

## Installation

To install the xeonCAPTCHA, you will need to be using [Composer](http://getcomposer.org/) in your project. If you aren't using Composer yet, it's really simple! Here's how to install composer and the xeonCAPTCHA.
```sh
# Install Composer
curl -sS https://getcomposer.org/installer | php

# Add the xeonCAPTCHA as a dependency
php composer.phar require neto737/xeoncaptcha
```

Next, require Composer's autoloader, in your application, to automatically load the xeonCAPTCHA in your project:

```php
require 'vendor/autoload.php';

use neto737\xeonCAPTCHA;
```

Or if put the following in your `composer.json`:

```json
"neto737/xeoncaptcha": "^1.1"
```
  
## Example

### Displaying the CAPTCHA

Create a script (for example `captcha.php`) that outputs the image:

```php
require 'vendor/autoload.php';

use neto737\xeonCAPTCHA;

//If you set the second variable as true your CAPTCHA will be a math CAPTCHA
$xeon = new xeonCAPTCHA(xeonCAPTCHA::IMG_PNG, false);

//This function sends the image to the browser
$xeon->generateCAPTCHA(155, 30, 20, 5, 'xeonCAPTCHA', '', 22);
```

Then show it in your form:

```html
<form method="POST" action="submit.php">
    <img src="captcha.php" alt="CAPTCHA">
    <input type="text" name="CAPTCHA">
    <button type="submit">Send</button>
</form>
```

### Validating the answer

```php
require 'vendor/autoload.php';

use neto737\xeonCAPTCHA;

//If you set a custom session name when generating the image, use the same one here
$xeon = new xeonCAPTCHA(xeonCAPTCHA::IMG_PNG, false);

if ($xeon->validateCAPTCHA($_POST['CAPTCHA'] ?? '')) {
    //Correct answer
} else {
    //Wrong answer
}
```

Each CAPTCHA can only be validated once: the answer is removed from the session after every check, whether it was correct or not.

## Credits
- <a href="https://github.com/neto737" target="_blank">Neto Melo</a>

## Donate
[![Donate BTC](https://img.shields.io/badge/donate-BTC-ff9900.svg)](https://blockchain.info/address/12oyGgGHYp1NxtoQFUmaoqm1z8XAeTQKUb) [![Donate ETH](https://img.shields.io/badge/donate-ETH-3C3C3D.svg)](https://etherscan.io/address/0xE461A5aC39a86Ec651AB49277637e6d4417257fA)