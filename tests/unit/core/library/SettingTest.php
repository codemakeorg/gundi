<?php
namespace unit\core\library;

use Core\Library\Setting\Setting;

class SettingTest extends \Gundi_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_oSetting;
    private $_oParams = [];
    public function setUp()
    {
        parent::setUp();
        $this->_oSetting = $this->getMockBuilder(Setting::class)
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
    }

    public function testGetParam()
    {
        $this->assertEquals($this->_oSetting->getParam('core.site_title'), "Gundi");
        $this->assertEquals($this->_oSetting->getParam('core.theme_layout'), "index");
        $this->assertEquals($this->_oSetting->getParam(['db','host']), "localhost");
    }
    public function testSetParam()
    {
        $this->_oSetting->setParam('core.my_param', true);
        $this->_oSetting->setParam(['core.my_param_array' => false]);
        $this->assertEquals($this->_oSetting->getParam('core.my_param'), true);
        $this->assertEquals($this->_oSetting->getParam('core.my_param_array'), false);
    }
    public function testIsParam()
    {
        $this->_oSetting->setParam('core.my_param', true);
        $this->assertTrue($this->_oSetting->isParam('core.my_param'));
        $this->assertFalse($this->_oSetting->isParam('core.my_array'));
    }

    public function testToArray()
    {
        $this->_oSetting->setParam('core.my_param', true);
        $aParams = $this->_oSetting->toArray();
        $this->assertArrayHasKey('core.my_param', $aParams);
        $this->assertArrayNotHasKey('core.my_array', $aParams);
    }

    public function testUnsetParam()
    {
        $this->_oSetting->unsetParam('core.my_param');
        $this->assertFalse($this->_oSetting->isParam('core.my_param'));
    }
}