<?php
namespace unit\core\Library\View\Extension {

    use Core\Library\View\Html\Extension\File;
    use Core\Library\Setting\Setting;

    class FileTest extends \Gundi_Framework_TestCase
    {
        private $_oFile;
        private $_oSetting;
        public function setUp()
        {
            parent::setUp();
            $this->_oSetting = $this->getMockBuilder(Setting::class)
                ->disableOriginalConstructor()
                ->setMethods(null)
                ->getMock();
            $this->_oSetting->setParam('core.servers', [1=>'http://127.0.0.1', 2=>'https://127.0.0.2', 3=>'http://127.0.0.3']);
            $this->_oSetting->setParam('core.path', 'http://127.0.0.1/');
            $this->_oSetting->setParam('core.app_dir', '\var\www\html\gundi\app\\');
            $this->_oFile = new File($this->_oSetting);
        }

        public function testInstance()
        {
            $this->assertInstanceOf(File::class, $this->_oFile);
        }
        public function testSrcUrl()
        {
            $this->assertEquals('https://127.0.0.2/', $this->_oFile->server(2)->get());
            $this->assertEquals('http://127.0.0.1/02/02/2016/123.png', $this->_oFile->file('02/02/2016/123.png')->get());
            $this->assertEquals('https://127.0.0.2/02/02/2016/123.png', $this->_oFile->file('02/02/2016/123.png')->server(2)->get());
            $this->assertEquals('https://127.0.0.2/02/02/2016/123_1024.png', $this->_oFile->file(['02/02/2016/123%s.png'=>'_1024'])->server(2)->get());
            $this->assertEquals('http://127.0.0.1/02/02/2016/123_500.png', $this->_oFile->file(['02/02/2016/123%s.png'=>'_500'])->server(1)->get());
            $this->assertEquals('http://127.0.0.1/File/Pic/Admin/02/02/2016/123_500.png', $this->_oFile->from('File/Pic/Admin')->server(1)->file(['02/02/2016/123%s.png'=>'_500'])->get());
        }
        public function testSrcPath()
        {
            $this->assertEquals('\var\www\html\gundi\app\\', $this->_oFile->get('path'));
            $this->assertEquals('https://127.0.0.2/', $this->_oFile->server(2)->get('path'));
            $this->assertEquals('\var\www\html\gundi\app\File\Pic\Admin\02\02\2016\123_500.png', $this->_oFile->from('File/Pic/Admin')->file(['02/02/2016/123%s.png'=>'_500', '01/02/2016/123%s.png'=>'_510'])->get('path'));
            $this->assertEquals('https://127.0.0.2/File/Pic/Admin/02/02/2016/123_500.png', $this->_oFile->from('File/Pic/Admin')->server(2)->file(['02/02/2016/123%s.png'=>'_500', '01/02/2016/123%s.png'=>'_510'])->get('path'));
        }

        public function testSrcWithOutServers()
        {
            $oSetting = $this->getMockBuilder(Setting::class)
                ->disableOriginalConstructor()
                ->setMethods(null)
                ->getMock();
            $oSetting->setParam('core.servers', []);
            $oSetting->setParam('core.path', 'http://127.0.0.1/');
            $oSetting->setParam('core.app_dir', '\var\www\html\gundi\app\\');
            $oFile = new File($oSetting);

            $this->assertEquals('\var\www\html\gundi\app\\', $oFile->get('path'));
            $this->assertEquals('\var\www\html\gundi\app\\', $oFile->server(2)->get('path'));
        }

    }
}