<?php

namespace Module\Core\Component\Controller;

use Core\Library\Component\Controller;
use Core\Library\Theme\Theme;

class DisplayErr404 extends Controller
{
    /**
     * @var Theme
     */
    private $_oTheme;

    public function __construct(Theme $oTheme)
    {
        $this->_oTheme = $oTheme;
    }

    /**
     * Index page is a error 404
     */

    public function index()
    {
        $this->_oTheme->setLayout('blank');
        header(Gundi()->Url->getHeaderCode(404));
    }

}
