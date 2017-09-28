<?php

namespace andahrm\report\controllers;

use andahrm\structure\models\Section;
use andahrm\structure\models\SectionSearch;
use andahrm\structure\models\Position;
use yii\data\ActiveDataProvider;

class PositionController extends \yii\web\Controller
{
     public function actions()
    {
        $this->layout='person-menu-left';
    }
    
    public function actionIndex()
    {
        //$modelSections = Section::find();
        $modelPositions = Position::find();
        $modelPositions->groupBy(['section_id','position_level_id']);
        $modelPositions->select(['*','count(id) as count']);
        
        //$modelPositions = Position::find()->all();
        //$dataProvider = $modelSections->search(Yii::$app->request->queryParams);
        
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

        return $this->render('index', [
            //'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        
        
       
    }

}
