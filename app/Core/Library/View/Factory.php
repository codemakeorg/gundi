<?php
namespace Core\Library\View;

use Core\Contract\View\IViewFactory;
use Core\Library\Component\Component;
use Core\Library\Event\Dispatcher as EventDispatcher;
use Core\Library\Setting\Setting;
use Core\Library\Theme\Theme;
use Core\Library\View\Html\View;
use Core\Library\View\Html\ViewProvider;

class Factory implements IViewFactory
{
    /**
     * @var Theme
     */
    private $_oTheme;
    /**
     * @var Setting
     */
    protected $_oSetting;
    /**
     * @var EventDispatcher
     */
    private $_oEventDispatcher;

    public function __construct(Theme $oTheme, Setting $oSetting, EventDispatcher $oEvent)
    {
        $this->_oTheme = $oTheme;
        $this->_oSetting = $oSetting;
        $this->_oEventDispatcher = $oEvent;
    }

    /**
     * @param Component $oComponent
     * @param string $sType
     * @param string $sTplName
     * @return View|JsonView
     */
    public function create(Component &$oComponent, $sType = 'html', $sTplName = 'index')
    {
        switch ($sType) {
            case 'json':
                $oView = new JsonView();
                break;
            default:
                $oView = new View();
                $oViewProvider = new ViewProvider();

                $aParts = explode('\\', get_class($oComponent));

                $oViewProvider
                    ->setModulesDir($this->_oSetting->getParam('core.dir_module'))
                    ->setModuleName($aParts[1])
                    ->setTemplateDir($oComponent->getViewDir() . array_pop($aParts))
                    ->setTemplateExt($this->_oSetting->getParam('core.tmp_ext'))
                    ->setThemeDir($this->_oSetting->getParam('core.themes_dir') . $this->_oTheme->getTheme() . GUNDI_DS);

                $this->_oTheme->setTemplate($sTplName);

                $oView
                    ->setViewProvider($oViewProvider)
                    ->setTheme($this->_oTheme);

                $this->_oEventDispatcher->fire(Events::HTML_CREATED, [$oView]);
        }

        return $oView;
    }
}