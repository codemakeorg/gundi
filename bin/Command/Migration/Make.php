<?php
namespace bin\Command\Migration;

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
        return 'Generates migration file to module';
    }

    public function configure()
    {
        $this->setDefinition([
            new InputOption('module', 'm', InputOption::VALUE_REQUIRED, 'Module name for generating migration'),
            new InputOption('name', 'mn', InputOption::VALUE_REQUIRED, 'File name for generating migration'),
        ]);
        parent::configure();
    }

    public function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        $this->_oOutput = $oOutput;
        $this->_oInput = $oInput;
        $this->checkInput();

        $sFileName = $this->getMigrationFileName();

        $this->generateFile($sFileName, $this->getFileContent(), $aParams =
            [
                'moduleName' => $this->getUcOption('module'),
                'name' => $this->getUcOption('name'),
            ]);

        $this->_oOutput->writeln("<info>Migration file was successful created</info>");
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

        $sFile = $this->getMigrationFileName();

        if (file_exists($sFile)) {
            $this->_oOutput->writeln("<error>Migration file\"{$this->getUcOption('name')}\" for module \"{$sModuleName}\" already exists!</error>");
            exit(1);
        }
    }

    protected function getMigrationFileName()
    {
        return GUNDI_DIR_MODULE . $this->getUcOption('module') . GUNDI_DS . 'Database' . GUNDI_DS . 'Migration' . GUNDI_DS . $this->getUcOption('name') . '.php';
    }

    protected function getFileContent()
    {
        $sClassContent = <<<'content'
<?php
namespace Module\%moduleName%\Database\Migration;

use Core\Library\Database\Migration;

Class %name% extends Migration
{

    public function up()
    {
        //todo::define method content
        //$this->schema()->create('users', function ($collection) {
        //    $collection->unique('email');
        //    $collection->unique('username');
        //});
    }

    public function down()
    {
        //todo::define method content
    }

}
content;

        return $sClassContent;
    }
}