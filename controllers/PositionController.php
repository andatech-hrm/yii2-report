<?php

namespace andahrm\report\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use andahrm\structure\models\Section;
use andahrm\structure\models\SectionSearch;
use andahrm\report\models\Position;
use andahrm\structure\models\FiscalYear;
use andahrm\report\models\YearSearch;

use andahrm\structure\models\PositionSearch;

class PositionController extends \yii\web\Controller
{
     public function actions()
    {
        $this->layout='position-menu-left';
    }
    
    public function actionIndex($code=null)
    {
        $test = new \console\controllers\TestController(fhir, Yii::$app); 
         $test->runAction('index');
        
        $test = new \console\controllers\TestController(Yii::$app->controller->id, Yii::$app); 
        $test->actionIndex();
        exit();
        
        
        if($code){
            $models = Position::find()->all();
            foreach($models as $model){
                $model->code = $model->generatCode;
                $model->save(false);
            }
        }
        
        $searchModel = new PositionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort->defaultOrder = [
            'person_type_id'=>SORT_ASC,
            'section_id'=>SORT_ASC,
            'position_line_id'=>SORT_ASC,
            'number'=>SORT_ASC,
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionCapacity()
    {
        
        
         $models['year-search'] = new YearSearch();
        if(!$models['year-search']->load(Yii::$app->request->get())){
            $models['year-search']->year = date('Y');
        }
        
        
        //$modelSections = Section::find();
        $modelPositions = Position::find();
        $modelPositions->select(['*', 'count(*) as count_year']);
        
        // $select = [];
        // $select[] = "*";
        // foreach(FiscalYear::getList() as $year => $yearTh){
        //     $select[] = 'y'.$year;
        //     //$modelPositions->addSelect('y'.$year);
        // }
        
        $modelPositions->groupBy(['section_id','position_line_id', 'position_level_id']);
        
        //$modelPositions = Position::find()->all();
        //$dataProvider = $modelSections->search(Yii::$app->request->queryParams);
        
        // print_r(FiscalYear::getList());
        // exit();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $modelPositions,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    //'created_at' => SORT_DESC,
                    'section_id' => SORT_ASC, 
                ]
            ],
        ]);

        return $this->render('capacity', [
            //'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'models' => $models,
        ]);
        
        
       
    }

}
