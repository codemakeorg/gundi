<?php
namespace unit\core\library\theme;


use Core\Library\Theme\Theme;

class ThemeTest extends \Gundi_Framework_TestCase
{
    /**
     * @var Theme
     */
    private $_oTheme;

    public function setUp()
    {
        parent::setUp();
        $this->_oTheme = new MockTheme();
    }


    public function testSetTemplate()
    {
        $this->assertInstanceOf(Theme::class, $this->_oTheme->setLayout('123'));
    }

    public function testGetTemplate()
    {
        $this->assertInstanceOf(Theme::class, $this->_oTheme->setTemplate('123'));
        $this->assertEquals('123', $this->_oTheme->getTemplate());
    }

    public function testSetTheme()
    {
        $this->assertInstanceOf(Theme::class, $this->_oTheme->setTheme('sdfsd'));
    }


    public function testGetTheme()
    {
        $this->assertInstanceOf(Theme::class, $this->_oTheme->setTheme('test'));
        $this->assertEquals('test', $this->_oTheme->getTheme());
    }


    public function testSetLayout()
    {
        $this->assertInstanceOf(Theme::class, $this->_oTheme->setLayout('sdfsd'));
    }

    public function getLayout()
    {
        $this->assertInstanceOf(Theme::class, $this->_oTheme->setLayout('index'));
        $this->assertEquals('index', $this->_oTheme->getLayout());
    }

}

Class MockTheme extends Theme
{
    public function __construct()
    {
    }
}