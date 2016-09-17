<?php
namespace Core\Library\View\Html;

use Core\Contract\View\IViewProvider;

class ViewProvider implements IViewProvider
{
    private $_aFindedThemeTpl = [];
    private $_sThemeDir = '';
    private $_sTplDir = '';
    private $_sExt = '.php';
    private $_sModuleName = 'System';
    private $_sModulesDir = '';

    public function getTemplateFile($sTemplate)
    {
        $sTpl = file_exists($this->getTplFromTheme($sTemplate))
            ? $this->getTplFromTheme($sTemplate)
            : $this->getTplFromModule($sTemplate);
        return $sTpl;
    }

    private function getTplFromTheme($sTpl)
    {
        if (!isset($this->_aFindedThemeTpl[$sTpl])) {
            $aTplParts = explode(':', $sTpl);
            $sTplDir = str_replace(substr($this->_sTplDir, 0, strpos($this->_sTplDir, '/') + 1), '', $this->_sTplDir);
            $this->_aFindedThemeTpl[$sTpl] = (count($aTplParts) > 1)
                ? $this->_sThemeDir . '/' . $aTplParts[0] . '/' . $sTplDir . '/' . $aTplParts[1] . $this->_sExt
                : $this->_sThemeDir . '/' . $this->_sModuleName  .  '/' . $sTplDir . '/' . $aTplParts[0] . $this->_sExt;
        }
        return $this->_aFindedThemeTpl[$sTpl];
    }

    private function getTplFromModule($sTpl)
    {
        $aTplParts = explode(':', $sTpl);
        return (count($aTplParts) > 1)
            ? $this->_sModulesDir . '/' . $aTplParts[0] . '/' . $this->_sTplDir . '/' . $aTplParts[1] . $this->_sExt
            : $this->_sModulesDir . '/' . $this->_sModuleName . '/' . $this->_sTplDir . '/' . $aTplParts[0] . $this->_sExt;
    }

    public function setThemeDir($sThemeDir)
    {
        if (is_null($sThemeDir) || !is_dir($sThemeDir)) {
            throw  new  \LogicException('The theme path "' . $sThemeDir . '" does not exist.');
        }
        $this->_sThemeDir = $sThemeDir;
        return $this;
    }

    public function setTemplateExt($sExt)
    {
        $this->_sExt = $sExt;
        return $this;
    }

    public function setTemplateDir($sTplDir)
    {
        $this->_sTplDir = $sTplDir;
        return $this;
    }

    public function getTemplateExt()
    {
        return $this->_sExt;
    }

    public function getThemeDir()
    {
        return $this->_sThemeDir;
    }

    public function getTemplateDir()
    {
        return $this->_sTplDir;
    }

    public function setModuleName($sModuleName)
    {
        $this->_sModuleName = $sModuleName;
        return $this;
    }

    public function getModuleName()
    {
        return $this->_sModuleName;
    }

    public function setModulesDir($sModulesDir)
    {
        if (!is_dir($sModulesDir)) {
            throw  new  \LogicException('The module path "' . $sModulesDir . '" does not exist.');
        }
        $this->_sModulesDir = $sModulesDir;
        return $this;
    }

    public function getModulesDir()
    {
        return $this->_sModulesDir;
    }
}