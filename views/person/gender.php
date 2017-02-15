<?php
use yii\helpers\ArrayHelper;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use andahrm\structure\models\FiscalYear;
use andahrm\report\models\PersonType;


$this->title =  Yii::t('andahrm/report', 'Gender');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Report'), 'url' => ['/report/index']];
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Person'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php  echo $this->render('_search', ['model' => $model]); ?>


<table class="table table-striped" id='datatable'>
    <thead>
        <tr>
            <th>ประเภท</th>
            
            <?php foreach($modelGender as $v):?>
            <th><?=$v['title']?></th>
            <?php endforeach;?>
            <th>รวม</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $totalCol=[];
       
        foreach($models as $type) : 
        $totalRow=0;
        ?>
        <tr>
            <th><?= $type->title; ?></th>
            <?php foreach($modelGender as $k=> $v):
                $totalRow += $type->gender[$k]['count'];
                $totalCol[$k] += $type->gender[$k]['count'];
            ?>
            <td><?=$type->gender[$k]['count']?></td>
            <?php endforeach;?>
            <th><?=$totalRow?></th>
        </tr>
        <?php endforeach; ?>
    </tbody>
    
    <tfooter>
        <tr>
            <td>
                <?=Yii::t('andahrm','Total')?>
            </td>
            <?php foreach($modelGender as $k=> $v):?>
            <td><?=$totalCol[$k]?></td>
            <?php endforeach;?>
            <td><?=array_sum($totalCol)?></td>
            
        </tr>
    </tfooter>
</table>


<?php
$data = [];

echo Highcharts::widget([
    'options' => [
        'chart' => [
            'type' => 'column',
        ],
        'data' => [
            'table' => 'datatable'
        ],
        'title' => ['text' => 'อัตตราส่วนตำแหน่งแบ่งตามประเภ'],
        'yAxis' => [
            'allowDecimals' => false,
            'title' => [
                'text' => 'Units'
            ]
        ],
        'tooltip' => [
            'formatter' => new JsExpression("function () {
                return '<b>' + this.series.name + '</b><br/>' +
                    this.point.y + ' ' + this.point.name.toLowerCase();
            }")
        ],
        'plotOptions' => [
            'series' => [
                'connectNulls' => false
            ]
        ]
    ]
]);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@bower/highcharts');
$this->registerJsFile($directoryAsset.'/modules/data.js', ['depends' => ['\miloschuman\highcharts\HighchartsAsset']]);
$this->registerJsFile($directoryAsset.'/modules/exporting.js', ['depends' => ['\miloschuman\highcharts\HighchartsAsset']]);

?>