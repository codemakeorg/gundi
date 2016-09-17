<?php
namespace Core\Library\View;

class JsonView extends AbstractView
{
    public function render()
    {
        header('Content-Type: application/json');
        $this->_aGlobalVars['meta'] = ['token'=>Gundi()->Token->make()];
        $aVars = array_merge($this->_aGlobalVars, $this->_aVars);
        return empty($aVars) ? '' : json_encode($aVars);
    }
}