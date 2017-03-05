<?php

namespace andahrm\report\models;

use Yii;
use yii\helpers\ArrayHelper;
use andahrm\report\models\PersonType;

/**
* This is the model class for table "person_type".
 
* @property integer $id
* @property string $code
* @property string $title
* @property integer $step_max 
* @property string $note
* @property integer $created_at
* @property integer $created_by
* @property integer $updated_at
* @property integer $updated_by
*
* @property BaseSalary[] $baseSalaries 
* @property Position[] $positions
* @property PositionLine[] $positionLines
* @property PositionType[] $positionTypes 
*/
class PersonLeave extends \andahrm\leave\models\Leave
{
    
  
  public function getCount(){
      $type = PersonType::find()->all();
       $newSelect = ArrayHelper::getColumn($type, function ($element) {
            return ['tyle'.$element->id =>0];
        });
      
      return (object)$newSelect;
  }
  
  
}
