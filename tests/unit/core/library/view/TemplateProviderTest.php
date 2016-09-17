<?php

use org\bovigo\vfs\vfsStream;

class TemplateProviderTest extends \Gundi_Framework_TestCase
{
    /**
     * @var \Core\Library\View\Html\ViewProvider
     */
    private $oViewProvider;


    public function setUp()
    {
        parent::setUp();
        vfsStream::setup('templates');
        $this->oViewProvider = new \Core\Library\View\Html\ViewProvider();
        $this->oViewProvider->setTemplateExt('php');
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf(\Core\Library\View\Html\ViewProvider::class, $this->oViewProvider);
    }

    public function testSetTemplateExt()
    {
        $this->assertInstanceOf(\Core\Library\View\Html\ViewProvider::class, $this->oViewProvider->setTemplateExt('.tpl'));
        $this->assertEquals('.tpl', $this->oViewProvider->getTemplateExt());
    }

    public function testSetInvalidThemeDir()
    {
        $this->setExpectedException('LogicException', 'The theme path "vfs://does/not/exist" does not exist.');
        $this->oViewProvider->setThemeDir(vfsStream::url('does/not/exist'));
    }

    public function testSetThemeDir()
    {
        vfsStream::setup('themes');
        $this->assertInstanceOf(\Core\Library\View\Html\ViewProvider::class, $this->oViewProvider->setThemeDir(vfsStream::url('themes')));
    }

    public function testGetThemeDir()
    {
        vfsStream::setup('themes');
        $this->oViewProvider->setThemeDir(vfsStream::url('themes'));
        $this->assertEquals(vfsStream::url('themes'), $this->oViewProvider->getThemeDir());
    }

    public function testSetInvalidModulesDir()
    {
        $this->setExpectedException('LogicException', 'The module path "vfs://does/not/exist" does not exist.');
        $this->oViewProvider->setModulesDir(vfsStream::url('does/not/exist'));
    }

    public function testSetModulesDir()
    {
        vfsStream::setup('Module');
        $this->assertInstanceOf(\Core\Library\View\Html\ViewProvider::class, $this->oViewProvider->setModulesDir(vfsStream::url('Module')));
    }

    public function testGetModulesDir()
    {
        vfsStream::setup('Module');
        $this->oViewProvider->setModulesDir(vfsStream::url('Module'));
        $this->assertEquals(vfsStream::url('Module'), $this->oViewProvider->getModulesDir());
    }

    public function testSetTplDir()
    {
        vfsStream::setup('View');
        $this->assertInstanceOf(\Core\Library\View\Html\ViewProvider::class, $this->oViewProvider->setTemplateDir(vfsStream::url('View')));
    }

    public function testGetTplDir()
    {
        vfsStream::setup('View');
        $this->oViewProvider->setTemplateDir(vfsStream::url('View'));
        $this->assertEquals(vfsStream::url('View'), $this->oViewProvider->getTemplateDir());
    }

    public function testSetModuleName()
    {
        $this->assertInstanceOf(\Core\Library\View\Html\ViewProvider::class, $this->oViewProvider->setModuleName('TestModule'));
    }

    public function testGetModuleName()
    {
        $this->oViewProvider->setModuleName('TestModule');
        $this->assertEquals('TestModule', $this->oViewProvider->getModuleName());
    }

    public function testGetTemplateFileFromModule()
    {
        $aFileStructure = [
            'Module' => [
                'TestModule' => [
                    'View' => ['test.tpl' => '']
                ]
            ],
            'Theme' => [
                'default' => []
            ],
        ];
        vfsStream::setup('app', 0777, $aFileStructure);

        $this->oViewProvider
            ->setTemplateExt('.tpl')
            ->setModuleName('TestModule')
            ->setModulesDir(vfsStream::url('app/Module'))
            ->setTemplateDir('View')
            ->setThemeDir(vfsStream::url('app/Theme/default'));

        $expectedTplFile = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, 'app/Module/TestModule/View/test.tpl');
        $this->assertEquals(vfsStream::SCHEME . '://' . $expectedTplFile, $this->oViewProvider->getTemplateFile('TestModule:test'));

        $expectedTplFile = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, 'app/Module/TestModule2/View/test.tpl');
        $this->assertEquals(vfsStream::SCHEME . '://' . $expectedTplFile, $this->oViewProvider->getTemplateFile('TestModule2:test'));

        $expectedTplFile = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, 'app/Module/TestModule/View/testTpl.tpl');
        $this->assertEquals(vfsStream::SCHEME . '://' . $expectedTplFile, $this->oViewProvider->getTemplateFile('testTpl'));

    }

    public function testGetTemplateFileFromTheme()
    {
        $aFileStructure = [
            'Module' => [
                'TestModule' => [
                    'View' => [
                        'Controller' => [
                            'TestCTRL' => ['test.tpl' => '']
                        ],
                    ]
                ],
            ],
            'Theme' => [
                'default' => [
                    'TestModule' => [
                        'Controller' => [
                            'TestCTRL' => ['test.tpl' => '']
                        ]
                    ],
                    'TestModule2' => [
                        'Controller' => [
                            'TestCTRL2' => ['test.tpl' => '']
                        ]
                    ],
                ]
            ]
        ];

        vfsStream::setup('app', 0777, $aFileStructure);

        $this->oViewProvider
            ->setTemplateExt('.tpl')
            ->setModuleName('TestModule')
            ->setModulesDir(vfsStream::url('app/Module'))
            ->setTemplateDir('View/Controller/TestCTRL')
            ->setThemeDir(vfsStream::url('app/Theme/default'));

        $expectedTplFile = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, 'app/Theme/default/TestModule/Controller/TestCTRL/test.tpl');
        $this->assertEquals(vfsStream::SCHEME . '://' . $expectedTplFile, $this->oViewProvider->getTemplateFile('TestModule:test'));

        $expectedTplFile = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, 'app/Theme/default/TestModule2/Controller/TestCTRL2/test.tpl');
        $this->oViewProvider->setTemplateDir('View/Controller/TestCTRL2');
        $this->assertEquals(vfsStream::SCHEME . '://' . $expectedTplFile, $this->oViewProvider->getTemplateFile('TestModule2:test'));

        $expectedTplFile = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, 'app/Theme/default/TestModule/Controller/TestCTRL/test.tpl');
        $this->oViewProvider->setTemplateDir('View/Controller/TestCTRL');
        $this->assertEquals(vfsStream::SCHEME . '://' . $expectedTplFile, $this->oViewProvider->getTemplateFile('test'));
    }
}
