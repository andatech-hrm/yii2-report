<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use andahrm\structure\models\FiscalYear;
/* @var $this yii\web\View */
/* @var $model andahrm\leave\models\LeaveSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="leave-search">

    <?php $form = ActiveForm::begin([
        //'action' => ['index'],
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
    ]); ?>
  
    <div class="row">
      <div class="col-sm-4 col-sm-offset-8">
        <?= $form->field($model, 'year')->dropDownList(FiscalYear::getList(),['onchange'=>'this.form.submit()']) ?>
      </div>
      
      <!--<div class="col-sm-3">-->
      <!--  <div class="form-group">-->
      <!--    <?= Html::submitButton(Yii::t('andahrm', 'Search'), ['class' => 'btn btn-primary']) ?>-->
      <!--  </div>-->
      <!--</div>-->
    
    </div>

    <?php ActiveForm::end(); ?>

</div>
