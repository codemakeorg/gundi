<?php
namespace Core\Contract\View;


use Core\Library\Component\Component;
use Core\Library\View\Html\View as HtmlView;
use Core\Library\View\JsonView;

interface IViewFactory
{
    /**
     * @param Component $oComponent
     * @param string $sType
     * @return HtmlView|JsonView
     */
    public function create(Component &$oComponent, $sType = 'html', $sTplName = 'index');
}