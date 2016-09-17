<?php
namespace Core\Library\Theme;

class Theme
{
    private $_sTheme;
    private $_sLayout;
    private $_sTpl;

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->_sTpl;
    }

    /**
     * @param mixed $sTpl
     * @return  $this
     */
    public function setTemplate($sTpl)
    {
        $this->_sTpl = $sTpl;
        return $this;
    }

    public function __construct()
    {
        $this->_sTheme = Gundi()->config->getParam('core.default_theme_name');
        $this->_sLayout = Gundi()->config->getParam('core.theme_layout');
    }

    /**
     * @return mixed
     */
    public function getTheme()
    {
        return $this->_sTheme;
    }

    /**
     * @param mixed $sTheme
     * @return $this
     */
    public function setTheme($sTheme)
    {
        $this->_sTheme = $sTheme;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLayout()
    {
        return $this->_sLayout;
    }

    /**
     * @param mixed $sLayout
     * @return $this
     */
    public function setLayout($sLayout)
    {
        $this->_sLayout = $sLayout;
        return $this;
    }
}