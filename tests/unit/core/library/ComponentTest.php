<?php
namespace unit\core\library;

use Core\Library\Component\Component;
use Core\Library\View\Html\View;
use Core\Library\View\Html\ViewProvider;
use Illuminate\Container\Container;

class ComponentTest extends \Gundi_Framework_TestCase
{
    /**
     * @var \Core\Library\Component\Component
     */
    private $oComponent = null;
    /**
     * @var \Core\Library\View\Html\View;
     */
    private $oHtmlView = null;

    public function setUp()
    {
        parent::setUp();
        $this->oComponent = $this->getMockBuilder(TestComponent::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->oHtmlView = new View();
        $this->oHtmlView->setViewProvider(new ViewProvider());
    }

    public function testIsComponentInstanced()
    {
        $this->assertInstanceOf(Component::class, $this->oComponent);
    }

    public function testSetView()
    {
        $this->assertInstanceOf(Component::class, $this->oComponent->setView($this->oHtmlView));
    }

    public function testViewDir()
    {
        $this->assertEquals('View', $this->oComponent->getViewDir());
    }

    public function testInjectDependence()
    {
        $this->oComponent->_viewProvider  = new ViewProvider();
        $this->assertInstanceOf(ViewProvider::class, $this->oComponent->_viewProvider);
    }

    public function testInjectDependenceWhenResolveComponent()
    {
        $oContainer = new Container();
        $oContainer->bind(TestComponent::class);
        $oContainer->bind(ViewProvider::class);

        $oContainer->resolving(function(TestComponent $oComponent, $oContainer){
            $oComponent->_oViewProvider = $oContainer[ViewProvider::class];
        });

        $oComponent  = $oContainer[TestComponent::class];

        $this->assertInstanceOf(ViewProvider::class, $oComponent->_oViewProvider);
    }
}

class TestComponent extends Component
{
    protected $sViewDir = 'View';
}