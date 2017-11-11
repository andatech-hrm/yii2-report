<?php
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use andahrm\structure\models\FiscalYear;

$this->title =  Yii::t('andahrm/report', 'Degree');
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Report'), 'url' => ['/report/default']];
$this->params['breadcrumbs'][] = ['label' =>  Yii::t('andahrm/report', 'Person'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

ข้อมูลประเภทตำแหน่งอันนี้รึป่าว
<?php

Html::a('ประเภทตำแหน่ง',['/structure/position-type']);
    
    ?>