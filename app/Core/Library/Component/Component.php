<?php
namespace Core\Library\Component;

/**
 * Class Component
 * @property \Core\Library\Event\Dispatcher _oEventDispatcher
 */
abstract class Component
{
    protected $_aDependencies = [];

    /**
     * @var \Core\Library\View\AbstractView
     */
    protected $oView;

    protected $sViewDir;

    /**
     * @return string
     */
    public final function getViewDir()
    {
        return $this->sViewDir;
    }

    /**
     * @return \Core\Library\View\AbstractView
     */
    public function getView()
    {
        return $this->oView;
    }

    /**
     * @param \Core\Library\View\AbstractView $oView
     * @return $this
     */
    public function setView(&$oView)
    {
        $this->oView = $oView;
        return $this;
    }

    /**
     * set dependency for this component
     * @param $sName
     * @param $oValue
     */
    public function __set($sName, $oValue)
    {
        $this->_aDependencies[$sName] = $oValue;
    }

    /**
     * get dependency
     * @param $sName
     * @return object
     */
    public function __get($sName)
    {
        return $this->_aDependencies[$sName];
    }

}