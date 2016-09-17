<?php

use Core\Library\Theme\Theme;
use org\bovigo\vfs\vfsStream;

class HtmlViewTest extends \Gundi_Framework_TestCase
{
    /**
     * @var \Core\Library\View\Html\View
     */
    private $oView;

    public function setUp()
    {
        parent::setUp();
        vfsStream::setup('app');
        $this->oView = new \Core\Library\View\Html\View();
        $this->oView->setViewProvider(new \Core\Library\View\Html\ViewProvider());
        $this->oView->setTheme(new MockTheme());
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(\Core\Library\View\Html\View::class, $this->oView);
    }

    public function tesSetViewProvider()
    {
        $this->assertInstanceOf(\Core\Library\View\Html\View::class, $this->oView->setViewProvider(new \Core\Library\View\Html\ViewProvider()));
    }

    public function testSetThemeDir()
    {
        $this->assertInstanceOf(\Core\Library\View\Html\View::class, $this->oView->setThemeDir(vfsStream::url('app')));
    }

    public function testNotSetProvider()
    {
        $this->setExpectedException('LogicException', 'ViewProvider does not exits');
        $this->oView
            ->setViewProvider(null)
            ->setThemeDir('test')
            ->setViewProvider(new \Core\Library\View\Html\ViewProvider());
    }

    public function testProviderException()
    {
        $this->setExpectedException('LogicException', 'The theme path "vfs://path/not/found" does not exist.');
        $this->oView->setThemeDir(vfsStream::url('path/not/found'));
    }

    public function testSetTemplateExt()
    {
        $this->assertInstanceOf(\Core\library\View\Html\View::class, $this->oView->setTemplateExt('.php'));
    }


    public function testDisplayTemplate()
    {
        $aFileStructure = [
            'Module' => [
                'TestModule' => [
                    'View' => ['test.tpl' => 'test']
                ]
            ],
            'Theme' => [
                'default' => []
            ],
        ];
        vfsStream::setup('app', 0777, $aFileStructure);

        $this->oView
            ->setThemeDir(vfsStream::url('app/Theme/default'))
            ->setModuleName('TestModule')
            ->setTemplateExt('.tpl')
            ->setModulesDir(vfsStream::url('app/Module'))
            ->setTemplateDir('View');

        $this->oView->getTheme()->setTemplate('TestModule:test');


        $this->assertEquals('test', $this->oView->render());
    }

    public function testDisplayLayout()
    {
        $aFileStructure = [
            'Module' => [
                'TestModule' => [
                    'View' => ['test.tpl' => '']
                ]
            ],
            'Theme' => [
                'default' => ['layout.tpl' => 'i am layout']
            ],
        ];
        vfsStream::setup('app', 0777, $aFileStructure);

        $this->oView
            ->setThemeDir(vfsStream::url('app/Theme/default/'))
            ->setModuleName('TestModule')
            ->setTemplateExt('.tpl')
            ->setModulesDir(vfsStream::url('app/Module'))
            ->setTemplateDir('View');

        $this->oView->getTheme()->setLayout('layout')->setTemplate('TestModule:test');

        $this->assertEquals('i am layout', $this->oView->render());
    }

    public function testDisplayLayoutWithContent()
    {
        $aFileStructure = [
            'Module' => [
                'TestModule' => [
                    'View' => ['test.tpl' => 'content']
                ]
            ],
            'Theme' => [
                'default' => ['layout.tpl' => '<p><?=$this->getContent()?></p>']
            ],
        ];

        vfsStream::setup('app', 0777, $aFileStructure);

        $this->oView
            ->setThemeDir(vfsStream::url('app/Theme/default/'))
            ->setModuleName('TestModule')
            ->setTemplateExt('.tpl')
            ->setModulesDir(vfsStream::url('app/Module'))
            ->setTemplateDir('View');

        $this->oView->getTheme()->setLayout('layout')->setTemplate('TestModule:test');

        $this->assertEquals('<p>content</p>', $this->oView->render());

    }

    public function testDisplayVar()
    {
        $aFileStructure = [
            'Module' => [
                'TestModule' => [
                    'View' => ['test.tpl' => '<?=$title?>']
                ]
            ],
            'Theme' => [
                'default' => ['layout.tpl' => '<?= $title?> <p><?=$this->getContent()?></p>']
            ],
        ];
        vfsStream::setup('app', 0777, $aFileStructure);

        $this->oView
            ->assign('title', 'word')
            ->globalAssign('title', 'Hello')
            ->setThemeDir(vfsStream::url('app/Theme/default/'))
            ->setModuleName('TestModule')
            ->setTemplateExt('.tpl')
            ->setModulesDir(vfsStream::url('app/Module'))
            ->setTemplateDir('View');

        $this->oView->getTheme()->setLayout('layout')->setTemplate('TestModule:test');

        $this->assertEquals('Hello <p>word</p>', $this->oView->render());
    }

    public function testRegisterFunc()
    {
        $this->assertInstanceOf(\Core\Library\View\Html\View::class, $this->oView->registerFunc('testFunc', function () {
        }));
        $this->assertTrue($this->oView->hasFunc('testFunc'));
        $this->assertTrue(false == $this->oView->hasFunc('noFunc'));
    }

    public function testNotFountFunction()
    {
        $this->setExpectedException(\LogicException::class, 'The function "notFunc" does not exits');
        $this->oView->notFunc();
    }

    public function testDisplayWithFunc()
    {
        $aFileStructure = [
            'Module' => [
                'TestModule' => [
                    'View' => ['test.tpl' => '<?=$this->testFunc($var);?>']
                ]
            ],
            'Theme' => [
                'default' => []
            ],
        ];
        vfsStream::setup('app', 0777, $aFileStructure);

        $this->oView
            ->registerFunc('testFunc', function ($value) {
                return 'nice ' . $value;
            })
            ->assign('var', 'word')
            ->setThemeDir(vfsStream::url('app/Theme/default/'))
            ->setTemplateExt('.tpl')
            ->setModulesDir(vfsStream::url('app/Module'))
            ->setTemplateDir('View');
        $this->oView->getTheme()->setTemplate('TestModule:test');
        $this->assertEquals('nice word', $this->oView->render());
    }

    public function testLoadExtension()
    {
        $this->assertEquals(false, $this->oView->hasFunc('testExtensionTest'));

        $oTestExtension = new MockViewExtension();
        $this->assertInstanceOf(\Core\Contract\View\IExtension::class, $oTestExtension);

        $this->assertInstanceOf(\Core\Library\View\Html\View::class, $this->oView->loadExtension($oTestExtension));

        $this->assertTrue($this->oView->hasFunc('testExtensionTest'));
    }

}

class MockTheme extends Theme
{

    public function __construct()
    {
    }
}

class MockViewExtension implements \Core\Contract\View\IExtension
{

    /**
     * @param \Core\Library\View\Html\View $oView
     */
    public function register(\Core\Library\View\Html\View &$oView)
    {
        $oView->registerFunc('testExtensionTest', function(){

        });
    }
}