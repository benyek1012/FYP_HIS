<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Bill;

/**
 * BillSearch represents the model behind the search form of `app\models\Bill`.
 */
class BillSearch extends Bill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_uid', 'rn', 'status_code', 'status_description', 'class', 'department_code', 'department_name', 'collection_center_code', 'nurse_responsible', 'bill_generation_datetime', 'generation_responsible_uid', 'description', 'bill_print_responsible_uid', 'bill_print_datetime', 'bill_print_id'], 'safe'],
            [['daily_ward_cost', 'bill_generation_billable_sum_rm', 'bill_generation_final_fee_rm'], 'number'],
            [['is_free'], 'integer'],
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
        $query = Bill::find();

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
            'daily_ward_cost' => $this->daily_ward_cost,
            'is_free' => $this->is_free,
            'bill_generation_datetime' => $this->bill_generation_datetime,
            'bill_generation_billable_sum_rm' => $this->bill_generation_billable_sum_rm,
            'bill_generation_final_fee_rm' => $this->bill_generation_final_fee_rm,
            'bill_print_datetime' => $this->bill_print_datetime,
        ]);

        $query->andFilterWhere(['like', 'bill_uid', $this->bill_uid])
            ->andFilterWhere(['like', 'rn', $this->rn])
            ->andFilterWhere(['like', 'status_code', $this->status_code])
            ->andFilterWhere(['like', 'status_description', $this->status_description])
            ->andFilterWhere(['like', 'class', $this->class])
            ->andFilterWhere(['like', 'department_code', $this->department_code])
            ->andFilterWhere(['like', 'department_name', $this->department_name])
            ->andFilterWhere(['like', 'collection_center_code', $this->collection_center_code])
            ->andFilterWhere(['like', 'nurse_responsible', $this->nurse_responsible])
            ->andFilterWhere(['like', 'generation_responsible_uid', $this->generation_responsible_uid])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'bill_print_responsible_uid', $this->bill_print_responsible_uid])
            ->andFilterWhere(['like', 'bill_print_id', $this->bill_print_id]);

        return $dataProvider;
    }
}
