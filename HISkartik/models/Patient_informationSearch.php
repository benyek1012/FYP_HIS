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
    public function rules()
    {
        return [
            [['patient_uid','first_reg_date', 'nric', 'name', 'nationality', 'sex', 'job', 'race', 'address1', 'address2', 'address3'], 'safe'],
            [['phone_number'], 'integer'],
            [['email'], 'email'],
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
        $query = Patient_information::find();

        // add conditions that should always apply here

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

        $query->andFilterWhere(['like', 'first_reg_date', $this->first_reg_date])
            ->andFilterWhere(['like', 'patient_uid', $this->patient_uid])
            ->andFilterWhere(['like', 'nric', $this->nric])
            ->andFilterWhere(['like', 'nationality', $this->nationality])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'sex', $this->sex])
            ->andFilterWhere(['like', 'race', $this->race])
            ->andFilterWhere(['like', 'phone_number', $this->phone_number])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'address1', $this->address1])
            ->andFilterWhere(['like', 'address2', $this->address2])
            ->andFilterWhere(['like', 'address3', $this->address3])
            ->andFilterWhere(['like', 'job', $this->job]);
        return $dataProvider;
    }
}
