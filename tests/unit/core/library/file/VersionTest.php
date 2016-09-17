<?php
namespace unit\core\library\database;

use Core\Library\File\VersionTrait;
use org\bovigo\vfs\vfsStream;

class VersionTest extends \Gundi_Framework_TestCase
{
    /**
     * @var VersionTrait
     */
    private $fileVersion;

    public function setUp()
    {
        parent::setUp();
        $this->fileVersion = $this->getMockForTrait(VersionTrait::class);
    }

    public function testGetBetweenVersionFiles()
    {
        $sInfo1 = '{"name": "module1", "author": "mebo soft", "description": "zxc", "version": "1.0.0"}';

        $aModules = [
            'module1' => [
                'Database' => [
                    'Migration' => [
                        'Module_1_0_0.php' => '',
                        'Module_1_1_0.php' => '',
                        'Module_1_0_1.php' => '',
                        'Module_1_0_2.php' => '',
                    ]
                ],
                'info.json' => $sInfo1
            ]
        ];

        vfsStream::setUp('Module', 0777, $aModules);

        $aActualfiles = $this->fileVersion->getBetweenVersionFiles(vfsStream::url('Module/module1/Database/Migration/'), '1.0.1', '1.1.0');
        $aExpectedfiles = [
            '1.0.1' => 'Module_1_0_1',
            '1.0.2' => 'Module_1_0_2',
            '1.1.0' => 'Module_1_1_0',
        ];
        $this->assertEquals($aExpectedfiles, $aActualfiles);

        $aActualfiles = $this->fileVersion->getBetweenVersionFiles(vfsStream::url('Module/module1/Database/Migration/'), '1.0.1', '1.0.1');
        $aExpectedfiles = [
            '1.0.1' => 'Module_1_0_1',
        ];
        $this->assertEquals($aExpectedfiles, $aActualfiles);
    }
}
