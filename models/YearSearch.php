<?php

namespace andahrm\report\models;

use Yii;
use yii\helpers\ArrayHelper;

class YearSearch extends \yii\base\Model
{
    
  public $year;
  public $start;
  public $end;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['year','start','end'], 'integer']
        ];
    }
}
