<?php

namespace unit\core\library\form;


use Core\Library\Form\Builder;
use Core\Library\Form\Field\Type\StringType;
use Core\Library\Theme\Theme;
use Core\Library\View\Html\View;
use Core\Library\View\Html\ViewProvider;
use org\bovigo\vfs\vfsStream;
use Tests\unit\Stubs\StubRequest;

class BuilderTest extends \Gundi_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testBuildedFormValue()
    {
        $oRequest = new StubRequest([
            'username' => 'demo',
            'name' => 'Demo Demo'
        ]);
        list($oView, $oTheme) = $this->getViewAndTheme();
        $oFormBuilder = new Builder($oRequest, $oTheme);
        $oForm = $oFormBuilder->build($oView, [
            ['type' => 'string', 'name' => 'username', 'title' => 'Nick Name'],
            ['type' => 'string', 'name' => 'name', 'title' => 'Your Name'],
        ]);

        $this->assertInstanceOf(StringType::class, $oForm->getField('username'));
        $this->assertInstanceOf(StringType::class, $oForm->getField('name'));

        $this->assertEquals('demo', $oForm->getFieldValue('username'));
        $this->assertEquals('Demo Demo', $oForm->getFieldValue('name'));

        $sExpectedForm = <<<'form'
<form><label for="username">Nick Name</label><input type="text" name="username" id="username" value="demo"><label for="name">Your Name</label><input type="text" name="name" id="name" value="Demo Demo"><form>
form;
        $this->assertEquals($sExpectedForm, $oForm->render());
    }

    private function getViewAndTheme()
    {
        $sFormHtml = <<<'form'
<form><?php foreach($fields as $sField=>$oFiled):?><?=$oFiled;?><?php endforeach;?><form>
form;
        $aFileStruct = [
            'Modules' => [
                'Core' => [
                    'View' => [
                        'Form' => [
                            'Type' => [
                                'string.php' => '<label for="<?=$name?>"><?=$title?></label><input type="text" name="<?=$name?>" id="<?=$name?>" value="<?=$value?>">',
                            ],
                            'form.php' => $sFormHtml,
                        ]

                    ]
                ]
            ]
        ];

        vfsStream::setup('smaker', null, $aFileStruct);

        $oTheme = new Theme();
        $oView = new View();

        $oView->setViewProvider(new ViewProvider())
            ->setTheme($oTheme)
            ->getViewProvider()
            ->setModulesDir(vfsStream::url('smaker/Modules'))
            ->setTemplateDir('View/Form');

        return [$oView, $oTheme];
    }
}