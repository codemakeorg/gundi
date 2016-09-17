<?php
namespace unit\core\library\database\module;


use Core\Library\Database\Module\Migrator;
use org\bovigo\vfs\vfsStream;

class MigratorTest extends \Gundi_Framework_TestCase
{
    /**
     * @var Migrator
     */
    private $migrator;

    public function setUp()
    {
        parent::setUp();
        $this->migrator = new Migrator();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(Migrator::class, $this->migrator);
    }

    public function testSetMinVersion()
    {
        $this->assertInstanceOf(Migrator::class, $this->migrator->setMinVersion('1.0.2'));
    }

    public function testSetMaxVersion()
    {
        $this->assertInstanceOf(Migrator::class, $this->migrator->setMaxVersion('1.1.2'));
    }

    public function testSetModuleName()
    {
        $this->assertInstanceOf(Migrator::class, $this->migrator->setModuleName('users'));
    }

    public function testSetInvalidMigrationDir()
    {
        $this->setExpectedException(\LogicException::class, 'The migration path "fake_dir" does not exist.');
        $this->migrator->setMigrationDir('fake_dir');
    }

    public function testSetMirgationDir()
    {
        $aFakeDir = [
            'module' => ['migration' => []],
        ];

        vfsStream::setUp('app', 0777, $aFakeDir);
        $this->assertInstanceOf(Migrator::class, $this->migrator->setMigrationDir(vfsStream::url('app/module/migration')));
    }

    public function testUpdate()
    {
        $this->assertMethodExist($this->migrator, 'update');
    }
}
