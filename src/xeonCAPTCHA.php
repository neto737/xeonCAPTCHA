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
     */
    public function __construct(int $imageType, bool $mathCAPTCHA = false, string $sessionName = 'xeonCode') {
        $this->imageType = $imageType;
        $this->mathCAPTCHA = $mathCAPTCHA;
        $this->sessionName = $sessionName;

        if (!extension_loaded('gd') && !function_exists('gd_info')) {
            return false;
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
     */
    public function generateCAPTCHA(int $width, int $height, int $fontSize, int $length = 5, string $font = 'xeonCAPTCHA', string $bgRGB = '', int $posY = 33) {
        $this->CAPTCHA['image'] = imagecreatetruecolor($width, $height);
        $this->generateString($length);
        $this->CAPTCHA['font'] = __DIR__ . '/' . $font . '.ttf';
        $this->CAPTCHA['bg_color'] = explode('_', $bgRGB);
        $this->CAPTCHA['color_FFF'] = imagecolorallocate($this->CAPTCHA['image'], 255, 255, 255);
        $this->CAPTCHA['color_DCDEE8'] = imagecolorallocate($this->CAPTCHA['image'], 220, 222, 232);
        $this->CAPTCHA['pos_x'] = 0.1;
        $this->CAPTCHA['pos_y'] = $posY;

        $_SESSION[$this->sessionName] = $this->CAPTCHA['session']; // SET SESSION DATA

        $this->captchaRGB($bgRGB, $width, $height); // SET THE IMAGE BACKGROUND COLOR

        $this->putText($length, $fontSize); // PUT THE TEXT INTO THE IMAGE

        $this->imageEllipse();

        self::output();
        self::cleanUp();
        return true;
    }

    private function generateString($length) {
        if (!$this->mathCAPTCHA) {
            $this->CAPTCHA['string'] = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
            $this->CAPTCHA['session'] = $this->CAPTCHA['string'];
        } else {
            $this->CAPTCHA['number_1'] = rand(0, 10) * rand(1, 3);
            $this->CAPTCHA['number_2'] = rand(0, 10) * rand(1, 3);
            $this->CAPTCHA['string'] = $this->CAPTCHA['number_1'] . '+' . $this->CAPTCHA['number_2'];
            $this->CAPTCHA['session'] = $this->CAPTCHA['number_1'] + $this->CAPTCHA['number_2'];
        }
    }

    private function putText($length, $fontSize) {
        for ($i = 0; $i < $length; $i++) {
            imagettftext($this->CAPTCHA['image'], mt_rand($fontSize - 2, $fontSize), mt_rand(-40, 40), intval($this->CAPTCHA['pos_x'] * 30), $this->CAPTCHA['pos_y'], imagecolorallocate($this->CAPTCHA['image'], 127, 127, mt_rand(0, 255)), $this->CAPTCHA['font'], substr($this->CAPTCHA['string'], $i, 1));
            $this->CAPTCHA['pos_x'] ++;
        }
    }

    private function captchaRGB($bgRGB, $width, $height) {
        if (strlen($bgRGB) > 0) {
            imagefilledrectangle($this->CAPTCHA['image'], 0, 0, $width, $height, imagecolorallocate($this->CAPTCHA['image'], $this->CAPTCHA['bg_color'][0], $this->CAPTCHA['bg_color'][1], $this->CAPTCHA['bg_color'][2]));
        } else {
            imagefilledrectangle($this->CAPTCHA['image'], 0, 0, $width, $height, $this->CAPTCHA['color_FFF']);
        }
    }

    private function imageEllipse() {
        for ($i = 0; $i < 10; $i++) {
            $cx = (int) rand(-1 * (110 / 2), 110 + (110 / 2));
            $cy = (int) rand(-1 * (40 / 2), 40 + (40 / 2));
            $h = (int) rand(40 / 2, 2 * 40);
            $w = (int) rand(110 / 2, 2 * 110);
            imageellipse($this->CAPTCHA['image'], $cx, $cy, $w, $h, $this->CAPTCHA['color_DCDEE8']);
        }
    }

    protected function output() {
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
        if (!isset($_SESSION[$this->sessionName]) || empty($_SESSION[$this->sessionName]) || empty($inputCode) || (strtoupper($_SESSION[$this->sessionName]) !== trim(strtoupper($inputCode)))) {
            unset($_SESSION[$this->sessionName]);
            return false;
        }

        unset($_SESSION[$this->sessionName]);
        return true;
    }

}
