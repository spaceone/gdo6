<?php
namespace GDO\Form;

use GDO\Core\GDT;

class GDT_Validator extends GDT
{
    public $validator;
    public $validateField;
    public function validator($fieldName, $validator) { $this->validateField = $fieldName;  $this->validator = $validator; return $this; }
    
    public function validate($value)
    {
        $form = GDT_Form::$VALIDATING_INSTANCE;
        return call_user_func($this->validator, $form, $this->validatorField(), $this->validatorField()->getValue());
    }
    public function renderCell() { return ''; }
    public function validatorField() { return GDT_Form::$VALIDATING_INSTANCE->fields[$this->validateField]; }
}
