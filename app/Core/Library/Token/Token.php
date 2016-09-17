<?php
namespace Core\Library\Token;
use Core\Contract\View\IExtension;
use Core\Library\View\Html\View;

class Token implements IExtension
{
    /**
     * get token and generate a new one if expired
     *
     * @access public
     * @static static method
     * @return string
     */
    public function make()
    {
        $iMaxTime = 60 * 60 * 24; // token is valid for 1 day
        $sSecureToken = Gundi()->Session->get('secure_token');
        $iStoredTime = Gundi()->Session->get('secure_token_time');

        if ($iMaxTime + $iStoredTime <= time() || empty($sSecureToken)) {
            Gundi()->Session->set('secure_token', md5(uniqid(rand(), true)));
            Gundi()->Session->set('secure_token_time', time());
        }

        return Gundi()->Session->get('secure_token');
    }

    /**
     * checks if CSRF token in session is same as in the form submitted
     *
     * @access public
     * @static static method
     * @return bool
     */
    public function isValid()
    {
        return $_POST['secure_token'] === Gundi()->Session->get('secure_token');
    }

    /**
     * @param View $oView
     */
    public function register(View &$oView)
    {
        $oView->registerFunc('token', [$this, 'generateInput']);
    }

    public function generateInput()
    {
        return '<input type="hidden" name="secure_token" value="' . $this->make() .'">';
    }
}
