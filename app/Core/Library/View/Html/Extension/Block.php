<?php
namespace Core\Library\View\Html\Extension;

use Core\Contract\View\IExtension;
use Core\Library\Router\Router;
use Core\Library\View\Html\View;
use Illuminate\Container\Container;

class Block implements IExtension
{
    /**
     * @var View
     */
    protected $_oView;
    protected $_aBlocks = [];
    /**
     * @var Container
     */
    protected $_oContainer = null;
    /**
     * @var Router
     */
    protected $_oRouter = null;


    public function __construct(Container $oContainer, Router $oRouter)
    {
        $this->_oContainer = $oContainer;
        $this->_oRouter = $oRouter;
    }

    /**
     * @param View $oView
     */
    public function register(View &$oView)
    {
        $this->_oView = clone $oView;
        $oView->registerFunc('block', [$this, 'renderBlock']);
    }


    public function add($sBlock, $sUri, $sCallback)
    {
        $sUri = $this->_oRouter->getBasePath() . $sUri;
        $this->_aBlocks[$sBlock][$sUri][] = str_replace('/', '\\', $sCallback);
    }

    /**
     * @param string $sBlock - block name
     * @return array
     */
    public function getCurrentBlocksByName($sBlock)
    {
        $sCurRoute = $this->_oRouter->getCurrentRoute();
        $aRouteBlock = isset($this->_aBlocks[$sBlock][$sCurRoute]) ? $this->_aBlocks[$sBlock][$sCurRoute] : [];
        return array_merge($aRouteBlock, isset($this->_aBlocks[$sBlock][$this->_oRouter->getBasePath() . '*']) ? $this->_aBlocks[$sBlock][$this->_oRouter->getBasePath() . '*'] : []);
    }

    public function renderBlock($sBlock)
    {
        $aBlock = $this->getCurrentBlocksByName($sBlock);
        $sContent = '';
        if ($aBlock) {
            foreach ($aBlock as $sCallback) {
                $aParts = explode('@', $sCallback);
                $sBlockClass = $aParts[0];
                $sBlockMethod = $aParts[1];
                /**
                 * @var $oBlock \Core\library\Component\Block
                 */
                $oBlock = $this->_oContainer->make($sBlockClass);
                $aParts = explode('\\', $sBlockClass);

                if (method_exists($oBlock, $sBlockMethod) && count($aParts) > 1) {
                    $this->_oView
                        ->getViewProvider()
                        ->setModuleName($aParts[1])
                        ->setTemplateDir($oBlock->getViewDir() . array_pop($aParts));
                    $this->_oView->getTheme()->setTemplate($sBlockMethod)->setLayout(null);
                    $oBlock->setView($this->_oView);
                    $aVars  =$this->_oRouter->getCurrentVars();
                    call_user_func_array([$oBlock, $sBlockMethod], (!empty($aVars)?$aVars:[]));
                    $sContent .= $this->_oView->render();
                }
            }
        }
        return $sContent;
    }

    /**
     * @return array
     */
    public function getBlocks()
    {
        return $this->_aBlocks;
    }

    /**
     * @param Container $oContainer
     * @return  $this
     */
    public function setContainer($oContainer)
    {
        $this->_oContainer = $oContainer;
        return $this;
    }
}