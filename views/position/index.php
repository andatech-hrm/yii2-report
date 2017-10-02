<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use andahrm\structure\models\FiscalYear;
/* @var $this yii\web\View */
/* @var $searchModel andahrm\edoc\models\EdocSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('andahrm/report', 'Position Report');
$this->params['breadcrumbs'][] = $this->title;
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
        <?=$form->field($models['year-search'], 'start')->dropDownList(FiscalYear::getList(), [
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

$columns[] = ['class' => 'yii\grid\SerialColumn'];
$columns[] = [
                'attribute'=>'section_id',
                'value'=>'section.title',
                'group'=>true,  // enable grouping,
                'groupedRow'=>true,                    // move grouped column to a single grouped row
                'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
                'groupEvenCssClass'=>'kv-grouped-row', // configure even group cell css class
            ];
$columns[] = [
                'label'=>Yii::t('andahrm/report','Government service'),
                'attribute'=>'title',
                'value'=>'titleLevel',
            ];
$columns[] = [
                
                'attribute'=>'count_year',
            ];
            
foreach(range($models['year-search']->start,$models['year-search']->end) as $year){
    $columns[] = [
        'label'=>$year,
        'content' => function($model) use($year){
            return $model->getRateDate($year);
        }
    ];
}



?>    
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => $columns
    ]); ?>
<?php //Pjax::end(); ?>
</div>
