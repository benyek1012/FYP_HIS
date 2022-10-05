<?php

namespace app\models;
use Yii;


class Report extends \yii\db\ActiveRecord{

    public $date_report;

    /**
    * {@inheritdoc}
    */
    public function rules()
    {
        return [  
            [['date_report'], 'safe'],
        ];
    }
  
}