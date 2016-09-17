<?php
namespace bin\Command\Seed;

use Core\Library\Database\Seeder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends \bin\Command\Migration\Run
{

    public function getDescription()
    {
        return 'Run seeders';
    }

    public function configure()
    {
        $this->setDefinition([
            new InputOption('modules', 'm', InputOption::VALUE_OPTIONAL, 'List of modules for run seeders'),
        ]);
    }

    public function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        $this->_oOutput = $oOutput;
        $this->_oInput = $oInput;

        $aModules = $this->getModules();
        foreach ($aModules as $sModule) {
            $aModuleSeeders = $this->getSeeders($sModule);
            foreach ($aModuleSeeders as $oSeeder) {
                $sClass = get_class($oSeeder);
                $this->_oOutput->writeln("<comment>running seeder \"{$sClass}\"</comment>");
                $oSeeder->run();
            }
        }

        $this->_oOutput->writeln("<info>Seeders was success complited</info>");
    }

    private function getSeeders($sModule)
    {
        $aRes = [];
        $sMigrationDir = GUNDI_DIR_MODULE . $sModule . GUNDI_DS . 'Database' . GUNDI_DS . 'Seed' . GUNDI_DS;
        if (is_dir($sMigrationDir)) {
            $oDiriterator = new \DirectoryIterator($sMigrationDir);
            foreach ($oDiriterator as $oFile) {
                if (!$oFile->isFile() && $oFile->getExtension() != 'php') continue;
                $sClass = '\Module\\' . $sModule . '\Database\Seed\\' . $oFile->getBasename('.php');
                $oObject = Gundi()->make($sClass);
                if ($oObject instanceof Seeder) {
                    $aRes[] = $oObject;
                }
            }
        }
        return $aRes;
    }
}