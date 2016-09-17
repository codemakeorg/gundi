<?php
namespace Core\Contract\Resource;


interface IDeleteable
{

    /**
     * @param string|int $mID
     * @return void
     */
    public function delete($mID);
}