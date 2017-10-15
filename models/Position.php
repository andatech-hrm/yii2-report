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
    public $count_salary;
    public $count_person;
    
    
     public function attributeLabels()
    {
        $attr = parent::attributeLabels();
        $attr['count_year'] = Yii::t('andahrm/structure', 'Count Year');
        $attr['sum'] = Yii::t('andahrm/report', 'Sum');
        return $attr;
    }
    
    public function getTitleLevel(){
        return $this->title.($this->position_level_id?' '.$this->positionLevel->title:'');
        //return $this->title.($this->position_level_id?' '.$this->positionLevel->title:'');
    }
    
    public function getCurrentPerson(){
        $model = self::find()
        ->select('count(person_position_salary.user_id) as count_person')
        ->joinWith('personPositionSalary')
        //->where(['<=','rate_date',$year])
        ->andWhere(['position.section_id'=>$this->section_id])
        ->andWhere(['position.position_line_id'=>$this->position_line_id])
        ->andWhere(['position.position_level_id'=>$this->position_level_id])
        //->groupBy('position_id')
        ->one();
        
        return $model->count_person?$model->count_person:0;
    }
    
    public function getCurrentSalary(){
        $model = self::find()
        ->select('sum(person_position_salary.salary) as count_salary')
        ->joinWith('personPositionSalary')
        //->where(['<=','rate_date',$year])
        ->andWhere(['position.section_id'=>$this->section_id])
        ->andWhere(['position.position_line_id'=>$this->position_line_id])
        ->andWhere(['position.position_level_id'=>$this->position_level_id])
        ->one();
        return $model->count_salary?$model->count_salary:0;
        //return $model->count_salary?Yii::$app->formatter->asDecimal($model->count_salary,0):0;
    }
    
    public function getYearSalary($year){
        $model = self::find()
        ->select('sum(person_position_salary.salary) as count_salary')
        ->joinWith('personPositionSalary')
        //->where(['<=','rate_date',$year])
        ->andWhere(['position.section_id'=>$this->section_id])
        ->andWhere(['position.position_line_id'=>$this->position_line_id])
        ->andWhere(['position.position_level_id'=>$this->position_level_id]);
        $last='';
        if(!empty($year)){
            $y = intval($year);
            $dateBetween = FiscalYear::getDateBetween($y);
            //$model->andWhere(['<', 'DATE(open_date)', $dateBetween->date_end]);
            $model->andWhere("DATE(person_position_salary.adjust_date) <= '{$dateBetween->date_end}' " );
            $last = $dateBetween->date_end;
        } 
        
        $model = $model->one();
        return $model->count_salary?$model->count_salary:0;
        //return $model->count_salary?Yii::$app->formatter->asDecimal($model->count_salary,0):0;
    }
    
    public function getTotalSalary($year){
        $model = self::find()
        ->select('sum(person_position_salary.salary) as count_salary')
        ->joinWith('personPositionSalary')
        //->where(['<=','rate_date',$year])
        ->andWhere(['position.section_id'=>$this->section_id])
        ->andWhere(['position.position_line_id'=>$this->position_line_id])
        ->andWhere(['position.position_level_id'=>$this->position_level_id]);
        $last='';
        if(!empty($year)){
            $y = intval($year);
            $dateBetween = FiscalYear::getDateBetween($y);
            //$model->andWhere(['<', 'DATE(open_date)', $dateBetween->date_end]);
            $model->andWhere("DATE(person_position_salary.adjust_date) <= '{$dateBetween->date_end}' " );
            $last = $dateBetween->date_end;
        } 
        
        $model = $model->one();
        return $model->count_salary?$model->count_salary:0;
        //return $model->count_salary?Yii::$app->formatter->asDecimal($model->count_salary,0):0;
    }
    
    public function getNewRate($year){
        
        $model = self::find()
        //->where(['<=','rate_date',$year])
        ->andWhere(['section_id'=>$this->section_id])
        ->andWhere(['position_line_id'=>$this->position_line_id])
        ->andWhere(['position_level_id'=>$this->position_level_id]);
        
        $last='';
        if(!empty($year)){
            $y = intval($year);
            $dateBetween = FiscalYear::getDateBetween($y);
            //$model->andWhere(['<', 'DATE(open_date)', $dateBetween->date_end]);
            $model->andWhere("DATE(open_date) <= '{$dateBetween->date_end}' OR open_date IS NULL" );
            $last = $dateBetween->date_end;
        } 
        
        
         $modelDown = self::find()
        //->where(['<=','rate_date',$year])
        ->andWhere(['section_id'=>$this->section_id])
        ->andWhere(['position_line_id'=>$this->position_line_id])
        ->andWhere(['position_level_id'=>$this->position_level_id]);
        
        if(!empty($year)){
            $y = intval($year);
            $dateBetween = FiscalYear::getDateBetween($y);
            //$model->andWhere(['<', 'DATE(open_date)', $dateBetween->date_end]);
            $modelDown->andWhere("DATE(close_date) <= '{$dateBetween->date_end}' " );
        } 
        
        
        //return $last.' '.($model->count() - $modelDown->count());
        return $model->count() - $modelDown->count();
    }
    
    public function getUpDown($oldYear,$year){
        //$oldYear = intval($oldYear)-1;
        $model = self::find()
        //->where(['<=','rate_date',$year])
        ->andWhere(['section_id'=>$this->section_id])
        ->andWhere(['position_line_id'=>$this->position_line_id])
        ->andWhere(['position_level_id'=>$this->position_level_id]);
        
        if(!empty($year)){
                $y = intval($year);
                $dateBetween = FiscalYear::getDateBetween($y);
                
                $model->andWhere(['>=', 'DATE(open_date)', $dateBetween->date_start])
                ->andWhere(['<=', 'DATE(open_date)', $dateBetween->date_end]);
            } 
        $up = $model->count();
        
        
        //close_date
        
        $modelClose = self::find()
        //->where(['<=','rate_date',$year])
        ->andWhere(['section_id'=>$this->section_id])
        ->andWhere(['position_line_id'=>$this->position_line_id])
        ->andWhere(['position_level_id'=>$this->position_level_id]);
        
        if(!empty($year)){
                $y = intval($year);
                $dateBetween = FiscalYear::getDateBetween($y);
                
                $modelClose->andWhere(['>=', 'DATE(close_date)', $dateBetween->date_start])
                ->andWhere(['<=', 'DATE(close_date)', $dateBetween->date_end]);
            } 
        $down = $modelClose->count();
        
        
        
        // $modelCurrent = self::find()
        // //->where(['<=','open_date',$year])
        // ->andWhere(['section_id'=>$this->section_id])
        // ->andWhere(['position_line_id'=>$this->position_line_id])
        // ->andWhere(['position_level_id'=>$this->position_level_id]);
        // if(!empty($oldYear)){
        //     $y = intval($oldYear);
        //     $dateBetween = FiscalYear::getDateBetween($y);
        //     $modelCurrent->andWhere("DATE(open_date) < '{$dateBetween->date_end}' OR open_date IS NULL" );
        // } 
        // $countCurrent = $modelCurrent->count();
        
        // $modelBefore = self::find()
        // //->where(['<=','open_date',$year])
        // ->andWhere(['section_id'=>$this->section_id])
        // ->andWhere(['position_line_id'=>$this->position_line_id])
        // ->andWhere(['position_level_id'=>$this->position_level_id]);
        // if(!empty($oldYear)){
        //     $y = intval($oldYear-1);
        //     $dateBetween = FiscalYear::getDateBetween($y);
        //     $modelBefore->andWhere("DATE(open_date) < '{$dateBetween->date_end}' OR open_date IS NULL" );
        // } 
        // $countBefore = $modelBefore->count();
        // $down = $countBefore - $countCurrent;
        $total = $up-$down;
        //return ;
        if($total>0){
            return "+".$total;
        }else{
            return $total?$total:'-';
            //return $down." = ".$countCurrent ."- ".$countBefore;
            //return $down?"-".$down:'-';
            //return $down>0?'-'.$down:0;
        }
    }

}
