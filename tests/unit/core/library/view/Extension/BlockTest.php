<?php
namespace unit\core\Library\View\Extension {

    use Core\Library\Request\Request;
    use Core\Library\Router\Router;
    use Core\Library\Theme\Theme;
    use Core\Library\View\Html\Extension\Block;
    use Core\Library\View\Html\View;
    use Illuminate\Container\Container;
    use Module\TestModule\Component\Block\MockBlock;
    use org\bovigo\vfs\vfsStream;

    class BlockTest extends \Gundi_Framework_TestCase
    {
        /**
         * @var Block
         */
        private $_oBlock;


        private $_oContainer;

        public function setUp()
        {
            parent::setUp();
            $_SERVER['REQUEST_METHOD'] = '';
            vfsStream::setup('app');
            $this->_oContainer = $this->getMock(Container::class, ['make']);
            $this->_oBlock = new Block($this->_oContainer, new Router(new Request()));
        }

        public function testInstance()
        {
            $this->assertInstanceOf(Block::class, $this->_oBlock);
        }

        public function testLoadExtToView()
        {
            $oView = new View();
            $oView->loadExtension($this->_oBlock);
            $this->assertTrue($oView->hasFunc('block'));
        }

        public function testAddBlock()
        {
            $this->assertTrue(method_exists($this->_oBlock, 'add'));

            $oRouter = $this->getMockBuilder(Router::class)
                ->disableOriginalConstructor()->setMethods(['getBasePath'])->getMock();
            $oRouter->expects($this->once())->method('getBasePath')->willReturn('');

            $oBlock = new Block($this->_oContainer, $oRouter);
            $oBlock->add('testName', 'testuri', '\TestModule\Component\Block\TestBlock@test');
            $aBlocks = $oBlock->getBlocks();
            $this->assertEquals(['testName' => ['testuri' => ['\TestModule\Component\Block\TestBlock@test']]], $aBlocks);
        }

        public function testGetCurrentBlocksByName()
        {
            $this->assertTrue(method_exists($this->_oBlock, 'getCurrentBlocksByName'));

            $oRouter = $this->getMockBuilder(Router::class)
                ->disableOriginalConstructor()->setMethods(['getCurrentRoute'])->getMock();
            $oRouter->expects($this->any())->method('getCurrentRoute')->willReturn('testUri');

            $oBlock = new Block($this->_oContainer, $oRouter);
            $oBlock->add('test', 'testUri', '\TestModule\Component\Block\TestBlock@test');
            $this->assertEquals(['\TestModule\Component\Block\TestBlock@test'], $oBlock->getCurrentBlocksByName('test'));
        }

        public function testRenderBlock()
        {
            $this->assertTrue(method_exists($this->_oBlock, 'renderBlock'));

            $aDirStructure = [
                'Module' => [
                    'TestModule' => [
                        'View' => [
                            'Block' => [
                                'MockBlock' => ['index.tpl' => 'test']
                            ]
                        ]
                    ]
                ],
                'Theme' => [
                    'default' => []
                ]
            ];

            vfsStream::setup('app', 0777, $aDirStructure);

            $oView = new View();
            $oProvider = new \Core\Library\View\Html\ViewProvider();

            $oProvider->setModulesDir(vfsStream::url('app/Module/'))
                ->setThemeDir(vfsStream::url('app/Theme/default/'))
                ->setTemplateExt('.tpl');

            $oMockBlockComponent = new MockBlock();

            $oRouter = $this->getMockBuilder(Router::class)
                ->disableOriginalConstructor()->setMethods(['getBasePath', 'getCurrentRoute', 'getCurrentVars'])->getMock();
            $oRouter->expects($this->any())->method('getBasePath')->willReturn('');
            $oRouter->expects($this->any())->method('getCurrentRoute')->willReturn('testUri');
            $oRouter->expects($this->any())->method('getCurrentVars')->willReturn([]);
            $oContainer = $this->getMock(Container::class, ['make']);

            $oContainer->expects($this->at(0))->method('make')->willReturn($oMockBlockComponent);

            $oBlock = new Block($oContainer, $oRouter);

            $oView->setViewProvider($oProvider)->setTheme(new MockTheme());
            $oView->loadExtension($oBlock);

            $oBlock->add('test', 'testUri', 'Module\TestModule\Component\Block\MockBlock@index');

            $this->assertEquals('test', $oBlock->renderBlock('test'));
        }

    }

    class MockTheme extends Theme
    {

        public function __construct()
        {
        }
    }
}


namespace Module\TestModule\Component\Block {

    use Core\Library\Component\Block;

    class MockBlock extends Block
    {
        public function index()
        {

        }

        public function process()
        {
            // TODO: Implement process() method.
        }
    }

}



