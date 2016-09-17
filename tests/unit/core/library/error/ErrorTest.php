<?php
namespace unit\core\library;

use \Core\Library\Error;
use Core\Contract\View\IViewFactory;
use Core\Library\Component\Controller;
use Core\Library\Dispatch\Dispatch;
use Core\Library\Router\Router;
use Core\Library\View\JsonView;
use Illuminate\Container\Container;

class ErrorTest extends \Gundi_Framework_TestCase
{
    /**
     * @var \Core\Library\Error\Error
     */
    private $_oError = null;

    public function setUp()
    {
        parent::setUp();
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        if (!defined('GUNDI_TIME')){
            define('GUNDI_TIME', time());
        }
        $this->_oError = $this->getMockBuilder(\Core\Library\Error\Error::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
    }

    public function tearDown()
    {
        error_reporting(E_ALL);
    }

    public function testLog()
    {
        $this->assertTrue($this->_oError->log('Test log'));
    }

    public function testSkip()
    {
        error_reporting(E_ALL);
        $this->_oError->skip(true);
        $this->assertEquals(error_reporting(), 0);
    }

    public function testErrors()
    {
        $this->_oError->skip(true);
        $this->_oError->set('my error');
        $this->assertFalse($this->_oError->isPassed());
        $this->assertArrayHasKey(0, $this->_oError->get());
        $this->_oError->reset();
        $this->assertEquals([], $this->_oError->get());
        $this->assertEquals(error_reporting(), 0);
        error_reporting(E_ALL);
    }

}

