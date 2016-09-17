<?php
namespace Core\Library\Form\Field\Type;

class TextType extends StringType
{
    protected $rules = [
        'required' => false,
        'length' => 5000
    ];

    protected $aInfo  = [
        'template' => 'Core:Type/text'
    ];
}