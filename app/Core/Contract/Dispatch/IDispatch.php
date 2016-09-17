<?php
namespace Core\Contract\Dispatch;

interface IDispatch
{
    /**
     * dispatching Controller
     * @return mixed
     */
    public function dispatch();
}