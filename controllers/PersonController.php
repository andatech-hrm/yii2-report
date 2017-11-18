<?php

namespace andahrm\report\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use andahrm\person\models\Person;
use andahrm\person\models\Religion;
use andahrm\person\models\Education;
use andahrm\person\models\EducationLevel;

use andahrm\report\models\PersonSearch;
use andahrm\positionSalary\models\PersonPosition;
use andahrm\structure\models\FiscalYear;
use andahrm\structure\models\Position;
use andahrm\structure\models\Section;
use andahrm\structure\models\PositionType;
//use andahrm\positionSalary\models\PersonPositionSalary;
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
        $searchModel = new PersonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $header = [];
        if($get=Yii::$app->request->get('PersonSearch')){
            if(isset($get['person_type_id'])){
                $personType = PersonType::findOne($get['person_type_id']);
                $header[] = 'แบ่งตามประเภทบุคคล ' 
                .$personType->title;
            }
            if(isset($get['year'])){
                $header[] = 'ประจำปี '
                            .($get['year']+543);
            }
        }
        
        //$dataProvider->pagination->pageSize = Yii::$app->params['app-settings']['reading']['pagesize'];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'header'=>$header,
        ]);
    }
    
    public function actionPosition()
    {
        $model = PersonPosition::find()->all();
        return $this->render('position', ['model' => $model]);
    }
    
    public function actionType()
    {
        
        $models['year-search'] = new YearSearch();
        $models['year-search']->load(Yii::$app->request->get());
        
        $user = PersonPositionSalary::find()
            ->select(['user_id'])
            //->where('position_id = position.id')
            ->groupBy(['user_id']);
        
        
        // $person = PersonPositionSalary::find()
        //     ->select(['count(*) as count'])
        //     ->joinWith('position')
        //     ->where('position.person_type_id = person_type.id')
        //     //->andWhere(['user_id'=>$user])
        //     ->groupBy(['user_id'])
        //     ->groupBy(['position.person_type_id']);
        
        $position = Position::find()
            ->select(['count(*)'])
            ->joinWith('personPositionSalaries')
            ->where('position.person_type_id = person_type.id')
            ->andWhere(['user_id'=>$user]);
            //->groupBy(['person_type_id']);
            
        $person = Person::find()
            ->select('count(distinct(person.user_id))')
            //->distinct('person.user_id')
            ->joinWith('positionSalary.position')
            ->where('position.person_type_id = person_type.id');
        if($models['year-search']->year !== null && !empty($models['year-search']->year)){
                $y = intval($models['year-search']->year);
                $dateBetween = FiscalYear::getDateBetween($y);
                
                $person->andWhere(['>=', 'DATE(person_position_salary.adjust_date)', $dateBetween->date_start])
                ->andWhere(['<=', 'DATE(person_position_salary.adjust_date)', $dateBetween->date_end]);
            }    
            
            
            
        
        $query = PersonType::find();
        $query->select(['person_type.*','count'=>$person]);
        //$query->leftJoin('position', 'position.person_type_id = person_type.id')
          //      ->leftJoin('person_position_salary', 'person_position_salary.position_id = position.id');
        
        $query->andWhere(['!=', 'parent_id', '0']);
                
        //$query->groupBy(['position.person_type_id']);
                
        $modelPersonType = $query->all();
        /*
        $person = PersonPositionSalary::find()->joinWith('position')->groupBy(['position.person_type_id'])->select(['position.person_type_id','count(user_id) as count'])->all();
        
        
        $arr = ArrayHelper::index($person,'person_type_id');
        
        foreach( $models as $model ){
            $model->count = isset($arr[$model->id])&&$arr[$model->id]?$arr[$model->id]->count:0;
        }
        */
        
        // echo "<pre>";
        // print_r($models);
        // print_r($arr);
        // exit();
        
        $dataProvider = new ActiveDataProvider([
        'query' => $query,
        'pagination' => false,
        'sort' => [
            'defaultOrder' => [
                'parent_id' => SORT_ASC,
                'sort'=>SORT_ASC,
            ]
        ],
    ]);
        
        
        return $this->render('type', ['model' => $modelPersonType ,'dataProvider'=>$dataProvider,'models'=>$models]);
    }
    
    
    
    public function actionGender(){
        //$models['person-type'] = PersonType::find()->all();
        $models['year-search'] = new YearSearch();
        if(!$models['year-search']->load(Yii::$app->request->get())){
            //$models['year-search']->year = date('Y');
        }
        
        $genderMaleCount = Person::find()
            ->select('count(distinct(person.user_id))')
            ->joinWith('positionSalary.position')
            ->where('position.person_type_id = person_type.id')
            ->andWhere(['gender'=>'m']);
            
        $genderFemaleCount = Person::find()
            ->select('count(distinct(person.user_id))')
            ->joinWith('positionSalary.position')
            ->where('position.person_type_id = person_type.id')
            ->andWhere(['gender'=>'f']);
        
        if($models['year-search']->year !== null && !empty($models['year-search']->year)){
            $y = intval($models['year-search']->year);
            $dateBetween = FiscalYear::getDateBetween($y);
            
            $genderMaleCount->andWhere(['>=', 'DATE(person_position_salary.adjust_date)', $dateBetween->date_start])
            ->andWhere(['<=', 'DATE(person_position_salary.adjust_date)', $dateBetween->date_end]);
            
            $genderFemaleCount->andWhere(['>=', 'DATE(person_position_salary.adjust_date)', $dateBetween->date_start])
            ->andWhere(['<=', 'DATE(person_position_salary.adjust_date)', $dateBetween->date_end]);
        }    
        
        
        $query = PersonType::find();
        $query->select([
            'person_type.*',
            'genderMaleCount'=>$genderMaleCount,
            'genderFemaleCount'=>$genderFemaleCount
        ]);
        $query->andWhere(['!=', 'parent_id', '0']);
        $modelPersonType = $query->all();
        
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'parent_id' => SORT_ASC,
                    'sort'=>SORT_ASC,
                ]
            ],
        ]);

        return $this->render('gender', ['model' => $modelPersonType,'models' => $models,'dataProvider'=>$dataProvider,]);
    }
    
    public function actionGender1(){
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
                
                $query->andWhere(['<=', 'DATE(person_contract.start_date)', $dateBetween->date_end])
                ->andWhere(['>=', 'DATE(person_contract.end_date)', $dateBetween->date_start]);
                
            }
            $models['person-position-salary'][$key] = $query
            ->one();
        endforeach;

        return $this->render('gender', ['models' => $models]);
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
                
                $query->andWhere(['<=', 'DATE(person_contract.start_date)', $dateBetween->date_end])
                ->andWhere(['>=', 'DATE(person_contract.end_date)', $dateBetween->date_start]);
                
            }
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
    
    public function actionReligion(){
        $modelReligion = Religion::find()->all();
        
        $dataProvider = new ArrayDataProvider([
            'allModels'=>$modelReligion
            ]);
        
        return $this->render('religion',[
            'modelReligion'=>$modelReligion,
            'dataProvider'=>$dataProvider
            ]);
    }
    
    // public function actionDegree(){
        
    //     $modelUser = Education::find()
    //         //->limit(1)
    //         ->groupBy('user_id')
    //         ->orderBy(['degree'=>SORT_ASC, 'year_end'=>SORT_ASC])
    //         ->all();
        
    //     $modelDegree = Education::find()
    //     //->where(['=', "user_id",$find ])
    //     //->distinct('degree')
    //     //->from('person_education as ss')
    //     //->select(['degree'])
    //     ->orderBy('degree')
    //     //->addSelect(['count_person'=>$find])
    //     ->groupBy('degree');
    //     //->asArray();
    //     //echo $modelDegree->createCommand()->getRawSql();
    //     $modelDegree = $modelDegree->all();
        
    //     $modelDegrees = [];
    //     $f_degree = $modelDegree[0]->degree;
    //     $count =0;
    //     foreach($modelDegree as $degree){
            
    //         if($f_degree != $degree->degree){
    //             $f_degree = $degree->degree;
    //             $count = 0;
    //         }
            
    //         foreach($modelUser as $user){
    //             if($user->degree == $degree->degree)
    //             $count++;
    //         }
    //         if($count){
    //             $degree->count_person = $count;
    //             $modelDegrees[] = $degree;
    //         }
    //     }
        
    //     ArrayHelper::multisort($modelDegrees, ['count_person'], [SORT_DESC]);
        
    //     //$modelDegrees = Education::find()->select(['degree','count_person'=>'count(user_id)'])->groupBy('degree')->all();
        
    //     // echo "<pre>";
    //     // print_r($modelDegrees);
    //     // exit();
        
    //     $dataProvider = new ArrayDataProvider([
    //         'allModels'=>$modelDegrees,
    //         'pagination'=>false,
    //         ]);
        
    //     return $this->render('degree',[
    //         'modelDegree'=>$modelDegrees,
    //         'dataProvider'=>$dataProvider
    //         ]);
    // }
    
    
    public function actionDegree(){
        
        $modelUser = Education::find()
            //->select('count(distinct(user_id))')
            //->where("level_id = ss.id")
            ->groupBy('user_id')
            //->limit(1)
            ->orderBy(['year_end'=>SORT_DESC])
            ->all();
        
        $modelDegree = EducationLevel::find()
        //->where(['=', "user_id",$find ])
        //->distinct('degree')
        //->select(['*','count_person'=>$modelUser])
        //->from('person_education_level as ss')
        //->select(['degree'])
        //->orderBy('degree')
        //->addSelect(['count_person'=>$find])
        //->groupBy('degree');
        //->asArray();
        //echo $modelDegree->createCommand()->getRawSql();
        ->all();
        
        $modelDegrees = [];
        $f_degree = $modelDegree[0]->id;
        $count =0;
        foreach($modelDegree as $degree){
            
            if($f_degree != $degree->id){
                $f_degree = $degree->id;
                $count = 0;
            }
            
            foreach($modelUser as $user){
                if($user->level_id == $f_degree)
                $count++;
            }
            //if($count){
                $degree->count_person = $count;
                $modelDegrees[] = $degree;
            //}
        }
        
        // ArrayHelper::multisort($modelDegrees, ['count_person'], [SORT_DESC]);
        
        //$modelDegrees = Education::find()->select(['degree','count_person'=>'count(user_id)'])->groupBy('degree')->all();
        
        // echo "<pre>";
        // print_r($modelDegrees);
        // exit();
        
        $dataProvider = new ArrayDataProvider([
            'allModels'=>$modelDegrees,
            'pagination'=>false,
            ]);
        
        return $this->render('degree',[
            'modelDegree'=>$modelDegrees,
            'dataProvider'=>$dataProvider
            ]);
    }
    
     
    public function actionPositionType(){
        
        $modelPerson = PersonPositionSalary::find()
            ->select('count(distinct(user_id))')
            ->joinWith('position')
            ->where('position.position_type_id = ssss.id');
            //->groupBy('position.section_id');
        
        $modelPositionType= PositionType::find()
        ->from('position_type as ssss')
        //->where(['!=', "title",'อัตราเงินเดือน' ])
        ->select(['*','count_person'=>$modelPerson])
        ->orderBy(['person_type_id'=>SORT_ASC,'id'=>SORT_ASC])
        ->all();
        
        $modelNotSet = PersonPositionSalary::find()
            //->select('distinct(user_id)')
            ->joinWith('position')
            ->where('position.position_type_id IS NULL')
            //->groupBy('user_id')
            ->count();
        
        $newModel = new PositionType();
        $newModel->id = "0";
        $newModel->title = "อื่นๆ";
        $newModel->count_person = $modelNotSet;
        $modelPositionType[] = $newModel;
        
        //$modelDegrees = Education::find()->select(['degree','count_person'=>'count(user_id)'])->groupBy('degree')->all();
        
        // echo "<pre>";
        // print_r($modelDegrees);
        // exit();
        
        $dataProvider = new ArrayDataProvider([
            'allModels'=>$modelPositionType,
            'pagination'=>false,
            ]);
        
        return $this->render('position-type',[
            'models'=>$modelPositionType,
            'dataProvider'=>$dataProvider
            ]);
    }
    
    
    public function actionSection(){
        
        $modelPerson = PersonPositionSalary::find()
            ->select('count(distinct(user_id))')
            ->joinWith('position')
            ->where('position.section_id = ssss.id');
            //->groupBy('position.section_id');
        
        $modelSection= Section::find()
        //->where(['=', "user_id",$find ])
        //->distinct('degree')
        ->from('section as ssss')
        ->select(['*','count_person'=>$modelPerson]);
        //->asArray();
        //echo $modelDegree->createCommand()->getRawSql();
        $modelSection = $modelSection->all();
        
        
        //$modelDegrees = Education::find()->select(['degree','count_person'=>'count(user_id)'])->groupBy('degree')->all();
        
        // echo "<pre>";
        // print_r($modelDegrees);
        // exit();
        
        $dataProvider = new ArrayDataProvider([
            'allModels'=>$modelSection,
            'pagination'=>false,
            ]);
        
        return $this->render('section',[
            'models'=>$modelSection,
            'dataProvider'=>$dataProvider
            ]);
    }
    
    
    public function actionRangeAge(){
        
        $range_age = [];
        // $range_age[] = [
        //     'title'=>'ต่ำกว่า 20',
        //     'start'=>date('Y-m-d'),
        //     'end'=>date('Y-m-d', strtotime('-20 year')),
        //     'count_person'=>0
        // ];
        // $range_age[] = [
        //     'title'=>'21 - 30',
        //     'start'=>date('Y-m-d', strtotime('-20 year')),
        //     'end'=>date('Y-m-d', strtotime('-30 year')),
        //     'count_person'=>0
        //     ];
        // $range_age[] = [
        //     'title'=>'31 - 40',
        //     'start'=>date('Y-m-d', strtotime('-30 year')),
        //     'end'=>date('Y-m-d', strtotime('-40 year')),
        //     'count_person'=>0
        //     ];
        // $range_age[] = [
        //     'title'=>'41 - 50',
        //     'start'=>date('Y-m-d', strtotime('-40 year')),
        //     'end'=>date('Y-m-d', strtotime('-50 year')),
        //     'count_person'=>0
        //     ];
        // $range_age[] = [
        //     'title'=>'51 - 60',
        //     'start'=>date('Y-m-d', strtotime('-50 year')),
        //     'end'=>date('Y-m-d', strtotime('-60 year')),
        //     'count_person'=>0
        //     ];
            
            $range_age[] = [
            'title'=>'ต่ำกว่า 20',
            'start'=>0,
            'end'=>20,
            'count_person'=>0,
            'data'=>[]
        ];
        $range_age[] = [
            'title'=>'21 - 30',
            'start'=>21,
            'end'=>30,
            'count_person'=>0,
            'data'=>[]
            ];
        $range_age[] = [
            'title'=>'31 - 40',
            'start'=>31,
            'end'=>40,
            'count_person'=>0,
            'data'=>[]
            ];
        $range_age[] = [
            'title'=>'41 - 50',
            'start'=>41,
            'end'=>50,
            'count_person'=>0,
            'data'=>[]
            ];
        $range_age[] = [
            'title'=>'51 - 60',
            'start'=>51,
            'end'=>60,
            'count_person'=>0,
            'data'=>[]
            ];
            
        $models = Person::find()
        ->select(['*','timestampdiff(YEAR,birthday,NOW()) as age'])
        ->orderBy(['birthday'=>SORT_DESC])
        ->all();
        
        $index = 0;
        $count_person =0;
        $rangeOld = $range_age[$index];
        $data = [];
        foreach($models as $model){
            //echo strtotime($rangeOld['end']).' '.strtotime($model->birthday).'<br/>';
            //echo $rangeOld['end'] .'>='. $model->age."<br/>";
            
            // if(!($model->age >= $rangeOld['start'] && $model->age <= $rangeOld['end'] )){
            //     echo $rangeOld['start'] .'<='. $model->age." - ";
            //     echo $rangeOld['end'] .'>='. $model->age."<br/>";
            //     //echo $model->birthday.'<br/>';
                
            //     $data = [];
            //     $index++;
            //     $count_person=0;
            //     if((count($range_age)-1)>$index){
            //         $rangeOld = $range_age[$index];
            //     }
            //     //$rangeOld = (count($range_age)-1)>$index?$range_age[$index]:$range_age[$index-1];
            // }elseif($model->age >= $rangeOld['start'] && $model->age <= $rangeOld['end'] ){
            
            
                foreach($range_age as $key => $range){
                    if($model->age >= $range['start'] && $model->age <= $range['end'] ){
                        //$count_person+=1;
                        $range_age[$key]['count_person'] += 1;
                        $range_age[$key]['data'][] = ['id'=>$model->user_id,'age'=>$model->age];
                    }
                }
            
        }
        
        // echo "<pre>";
        // print_r($range_age);
        // exit();
        $dataProvider = new ArrayDataProvider([
            'allModels'=>$range_age,
            'pagination'=>false,
            ]);
        
        
        return $this->render('range-age',[
            'dataProvider'=>$dataProvider,
            'models'=>$range_age,
            ]);
    }
    
    
    
}
