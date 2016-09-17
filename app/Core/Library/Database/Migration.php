<?php
namespace Core\Library\Database;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Migrations\Migration as BaseMigration;

class Migration extends BaseMigration
{
    protected function schema()
    {
        return Manager::schema();
    }
}