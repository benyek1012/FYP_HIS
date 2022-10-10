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
        // $query = Patient_information::find();

        // // add conditions that should always apply here

        // $dataProvider = new ActiveDataProvider([
        //     'query' => $query,
        // ]);

        // $this->load($params);

        // if (!$this->validate()) {
        //     // uncomment the following line if you do not want to return any records when validation fails
        //     // $query->where('0=1');
        //     return $dataProvider;
        // }

        // // grid filtering conditions

        // $query->andFilterWhere(['like', 'first_reg_date', $this->first_reg_date])
        //     ->andFilterWhere(['like', 'patient_uid', $this->patient_uid])
        //     ->andFilterWhere(['like', 'nric', $this->nric])
        //     ->andFilterWhere(['like', 'nationality', $this->nationality])
        //     ->andFilterWhere(['like', 'name', $this->name])
        //     ->andFilterWhere(['like', 'sex', $this->sex])
        //     ->andFilterWhere(['like', 'race', $this->race])
        //     ->andFilterWhere(['like', 'phone_number', $this->phone_number])
        //     ->andFilterWhere(['like', 'email', $this->email])
        //     ->andFilterWhere(['like', 'address1', $this->address1])
        //     ->andFilterWhere(['like', 'address2', $this->address2])
        //     ->andFilterWhere(['like', 'address3', $this->address3])
        //     ->andFilterWhere(['like', 'job', $this->job]);
        // return $dataProvider;

        $this->load($params);
        
        $datetime = Patient_admission::find()
        ->select('MAX(entry_datetime)')
        ->from("patient_admission")
        ->groupBy('patient_uid');

        $query = Patient_admission::find()
        ->select('patient_admission.*')
        ->from('patient_admission')
        ->joinWith('patient_information',true)
        ->where(['like', 'entry_datetime', $this->entry_datetime])
        // ->andWhere(['name' => $this->name])
        ->groupBy(['patient_uid']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // if(empty($this->entry_datetime)){

        //     $query->where(['entry_datetime' => NULL]);
        // }
        // else{
        //     $query->andFilterWhere(['like', 'entry_datetime', $this->entry_datetime]);
        // }
        if (!$this->validate()) {
            return $dataProvider;
        }
 
        return $dataProvider;
    }
}
