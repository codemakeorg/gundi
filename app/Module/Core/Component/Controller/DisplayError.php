<?php

namespace Module\Core\Component\Controller;

use Core\Library\Component\Controller;

class DisplayError extends Controller
{
    public function index($sErrorMessage)
    {
        $this->oView->assign('sErrorMessage', $sErrorMessage);
    }

}
