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
    public $ward_code;
    public $medical_legal_code;
    public $discharge_date;

    public function rules()
    {
        return [
            // [['patient_uid','first_reg_date', 'nric', 'name', 'nationality', 'sex', 'job', 'race', 'address1', 'address2', 'address3'], 'safe'],
            // [['phone_number'], 'integer'],
            // [['email'], 'email'],

            [['name','nric','race','sex','rn', 'entry_datetime', 'patient_uid', 'initial_ward_code', 'initial_ward_class', 'reference',
                 'guarantor_name', 'guarantor_nric', 'guarantor_phone_number', 'guarantor_email','type', 'ward_code', 'discharge_date'], 'safe'],
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

    public function search_name($params)
    {
        $this->load($params);
        $datetime = Patient_admission::find()
        ->select('MAX(entry_datetime)')
        ->from("patient_admission")
        ->groupBy('patient_uid');

        $query = Patient_admission::find()
        ->select('patient_admission.*')
        ->from('patient_admission')
        ->joinWith('patient_information',true)
        ->where(['in','entry_datetime',$datetime])
        // ->andWhere(['name' => $this->name])
        ->groupBy(['patient_uid']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        if(empty($this->name)){

            $query->where(['name' => NULL]);
        }
        else{
            $query->andFilterWhere(['like', 'name', $this->name]);
        }
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
      
        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search_date($params)
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
            'pagination' => [
                'pageSize' => 30,
            ],
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

    public function search_ward($params)    
    {
        $this->load($params);
        $ward_code_list = Ward::find()
        ->select('bill_uid')
        ->from("ward")
        ->where(['ward_code' => $this->ward_code]);

        $rn_list = Bill::find()
        ->select('rn')
        ->from("bill")
        ->where(['deleted' => 0])
        ->andWhere( ['IS NOT', 'bill_generation_datetime', null])
        ->andWhere(['in','bill_uid',$ward_code_list]);

        $query = Patient_admission::find()
        ->select('patient_admission.*')
        ->from('patient_admission')
        ->joinWith('patient_information',true)
        ->where(['in','rn',$rn_list])
        ->groupBy(['patient_uid']);
        //->orderBy(['entry_datetime' => SORT_DESC]);

    //    var_dump($query->all());
    //    exit;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 30,
            ],
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

    public function search_discharge($params)
    {
        $this->load($params);
        $rn_list = Bill::find()
        ->select('rn')
        ->from("bill")
        ->where(['CAST(discharge_date as DATE)' => $this->discharge_date])
        ->andWhere(['deleted' => 0])
        ->andWhere( ['IS NOT', 'bill_generation_datetime', null]);

        // echo '<pre>';
        // var_dump($rn_list);
        // echo '</pre>';
        // exit;

        // echo "<pre>";
        // var_dump($datetime->all());
        // exit();
        // echo "</pre>";
   

        $query = Patient_admission::find()
        ->select('patient_admission.*')
        ->from('patient_admission')
        ->joinWith('patient_information',true)
        ->where(['in','rn',$rn_list])
        ->groupBy(['patient_uid']);
        //->orderBy(['entry_datetime' => SORT_DESC]);

    //    var_dump($query->all());
    //    exit;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 30,
            ],
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
