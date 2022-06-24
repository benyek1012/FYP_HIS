<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Patient_admission;

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
            [['name','nric','race','sex','rn', 'entry_datetime', 'patient_uid', 'initial_ward_code', 'initial_ward_class', 'reference', 'guarantor_name', 'guarantor_nric', 'guarantor_phone_number', 'guarantor_email','type'], 'safe'],
            [['medical_legal_code', 'reminder_given'], 'integer'],
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
        
        // $query = Patient_admission::find()->innerJoinWith('patient_information', true);

        // add conditions that should always apply here

        $query = Patient_admission::find()
        ->select('patient_information.*, MAX(rn) as rn, MAX(entry_datetime) as entry_datetime')
        ->from('patient_admission')
        ->joinWith('patient_information',true)
        ->groupBy(['patient_uid']);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'entry_datetime' => $this->entry_datetime,
            'medical_legal_code' => $this->medical_legal_code,
            'reminder_given' => $this->reminder_given,
        ]);

        $query->andFilterWhere(['like', 'rn', $this->rn])
            ->andFilterWhere(['like', 'patient_uid', $this->patient_uid])
            ->andFilterWhere(['like', 'initial_ward_code', $this->initial_ward_code])
            ->andFilterWhere(['like', 'initial_ward_class', $this->initial_ward_class])
            ->andFilterWhere(['like', 'reference', $this->reference])
            ->andFilterWhere(['like', 'guarantor_name', $this->guarantor_name])
            ->andFilterWhere(['like', 'guarantor_nric', $this->guarantor_nric])
            ->andFilterWhere(['like', 'guarantor_phone_number', $this->guarantor_phone_number])
            ->andFilterWhere(['like', 'guarantor_email', $this->guarantor_email])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'nric', $this->nric])
            ->andFilterWhere(['like', 'sex', $this->sex])
            ->andFilterWhere(['like', 'race', $this->race]);
        return $dataProvider;
    }
}
