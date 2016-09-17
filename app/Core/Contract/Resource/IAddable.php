<?php
namespace Core\Contract\Resource;


interface IAddable
{
    /**
     * Show add form
     * @return void
     */
    public function add();

    /**
     * Save to DB
     * @return void
     */
    public function create();
}