<?php

namespace andahrm\report\controllers;

use Yii;
use yii\web\Controller;
use andahrm\person\models\Person;
use andahrm\positionSalary\models\PersonPosition;
use andahrm\positionSalary\models\PersonPositionSalary;
use andahrm\report\models\PersonType;
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
        
        $this->layout = 'person-menu-left';
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
    
    public function actionGender()
    {
        
        $model= new PersonType(['scenario'=>'report']);
        
        
        if($model->load(Yii::$app->request->get())){

        }
        
        $modelType = PersonType::find()->all();
                //$modelType->scenario = 'report';
                //$modelType = ArrayHelper::index($modelType,'id');
                
                $modelGender = Person::find()->select('gender')->distinct()->groupBy('gender')->orderBy(['gender'=>SORT_DESC])->all();
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
                
                
                
        
        
        return $this->render('gender', [
            'model'=>$model,
            'models' => $modelType,
            'modelGender'=>$newGender
        ]);
    }
}
