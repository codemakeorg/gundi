<?php
namespace Core\Library\Gundi;

use Core\Contract\Gundi\IBootstrap;

class Bootstrap implements IBootstrap
{
    protected $oGundi;

    public function __construct(Gundi $oGundi)
    {
        $this->oGundi = $oGundi;
    }

    /**
     * @param array $aData
     * @return $this
     */
    public function boot(array $aData)
    {
        foreach ($aData as $sMethod => &$aArgs) {
            call_user_func([$this, $sMethod], $aArgs);
        }
        return $this;
    }

    /**
     * register singletons services
     * @param array $aSingletons
     * @return $this
     */
    protected function singleton(array $aSingletons)
    {
        foreach ($aSingletons as &$aSingleton) {
            call_user_func_array([$this->oGundi, 'singleton'], $aSingleton);
        }
        return $this;
    }

    /**
     * register events listeners
     * @param array $aListens
     * @return $this
     */
    protected function eventListen(array $aListens)
    {
        $oEventDispatcher = $this->oGundi->events;
        foreach ($aListens as &$aListen) {
            call_user_func_array([$oEventDispatcher, 'listen'], $aListen);
        }
        return $this;
    }

    /**
     * @param array $aProviders - service providers list
     * @return $this
     */
    protected function serviceProvider(array $aProviders)
    {
        foreach ($aProviders as &$sProvider) {
            $this->oGundi->register($sProvider);
        }
        return $this;
    }

    /**
     * @param array $aRoutes
     * @return $this
     */
    protected function routes(array $aRoutes)
    {
        $oRouter = $this->oGundi->Router;
        foreach ($aRoutes as $sMethod => &$_aRoutes) {
            foreach ($_aRoutes as &$aRoute) {
                call_user_func_array([$oRouter, $sMethod], $aRoute);
            }
        }
        return $this;
    }

    /**
     * @param array $aBlocks
     * @return $this
     */
    protected function blocks(array $aBlocks)
    {
        $oBlock = $this->oGundi->Block;
        foreach ($aBlocks as $aBlock) {
            call_user_func_array([$oBlock, 'add'], $aBlock);
        }
        return $this;
    }

    /**
     * @param $mCallback
     * @return mixed
     */
    protected function call($mCallback)
    {
        return call_user_func($mCallback);
    }
}