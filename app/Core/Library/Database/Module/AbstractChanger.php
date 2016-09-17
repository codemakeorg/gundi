<?php

namespace Core\Library\Database\Module;


class AbstractChanger
{
    protected $_sMinVersion;
    protected $_sMaxVersion;
    protected $_sModuleName;

    /**
     * @param string $sMinVersion
     * @return AbstractChanger
     */
    public function setMinVersion($sMinVersion)
    {
        $this->_sMinVersion = $sMinVersion;
        return $this;
    }

    /**
     * @param string $sMaxVersion
     * @return AbstractChanger
     */
    public function setMaxVersion($sMaxVersion)
    {
        $this->_sMaxVersion = $sMaxVersion;
        return $this;
    }

    /**
     * @param string $sModuleName
     * @return AbstractChanger
     */
    public function setModuleName($sModuleName)
    {
        $this->_sModuleName = $sModuleName;
        return $this;
    }
}