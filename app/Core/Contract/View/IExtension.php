<?php
namespace Core\Contract\View;

use Core\Library\View\Html\View;

interface IExtension
{
    /**
     * @param View $oView
     */
    public function register(View &$oView);
}