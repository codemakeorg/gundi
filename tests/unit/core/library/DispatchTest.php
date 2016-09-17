<?php
namespace unit\core\library;

use Core\Contract\View\IViewFactory;
use Core\Library\Component\Controller;
use Core\Library\Dispatch\Dispatch;
use Core\Library\Router\Router;
use Core\Library\View\JsonView;
use Illuminate\Container\Container;
use Core\Library\Token\Token;
use Core\Library\View\AbstractView;

class DispatchTest extends \Gundi_Framework_TestCase
{
    /**
     * @var \Core\Library\Router
     */
    private $oRouter;
    private $oFactoryView;

    private $oContainer;

    public function setUp()
    {
        parent::setUp();
        $this->addService('Token', $this->getMockForService(Token::class, 'Token', ['make']));
        $this->oRouter = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->setMethods(['getHandlerData', 'getRouteExt'])
            ->getMock();


        $this->oFactoryView = $this->getMockForAbstractClass(IViewFactory::class, [], 'MockViewFactory', false, false, true, ['create']);
        $this->oFactoryView->expects($this->any())->method('create')->will($this->returnCallback(function(){
            return new MockView();
        }));

        $this->oContainer = $this->getMock(Container::class, ['make']);
    }


    public function testDispatchIfCallable()
    {
        $this->oRouter
            ->expects($this->once())
            ->method('getHandlerData')
            ->will($this->returnValue([
                'function' => function ($var) {
                    echo $var;
                },
                'vars' => ['test'],
            ]));

        $oFrontCtrl = new Dispatch($this->oRouter, $this->oFactoryView, $this->oContainer);
        $oFrontCtrl->dispatch();
        $this->expectOutputString('test');
    }

    public function testDispatchIfController()
    {
        $this->oRouter
            ->expects($this->once())
            ->method('getHandlerData')
            ->will($this->returnValue([
                'Controller' => '\unit\core\library\MockController',
                'method' => 'dispatchIfController',
                'vars' => ['tester'],
            ]));

        $this->oRouter
            ->expects($this->once())
            ->method('getRouteExt')
            ->will($this->returnValue('html'));

        $this->oContainer->expects($this->once())->method('make')->will($this->returnValue(new MockController()));

        $oFrontCtrl = new Dispatch($this->oRouter, $this->oFactoryView, $this->oContainer);
        $oFrontCtrl->dispatch();
        $this->expectOutputString('Hello, tester');
    }

    public function testDispatchControllerOptional()
    {
        $this->oContainer->expects($this->once())->method('make')->will($this->returnValue(new MockController()));
        $oFrontCtrl = new Dispatch($this->oRouter, $this->oFactoryView, $this->oContainer);
        $oFrontCtrl->dispatchController('\unit\core\library\MockController@dispatchIfController', ['sName'=>'tester2']);
        $this->expectOutputString('Hello, tester2');
    }
}

class MockController extends Controller
{
    public function __construct()
    {
    }

    public function dispatchIfController($sName)
    {
        echo 'Hello, ' . $sName;
    }
}

class MockView extends AbstractView{
    public function render()
    {
        return '';
    }
}