<?php
namespace Core\Contract\Resource;


interface IShowable
{
    /**
     * Show list
     * @return void
     */
    public function index();

    /**
     * Show resource
     * @param $mId
     * @return void
     */
    public function show($mId);
}