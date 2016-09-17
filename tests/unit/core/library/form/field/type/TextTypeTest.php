<?php
namespace unit\core\library\form\field\type {


    use Core\Library\Form\Exception\RequiredArgumentException;
    use Core\Library\Form\Field\Type\StringType;
    use Core\Library\Form\Field\Type\TextType;
    use Core\Library\Theme\Theme;
    use Core\Library\Token\Token;
    use Core\Library\View\Html\View;
    use Core\Library\View\Html\ViewProvider;
    use Core\Library\View\JsonView;
    use org\bovigo\vfs\vfsStream;

    class TextTypeTest extends \Gundi_Framework_TestCase
    {

        public function setUp()
        {
            parent::setUp();
            $this->getMockForService(Token::class);
        }

        public function testInstanceInvalidArgument()
        {
            $this->setExpectedException(RequiredArgumentException::class);
            new TextType([]);
        }

        public function testInstance()
        {
            $oStringType = new TextType(['name' => 'test_field', 'title' => 'Test Field String']);
            $this->assertInstanceOf(TextType::class, $oStringType);
        }

        public function testEmpty()
        {
            $oStringType = new TextType(['name' => 'test', 'title' => 'test title', 'value' => 'test value']);
            $this->assertNotTrue($oStringType->isEmpty());

            $oStringType2 = new TextType(['name' => 'test', 'title' => 'test title']);
            $this->assertTrue($oStringType2->isEmpty());
        }

        public function testValidate()
        {
            //test required
            $oStringType = new TextType(['name' => 'test', 'title' => 'test title', 'rules' => 'required']);
            $this->assertNotTrue($oStringType->isValid());

            //test length
            $oStringType = new TextType(['name' => 'test', 'title' => 'test title', 'rules' => 'required|max:2', 'value' => '123456']);
            $this->assertNotTrue($oStringType->isValid());
        }

        public function testRenderHtml()
        {
            $aFileStruct = [
                'Modules' => [
                    'Core' => [
                        'View' => [
                            'Type' => [
                                'text.php' => '<textarea name="<?=$name?>" id="<?=$name?>"><?=$value?></textarea>'
                            ]
                        ]
                    ]
                ]
            ];

            vfsStream::setup('smaker', null, $aFileStruct);

            $oStringType = new TextType(['name' => 'test', 'title' => 'test title', 'value' => 'test value']);

            $oTheme = new MockTypeTheme();
            $oView = new View();
            $oView->setViewProvider(new ViewProvider())
                ->setTheme($oTheme)
                ->getViewProvider()
                ->setModulesDir(vfsStream::url('smaker/Modules'))
                ->setTemplateDir('View');
            $oStringType->setView($oView)->setTheme($oTheme);

            $sStringMarkup = '<textarea name="test" id="test">test value</textarea>';
            $this->assertEquals($sStringMarkup, $oStringType->render());
        }

        public function testRenderJson()
        {
            $oStringType = new StringType(['name' => 'test', 'title' => 'test title', 'value' => 'test value']);

            $oTheme = new MockTypeTheme();
            $oView = new JsonView();
            $oStringType->setView($oView)->setTheme($oTheme);

            $aActualOutput = json_decode($oStringType->render(), true);
            $this->assertEquals('test', $aActualOutput['name']);
            $this->assertEquals('test value', $aActualOutput['value']);
            $this->assertEquals('test title', $aActualOutput['title']);
        }
    }

    class MockTypeTheme extends Theme
    {

        public function __construct()
        {
        }
    }
}