<?php

namespace andahrm\report\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use andahrm\person\models\Person;
use andahrm\positionSalary\models\PersonPosition;
use andahrm\report\models\PersonPositionSalary;
use andahrm\report\models\PersonType;
use andahrm\report\models\PersonLeave;
use andahrm\report\models\YearSearch;
use andahrm\report\models\Contract;
use yii\helpers\ArrayHelper;

/**
 * Default controller for the `report` module
 */
class PersonController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
     public function actions()
    {
        $this->layout='person-menu-left';
    }
     
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionPosition()
    {
        $model = PersonPosition::find()->all();
        return $this->render('position', ['model' => $model]);
    }
    
    public function actionType()
    {
        $model = PersonType::find()->all();
        return $this->render('type', ['model' => $model]);
    }
    
    public function actionGender_mad()
    {
        
        $model= new PersonType(['scenario'=>'report']);
        
        
        if($model->load(Yii::$app->request->get())){

        }
        
        $modelType = PersonType::find()->all();
                //$modelType->scenario = 'report';
                //$modelType = ArrayHelper::index($modelType,'id');
                
                $modelGender = Person::find()->select('gender')->distinct()->groupBy('gender')->orderBy('gender')->all();
                $modelGender = ArrayHelper::map($modelGender,'gender','genderText');
                $newGender = [];
                foreach($modelType as $type){
                    
                    foreach($modelGender as $kg => $vg){
                        $countGender = PersonPositionSalary::find()
                                ->joinWith('position')
                                ->joinWith('user')
                                ->where(['position.person_type_id'=>$type->id])
                                ->andWhere(['person.gender'=>$kg])
                        // ->groupBy('person.gender')
                        // ->orderBy('person.gender')
                        ->count();
                        $newGender[$kg]['title'] = $modelGender[$kg];
                        $newGender[$kg]['count'] = $countGender;
                        $type->gender = $newGender;
                    }
                }
                
                 //echo "<pre>";
                 //print_r($modelGender);
                //\Yii::$app->end();
                //$models['person'] = \andahrm\person\models\Person::find()->all();
                //$models['person'] = \andahrm\person\models\Person::find()->all();
                
                
                
        
        
        return $this->render('gender_mad', [
            'model'=>$model,
            'models' => $modelType,
            'modelGender'=>$newGender
        ]);
    }
    
    public function actionGender(){
        $models['person-type'] = PersonType::find()->all();
        $models['year-list'] = PersonPositionSalary::find()
        ->select('YEAR(`adjust_date`) as year')
        ->groupBy('YEAR(`adjust_date`)')->asArray()->all();
        $models['year-search'] = new YearSearch();
        $models['year-search']->load(Yii::$app->request->get());
        
        foreach ($models['person-type'] as $key => $personType) :
            $query = PersonPositionSalary::find()
            ->select("
            person.firstname_th, 
            SUM(CASE WHEN person.gender = 'm' THEN 1 ELSE 0 END) as genderMaleCount,
            SUM(CASE WHEN person.gender = 'f' THEN 1 ELSE 0 END) as genderFemaleCount,
            ")
            ->joinWith('position')
            ->joinWith('user')
            ->where(['position.person_type_id' => $personType->id]);
            
            if($models['year-search']->year !== null && !empty($models['year-search']->year)){
                $y = intval($models['year-search']->year);
                $dateBetween = \andahrm\structure\models\FiscalYear::getDateBetween($y);
                $query = $query->andwhere(['between', 'DATE(person_position_salary.adjust_date)', $dateBetween->date_start, $dateBetween->date_end]);
            }
            $models['person-position-salary'][$key] = $query
            ->one();
        endforeach;

        return $this->render('gender', ['models' => $models]);
    }
    
    /////////////////
    public function actionGender2()
    {
        $models['person-type'] = PersonType::find()->all();
        $models['year-search'] = new YearSearch();
        $models['year-search']->load(Yii::$app->request->get());
        
        foreach ($models['person-type'] as $key => $personType) :
            $query = Contract::find()
            ->select("
            person.firstname_th, 
            SUM(CASE WHEN person.gender = 'm' THEN 1 ELSE 0 END) as genderMaleCount,
            SUM(CASE WHEN person.gender = 'f' THEN 1 ELSE 0 END) as genderFemaleCount,
            ")
            ->joinWith('position')
            ->joinWith('user')
            ->where(['position.person_type_id' => $personType->id]);
            
            if($models['year-search']->year !== null && !empty($models['year-search']->year)){
                $y = intval($models['year-search']->year);
                $dateBetween = \andahrm\structure\models\FiscalYear::getDateBetween($y);
                // $query = $query->andWhere(['between', 'DATE(person_contract.start_date)', $dateBetween->date_start, $dateBetween->date_end]);
                
                $query->andWhere(['<=', 'DATE(person_contract.start_date)', $dateBetween->date_end])
                ->andWhere(['>=', 'DATE(person_contract.end_date)', $dateBetween->date_start]);
                
            }
            // $query->groupBy('person_contract.user_id');
            $models['person-position-salary'][$key] = $query
            ->one();
        endforeach;
        return $this->render('gender2', ['models' => $models]);
    }
    //////////////////
    
    public function actionLevel()
    {
        $models['person-level'] = \andahrm\structure\models\PositionLevel::find()->all();
        foreach($models['person-level'] as $key => $level) {
            $query = PersonPositionSalary::find()
            ->select(['levelPersonCount' => 'COUNT(*)'])
            ->joinWith('position')
            ->joinWith('user')
            ->where(['position.position_level_id' => $level->id]);
            
            // $models['person-position-salary'][$level->id] = $query->asArray()
            // ->one();
            $models['person-position-salary'][$level->id] = $query->one();
        }
        
        // return $this->renderContent('ssss');
        // echo '<pre>';
        // echo $models['person-position-salary'][2]->PersonCount;
        // print_r($models['person-position-salary']);
        // exit();
        
        //Yii::$app->end();

        return $this->render('level', ['models' => $models]);
    }
    
    public function actionLeave(){
        $models['person'] = PersonPositionSalary::find()->all();
        $models['leaveType'] = \andahrm\leave\models\LeaveType::find()->all();
        
        $newSelect = [];
       
       $newSelect = ArrayHelper::getColumn($models['leaveType'], function ($element) {
            return "SUM(CASE WHEN leave_type_id = {$element->id} THEN 1 ELSE 0 END) as type".$element->id;
    });
    
      
    // print_r($newSelect);
    // exit();

        $query = PersonLeave::find()
        ->joinWith('leaveType')
        ->select(ArrayHelper::merge(['leave.created_by'],$newSelect))
        ->groupBy(['created_by']);
        
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            // 'sort' => [
            //     'defaultOrder' => [
            //         'created_at' => SORT_DESC,
            //         'title' => SORT_ASC, 
            //     ]
            // ],
        ]);

        $models['provider']=$provider;
        
        
        
        return $this->render('leave'
        , ['models' => $models]
        );
    }
    
}
