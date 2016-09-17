<?php
namespace Core\Library\View;

use Core\Library\View\Html\View;

class Events
{
    const HTML_CREATED = 'View\Html\Created';

    protected $oGundi;

    public function __construct()
    {
        $this->oGundi = Gundi();
    }

    public function loadExt(View $oView)
    {
        $aExtension = [
            $this->oGundi['Uri'],
            $this->oGundi['Token'],
            $this->oGundi['Asset'],
            $this->oGundi['File'],
            $this->oGundi['Block'],//this extension must be register last else will be other extension not available
        ];

        $oView->registerFunc('getParam', function($sName, $mDef = null){
            return $this->oGundi['config']->getParam($sName, $mDef);
        });

        foreach ($aExtension as &$oExtension) {
            $oView->loadExtension($oExtension);
        }
    }
}