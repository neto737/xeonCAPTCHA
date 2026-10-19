<h1 align="center">xeonCAPTCHA</h1>

<p align="center">
  A simple, dependency-free CAPTCHA for PHP.<br>
  Show a distorted text or math challenge as an image and check the answer with one call.
</p>

<p align="center">
  <a href="https://packagist.org/packages/neto737/xeoncaptcha"><img src="https://img.shields.io/packagist/v/neto737/xeoncaptcha?label=packagist" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/neto737/xeoncaptcha"><img src="https://img.shields.io/packagist/dt/neto737/xeoncaptcha" alt="Total Downloads"></a>
  <img src="https://img.shields.io/badge/php-%E2%89%A5%207.4-777BB4?logo=php&logoColor=white" alt="PHP 7.4 or later">
  <a href="LICENSE"><img src="https://img.shields.io/packagist/l/neto737/xeoncaptcha" alt="License"></a>
</p>

---

## Features

- **Two challenge types:** random letters, or a simple sum such as `12+9`.
- **Four image formats:** PNG, JPEG, GIF and WebP.
- **Customisable image:** size, font size, number of characters, background colour and text position.
- **One-call validation:** case-insensitive, ignores surrounding spaces, and each CAPTCHA can only be used once.
- **Secure by default:** answers are generated with `random_int()`, compared with the timing-safe `hash_equals()`, and the image is sent with no-cache headers.
- **No dependencies** other than PHP's GD extension. The answer is kept in the PHP session.

## Requirements

- PHP 7.4 or later
- The GD extension with FreeType support, and support for the image format you choose (for example, WebP)
- PHP sessions

## Installation

```sh
composer require neto737/xeoncaptcha
```

## Quick start

A CAPTCHA has two parts: a script that sends the image, and a check when the form is submitted.

**1. Send the image** (for example, `captcha.php`):

```php
require 'vendor/autoload.php';

use neto737\xeonCAPTCHA;

// Pass true as the second argument for a math CAPTCHA
$xeon = new xeonCAPTCHA(xeonCAPTCHA::IMG_PNG, false);

// 155×30 image, font size 20, 5 characters, bundled font, white background, text baseline at 22px
$xeon->generateCAPTCHA(155, 30, 20, 5, 'xeonCAPTCHA', '', 22);
```

**2. Show it in your form:**

```html
<form method="POST" action="submit.php">
    <img src="captcha.php" alt="CAPTCHA">
    <input type="text" name="CAPTCHA" autocomplete="off">
    <button type="submit">Send</button>
</form>
```

**3. Check the answer** (in `submit.php`):

```php
require 'vendor/autoload.php';

use neto737\xeonCAPTCHA;

$xeon = new xeonCAPTCHA(xeonCAPTCHA::IMG_PNG);

if ($xeon->validateCAPTCHA($_POST['CAPTCHA'] ?? '')) {
    // Correct answer
} else {
    // Wrong answer, or the CAPTCHA was already used
}
```

> [!NOTE]
> The answer is removed from the session after every check, right or wrong. After a wrong answer, show a new image. If you set a custom session name when creating the image, use the same name when you validate.

## API reference

### `new xeonCAPTCHA($imageType, $mathCAPTCHA = false, $sessionName = 'xeonCode')`

Starts the PHP session if it isn't started yet.

| Argument | Type | Description |
| --- | --- | --- |
| `$imageType` | `int` | `xeonCAPTCHA::IMG_PNG`, `IMG_JPEG`, `IMG_GIF` or `IMG_WEBP` |
| `$mathCAPTCHA` | `bool` | `true` for a sum (answer from 0 to 60), `false` for letters |
| `$sessionName` | `string` | The session key where the answer is stored |

### `generateCAPTCHA($width, $height, $fontSize, $length = 5, $font = 'xeonCAPTCHA', $bgRGB = '', $posY = 33)`

Sends the image to the browser along with its `Content-Type` and no-cache headers, then returns `true`. Call it before any other output.

| Argument | Type | Description |
| --- | --- | --- |
| `$width`, `$height` | `int` | Image size in pixels |
| `$fontSize` | `int` | Font size. Each character is drawn up to 2 points smaller and rotated at random. |
| `$length` | `int` | Number of letters (A–Z, no repeats). Ignored for math CAPTCHAs. |
| `$font` | `string` | Name of a `.ttf` file in the library's `src/` directory, without the extension. Only the bundled `xeonCAPTCHA` font is included. |
| `$bgRGB` | `string` | Background colour as `RRR_GGG_BBB`, for example `240_240_255`. Empty means white. |
| `$posY` | `int` | Vertical position of the text baseline, in pixels |

> [!NOTE]
> Characters are placed 30px apart, and the noise is sized for a small image. Sizes close to the examples above (about 155×30 to 155×40, font size 20) give the best results.

### `validateCAPTCHA($inputCode): bool`

Returns `true` if `$inputCode` matches the current CAPTCHA. Letter case and surrounding spaces are ignored. Non-string input (for example, `CAPTCHA[]=x`) returns `false`. The stored answer is always removed, so each CAPTCHA can only be checked once.

## Error handling

Problems with your setup or arguments are thrown as exceptions:

```php
try {
    $xeon = new xeonCAPTCHA(xeonCAPTCHA::IMG_WEBP);
    $xeon->generateCAPTCHA(155, 30, 20, 5, 'xeonCAPTCHA', '240_240_255', 22);
} catch (\RuntimeException $e) {
    // GD, FreeType or the chosen image format is not available
} catch (\InvalidArgumentException $e) {
    // $font is not a plain font name or the font doesn't exist,
    // or $bgRGB is not in the RRR_GGG_BBB format (0 to 255)
}
```

A wrong answer is not an error: `validateCAPTCHA()` returns `false`.

**Upgrading from 1.1.3?** Version 1.1.4 needs PHP 7.4 or later. It now throws the exceptions above instead of failing later with warnings or fatal errors. Nothing else changes.

## Examples

The [`examples/`](examples) folder contains a text CAPTCHA form, a math CAPTCHA form, and the script that sends their images.

## Contributing

Bug reports and pull requests are welcome. Please keep the code compatible with PHP 7.4.

## Credits

- Created and maintained by [Neto Melo](https://github.com/neto737).
- The bundled font is "Cube vol.2" by TamaFactory.

## Support the project :heart:

If this library saves you time, you can support its development via [Liberapay](https://liberapay.com/neto737), [PayPal](https://paypal.me/neto737) or crypto:

```
BTC: bc1q89ntljt5lk7g9z68f5cjs83qfm2xme7g4hkur7
ETH: 0xeef9220639F14E7A0FD825AAAd0574e5a8aD7A4B
LTC: ltc1q508qfkd09vyya6c5zkfx4r248pf3ezj9ngjdr2
```

## License

Released under the [GNU General Public License v3.0](LICENSE).
