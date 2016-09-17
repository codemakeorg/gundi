<?php

namespace Core\Contract\Request;

/**
 * Interface IRequest
 * @package Core\Contract
 */
interface IRequest extends \ArrayAccess
{
    public function get($sName, $sType = null);
    public function post($sName, $sType = null);
    public function getExt();
    public function getUri();
    public function getHttpMethod();
    public function isPost();
}