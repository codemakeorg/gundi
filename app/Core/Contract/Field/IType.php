<?php

namespace Core\Contract\Field;


interface IType
{
    /**
     * @return boolean
     */
    public function isValid();

    /**
     * @return boolean
     */
    public function isEmpty();

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return string mixed - html markup
     */
    public function render();

    /**
     * @return array
     */
    public function getErrors();
}