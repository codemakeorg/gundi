<?php
namespace Core\Library\Component;

use Core\Contract\View\IBlock;

abstract class Block extends Component implements IBlock
{
    protected $sViewDir = 'View/Block/';
}