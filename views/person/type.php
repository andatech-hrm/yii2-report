<?php
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use kartik\grid\GridView;
use yii\helpers\Html;

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


echo Highcharts::widget([
    'options' => [
        'chart' => [
            'type' => 'pie',
            'plotBackgroundColor' => null,
            'plotBorderWidth' => null,
            'plotShadow' => false,
        ],
        'title' => ['text' => 'อัตตราส่วนตำแหน่งแบ่งตามประเภท'],
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
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // //'id',
            // 'code',
            // 'date_code:date',
            // //'testDate',
            // [
            //     'attribute'=>'section_id',
            //     'value'=>'section.title',
            //     'group'=>true,  // enable grouping,
            //     'groupedRow'=>true,                    // move grouped column to a single grouped row
            //     'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
            //     'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
            // ],
            [
                'attribute'=>'parent',
                'value'=>'parent.title',
                'group'=>true,
            ],
            [
                //'label'=>Yii::t('andatech/report','Government service'),
                'attribute'=>'title',
                //'value'=>'titleLevel',
            ],
            [
                
                'label'=>Yii::t('andatech/report','Position count'),
                'format'=>'html',
                 'value'=>function($model){
                     $count = $model->count?$model->count:0;
                     $where['person_type_id'] = $model->id;
                     $where['person_type_id2'] = $model->id;
                     
                     return Html::a($count,['/report/person','PersonSearch'=>$where]);
                 },
            ],
        ],
    ]); ?>
