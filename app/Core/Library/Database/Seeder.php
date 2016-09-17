<?php
namespace Core\Library\Database;

use Illuminate\Container\Container;
use \Illuminate\Database\Seeder as BaseSeeder;

abstract class Seeder extends BaseSeeder
{
    public function __construct(Container $oContainer)
    {
        $this->setContainer($oContainer);
    }
}