<?php
namespace Core\Library\Database\Module;

use Core\Library\File\VersionTrait;

class Migrator extends AbstractChanger
{
    use VersionTrait;

    private $_sMigrationDir;

    /**
     * @return mixed
     */
    public function update()
    {
        $aMigrations = $this->getBetweenVersionFiles($this->_sMigrationDir, $this->_sMinVersion, $this->_sMaxVersion);
        foreach ($aMigrations as $sVersion => $sShortClass) {
            $this->getClass($sShortClass)->up();
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function downgrade()
    {
        $aMigrations = $this->getBetweenVersionFiles($this->_sMigrationDir, $this->_sMinVersion, $this->_sMaxVersion);
        $aMigrations = array_reverse($aMigrations);
        foreach ($aMigrations as $sVersion => $sShortClass) {
            $this->getClass($sShortClass)->down();
        }
        return $this;
    }

    /**
     * @param string $sMigrationDir
     * @return Migrator
     */
    public function setMigrationDir($sMigrationDir)
    {
        if (!is_dir($sMigrationDir)) {
            throw  new \LogicException("The migration path \"{$sMigrationDir}\" does not exist.");
        }

        $this->_sMigrationDir = $sMigrationDir;
        return $this;
    }


    private function getClass($sShortClass)
    {
        $sClass = $sClass = '\Module\\' . $this->_sModuleName . '\Database\Migration\\' . $sShortClass;
        return new $sClass;
    }

}