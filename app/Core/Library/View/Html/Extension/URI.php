<?php
namespace Core\Library\View\Html\Extension;

use Core\Contract\View\IExtension;
use Core\Library\View\Html\View;

class URI implements IExtension
{
    /**
     * Register extension functions.
     * @param View $oView
     * @return null
     */
    public function register(View &$oView)
    {
        $oView->registerFunc('uri', [$this, 'uri']);
    }

    public function uri()
    {
        return Gundi()->Url;
    }
}