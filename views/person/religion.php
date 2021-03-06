<?php
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use andahrm\structure\models\FiscalYear;

$this->title =  Yii::t('andahrm/report', 'Religion');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Report'), 'url' => ['/report/default']];
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Person'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
$data = [];
$selected = false;
foreach ($modelReligion as $religion) {
    //$arr = ['name' => $type->title, 'y' => count($type->positions)];
    $arr = ['name' => $religion->title, 'y' => $religion->count_person];
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
        'title' => ['text' => 'ร้อยละของบุคลากรแบ่งตามศาสนา'],
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


<?=GridView::widget([
    'dataProvider'=>$dataProvider,
    'showPageSummary' => true,
    'columns'=>[
        ['class' => 'kartik\grid\SerialColumn'],
        [
            'attribute'=>'title',
            'pageSummary'=>Yii::t('andahrm','Total'),
            
        ],
        [
            'attribute'=>'count_person',
            'label'=>Yii::t('andahrm', 'Count Person'),
            'content'=>function($model){
                     $where['religion_id'] = $model->id;
                     return Html::a($model->count_person,['/report/person','PersonSearch'=>$where]);
                 },
            'pageSummary'=>true,
        ]
        ]
    ]);
    
    ?>