<?php
namespace Core\Library\View;

abstract class AbstractView
{
    protected $_aVars = [];
    protected $_aGlobalVars = [];

    public function __clone()
    {
        $this->clean();
        return $this;
    }

    public function __toString()
    {
        return $this->render();
    }

    abstract public function render();

    /**
     * Clean all or a specific variable from memory.
     *
     * @param mixed $mName Variable name to destroy, or leave blank to destory all variables or pass an ARRAY of variables to destroy.
     */
    public function clean($mName = '')
    {
        if ($mName) {
            if (!is_array($mName)) {
                $mName = array($mName);
            }

            foreach ($mName as $sName) {
                unset($this->_aVars[$sName]);
            }

            return;
        }

        $this->_aVars = [];
    }

    /**
     * Assign a variable so we can use it within an HTML template.
     * @param mixed $mVars STRING variable name or ARRAY of variables to assign with both keys and values.
     * @param string $sValue Variable value, only if the 1st argument is a STRING.
     * @return object Returns self.
     */
    public function assign($mVars, $sValue = '')
    {
        if (!is_array($mVars)) {
            $mVars = array($mVars => $sValue);
        }

        foreach ($mVars as $sVar => $sValue) {
            $this->_aVars[$sVar] = $sValue;
        }

        return $this;
    }

    /**
     * Assign a variable so we can use it within an HTML  template.
     * @param mixed $mVars STRING variable name or ARRAY of variables to assign with both keys and values.
     * @param string $sValue Variable value, only if the 1st argument is a STRING.
     * @return object Returns self.
     */
    public function globalAssign($mVars, $sValue = '')
    {
        if (!is_array($mVars)) {
            $mVars = array($mVars => $sValue);
        }

        foreach ($mVars as $sVar => $sValue) {
            $this->_aGlobalVars[$sVar] = $sValue;
        }

        return $this;
    }

    /**
     * Get a variable we assigned with the method assign().
     *
     * @see self::assign()
     * @param string $sName Variable name.
     * @return string Variable value.
     */
    public function getVar($sName)
    {
        return (isset($this->_aVars[$sName]) ? $this->_aVars[$sName] : '');
    }

}