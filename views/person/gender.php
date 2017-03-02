<?php
use yii\helpers\ArrayHelper;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;
?>
<?php
foreach ($models['person-type'] as $key => $personType) :
    $gender = $models['person-position-salary'][$key];
    $arr[] = [
        'title' => $personType->title,
        'male' => $gender->genderMaleCount?intval($gender->genderMaleCount):0,
        'female' => $gender->genderFemaleCount?intval($gender->genderFemaleCount):0,
        'sum' => $gender->genderMaleCount + $gender->genderFemaleCount,
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
echo $form->field($models['year-search'], 'year')->dropDownList(ArrayHelper::map($models['year-list'], 'year', 'year'), [
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
        'title' => ['text' => 'ประเภทบุคลากร '.$models['year-search']->year],
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
            ['name' => 'ชาย', 'data' => ArrayHelper::getColumn($arr, 'male'), 'color' => '#368BC1'],
            ['name' => 'หญิง', 'data' => ArrayHelper::getColumn($arr, 'female'), 'color' => '#F660AB'],
        ]
    ]
]);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@bower/highcharts');
$this->registerJsFile($directoryAsset.'/modules/exporting.js', ['depends' => ['\miloschuman\highcharts\HighchartsAsset']]);

?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ประเภท</th>
            <th>ชาย</th>
            <th>หญิง</th>
            <th>รวม</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalMale = 0;
        $totalFemale = 0;
        $totalSum = 0;
        ?>
        <?php foreach ($arr as $v) : ?>
        <tr>
            <th><?=$v['title']?></th>
            <td><?=$v['male']?> <?php $totalMale += $v['male']; ?></td>
            <td><?=$v['female']?> <?php $totalFemale += $v['female']; ?></td>
            <td class="text-warning"><?=$v['sum']?> <?php $totalSum += $v['sum']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfooter>
        <tr style="font-size: 1.2em;" class="text-danger">
            <th class="text-right">รวม</th>
            <td><?=$totalMale?></td>
            <td><?=$totalFemale?></td>
            <td><?=$totalSum?></td>
        </tr>
    </tfooter>
</table>
