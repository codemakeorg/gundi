<?php
namespace Core\Contract\Gundi;

interface IBootstrap
{
    /**
     * Boot system and module
     * @param array $aData
     * @return void
     */
    public function boot(array $aData);
}