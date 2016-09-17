<?php
namespace Core\Contract\View;

interface IViewProvider
{
    public function getTemplateFile($sTemplate);

    /**
     * @param $sExt - template expansion
     * @return $this
     */
    public function setTemplateExt($sExt);

    /**
     * @return string - template expansion
     */
    public function getTemplateExt();

    /**
     * @param $sThemeDir - theme directory
     * @return $this
     */
    public function setThemeDir($sThemeDir);

    /**
     * @return string - theme directory
     */
    public function getThemeDir();

    public function setTemplateDir($sTplDir);

    public function getTemplateDir();

    /**
     * @param string $sModuleName
     * @return $this
     */
    public function setModuleName($sModuleName);

    public function getModuleName();

    public function setModulesDir($sModulesDir);

    public function getModulesDir();
}