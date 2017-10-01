<?php

namespace andahrm\report\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use andahrm\person\models\Person;
use andahrm\positionSalary\models\PersonPositionSalary;
use andahrm\structure\models\FiscalYear;



class Position extends \andahrm\structure\models\Position
{
    //  public $y2019;
    //  public $y2018;
    //  public $y2016;
    //  public $y2015;
    //  public $y2014;
    //  public $y2013;
    
    // public function init(){
    //     parent::init();
    //     foreach(FiscalYear::getList() as $year=>$th){
    //         $this->{'y'.$year} = null;
    //     }
    // }

    
    //  public function attributes()
    // {
       
    //     return array_merge(parent::attributes(),['countYear']);
    // }
    
    public $count_year;
    
     public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['count_year'] = Yii::t('andahrm/structure', 'countYear');
        $attr['sum'] = Yii::t('andahrm/report', 'Sum');
        return $attr;
    }
    
    
    
    public function getRateDate($year){
        $model = self::find()->where(['<=','rate_date',$year])
        ->andWhere(['section_id'=>$this->section_id])
        ->andWhere(['position_line_id'=>$this->position_line_id])
        ->andWhere(['position_level_id'=>$this->position_level_id])
        ->count();
        return $model;
    }

}
