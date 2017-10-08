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
        $models['year-search']->load(Yii::$app->request->get());
         
        
        $modelPositionsCount = Position::find()->from("position p");
        $modelPositionsCount->select(['count(p.id)']);
        $modelPositionsCount->where('p.section_id = position.section_id');
        $modelPositionsCount->andWhere('p.position_line_id = position.position_line_id');
        $modelPositionsCount->andWhere('p.position_level_id = position.position_level_id');
       if($models['year-search']->start !== null && !empty($models['year-search']->start)){
            $y = intval($models['year-search']->start);
            $dateBetween = FiscalYear::getDateBetween($y);
            $modelPositionsCount->andWhere("DATE(p.open_date) <= '{$dateBetween->date_end}' OR p.open_date IS NULL" );
            //$modelPositionsCount->Where(['p.open_date'=>null ]);
        } 
        
        //$modelPositionsCount->groupBy(['p.section_id','p.position_line_id', 'p.position_level_id']);
         
        
        
        //$modelSections = Section::find();
        $modelPositions = Position::find();
        $modelPositions->select(['*', "count_year" => $modelPositionsCount]);
        
        //  $modelPositions->where('section_id = 2');
        // $modelPositions->andWhere('position_line_id = 2');
        // $modelPositions->andWhere('position_level_id = 8');
        
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
                    'id' => SORT_ASC, 
                ]
            ],
        ]);

        return $this->render('capacity', [
            //'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'models' => $models,
        ]);
        
        
       
    }
    
    
    public function actionPay(){
        return 555;
    }

}
