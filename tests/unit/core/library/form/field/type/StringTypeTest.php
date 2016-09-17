<?php
namespace unit\core\library\form\field\type;


use Core\Library\Form\Exception\RequiredArgumentException;
use Core\Library\Form\Field\Type\StringType;
use Core\Library\Theme\Theme;
use Core\Library\Token\Token;
use Core\Library\View\Html\View;
use Core\Library\View\Html\ViewProvider;
use Core\Library\View\JsonView;
use org\bovigo\vfs\vfsStream;

class StringTypeTest extends \Gundi_Framework_TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->getMockForService(Token::class);
    }

    public function testInstanceInvalidArgument()
    {
        $this->setExpectedException(RequiredArgumentException::class);
        new StringType([]);
    }

    public function testInstance()
    {
        $oStringType = new StringType(['name' => 'test_field', 'title' => 'Test Field String']);
        $this->assertInstanceOf(StringType::class, $oStringType);
    }

    public function testEmpty()
    {
        $oStringType = new StringType(['name' => 'test', 'title' => 'test title', 'value' => 'test value']);
        $this->assertNotTrue($oStringType->isEmpty());

        $oStringType2 = new StringType(['name' => 'test', 'title' => 'test title']);
        $this->assertTrue($oStringType2->isEmpty());
    }

    public function testValidate()
    {
        //test required
        $oStringType = new StringType(['name' => 'test', 'title' => 'test title', 'rules' => 'required']);
        $this->assertNotTrue($oStringType->isValid());

        //test length
        $oStringType = new StringType(['name' => 'test', 'title' => 'test title', 'rules' => ['required', 'max:2'], 'value' => '123456']);
        $this->assertNotTrue($oStringType->isValid());
    }

    public function testRenderHtml()
    {
        $aFileStruct = [
            'Modules' => [
                'Core' => [
                    'View' => [
                        'Type' => [
                            'string.php' => '<label for="<?=$name?>"><?=$title?></label><input type="text" name="<?=$name?>" id="<?=$name?>" value="<?=$value?>">'
                        ]
                    ]
                ]
            ]
        ];

        vfsStream::setup('smaker', null, $aFileStruct);

        $oStringType = new StringType(['name' => 'test', 'title' => 'test title', 'value' => 'test value']);

        $oTheme = new MockTheme();
        $oView = new View();
        $oView->setViewProvider(new ViewProvider())
            ->setTheme($oTheme)
            ->getViewProvider()
            ->setModulesDir(vfsStream::url('smaker/Modules'))
            ->setTemplateDir('View');
        $oStringType->setView($oView)->setTheme($oTheme);

        $sStringMarkup = '<label for="test">test title</label><input type="text" name="test" id="test" value="test value">';
        $this->assertEquals($sStringMarkup, $oStringType->render());
    }

    public function testRenderJson()
    {
        $oStringType = new StringType(['name' => 'test', 'title' => 'test title', 'value' => 'test value']);

        $oTheme = new MockTheme();
        $oView = new JsonView();
        $oStringType->setView($oView)->setTheme($oTheme);

        $aActualOutput = json_decode($oStringType->render(), true);
        $this->assertEquals('test', $aActualOutput['name']);
        $this->assertEquals('test value', $aActualOutput['value']);
        $this->assertEquals('test title', $aActualOutput['title']);
    }
}

class MockTheme extends Theme
{

    public function __construct()
    {
    }
}
