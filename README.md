 
# xeonCAPTCHA

A simple CAPTCHA system written in PHP.

[![Latest Stable Version](https://poser.pugx.org/neto737/xeoncaptcha/version)](https://packagist.org/packages/neto737/xeoncaptcha) [![Total Downloads](https://poser.pugx.org/neto737/xeoncaptcha/downloads)](https://packagist.org/packages/neto737/xeoncaptcha) [![Latest Unstable Version](https://poser.pugx.org/neto737/xeoncaptcha/v/unstable)](//packagist.org/packages/neto737/xeoncaptcha) [![License](https://poser.pugx.org/neto737/xeoncaptcha/license)](https://packagist.org/packages/neto737/xeoncaptcha)

## Requirements
- PHP 7.0 or earlier with:
  - GD

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
"neto737/xeoncaptcha": "*"
```
  
## Example

```php
require 'vendor/autoload.php';

use neto737\xeonCAPTCHA;

//If you set the second variable as true your CAPTCHA will be a math CAPTCHA
$xeon = new xeonCAPTCHA(xeonCAPTCHA::IMG_PNG, false);

//This function will return an image
$xeon->generateCAPTCHA(155, 30, 20, 5, 'xeonCAPTCHA', '', 22);
```

## Credits
- <a href="https://github.com/neto737" target="_blank">Neto Melo</a>

## Donate
[![Donate BTC](https://img.shields.io/badge/donate-BTC-ff9900.svg)](https://blockchain.info/address/12oyGgGHYp1NxtoQFUmaoqm1z8XAeTQKUb) [![Donate ETH](https://img.shields.io/badge/donate-ETH-3C3C3D.svg)](https://etherscan.io/address/0xE461A5aC39a86Ec651AB49277637e6d4417257fA)