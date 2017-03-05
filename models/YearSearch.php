<?php

namespace andahrm\report\models;

use Yii;
use yii\helpers\ArrayHelper;

class YearSearch extends \yii\base\Model
{
    
  public $year;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year'], 'integer']
        ];
    }
}
