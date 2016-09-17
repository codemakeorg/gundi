<?php

namespace Core\Library\Form;

use Core\Contract\Request\IRequest;
use Core\Library\Theme\Theme;
use Core\Library\View\AbstractView;
use Core\Library\View\Html\View as HtmlView;

class Builder
{
    /**
     * @var IRequest
     */
    protected $oRequest;
    /**
     * @var Theme
     */
    protected $oTheme;

    private $_sFormTemplateDir = 'View/Form';

    public function __construct(IRequest $oRequest, Theme $oTheme)
    {
        $this->oRequest = $oRequest;
        $this->oTheme = clone $oTheme;
        $this->oTheme->setLayout(null);
        $this->oTheme->setTheme(null);
    }

    /**
     * @param AbstractView $oView
     * @param array $aFields - fields data
     * @return Form
     */
    public function build(AbstractView $oView, array $aFields, array $aFormData = [])
    {
        $oView = $this->getConfiguredView(clone $oView);
        $oForm = new Form($oView, $this->oTheme, $aFormData);
        foreach ($aFields as $aField) {
            $sType = $aField['type'];
            $oForm->addField($sType, $aField);
            $mValue = (isset($this->oRequest[$aField['name']]))
                ? $this->oRequest[$aField['name']]
                : (isset($aField['value']) ? $aField['value'] : null);
            $oForm->setFieldValue($aField['name'], $mValue);
        }
        return $oForm;
    }

    private function getConfiguredView(AbstractView $oView)
    {
        if ($oView instanceof HtmlView) {
            $oViewProvider = clone $oView->getViewProvider();
            $oViewProvider->setTemplateDir($this->_sFormTemplateDir);
            $oView->setViewProvider($oViewProvider);
            $oView->setTheme($this->oTheme);
        }

        return $oView;
    }

    /**
     * @param string $sFormTemplateDir
     * @return  $this
     */
    public function setFormTemplateDir($sFormTemplateDir)
    {
        $this->_sFormTemplateDir = $sFormTemplateDir;
        return $this;
    }
}