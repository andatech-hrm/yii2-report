<?php
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;

$this->title =  Yii::t('andahrm/report', 'Person Type');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Report'), 'url' => ['/report/default']];
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Person'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
$data = [];
$selected = false;
foreach ($model as $type) {
    $arr = ['name' => $type->title, 'y' => count($type->positions)];
    $data[] = $arr;
}

usort($data,function($a,$b){
    $c = $b['y'] - $a['y'];
    // $c .= $b['availability'] - $a['availability'];
    // $c .= strcmp($a['nick_name'],$b['nick_name']);
    return $c;
});



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
            'pointFormat' => '{series.name}: <b>{point.y:.1f}</b>'
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