<?php
namespace Core\Library\Database;


use Core\Library\Gundi\Gundi;
use Illuminate\Database\Connectors\ConnectionFactory;

class DatabaseManager extends \Illuminate\Database\DatabaseManager
{
    public function __construct(Gundi $app, ConnectionFactory $factory)
    {
        parent::__construct($app, $factory);
    }
}