<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use andahrm\structure\models\FiscalYear;
/* @var $this yii\web\View */
/* @var $searchModel andahrm\edoc\models\EdocSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('andahrm/report', 'Capacity');
$this->params['breadcrumbs'][] = $this->title;

$new_sort = FiscalYear::getList();
ksort($new_sort);
?>
<div class="edoc-index">

<?php
    $form = ActiveForm::begin([
        'action' => [$this->context->action->id],
        'method' => 'get',
       // 'options' => ['data-pjax' => true ],
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
    <div class="row">
    <div class="col-sm-6">
        <?=$form->field($models['year-search'], 'start')->dropDownList($new_sort, [
        'prompt' => '--ทั้งหมด--',
        'class'=>'form-control selct-year',
        //'onchange'=>'this.form.submit()'
    ])->label('เริ่มปี');?>
    </div>
    <div class="col-sm-6">
    <?=$form->field($models['year-search'], 'end')->dropDownList(FiscalYear::getList(), [
        'prompt' => '--ทั้งหมด--',
        'class'=>'form-control selct-year',
        //'onchange'=>'this.form.submit()'
    ])->label('สิ้นสุดปี');
    ?>
     </div> 
     </div>
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
                'label'=>Yii::t('andahrm/report','Government service'),
                'attribute'=>'title',
                'value'=>'titleLevel',
                'headerOptions' => ['style' => 'display: none;',],
            ];
            
            $columns[] = [
                'attribute'=>'count_year',
                'pageSummary'=>true,
                 'value' => function($model){
                    return $model->count_year;
                },
                'content' => function($model){
                    $where['section_id'] = $model->section_id;
                    $where['position_level_id'] = $model->position_level_id;
                    $where['position_line_id'] = $model->position_line_id;
                    return Html::a($model->count_year,['/report/position','PositionSearch'=>$where]);
                },
               'headerOptions' => ['style' => 'display: none;',],
            ];
            
            $colMerge = 0;
            if($models['year-search']->start && $models['year-search']->end){
                foreach(range($models['year-search']->start,$models['year-search']->end) as $year){
                    $columns[] = [
                        'label'=>$year+543,
                        'pageSummary'=>true,
                        'value' => function($model) use($year){
                            return $model->getNewRate($year);
                        },
                        'content' => function($model) use($year){
                            $where['section_id'] = $model->section_id;
                            $where['position_level_id'] = $model->position_level_id;
                            $where['position_line_id'] = $model->position_line_id;
                            $where['year'] = $year;
                            return Html::a($model->getNewRate($year),['/report/position','PositionSearch'=>$where]);
                        }
                    ];
                    ++$colMerge;
                }
            }
            
            if($models['year-search']->start && $models['year-search']->end){
                foreach(range($models['year-search']->start,$models['year-search']->end) as $year){
                    $columns[] = [
                        'label'=>$year+543,
                        'pageSummary'=>true,
                        'value' => function($model) use($year,$models){
                            $oldYear = $models['year-search']->start;
                            return $model->getUpDown($oldYear,$year);
                        },
                        'content' => function($model) use($year,$models){
                            $where['section_id'] = $model->section_id;
                            $where['position_level_id'] = $model->position_level_id;
                            $where['position_line_id'] = $model->position_line_id;
                            //$where['year'] = date('Y');
                            if($get = Yii::$app->request->queryParams){
                               // $where['year'] = $get['YearSearch']['year'];
                            }
                            $oldYear = $models['year-search']->start;
                            return Html::a($model->getUpDown($oldYear,$year),['/report/position','PositionSearch'=>$where]);
                        }
                    ];
                }
            }
            
  $columns[] = [
                'label'=>Yii::t('andahrm/structure', 'Note'),
                'attribute'=>'note',
                'headerOptions' => ['style' => 'display: none;',],
            ];   
            
            
$beforeColumns[] = ['content'=>'#', 'options'=>['rowspan'=>2, 'class'=>'text-center warning']];
$beforeColumns[] = ['content'=>Yii::t('andahrm/report','Government service'), 'options'=>['rowspan'=>2, 'class'=>'text-center warning']];
$beforeColumns[] = ['content'=>Yii::t('andahrm/report', 'Rate Old'), 'options'=>['rowspan'=>2, 'class'=>'text-center warning']];
if($colMerge){
    $beforeColumns[] = ['content'=>Yii::t('andahrm/report','Rate New'), 'options'=>['colspan'=>$colMerge, 'class'=>'text-center warning']];
    $beforeColumns[] = ['content'=>Yii::t('andahrm/report','Up Down'), 'options'=>['colspan'=>$colMerge, 'class'=>'text-center warning']];
}
$beforeColumns[] = ['content'=>Yii::t('andahrm/structure', 'Note'), 'options'=>[ 'rowspan'=>2,'class'=>'text-center warning']];


?>    
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'showPageSummary' => true,
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
