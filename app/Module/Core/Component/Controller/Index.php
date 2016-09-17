<?php

namespace Module\Core\Component\Controller;

use Core\Library\Component\Controller;

class Index extends Controller
{
    public function index()
    {
       $this->oView->assign('title', 'Hello i am a core index');
    }
}

?>