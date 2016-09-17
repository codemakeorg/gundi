<?php
namespace Core\Library\Dispatch;

use Core\Contract\Dispatch\IDispatch;
use Core\Contract\View\IViewFactory;
use Core\Library\Router\Router;
use Illuminate\Container\Container;

class Dispatch implements IDispatch
{
    /**
     * @var Router
     */
    private $_oRouter;
    /**
     * @var IViewFactory
     */
    private $_oFactoryView;

    /**
     * @var Container
     */
    private $_oContainer;

    /**
     * FrontController constructor.
     * @param Router $oRouter
     * @param IViewFactory $oFactoryView
     */
    public function __construct(Router $oRouter, IViewFactory $oFactoryView, Container $oConatiner)
    {
        $this->_oRouter = $oRouter;
        $this->_oFactoryView = $oFactoryView;
        $this->_oContainer = $oConatiner;
    }

    /**
     * dispatching Controller
     * @return void
     */
    public function dispatch()
    {
        $mHandler = $this->_oRouter->getHandlerData();

        if (array_key_exists('function', $mHandler)) {

            call_user_func_array($mHandler['function'], $mHandler['vars']);
        } else {
            /**
             * @var $oController \Core\Library\Component\Component
             */

            $oController = $this->_oContainer->make($mHandler['Controller']);

            $oView = $this->_oFactoryView->create($oController, $this->_oRouter->getRouteExt(), $mHandler['method']);

            $oController->setView($oView);

            call_user_func_array([$oController, $mHandler['method']], $mHandler['vars']);

            echo $oView->render();
        }
    }

    /**
     * dispatching Controller optional
     * @param $sController
     * @param $sFormat
     * @return void
     */
    public function dispatchController($sController, $mArgs=[], $sFormat='html')
    {
        $aParts = explode('@', $sController);
        $oController = $this->_oContainer->make($aParts[0]);

        $oView = $this->_oFactoryView->create($oController, $sFormat, $aParts[1]);
        $oView->assign($mArgs);
        $oController->setView($oView);
        call_user_func_array([$oController, $aParts[1]], $mArgs);
        echo $oView->render();
    }

}