<?php

/**
 *                        ____    _    ____ _____ ____ _   _    _    
 * __  _____  ___  _ __  / ___|  / \  |  _ \_   _/ ___| | | |  / \   
 * \ \/ / _ \/ _ \| '_ \| |     / _ \ | |_) || || |   | |_| | / _ \  
 *  >  <  __/ (_) | | | | |___ / ___ \|  __/ | || |___|  _  |/ ___ \ 
 * /_/\_\___|\___/|_| |_|\____/_/   \_\_|    |_| \____|_| |_/_/   \_\
 *                                                                                                       
 *
 * @package xeonCAPTCHA
 * @author  Neto Melo <neto737@live.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3
 * @version 1.0
 */

namespace neto737;

class xeonCAPTCHA {

    const IMG_PNG = 1;
    const IMG_JPEG = 2;
    const IMG_GIF = 3;
    const IMG_WEBP = 4;

    protected array $CAPTCHA = [];
    protected int $imageType;
    protected bool $mathCAPTCHA;
    protected string $sessionName;

    /**
     * Class initialization
     * 
     * @param int $imageType        Set your image type (PNG, JPEG or GIF)
     * @param bool $mathCAPTCHA     Is your CAPTCHA a math CAPTCHA?
     * @param string $sessionName   Set the default name of xeonCAPTCHA's sessions
     * @throws \RuntimeException    If GD, FreeType or the chosen image type is not available
     */
    public function __construct(int $imageType, bool $mathCAPTCHA = false, string $sessionName = 'xeonCode') {
        $this->imageType = $imageType;
        $this->mathCAPTCHA = $mathCAPTCHA;
        $this->sessionName = $sessionName;

        if (!extension_loaded('gd') || !function_exists('imagetypes')) {
            throw new \RuntimeException('xeonCAPTCHA requires the GD extension.');
        }

        if (!function_exists('imagettftext')) {
            throw new \RuntimeException('xeonCAPTCHA requires GD with FreeType support.');
        }

        if (!$this->isImageTypeSupported($imageType)) {
            throw new \RuntimeException('The chosen image type is not supported by your GD installation.');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * This function will generate your CAPTCHA image with your own preferences
     * 
     * @param int $width    The width of your CAPTCHA image
     * @param int $height   The height of your CAPTCHA image
     * @param int $fontSize The fontsize of your CAPTHCA image
     * @param int $length   The length of characters that will appears in your CAPTCHA image
     * @param string $font  Set your font, xeonCAPTCHA is default
     * @param string $bgRGB You can personalize your CAPTCHA image with RGB (example RRR_GGG_BBB
     * @param int $posY     The Position Y
     * @return boolean      It will return the image
     * @throws \InvalidArgumentException If $font or $bgRGB is invalid
     */
    public function generateCAPTCHA(int $width, int $height, int $fontSize, int $length = 5, string $font = 'xeonCAPTCHA', string $bgRGB = '', int $posY = 33) {
        $fontPath = $this->resolveFont($font);
        $bgColor = $this->parseRGB($bgRGB);

        $this->CAPTCHA['image'] = imagecreatetruecolor($width, $height);
        $this->generateString($length);
        $this->CAPTCHA['font'] = $fontPath;
        $this->CAPTCHA['bg_color'] = $bgColor;
        $this->CAPTCHA['color_DCDEE8'] = imagecolorallocate($this->CAPTCHA['image'], 220, 222, 232);
        $this->CAPTCHA['pos_x'] = 0.1;
        $this->CAPTCHA['pos_y'] = $posY;

        $_SESSION[$this->sessionName] = $this->CAPTCHA['session']; // SET SESSION DATA

        $this->captchaRGB($width, $height); // SET THE IMAGE BACKGROUND COLOR

        $this->putText($fontSize); // PUT THE TEXT INTO THE IMAGE

        $this->imageEllipse();

        self::output();
        self::cleanUp();
        return true;
    }

    /**
     * Returns the path of a font bundled with xeonCAPTCHA
     *
     * @param string $font The font name, without path or extension
     * @return string      The path of the font file
     * @throws \InvalidArgumentException If the font name is not a plain name or the font does not exist
     */
    private function resolveFont(string $font): string {
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $font)) {
            throw new \InvalidArgumentException('The font must be a plain font name, without a path or extension.');
        }

        $fontPath = __DIR__ . '/' . $font . '.ttf';

        if (!is_file($fontPath)) {
            throw new \InvalidArgumentException(sprintf('The font "%s" was not found.', $font));
        }

        return $fontPath;
    }

    /**
     * Parses a background colour in the RRR_GGG_BBB format
     *
     * @param string $bgRGB The colour, for example 255_255_255, or an empty string for white
     * @return int[]        The red, green and blue values
     * @throws \InvalidArgumentException If the colour is not in the RRR_GGG_BBB format
     */
    private function parseRGB(string $bgRGB): array {
        if ($bgRGB === '') {
            return [255, 255, 255];
        }

        if (!preg_match('/^(\d{1,3})_(\d{1,3})_(\d{1,3})$/', $bgRGB, $matches) || max($matches[1], $matches[2], $matches[3]) > 255) {
            throw new \InvalidArgumentException('The background colour must be in the RRR_GGG_BBB format, with values from 0 to 255.');
        }

        return [(int) $matches[1], (int) $matches[2], (int) $matches[3]];
    }

    private function generateString($length) {
        if (!$this->mathCAPTCHA) {
            $this->CAPTCHA['string'] = substr($this->secureShuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
            $this->CAPTCHA['session'] = $this->CAPTCHA['string'];
        } else {
            $this->CAPTCHA['number_1'] = random_int(0, 10) * random_int(1, 3);
            $this->CAPTCHA['number_2'] = random_int(0, 10) * random_int(1, 3);
            $this->CAPTCHA['string'] = $this->CAPTCHA['number_1'] . '+' . $this->CAPTCHA['number_2'];
            $this->CAPTCHA['session'] = $this->CAPTCHA['number_1'] + $this->CAPTCHA['number_2'];
        }
    }

    /**
     * Fisher-Yates shuffle using a cryptographically secure random source
     *
     * @param string $string The string to shuffle
     * @return string        The shuffled string
     */
    private function secureShuffle(string $string): string {
        $chars = str_split($string);

        for ($i = count($chars) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
        }

        return implode('', $chars);
    }

    private function putText($fontSize) {
        for ($i = 0; $i < strlen($this->CAPTCHA['string']); $i++) {
            imagettftext($this->CAPTCHA['image'], random_int($fontSize - 2, $fontSize), random_int(-40, 40), intval($this->CAPTCHA['pos_x'] * 30), $this->CAPTCHA['pos_y'], imagecolorallocate($this->CAPTCHA['image'], 127, 127, random_int(0, 255)), $this->CAPTCHA['font'], substr($this->CAPTCHA['string'], $i, 1));
            $this->CAPTCHA['pos_x'] ++;
        }
    }

    private function captchaRGB($width, $height) {
        imagefilledrectangle($this->CAPTCHA['image'], 0, 0, $width, $height, imagecolorallocate($this->CAPTCHA['image'], $this->CAPTCHA['bg_color'][0], $this->CAPTCHA['bg_color'][1], $this->CAPTCHA['bg_color'][2]));
    }

    private function imageEllipse() {
        for ($i = 0; $i < 10; $i++) {
            $cx = random_int(-1 * intdiv(110, 2), 110 + intdiv(110, 2));
            $cy = random_int(-1 * intdiv(40, 2), 40 + intdiv(40, 2));
            $h = random_int(intdiv(40, 2), 2 * 40);
            $w = random_int(intdiv(110, 2), 2 * 110);
            imageellipse($this->CAPTCHA['image'], $cx, $cy, $w, $h, $this->CAPTCHA['color_DCDEE8']);
        }
    }

    private function isImageTypeSupported(int $imageType): bool {
        switch ($imageType) {
            case self::IMG_JPEG:
                return (imagetypes() & \IMG_JPG) !== 0;

            case self::IMG_GIF:
                return (imagetypes() & \IMG_GIF) !== 0;

            case self::IMG_WEBP:
                return (imagetypes() & \IMG_WEBP) !== 0;

            default:
                return (imagetypes() & \IMG_PNG) !== 0;
        }
    }

    protected function output() {
        header('Cache-Control: no-store, no-cache');
        header('Pragma: no-cache');

        switch ($this->imageType) {
            case self::IMG_JPEG:
                header('Content-Type: image/jpeg');
                imagejpeg($this->CAPTCHA['image']);
                break;

            case self::IMG_GIF:
                header('Content-Type: image/gif');
                imagegif($this->CAPTCHA['image']);
                break;

            case self::IMG_WEBP:
                header('Content-Type: image/webp');
                imagewebp($this->CAPTCHA['image']);
                break;

            default:
                header('Content-Type: image/png');
                imagepng($this->CAPTCHA['image']);
                break;
        }
    }

    private function cleanUp() {
        imagedestroy($this->CAPTCHA['image']);
    }

    /**
     * This function will check if user code is correct
     * 
     * @param string $inputCode The code to verify if it's valid or not
     * @return boolean          Will return TRUE to valid codes and FALSE to invalid codes
     */
    public function validateCAPTCHA($inputCode) {
        $expectedCode = $_SESSION[$this->sessionName] ?? null;
        unset($_SESSION[$this->sessionName]);

        if (!is_string($inputCode) || (!is_string($expectedCode) && !is_int($expectedCode))) {
            return false;
        }

        $expectedCode = strtoupper((string) $expectedCode);
        $inputCode = strtoupper(trim($inputCode));

        if ($expectedCode === '' || $inputCode === '') {
            return false;
        }

        return hash_equals($expectedCode, $inputCode);
    }

}
