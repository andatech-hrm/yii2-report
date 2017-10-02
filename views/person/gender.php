<?php
use yii\helpers\ArrayHelper;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;
use andahrm\structure\models\FiscalYear;
use kartik\grid\GridView;
use yii\helpers\Html;
?>
<?php
foreach ($model as $key => $personType) :
    //$gender = $models['person-position-salary'][$key];
    $arr[] = [
        'title' => $personType->title,
        'male' => $personType->genderMaleCount?intval($personType->genderMaleCount):0,
        'female' => $personType->genderFemaleCount?intval($personType->genderFemaleCount):0,
        'sum' => $personType->genderMaleCount + $personType->genderFemaleCount,
    ];
endforeach;
?>

<?php
$form = ActiveForm::begin([
    'action' => [$this->context->action->id],
    'method' => 'get',
    'options' => ['data-pjax' => true ],
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-4',
            'offset' => 'col-sm-offset-4',
            'wrapper' => 'col-sm-8',
            'error' => '',
            'hint' => '',
        ],
    ],
]);
echo $form->field($models['year-search'], 'year')->dropDownList(FiscalYear::getList(), [
    'prompt' => '--ทั้งหมด--',
    'onchange'=>'this.form.submit()'
])->label('ปีงบประมาณ');
ActiveForm::end();
?>

<?php
// print_r(ArrayHelper::getColumn($arr, 'title'));

echo Highcharts::widget([
    'options' => [
        'chart' => [
            'type' => 'column',
        ],
        'title' => ['text' => 'ประเภทบุคลากร '.($models['year-search']->year+543)],
        'credits' => [
            'enabled' => false
        ],
        'xAxis' => [
            'categories' => ArrayHelper::getColumn($arr, 'title'),
            'crosshair' => true
        ],
        'yAxis' => [
            'min' => 0,
            'title' => [
                'text' => 'คน'
            ]
        ],
        'tooltip' => [
            'headerFormat' => '<span style="font-size:10px">{point.key}</span><table>',
            'pointFormat' => '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
            'footerFormat' => '</table>',
            'shared' => true,
            'useHTML' => true
        ],
        'plotOptions' => [
            'column' => [
                'pointPadding' => 0.2,
                'borderWidth' => 0
            ]
        ],
        'series' => [
            ['name' => Yii::t('andahrm/report', 'Male'), 'data' => ArrayHelper::getColumn($arr, 'male'), 'color' => '#368BC1'],
            ['name' => Yii::t('andahrm/report', 'Female'), 'data' => ArrayHelper::getColumn($arr, 'female'), 'color' => '#F660AB'],
        ]
    ]
]);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@bower/highcharts');
$this->registerJsFile($directoryAsset.'/modules/exporting.js', ['depends' => ['\miloschuman\highcharts\HighchartsAsset']]);

?>

   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'showPageSummary' => true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute'=>'parent_id',
                'value'=>'parent.title',
                'group'=>true,
                'pageSummary' => Yii::t('andahrm', 'Total'),
            ],
            [
                //'label'=>Yii::t('andatech/report','Government service'),
                'attribute'=>'title',
                //'value'=>'titleLevel',
            ],
            [
                'attribute'=>'genderMaleCount',
                'format'=>'html',
                'content'=>function($model){
                     $count = $model->genderMaleCount?$model->genderMaleCount:0;
                     $where['person_type_id'] = $model->id;
                     $where['gender'] = 'm';
                     $where['year'] = date('Y');
                     if($get = Yii::$app->request->queryParams){
                        $where['year'] = $get['YearSearch']['year'];
                     }
                     //$where['person_type_id2'] = $model->id;
                     
                     return Html::a($count,['/report/person','PersonSearch'=>$where]);
                 },
                 'value'=>function($model){
                      return $model->genderMaleCount?$model->genderMaleCount:0;
                 },
                 'pageSummary'=>true,
            ],
            [
                'attribute'=>'genderFemaleCount',
                'format'=>'html',
                'content' => function($model){
                    $count = $model->genderFemaleCount?$model->genderFemaleCount:0;
                     $where['person_type_id'] = $model->id;
                     $where['gender'] = 'f';
                     $where['year'] = date('Y');
                     if($get = Yii::$app->request->queryParams){
                        $where['year'] = $get['YearSearch']['year'];
                     }
                     //$where['person_type_id2'] = $model->id;
                     
                     return Html::a($count,['/report/person','PersonSearch'=>$where]);
                   
                },
                'value'=>function($model){
                      return $model->genderFemaleCount?$model->genderFemaleCount:0;
                 },
                 'pageSummary'=>true,
            ],
            [
                'attribute'=>'sum',
                'value'=>function($model){
                     $count = $model->genderMaleCount?$model->genderMaleCount:0;
                     $count1 = $model->genderFemaleCount?$model->genderFemaleCount:0;
                     return $count+$count1;
                 },
                'pageSummary'=>true,
            ]
        ],
    ]); ?>
