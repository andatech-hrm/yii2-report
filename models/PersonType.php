<?php

namespace andahrm\report\models;

use Yii;
use yii\helpers\ArrayHelper;

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
class PersonType extends \andahrm\structure\models\PersonType
{
    
  public $gender;
  public $year;
  
  public function scenarios(){
      $scenarios = parent::scenarios();
      $scenarios['report'] = ['gender','year'];
      return $scenarios;
    }
    
     public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['year'] = Yii::t('andahrm/structure', 'ปีงบประมาณ');
        return $attr;
    }
  
  
}
