<?php
namespace bin\Command\Migration;

use Core\Library\Database\Migration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{
    /**
     * @var OutputInterface
     */
    protected $_oOutput;
    /**
     * @var InputInterface
     */
    protected $_oInput;

    public function getDescription()
    {
        return 'Run migrations';
    }

    public function configure()
    {
        $this->setDefinition([
            new InputOption('modules', 'm', InputOption::VALUE_OPTIONAL, 'List of modules for run migrations'),
        ]);
        parent::configure();
    }

    public function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        $this->_oOutput = $oOutput;
        $this->_oInput = $oInput;

        $aModules = $this->getModules();
        foreach ($aModules as $sModule) {
            $aModuleMigrations = $this->getMigrations($sModule);
            foreach ($aModuleMigrations as $oMigration) {
                $sClass = get_class($oMigration);
                $this->_oOutput->writeln("<comment>running migration \"{$sClass}\"</comment>");
                $this->runMigration($oMigration);
            }
        }

        $this->_oOutput->writeln("<info>Migrations was success complited</info>");
    }

    protected function runMigration($oMigration)
    {
        $oMigration->up();
    }

    private function getMigrations($sModule)
    {
        $aRes = [];
        $sMigrationDir = GUNDI_DIR_MODULE . $sModule . GUNDI_DS . 'Database' . GUNDI_DS . 'Migration' . GUNDI_DS;
        if (is_dir($sMigrationDir)) {
            $oDiriterator = new \DirectoryIterator($sMigrationDir);
            foreach ($oDiriterator as $oFile) {
                if (!$oFile->isFile() && $oFile->getExtension() != 'php') continue;
                $sClass = '\Module\\' . $sModule . '\Database\Migration\\' . $oFile->getBasename('.php');
                $oObject = new $sClass();
                if ($oObject instanceof Migration) {
                    $aRes[] = $oObject;
                }
            }
        }
        return $aRes;
    }

    protected function getModules()
    {
        $aRes = [];
        $sInputModules = $this->_oInput->getOption('modules');
        if (!empty($sInputModules)) {
            $aRes = array_map(function ($sModuleName) {
                return ucfirst($sModuleName);
            }, explode(',', $sInputModules));
        } else {
            $oDirIterator = new \DirectoryIterator(GUNDI_DIR_MODULE);
            foreach ($oDirIterator as $oFile) {
                if ($oFile->isDot() || $oFile->isFile()) continue;
                $aRes[] = $oFile->getFilename();
            }
        }
        return $aRes;
    }
}