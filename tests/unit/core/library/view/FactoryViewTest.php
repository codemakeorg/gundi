<?php
namespace unit\core\library\view {

    use Core\Library\Event\Dispatcher;
    use Core\Library\Setting\Setting;
    use Core\library\Theme\Theme;
    use Core\Library\View\Factory;
    use Core\Library\View\Html\View;
    use Core\Library\View\JsonView;
    use Modules\TestModule\Mock\MockComponent;
    use org\bovigo\vfs\vfsStream;

    class FactoryViewTest extends \Gundi_Framework_TestCase
    {
        /**
         * @var Setting
         */
        private $_oSetting;
        public function setUp()
        {
            parent::setUp();
            $this->_oSetting = new Setting();

            $aFileStructure = [
                'Modules' => [
                    'TestModule' => [
                        'View' => []
                    ]
                ],
                'Themes' => [
                    'default' => ['index.php' => '']
                ]
            ];

            vfsStream::setup('app', null, $aFileStructure);

            $this->_oSetting->setParam('core.dir_module', vfsStream::url('app/Modules/'));
            $this->_oSetting->setParam('core.tmp_ext', '.tpl');
            $this->_oSetting->setParam('core.themes_dir', vfsStream::url('app/Themes/'));

        }

        public function testCreateView()
        {
            $oComponent = new MockComponent();
            $oMockTheme = new MockTheme();
            $oMockTheme->setLayout('index')->setTheme('default');

            $oEventDispatcher = new Dispatcher();
            $oFactoryView = new Factory($oMockTheme, $this->_oSetting, $oEventDispatcher);
            $oView = $oFactoryView->create($oComponent, 'html');
            $this->assertInstanceOf(View::class, $oView);

            $this->assertEquals('TestModule', $oView->getViewProvider()->getModuleName());
            $this->assertEquals('View/MockComponent', $oView->getViewProvider()->getTemplateDir());

            $this->assertEquals('.tpl', $oView->getViewProvider()->getTemplateExt());
            $this->assertEquals(vfsStream::url('app/Themes/default/'), $oView->getViewProvider()->getThemeDir());

            $oView = $oFactoryView->create($oComponent, 'json');
            $this->assertInstanceOf(JsonView::class, $oView);
        }
    }

    class MockTheme extends Theme
    {

        public function __construct()
        {
        }
    }
}

namespace Modules\TestModule\Mock {

    use Core\Library\Component\Component;

    class MockComponent extends Component
    {
        public function __construct()
        {
        }

        protected $sViewDir = 'View/';
    }
}
