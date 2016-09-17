<?php
namespace unit\core\library\form;

use Core\Library\Form\Field\AbstractType;
use Core\Library\Form\Field\Type\StringType;
use Core\Library\Form\Form;
use Core\Library\Theme\Theme;
use Core\Library\View\Html\View;
use Core\Library\View\Html\ViewProvider;
use org\bovigo\vfs\vfsStream;

class FormTest extends \Gundi_Framework_TestCase
{

    public function testAddField()
    {
        $oView = new View();
        $oForm = new Form($oView, new MockTheme());
        $oForm->addField('string', [
            'name' => 'username',
            'title' => 'Your name'
        ]);
        $this->assertInstanceOf(StringType::class, $oForm->getField('username'));
        $this->assertInstanceOf(StringType::class, $oForm['username']);
    }

    public function testRegisterType()
    {
        $oView = new View();
        $oForm = new Form($oView, new MockTheme());
        $oForm->registerType('text', MockTestType::class);
        $oForm->addField('text', [
            'name' => 'test',
            'title' => 'test'
        ]);
        $this->assertInstanceOf(MockTestType::class, $oForm->getField('test'));
        $this->assertInstanceOf(MockTestType::class, $oForm['test']);
    }

    public function testRenderFieldTest()
    {
        list($oView, $oTheme) = $this->getViewAndTheme();
        $sStringMarkup = '<label for="test">test title</label><input type="text" name="test" id="test" value="test value">';
        $oForm = new Form($oView, $oTheme);
        $oForm->addField('string', ['name' => 'test', 'title' => 'test title', 'value' => 'test value']);
        $this->assertEquals($sStringMarkup, $oForm->renderField('test'));
        $this->assertEquals($sStringMarkup, $oForm->renderField('test', 'Core:Type/string2'));
        $this->assertEquals($sStringMarkup, (string) $oForm['test']);
    }

    public function testFieldValueTest()
    {
        list($oView, $oTheme) = $this->getViewAndTheme();
        $sStringMarkupWithoutValue = '<label for="test">test title</label><input type="text" name="test" id="test" value="">';
        $sStringMarkupWithoutValue2 = '<label for="test2">test title</label><input type="text" name="test2" id="test2" value="">';

        $sStringMarkupWithValue = '<label for="test">test title</label><input type="text" name="test" id="test" value="test1">';
        $sStringMarkupWithValue2 = '<label for="test2">test title</label><input type="text" name="test2" id="test2" value="test2">';

        $oForm = new Form($oView, $oTheme);
        $oForm->addField('string', ['name' => 'test', 'title' => 'test title']);
        $oForm->addField('string', ['name' => 'test2', 'title' => 'test title']);

        $this->assertEquals($sStringMarkupWithoutValue, $oForm->renderField('test'));
        $this->assertEquals($sStringMarkupWithoutValue, $oForm->renderField('test', 'Core:Type/string2'));
        $this->assertEquals($sStringMarkupWithoutValue, (string) $oForm['test']);

        $this->assertEquals($sStringMarkupWithoutValue2, $oForm->renderField('test2'));
        $this->assertEquals($sStringMarkupWithoutValue2, $oForm->renderField('test2', 'Core:Type/string2'));
        $this->assertEquals($sStringMarkupWithoutValue2, (string) $oForm['test2']);

        $oForm->setFieldValue('test', 'test1');
        $this->assertEquals($sStringMarkupWithValue, $oForm->renderField('test'));

        $oForm->setFieldsValue([
            'test' => 'test1',
            'test2' => 'test2',
            'asdfsda' => 'asdfsadf',
        ]);

        $this->assertEquals($sStringMarkupWithValue, $oForm->renderField('test'));
        $this->assertEquals($sStringMarkupWithValue2, $oForm->renderField('test2'));

        $this->assertEquals('test1', $oForm->getFieldValue('test'));
        $this->assertEquals('test2', $oForm->getFieldValue('test2'));

        $aActualValues = $oForm->getFieldsValue();
        $this->assertEquals('test1', $aActualValues['test']);
        $this->assertEquals('test2', $aActualValues['test2']);
    }

    public function testRender()
    {
        $sFormHtml = <<<'form'
<form><?php foreach($fields as $sField=>$oFiled):?><?=$oFiled;?><?php endforeach;?><form>
form;
        $sActualForm = <<<'form'
<form><label for="test">test title</label><input type="text" name="test" id="test" value=""><form>
form;
        $sActualForm2 = <<<'form'
<form><label for="test">test title</label><input type="text" name="test" id="test" value="test value"><form>
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
                            'form2.php' => $sFormHtml,
                        ]

                    ]
                ]
            ]
        ];

        vfsStream::setup('smaker', null, $aFileStruct);

        $oTheme = new MockTheme();
        $oView = new View();

        $oView->setViewProvider(new ViewProvider())
            ->setTheme($oTheme)
            ->getViewProvider()
            ->setModulesDir(vfsStream::url('smaker/Modules'))
            ->setTemplateDir('View/Form');

        $oForm = new Form($oView, $oTheme);
        $oForm->addField('string', ['name' => 'test', 'title' => 'test title']);
        $this->assertEquals($sActualForm, $oForm->render());
        $this->assertEquals($sActualForm, $oForm->render('Core:form2'));
        $this->assertEquals($sActualForm, (string)$oForm);

        $oForm->setFieldValue('test', 'test value');
        $this->assertEquals($sActualForm2, $oForm->render());
    }

    public function testIsValid()
    {
        list($oView, $oTheme) = $this->getViewAndTheme();
        $oForm = new Form($oView, $oTheme);
        $oForm->addField('string', ['name' => 'test', 'title' => 'test title', 'rules' => 'required']);
        $oForm->addField('string', ['name' => 'test2', 'title' => 'test title']);
        $this->assertNotTrue($oForm->isValid());
        $oForm->setFieldValue('test', 'asdfsadfas');
        $this->assertTrue($oForm->isValid());
    }

    public function testGetErrors()
    {
        list($oView, $oTheme) = $this->getViewAndTheme();
        $oForm = new Form($oView, $oTheme);
        $oForm->addField('string', ['name' => 'test', 'title' => 'test title', 'rules' => 'required']);
        $this->assertNotTrue($oForm->isValid());
        $aErrors = $oForm->getErrors();
        $this->assertTrue(is_array($aErrors));
        $this->assertArrayHasKey('test', $aErrors);
        $this->assertEquals('validation.required', $aErrors['test'][0]);
    }

    private function getViewAndTheme()
    {
        $aFileStruct = [
            'Modules' => [
                'Core' => [
                    'View' => [
                        'Form' => [
                            'Type' => [
                                'string.php' => '<label for="<?=$name?>"><?=$title?></label><input type="text" name="<?=$name?>" id="<?=$name?>" value="<?=$value?>">',
                                'string2.php' => '<label for="<?=$name?>"><?=$title?></label><input type="text" name="<?=$name?>" id="<?=$name?>" value="<?=$value?>">',
                            ]
                        ]

                    ]
                ]
            ]
        ];

        vfsStream::setup('smaker', null, $aFileStruct);

        $oTheme = new MockTheme();
        $oView = new View();

        $oView->setViewProvider(new ViewProvider())
            ->setTheme($oTheme)
            ->getViewProvider()
            ->setModulesDir(vfsStream::url('smaker/Modules'))
            ->setTemplateDir('View/Form');

        return [$oView, $oTheme];
    }
}

class MockTheme extends Theme
{

    public function __construct()
    {
    }
}

class MockTestType extends AbstractType{}