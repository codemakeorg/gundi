<?php
namespace bin\Command\Seed;

use bin\Command\GenerateTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Make extends Command
{
    use GenerateTrait;

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
        return 'Generate seed file to module';
    }

    public function configure()
    {
        $this->setDefinition([
            new InputOption('module', 'm', InputOption::VALUE_REQUIRED, 'Module name for generating seed'),
            new InputOption('file', 'f', InputOption::VALUE_REQUIRED, 'File name for generating seed'),
        ]);
        parent::configure();
    }

    public function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        $this->_oOutput = $oOutput;
        $this->_oInput = $oInput;
        $this->checkInput();

        $sFileName = $this->getSeedFileName();

        $this->generateFile($sFileName, $this->getFileContent(), $aParams =
            [
                'moduleName' => $this->getUcOption('module'),
                'name' => $this->getUcOption('file'),
            ]);

        $this->_oOutput->writeln("<info>Seed file was successful created</info>");
    }

    protected function getUcOption($sName)
    {
        return ucfirst($this->_oInput->getOption($sName));
    }

    protected function checkInput()
    {
        $sModuleName = $this->getUcOption('module');
        if (!is_dir(GUNDI_DIR_MODULE . $sModuleName) || empty($sModuleName)) {
            $this->_oOutput->writeln("<error>Module \"{$sModuleName}\" not exists!</error>");
            exit(1);
        }

        $sFile = $this->getSeedFileName();

        if (file_exists($sFile)) {
            $this->_oOutput->writeln("<error>Seed file\"{$this->getUcOption('file')}\" for module \"{$sModuleName}\" already exists!</error>");
            exit(1);
        }
    }

    protected function getSeedFileName()
    {
        return GUNDI_DIR_MODULE . $this->getUcOption('module') . GUNDI_DS . 'Database' . GUNDI_DS . 'Seed' . GUNDI_DS . $this->getUcOption('file') . '.php';
    }

    protected function getFileContent()
    {
        $sClassContent = <<<'content'
<?php
namespace Module\%moduleName%\Database\Seed;

use Core\Library\Database\Seeder;

Class %name% extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //todo::define method logic
    }

}
content;

        return $sClassContent;
    }

}