<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Patient_admission;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Types\Null_;

/**
 * Patient_admissionSearch represents the model behind the search form of `app\models\Patient_admission`.
 */
class Patient_admissionSearch extends Patient_admission
{
    /**
     * {@inheritdoc}
     */
    public $name;
    public $nric;
    public $race, $sex;
    public function rules()
    {
        return [
            [['name','nric','race','sex','rn', 'entry_datetime', 'patient_uid', 'initial_ward_code', 'initial_ward_class', 'reference','type'], 'safe'],
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
}
