<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Treatment_details;

/**
 * Treatment_detailsSearch represents the model behind the search form of `app\models\Treatment_details`.
 */
class Treatment_detailsSearch extends Treatment_details
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['treatment_details_uid', 'bill_uid', 'treatment_code', 'treatment_name'], 'safe'],
            [['item_per_unit_cost_rm', 'item_total_unit_cost_rm'], 'number'],
            [['item_count'], 'integer'],
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
        $query = Treatment_details::find();

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
            'item_per_unit_cost_rm' => $this->item_per_unit_cost_rm,
            'item_count' => $this->item_count,
            'item_total_unit_cost_rm' => $this->item_total_unit_cost_rm,
        ]);

        $query->andFilterWhere(['like', 'treatment_details_uid', $this->treatment_details_uid])
            ->andFilterWhere(['like', 'bill_uid', $this->bill_uid])
            ->andFilterWhere(['like', 'treatment_code', $this->treatment_code])
            ->andFilterWhere(['like', 'treatment_name', $this->treatment_name]);

        return $dataProvider;
    }
}
