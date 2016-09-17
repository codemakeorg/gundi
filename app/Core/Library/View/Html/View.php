<?php
namespace Core\Library\View\Html;

use Core\Contract\View\IExtension;
use Core\Library\View\AbstractView;

class View extends AbstractView
{
    /**
     * @var \Core\Library\Theme\Theme
     */
    private $_oTheme;
    private $_sContent = '';
    public $_aFunctions = [];

    /**
     * add extension
     * @param IExtension $oExtension
     * @return $this
     */
    public function loadExtension(IExtension $oExtension)
    {
        $oExtension->register($this);

        return $this;
    }

    /**
     * register function
     * @param string $sName
     * @param callable $cCallBack
     * @return  $this
     */
    public function registerFunc($sName, $cCallBack)
    {
        $this->_aFunctions[$sName] = $cCallBack;
        return $this;
    }

    /**
     * @param $sName - function name
     * @return bool
     */
    public function hasFunc($sName)
    {
        return isset($this->_aFunctions[$sName]);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->_sContent;
    }

    /**
     * @var \Core\Contract\View\IViewProvider
     */
    private $_oViewProvider = null;

    public function __call($sName, $mArguments)
    {
        if (is_null($this->_oViewProvider)) {
            throw  new \LogicException('ViewProvider does not exits');
        }

        if (method_exists($this->_oViewProvider, $sName)) {
            call_user_func_array([$this->_oViewProvider, $sName], $mArguments);
        } elseif (isset($this->_aFunctions[$sName])) {
            return call_user_func_array($this->_aFunctions[$sName], $mArguments);
        } else {
            throw new \LogicException('The function "' . $sName . '" does not exits');
        }

        return $this;
    }

    protected function _render($sTpl, $aVars = [])
    {
        extract($aVars);
        ob_start();
        include $sTpl;
        return ob_get_clean();
    }

    /**
     * Render the template and layout.
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        try {
            $sContent = $this->_render($this->_oViewProvider->getTemplateFile($this->_oTheme->getTemplate()), array_merge($this->_aGlobalVars, $this->_aVars));

            $this->_sContent = $sContent;
            $sLayout = $this->_oTheme->getLayout();

            if (!is_null($sLayout)) {
                $sContent = $this->_render($this->_oViewProvider->getThemeDir() . $sLayout . $this->_oViewProvider->getTemplateExt(), $this->_aGlobalVars);
            }

            return $sContent;
        } catch (\Exception $e) {
            if (ob_get_length() > 0) {
                ob_end_clean();
            }
            throw $e;
        }
    }

    public function fetch($sTpl, $aData = [])
    {
        try {
            extract($aData);
            ob_start();
            include $this->_oViewProvider->getTemplateFile($sTpl);
            return ob_get_clean();
        } catch (\Exception $e) {
            if (ob_get_length() > 0) {
                ob_end_clean();
            }
            throw $e;
        }
    }

    /**
     * @return \Core\Contract\View\IViewProvider
     */
    public function &getViewProvider()
    {
        return $this->_oViewProvider;
    }

    /**
     * @param \Core\Contract\View\IViewProvider $oViewProvider
     * @return $this
     */
    public function setViewProvider($oViewProvider)
    {
        $this->_oViewProvider = $oViewProvider;
        return $this;
    }

    /**
     * @return \Core\Library\Theme\Theme
     */
    public function getTheme()
    {
        return $this->_oTheme;
    }

    /**
     * @param \Core\Library\Theme\Theme $oTheme
     * @return  $this
     */
    public function setTheme($oTheme)
    {
        $this->_oTheme = $oTheme;
        return $this;
    }
}