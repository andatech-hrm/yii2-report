<?php

namespace andahrm\report\models;


class PersonPositionSalary extends \andahrm\positionSalary\models\PersonPositionSalary
{
    public $year;
    
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['year'], 'integer'];
        return $rules;
    }
}