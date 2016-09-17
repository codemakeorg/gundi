<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class Gundi_Framework_TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Tests\unit\Stubs\MockGundi
     */
    protected $oGundi;

    /**
     * @var Capsule
     */
    protected $oCapsule;
    protected $oBootstrap;
    protected $oConnection;
    protected $aService = [];

    public function setUp()
    {
        $this->oGundi = new \Tests\unit\Stubs\MockGundi();
        $this->oBootstrap = new \Core\Library\Gundi\Bootstrap($this->oGundi);
        $aApp = include GUNDI_DIR_SETTING . 'App.php';
        $this->oBootstrap->boot($aApp);
        $GLOBALS['gundi_instance'] = $this->oGundi;
        $this->connect();
    }

    public function tearDown()
    {
        if ($this->oCapsule instanceof Capsule) {
            $this->oCapsule->getConnection()->disconnect();
        }
    }

    /**
     * @param string $sClassName
     * @param string $sServiceName
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockForService
    (
        $sClassName,
        $sServiceName = null,
        $aMethods = [],
        $aArguments = [],
        $bCallOriginalConstructor = true,
        $bCallOriginalClone = true
    )
    {
        if (is_null($sServiceName)) {
            $sClass = new ReflectionClass($sClassName);
            $sServiceName = $sClass->getShortName();
        }
        $mock = $this->getMock($sClassName, $aMethods, $aArguments, '', $bCallOriginalConstructor, $bCallOriginalClone);
        $this->addService($sServiceName, $mock);
        return $mock;
    }

    protected function make($sClassName, $aParams = [])
    {
        return $this->oGundi->make($sClassName, $aParams);
    }

    protected function addService($sName, $oService)
    {
        $this->oGundi->aService[$sName] = $oService;
    }

    protected  function assertMethodExist($mClass, $sMethod)
    {
        $sClass = is_string($mClass) ? $mClass : get_class($mClass);
        $oReflectionClass = new ReflectionClass($sClass);
        $this->assertTrue($oReflectionClass->hasMethod($sMethod), "\"$sMethod\" method not exist in class \"$sClass\"");
    }

    protected function connect()
    {
        $this->oConnection = $this->oGundi['db']->connection(GUNDI_DB_DRiVER);
        $this->oCapsule = new Capsule($this->oGundi);
        $this->oCapsule->addConnection($this->oGundi['config']['database.connections'][GUNDI_DB_DRiVER]);
        $this->oCapsule->setAsGlobal();
        $this->oCapsule->setEventDispatcher($this->oGundi['events']);
        $this->oCapsule->bootEloquent();
        $this->oGundi->instance(['\Illuminate\Database\Connection' => 'Connection'], $this->oCapsule);
    }
}