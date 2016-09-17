<?php
namespace unit\core\library\database\module;

use Core\Library\Database\Module\SeedRunner;
use org\bovigo\vfs\vfsStream;

class SeedRunnerTest extends \Gundi_Framework_TestCase
{
    /**
     * @var SeedRunner
     */
    private $seedRunner;

    public function setUp()
    {
        parent::setUp();
        $this->seedRunner = new SeedRunner();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(SeedRunner::class, $this->seedRunner);
    }

    public function testSetMinVersion()
    {
        $this->assertInstanceOf(SeedRunner::class, $this->seedRunner->setMinVersion('1.0.2'));
    }

    public function testSetMaxVersion()
    {
        $this->assertInstanceOf(SeedRunner::class, $this->seedRunner->setMaxVersion('1.1.2'));
    }

    public function testSetModuleName()
    {
        $this->assertInstanceOf(SeedRunner::class, $this->seedRunner->setModuleName('users'));
    }

    public function testSetInvalidSeedDir()
    {
        $this->setExpectedException(\LogicException::class, 'The seeder path "fake_dir" does not exist.');
        $this->seedRunner->setSeedDir('fake_dir');
    }

    public function testSetSeedDir()
    {
        $aFakeDir = [
            'module' => ['seed' => []],
        ];

        vfsStream::setUp('app', 0777, $aFakeDir);
        $this->assertInstanceOf(SeedRunner::class, $this->seedRunner->setSeedDir(vfsStream::url('app/module/seed')));
    }

    public function testUpdate()
    {
        $this->assertMethodExist($this->seedRunner, 'run');
    }
}
