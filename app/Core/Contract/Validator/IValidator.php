<?php
namespace Core\Contract\Validator;

interface IValidator
{
    /**
     * @param array $aVars - vars for validate
     * @return boolean
     */
    public function isValid($aVars);

    /**
     * @param array $aVars - vars for validate
     * @exception \LogicException
     * @return  void
     */
    public function validateOrFail($aVars);

    /**
     * @return mixed
     */
    public function getMessages();
}