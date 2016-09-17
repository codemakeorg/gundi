<?php
namespace Core\Library\Database\Module;

use Core\Library\File\VersionTrait;

class SeedRunner extends AbstractChanger
{
    use VersionTrait;

    private $_sSeedDir;

    /**
     * @return mixed
     */
    public function run()
    {
        $aMigrations = $this->getBetweenVersionFiles($this->_sSeedDir, $this->_sMinVersion, $this->_sMaxVersion);
        foreach ($aMigrations as $sVersion => $sShortClass) {
            Gundi()->make($sClass = '\Module\\' . $this->_sModuleName . '\Database\Seed\\' . $sShortClass)->run();
        }
        return $this;
    }

    /**
     * @param string $sSeedDir
     * @return $this
     */
    public function setSeedDir($sSeedDir)
    {
        if (!is_dir($sSeedDir)) {
            throw  new \LogicException("The seeder path \"{$sSeedDir}\" does not exist.");
        }

        $this->_sSeedDir = $sSeedDir;
        return $this;
    }
}