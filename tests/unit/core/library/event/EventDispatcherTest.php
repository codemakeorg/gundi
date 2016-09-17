<?php

class EventDispatcherTest extends \Gundi_Framework_TestCase
{
    public function testListen()
    {
        $oEvent = new \Core\Library\Event\Dispatcher();
        $oEvent->listen('test', function(){});
        $this->assertTrue($oEvent->hasListeners('test'));
    }

    public function testFire()
    {
        $oEvent = new \Core\Library\Event\Dispatcher();

        $oEvent->listen('test.event', function(){
           echo 'test';
        });

        $this->expectOutputString('test');
        $oEvent->fire('test.event');
    }
}