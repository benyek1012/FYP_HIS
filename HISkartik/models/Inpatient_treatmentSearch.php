<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Inpatient_treatment;

/**
 * Inpatient_treatmentSearch represents the model behind the search form of `app\models\Inpatient_treatment`.
 */
class Inpatient_treatmentSearch extends Inpatient_treatment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inpatient_treatment_uid', 'bill_uid'], 'safe'],
            [['inpatient_treatment_cost_rm'], 'number'],
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
        $query = Inpatient_treatment::find();

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
        $query->andFilterWhere([
            'inpatient_treatment_cost_rm' => $this->inpatient_treatment_cost_rm,
        ]);

        $query->andFilterWhere(['like', 'inpatient_treatment_uid', $this->inpatient_treatment_uid])
            ->andFilterWhere(['like', 'bill_uid', $this->bill_uid]);

        return $dataProvider;
    }
}
