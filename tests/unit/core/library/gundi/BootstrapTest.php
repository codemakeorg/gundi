<?php

namespace tests\unit\core\library\gundi;


use Core\Contract\Request\IRequest;
use Core\Library\Event\Dispatcher;
use Core\Library\Gundi\Bootstrap;
use Core\Library\Gundi\Gundi;
use Core\Library\Request\Request;
use Core\Library\Setting\Setting;
use Core\Library\Util\Url;

class BootstrapTest extends \Gundi_Framework_TestCase
{
    /**
     * @var Bootstrap
     */
    protected $oBootstrap;
    /**
     * @var Gundi
     */
    protected $oGundi;

    public function setUp()
    {
        $this->oGundi = new Gundi();
        $this->oBootstrap  = new Bootstrap($this->oGundi);
    }

    public function tearDown()
    {
    }

    public function testBootSingletons()
    {
        $this->oBootstrap->boot([
            'singleton' => [
                ['abstract' => [Url::class => 'Url']],
                ['abstract' => [IRequest::class => 'Request'], 'concrete' => Request::class],
                ['abstract' => [Setting::class => 'config']],
            ]
        ]);

        $this->assertInstanceOf(Url::class, $this->oGundi['Url']);
        $this->assertInstanceOf(IRequest::class, $this->oGundi['Request']);
        $this->assertInstanceOf(Setting::class, $this->oGundi['config']);
    }

    public function testBootEventListen()
    {
        $this->oBootstrap->boot([
            'singleton' => [
                ['abstract' => [Dispatcher::class => 'events']],
            ],
            'eventListen' => [
                [
                    'events' => 'test.event_listen',
                    'listener' => function() {
                        echo 'test.event_listen';
                    },
                ]
            ]
        ]);
        $oEventDis = $this->oGundi['events'];
        $this->assertInstanceOf(Dispatcher::class, $oEventDis);
        $this->expectOutputString('test.event_listen');
        $oEventDis->fire('test.event_listen');
    }
}
