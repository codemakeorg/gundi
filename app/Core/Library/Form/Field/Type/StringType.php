<?php
namespace Core\Library\Form\Field\Type;


use Core\Library\Form\Field\AbstractType;
use Core\Library\Validator\Exception;

class StringType extends AbstractType
{
    protected $aInfo  = [
        'template' => 'Core:Type/string',
    ];


    /**
     * @return void
     */
    protected function assignVars()
    {
        parent::assignVars();
        $this->oView->assign([
           'name' => $this->aInfo['name'],
           'title' => $this->aInfo['title'],
           'value' => $this->getValue()
        ]);
    }

}

