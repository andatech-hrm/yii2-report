<?php
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use andahrm\structure\models\FiscalYear;

$this->title =  Yii::t('andahrm/report', 'Person Type');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Report'), 'url' => ['/report/default']];
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Person'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
$data = [];
$selected = false;
foreach ($model as $type) {
    //$arr = ['name' => $type->title, 'y' => count($type->positions)];
    $arr = ['name' => $type->title, 'y' => $type->count*1];
    $data[] = $arr;
}

// usort($data,function($a,$b){
//     $c = $b['y'] - $a['y'];
//     // $c .= $b['availability'] - $a['availability'];
//     // $c .= strcmp($a['nick_name'],$b['nick_name']);
//     return $c;
// });
// echo "<pre>";
// print_r($data);
// exit();

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
echo Highcharts::widget([
    'options' => [
        'chart' => [
            'type' => 'pie',
            'plotBackgroundColor' => null,
            'plotBorderWidth' => null,
            'plotShadow' => false,
        ],
        'title' => ['text' => 'แบ่งตามประเภทบุคลากร'],
        'tooltip' => [
            'pointFormat' => '{series.name}: <b>{point.y}</b>'
        ],
        'plotOptions' => [
            'pie' => [
                'allowPointSelect' => true,
                'cursor' => 'pointer',
                'dataLabels' => [
                    'enabled' => true,
                    'format' => '<b>{point.name}</b>: {point.percentage:.1f} %',
                ],
                'showInLegend' => true
            ]
        ],
        'series' => [
            [
                'name' => 'Brands',
                'colorByPoint' => true,
                'data' => $data
            ]
        ]
    ]
]);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@bower/highcharts');
$this->registerJsFile($directoryAsset.'/modules/exporting.js', ['depends' => ['\miloschuman\highcharts\HighchartsAsset']]);
?>
  
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
                //'label'=>Yii::t('andahrm/report','Government service'),
                'attribute'=>'title',
                //'value'=>'titleLevel',
            ],
            [
                
                'label'=>Yii::t('andahrm/report','Person Amount'),
                'format'=>'html',
                 'content'=>function($model){
                     
                     $count = $model->count?$model->count:0;
                     $where['person_type_id'] = $model->id;
                     if($get = Yii::$app->request->queryParams){
                        $where['year'] = $get['YearSearch']['year'];
                     }
                     //$where['person_type_id2'] = $model->id;
                     
                     return Html::a($count,['/report/person','PersonSearch'=>$where]);
                 },
                 'value'=>function($model){
                      return $model->count?$model->count:0;
                 },
                 'pageSummary'=>true,
            ],
        ],
    ]); ?>
