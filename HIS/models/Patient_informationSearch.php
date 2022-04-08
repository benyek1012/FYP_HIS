<?php

namespace app\models;

use yii\base\Model;
use Yii;
use app\models\Patient_information;

/**
 * Patient_next_of_kinSearch represents the model behind the search form of `app\models\Patient_next_of_kin`.
 */
class Patient_informationSearch extends Patient_information
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nric'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

   
    public function search($params)
    {
         $query = Patient_information::find()->where(['nric' => $params]);
        // var_dump($query->prepare(Yii::$app->db->queryBuilder)->createCommand()->rawSql);
     
        if(!empty($query))
        {
            return true;
        }
        else{
            // var_dump("f");
            // exit();
            return false;
        }
    } 

    
}