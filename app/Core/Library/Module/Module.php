<?php

namespace Core\Library\Module;


class Module
{
    static private $_sDirModule;

    /**
     * Initial Core modules.
     */
    public static function loadCoreModules()
    {
        self::$_sDirModule = Gundi()->config->getParam('core.dir_module');
        self::loadModules(Gundi()->config->getParam('core.modules'));
    }

    public static function loadModules($aModules)
    {
        foreach ($aModules as &$sModuleName) {
            self::loadModule($sModuleName);
        }
    }

    /**
     * Initial module
     * @param $sModuleName
     */
    public static function loadModule($sModuleName)
    {
        $sBootsFile = self::$_sDirModule . $sModuleName . GUNDI_DS . 'Bootstrap.php';
        if (file_exists($sBootsFile)) {
            $aModuleSetings = include_once $sBootsFile;
            if (is_array($aModuleSetings)) {
                Gundi()->Bootstrap->boot($aModuleSetings);
            }
        }
    }
}