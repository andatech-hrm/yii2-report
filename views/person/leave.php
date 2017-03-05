<?php
use yii\grid\GridView;

use yii\helpers\ArrayHelper;



?>



<?php
$models['leaveType'] = ArrayHelper::getColumn($models['leaveType'], function ($element) {
            return [
                'label'=>$element->title,
                'value'=>function($model)use($element){
                    //$type='type'.$model->leave_type_id;
                    return $element->id;
                },
            ];
         });
$user_id[] = 
            [
            'label'=>Yii::t('andahrm/person','Fullname'),
            'value'=>'createdBy.fullname'
            ];
$columns = ArrayHelper::merge($user_id,$models['leaveType']);




echo GridView::widget([
    'dataProvider'=>$models['provider'],
    'columns'=>$columns
    ]);

?>