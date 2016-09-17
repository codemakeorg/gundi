<?php
namespace bin\Command\Migration;

class Reset extends Run
{
    public function getDescription()
    {
        return 'Reset migrations';
    }

    protected function runMigration($oMigration)
    {
        $oMigration->down();
    }
}