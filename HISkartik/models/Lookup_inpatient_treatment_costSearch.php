<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Lookup_inpatient_treatment_cost;

/**
 * Lookup_inpatient_treatment_costSearch represents the model behind the search form of `app\models\Lookup_inpatient_treatment_cost`.
 */
class Lookup_inpatient_treatment_costSearch extends Lookup_inpatient_treatment_cost
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inpatient_treatment_uid', 'kod'], 'safe'],
            [['cost_rm'], 'number'],
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
        $query = Lookup_inpatient_treatment_cost::find();

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
            'cost_rm' => $this->cost_rm,
        ]);

        $query->andFilterWhere(['like', 'inpatient_treatment_uid', $this->inpatient_treatment_uid])
            ->andFilterWhere(['like', 'kod', $this->kod]);

        return $dataProvider;
    }
}
