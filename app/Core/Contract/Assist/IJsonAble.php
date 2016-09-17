<?php
namespace Core\Contract\Assist;

interface IJsonAble
{
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $iOptions
     * @return string
     */
    public function toJson($iOptions = 0);
}