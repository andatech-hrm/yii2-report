<?php
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;

$this->title =  Yii::t('andahrm/report', 'Person Level');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Report'), 'url' => ['/report/default']];
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Person'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$arr =  [];
foreach($models['person-level'] as $key => $level) {
    
    $data[] = ['name' => $level->title, 'y' => intval($models['person-position-salary'][$level->id]['levelPersonCount'])];

}

// usort($data,function($a,$b){
//     $c = $b['y'] - $a['y'];
//     return $c;
// });

echo Highcharts::widget([
    'options' => [
        'chart' => [
            'type' => 'pie',
            'plotBackgroundColor' => null,
            'plotBorderWidth' => null,
            'plotShadow' => false,
        ],
        'title' => ['text' => Yii::t('andahrm/report', 'Level')],
        'tooltip' => [
            'pointFormat' => '{series.name}: <b>{point.y:f}</b> คน'
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
                'name' => 'จำนวน',
                'colorByPoint' => true,
                'data' => $data
            ]
        ]
    ]
]);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@bower/highcharts');
$this->registerJsFile($directoryAsset.'/modules/exporting.js', ['depends' => ['\miloschuman\highcharts\HighchartsAsset']]);
?>
<hr>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ประเภท</th>
            <th>จำนวน</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalMale = 0;
        $totalFemale = 0;
        $totalSum = 0;
        ?>
        <?php foreach ($data as $v) : ?>
        <tr>
            <th><?=$v['name']?></th>
            <td><?=$v['y']?> <?php $totalMale += $v['y']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfooter>
        <tr style="font-size: 1.2em;" class="text-danger">
            <th class="text-right">รวม</th>
            <td><?=$totalMale?></td>
        </tr>
    </tfooter>
</table>