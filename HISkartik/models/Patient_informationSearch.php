<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Patient_information;

/**
 * Patient_informationSearch represents the model behind the search form of `app\models\Patient_information`.
 */
class Patient_informationSearch extends Patient_information
{
    /**
     * {@inheritdoc}
     */
    public $entry_datetime;
    public $medical_legal_code;

    public function rules()
    {
        return [
            // [['patient_uid','first_reg_date', 'nric', 'name', 'nationality', 'sex', 'job', 'race', 'address1', 'address2', 'address3'], 'safe'],
            // [['phone_number'], 'integer'],
            // [['email'], 'email'],

            [['name','nric','race','sex','rn', 'entry_datetime', 'patient_uid', 'initial_ward_code', 'initial_ward_class', 'reference', 'guarantor_name', 'guarantor_nric', 'guarantor_phone_number', 'guarantor_email','type'], 'safe'],
            [['medical_legal_code' ], 'integer'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
        $datetime = Patient_admission::find()
        ->select('MAX(entry_datetime)')
        ->from("patient_admission")
        ->where(['CAST(entry_datetime as DATE)' => $this->entry_datetime])
        ->groupBy('patient_uid');

        // echo "<pre>";
        // var_dump($datetime->all());
        // exit();
        // echo "</pre>";
   

        $query = Patient_admission::find()
        ->select('patient_admission.*')
        ->from('patient_admission')
        ->joinWith('patient_information',true)
        //->where(['in','entry_datetime',$datetime])
        ->where(['in','entry_datetime',$datetime])
        ->groupBy(['patient_uid']);
        //->orderBy(['entry_datetime' => SORT_DESC]);

    //    var_dump($query->all());
    //    exit;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // if(empty($this->entry_datetime)){

        //     $query->where(['entry_datetime' => NULL]);
        // }
        // else{
        //     $query->andFilterWhere(['like', 'entry_datetime', $this->entry_datetime]);
        // }
        // if (!$this->validate()) {
        //     return $dataProvider;
        // }
 
        return $dataProvider;
    }
}
