<?php
/**
 * Product: Gundi
 * Class:   captcha
 * User:    Kalil uulu Bolot(bolotkalil@gmail.com)
 * Version: 0.0.1
 * Date:    20.11.2015
 * Time:    20:57
 */

namespace Core\Library\Util;

class Captcha
{

    private $sCode;
    private $rImage;

    /**
     * Generate captcha code or captcha image
     * @return object
     */
    public function generate()
    {
        $this->sCode = rand(1000, 9999);
        $this->rImage = imagecreatetruecolor(40, 18);
        $rBackGround = imagecolorallocate($this->rImage, 22, 86, 165); //background color blue
        $rText = imagecolorallocate($this->rImage, 255, 255, 255);//text color white
        for ($i = 0; $i < 3; $i++) {
            imageline($this->rImage, 0, rand() % 50, 200, rand() % 50, $rText);
        }
        imagefill($this->rImage, 0, 0, $rBackGround);

        imagestring($this->rImage, 5, 0, 0, $this->sCode, $rText);
        return $this;
    }

    /**
     *
     * Get captcha code or image
     * @param bool $bReturnCode
     * @return mixed
     */

    public function get($bReturnCode = false)
    {
        if ($bReturnCode == false) {
            header("Cache-Control: no-cache, must-revalidate");
            header('Content-type: image/png');
            imagepng($this->rImage);
            imagedestroy($this->rImage);
            return true;
        }
        return $this->sCode;
    }
}
