<?php
namespace Core\Library\Validator;

class Exception extends \LogicException
{
    private $aErrors = [];

    /**
     * @param array $aErrors
     * @return Exception
     */
    public function setErrors($aErrors)
    {
        $this->aErrors = $aErrors;
        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->aErrors;
    }
}