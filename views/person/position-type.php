<?php
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use andahrm\structure\models\FiscalYear;

$this->title =  Yii::t('andahrm/report', 'Position Type');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Report'), 'url' => ['/report/default']];
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Person'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
$data = [];
$selected = false;

foreach ($models as $key => $degree) {
    //$arr = ['name' => $type->title, 'y' => count($type->positions)];
    $arr = ['name' => $degree->title.' ('.$degree->count_person.')', 'y' => $degree->count_person*1];
    $data[] = $arr;
}



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
        'title' => ['text' => 'ร้อยละของบุคลากรแบ่งตามประเภทตำแหน่ง'],
        'tooltip' => [
            'pointFormat' => '{series.name}: <b>{point.y}</b>'
        ],
        'plotOptions' => [
            'pie' => [
                'allowPointSelect' => true,
                'cursor' => 'pointer',
                'dataLabels' => [
                    'enabled' => true,
                    'format' => '<b>{point.name}</b>: {point.percentage:.2f} %',
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


<?=GridView::widget([
    'dataProvider'=>$dataProvider,
    'showPageSummary' => true,
    'columns'=>[
        ['class' => 'kartik\grid\SerialColumn'],
        [
            'attribute'=>'person_type_id',
            'value'=>'personType.title',
            'value'=>function($model){
                $strCheck = 'อื่นๆ';
                return $model->person_type_id!=0?$model->personType->title:$strCheck;
            },
            'group'=>true,
        ],
        [
            'attribute'=>'title',
            'value'=>function($model){
                $strCheck = 'อัตราเงินเดือน';
                return $model->title!=$strCheck?$model->title:$model->personType->title;
            },
            'pageSummary'=>Yii::t('andahrm','Total'),
            
        ],
        [
            //'attribute'=>''
            'attribute'=>'count_person',
            //'contentOptions'=>['class'=>'text-right'],
            'content'=>function($model){
                     $where['position_type_id'] = $model->id;
                     return Html::a($model->count_person,['/report/person','PersonSearch'=>$where]);
                 },
            'pageSummary'=>true,
            //'pageSummaryOptions'=>['class'=>'text-right'],
        ]
        ]
    ]);
    
    ?>