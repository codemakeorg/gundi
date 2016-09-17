<?php

namespace Core\Library\Form;

use Core\Library\Form\Exception\FieldNotFoundException;
use Core\Library\Form\Field\AbstractType;
use Core\Library\Theme\Theme;
use Core\Library\View\AbstractView;
use JsonSerializable;

class Form implements \ArrayAccess, JsonSerializable
{
    /**
     * @var AbstractView
     */
    protected $oView;
    /**
     * @var Theme
     */
    protected $oTheme;

    protected $aTypes = [];

    protected $aFields = [];

    protected $aErrors = [];

    protected $aDefaultFormData = [
      'action' => '',
      'method' => 'POST',
      'enctype' => 'application/x-www-form-urlencoded',
    ];
    /**
     * @var \ArrayObject - form data (action, etc)
     */
    protected $aData = [];

    public function __construct(AbstractView $oView, Theme $oTheme, $aData = [])
    {
        $this->oView = clone $oView;
        $this->oTheme = $oTheme;
        $this->aData = array_merge($this->aDefaultFormData, $aData);
    }


    /**
     * @param array $aData - field data
     * @return  $this
     */
    public function addField($sType, $aData)
    {
        $sTypeClass = $this->getTypeClassName($sType);
        $sFieldName = $aData['name'];
        /**
         * @var $oType AbstractType
         */
        $oType = new $sTypeClass($aData);
        $oType->setView(clone $this->oView);
        $oType->setTheme($this->oTheme);
        $this->aFields[$sFieldName] = $oType;
        return $this;
    }

    /**
     * @param string $sField
     * @param mixed $mValue
     * @return $this
     */
    public function setFieldValue($sField, $mValue)
    {
        $this->getField($sField)->setValue($mValue);
        return $this;
    }

    /**
     * @param array $aData
     * @return mixed $this
     */
    public function setFieldsValue($aData)
    {
        foreach ($aData as $sField => $mValue) {
            if (isset($this->aFields[$sField])) {
                $this->setFieldValue($sField, $mValue);
            }
        }
        return $this;
    }

    /**
     * @param string $sField
     * @return mixed
     */
    public function getFieldValue($sField)
    {
        return $this->getField($sField)->getValue();
    }

    /**
     * @return array
     */
    public function getFieldsValue()
    {
        $aResult = [];
        foreach ($this->aFields as $sField => $oField) {
            $aResult[$sField] = $oField->getValue();
        }
        return $aResult;
    }

    /**
     * @param string $sName
     * @return AbstractType
     * @throws FieldNotFoundException
     */
    public function getField($sName)
    {
        if (!isset($this->aFields[$sName])) {
            throw new FieldNotFoundException("Field \"{$sName}\" not found in form");
        }
        return $this->aFields[$sName];
    }

    public function renderField($sField, $sTemplate = null)
    {
        return $this->getField($sField)->render($sTemplate);
    }

    /**
     * @param string $sTemplate
     * @return string
     */
    public function render($sTemplate = 'Core:form')
    {
        $this->oTheme->setTemplate($sTemplate);
        $this->oView->assign($this->getVars())->setTheme($this->oTheme);
        return $this->oView->render();
    }

    /**
     * @return  array
     */
    private function  getVars()
    {
        return [
            'data' => $this->aData,
            'fields' => $this->aFields,
            'errors' => $this->aErrors,
        ];
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $bResult = true;
        foreach ($this->aFields as $sField => $oField) {
            /**
             * @var $oField AbstractType
             */
            if (!$oField->isValid()) {
                $this->aErrors = array_merge($this->aErrors, $oField->getErrors()->toArray());
                $oField->setHasError(true);
                $bResult = false;
            }
        }
        return $bResult;
    }

    public function __toString()
    {
        return $this->render();
    }

    /**
     * @param string $sTypeName
     * @param string $sTypeClassName
     * @return $this
     */
    public function registerType($sTypeName, $sTypeClassName)
    {
        $this->aTypes[$sTypeName] = $sTypeClassName;
        return $this;
    }

    public function __set($sName, $mValue)
    {
        $this->aData[$sName] = $mValue;
    }

    private function getTypeClassName($sType)
    {
        return isset($this->aTypes[$sType]) ? $this->aTypes[$sType] : '\Core\Library\Form\Field\Type\\' . ucfirst($sType) . 'Type';
    }


    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->aFields[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->getField($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $value['name'] = $offset;
        $this->addField($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->aFields[$offset]);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->aErrors;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return $this->getVars();
    }
}