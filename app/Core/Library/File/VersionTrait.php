<?php
namespace Core\Library\File;


trait VersionTrait
{
    /**
     * @param $sDir
     * @param $sFromVersion
     * @param $sToVersion
     * @return array
     */
    public function getBetweenVersionFiles($sDir, $sFromVersion, $sToVersion)
    {
        $aFiles = [];
        if (is_dir($sDir)) {
            $oIterator = new \DirectoryIterator($sDir);
            foreach ($oIterator as $oItem) {

                if ($oItem->isDot() || !$oItem->isFile()) {
                    continue;
                }

                $sFileBaseName = $oItem->getFilename();
                $sFileVersion = str_replace(['_', '.php'], ['.', ''], substr($sFileBaseName, strpos($sFileBaseName, '_') + 1));

                if (version_compare($sFileVersion, $sFromVersion, '<') || version_compare($sFileVersion, $sToVersion, '>')) {
                    continue;
                }

                $aFiles[$sFileVersion] = $oItem->getBasename('.php');
            }
            uksort($aFiles, 'version_compare');
        }
        return $aFiles;
    }
}