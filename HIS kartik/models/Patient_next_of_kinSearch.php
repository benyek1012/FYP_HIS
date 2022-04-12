<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Patient_next_of_kin;

/**
 * Patient_next_of_kinSearch represents the model behind the search form of `app\models\Patient_next_of_kin`.
 */
class Patient_next_of_kinSearch extends Patient_next_of_kin
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nok_uid', 'patient_uid', 'nok_name', 'nok_relationship', 'nok_phone_number', 'nok_email'], 'safe'],
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
        $query = Patient_next_of_kin::find();

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
        $query->andFilterWhere(['like', 'nok_uid', $this->nok_uid])
            ->andFilterWhere(['like', 'patient_uid', $this->patient_uid])
            ->andFilterWhere(['like', 'nok_name', $this->nok_name])
            ->andFilterWhere(['like', 'nok_relationship', $this->nok_relationship])
            ->andFilterWhere(['like', 'nok_phone_number', $this->nok_phone_number])
            ->andFilterWhere(['like', 'nok_email', $this->nok_email]);

        return $dataProvider;
    }
}
