<?php
namespace bin\Command;

trait GenerateTrait
{
    public function generateFile($sFileName, $sContent, $aParams)
    {
        if (!is_dir(dirname($sFileName))) {
            $oldumask = umask(0);
            mkdir(dirname($sFileName), 0755, true);
            umask($oldumask);
        }

        $aPlaceholders = array_map(function ($sParamName) {
            return '%' . $sParamName . '%';
        }, array_keys($aParams));

        $sContent = str_replace($aPlaceholders, array_values($aParams), $sContent);

        file_put_contents($sFileName, $sContent);
    }
}