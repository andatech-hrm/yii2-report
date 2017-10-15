<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use andahrm\structure\models\FiscalYear;
use andahrm\report\models\Position;
/* @var $this yii\web\View */
/* @var $searchModel andahrm\edoc\models\EdocSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('andahrm/report', 'Capacity');
$this->params['breadcrumbs'][] = $this->title;

$new_sort = FiscalYear::getList();
ksort($new_sort);
$position = new Position;
?>
<div class="edoc-index">
<?=Html::tag('h2',
'บัญชีจัดตำแหน่งข้าราชการองค์การบริหารส่วนจังหวัดเข้าสู่ประเภทตำแหน่ง สายงานและระดับตำแหน่ง'
,['align'=>'center']
)?>
<?=Html::tag('h2',
'แนบท้ายคำสั่งองค์การบริหารส่วนจังหวัดยะลา'
,['align'=>'center']
)?>
<?php
    $form = ActiveForm::begin([
        'action' => [$this->context->action->id],
        'method' => 'get',
        //'options' => ['data-pjax' => true ],
        //'layout' => 'horizontal',
        'fieldConfig' => [
            //'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            // 'horizontalCssClasses' => [
            //     'label' => 'col-sm-4',
            //     'offset' => 'col-sm-offset-4',
            //     'wrapper' => 'col-sm-8',
            //     'error' => '',
            //     'hint' => '',
            // ],
        ],
    ]);
    ?>
    
     <?php
    ActiveForm::end();
$js[] = <<< JS
    var form = {$form->id};
    $('.selct-year').change(function(){
        var start = $("#yearsearch-start.selct-year option:selected").val();
        var end = $("#yearsearch-end.selct-year option:selected").val();
        //$("#yearsearch-end.selct-year").val(start);
        if(start && end){
            $(form).submit();
        }
        //alert($(this).find('option:selected').val());
    });

JS;
$this->registerJs(implode('\n',$js));    
?>
    
<?php

$columns[] = [
    'class' => 'kartik\grid\SerialColumn',
    'headerOptions' => ['style' => 'display: none;',],
];
$columns[] = [
                'attribute'=>'section_id',
                'value'=>'section.title',
                'group'=>true,  // enable grouping,
                'groupedRow'=>true,                    // move grouped column to a single grouped row
                'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
                'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
                'pageSummary' => Yii::t('andahrm', 'Total'),
            ];
$columns[] = [
                'attribute'=>'personPositionSalary.user.fullname',
                'value'=>function($model){
                    $model = $model->personPositionSalary;
                    //$model = $model?$model->user->fullname.' '.$model->user->user_id:'ว่าง';
                    $model = $model?$model->user->fullname:'ว่าง';
                    return $model;
                },
                'contentOptions'=> function($model){
                    $model = $model->personPositionSalary;
                    return $model?['nowrap'=>'nowrap']:['align'=>'center'];
                },
                'headerOptions' => ['style' => 'display: none;',],
               
            ];
$columns[] = [
                'attribute'=>'personPositionSalary.user.education.degree',
                // 'value'=>function($model){
                //     $value = $model->personPositionSalary->user->education->degree;
                //     return $value?$value:'ว่าง';
                //     },
                'headerOptions' => ['style' => 'display: none;',],
               
            ];
$columns[] = [
                'label'=>$position->getAttributeLabel('personPositionSalary.position_id'),
                'value'=>'code',
                //'headerOptions' => ['style' => 'display: none;',],
               
            ];
$columns[] = [
                'attribute'=>'title',
                //'value'=>'code',
                //'headerOptions' => ['style' => 'display: none;',],
               
            ];
$columns[] = [
                'attribute'=>'position_type_id',
                'value'=>'positionType.title',
                //'group'=>true,  // enable grouping,
                //'groupedRow'=>true,                    // move grouped column to a single grouped row
                //'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
                //'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
                //'pageSummary' => Yii::t('andahrm', 'Total'),
                //'headerOptions' => ['style' => 'display: none;',],
            ];

            
$columns[] = [
                
                'attribute'=>'position_level_id',
                'value'=>function($model){
                    //return $model->person_type_id.' '.$model->section_id;
                    return $model->position_level_id?$model->positionLevel->title:'-';
                },
                //'headerOptions' => ['style' => 'display: none;',],
            ];
            
            
            
$columns[] = [
                'attribute'=>'personPositionSalary.salary',
                'pageSummary'=>true,
                'format'=>['decimal', 0],
                'contentOptions'=>['align'=>'right'],
                'value'=>'sumSalary',
                // 'value'=>function($model){
                //     $salary = $model->sumSalary;
                //     return $salary?$salary:0;
                // }
            ];
            
$columns[] = [
                'label'=>'เงินประจำตำแหน่ง',
                //'content'=>'?'
            ];
$columns[] = [
                'label'=>'เงินค่าตอบแทนอื่น',
                //'content'=>'?'
            ];
$columns[] = [
                'label'=>'เงินเพิ่มอื่นๆ',
                //'content'=>'?'
            ];
             
            
  $columns[] = [
                'attribute'=>'note',
                'format'=>['decimal', 0],
                'value'=>'sumSalary',
                'headerOptions' => ['style' => 'display: none;',],
            ];   
            
            
$beforeColumns[] = ['content'=>'#', 'options'=>['rowspan'=>2,'class'=>'text-center info']];
$beforeColumns[] = ['content'=>$position->getAttributeLabel('personPositionSalary.user.fullname'), 'options'=>['rowspan'=>2, 'class'=>'text-center info']];
$beforeColumns[] = ['content'=>$position->getAttributeLabel('personPositionSalary.user.education.degree'), 'options'=>['rowspan'=>2 ,'class'=>'text-center info']];
//$beforeColumns[] = ['content'=>$position->getAttributeLabel('positionLine.title'), 'options'=>['rowspan'=>2, 'class'=>'text-center info']];
//$beforeColumns[] = ['content'=>$position->getAttributeLabel('position_level_id'), 'options'=>['rowspan'=>2, 'class'=>'text-center info']];
$beforeColumns[] = ['content'=>Yii::t('andahrm/report','Rate New'), 'options'=>['colspan'=>4, 'class'=>'text-center info']];
$beforeColumns[] = ['content'=>Yii::t('andahrm/report','Salary'), 'options'=>['colspan'=>4, 'class'=>'text-center info']];
// if($colMerge){
//     $beforeColumns[] = ['content'=>Yii::t('andahrm/report','New Rate'), 'options'=>['colspan'=>$colMerge, 'class'=>'text-center info']];
//     $beforeColumns[] = ['content'=>Yii::t('andahrm/report','Up Down'), 'options'=>['colspan'=>$colMerge, 'class'=>'text-center info']];
// }
$beforeColumns[] = ['content'=>Yii::t('andahrm/structure', 'Note'), 'options'=>[ 'rowspan'=>2,'class'=>'text-center info']];


?>    
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'showPageSummary' => true,
        'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => '-'],
        'beforeHeader'=>[
        [
            'columns'=>$beforeColumns,
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
        'columns' => $columns
    ]); ?>
<?php //Pjax::end(); ?>
</div>
