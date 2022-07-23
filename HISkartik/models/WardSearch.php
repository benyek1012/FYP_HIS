<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Ward;

/**
 * WardSearch represents the model behind the search form of `app\models\Ward`.
 */
class WardSearch extends Ward
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ward_uid', 'bill_uid', 'ward_code', 'ward_name', 'ward_start_datetime', 'ward_end_datetime', 'ward_number_of_days'], 'safe'],
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
        $query = Ward::find();

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
            'ward_start_datetime' => $this->ward_start_datetime,
            'ward_end_datetime' => $this->ward_end_datetime,
            'ward_number_of_days' => $this->ward_number_of_days,
        ]);

        $query->andFilterWhere(['like', 'ward_uid', $this->ward_uid])
            ->andFilterWhere(['like', 'bill_uid', $this->bill_uid])
            ->andFilterWhere(['like', 'ward_code', $this->ward_code])
            ->andFilterWhere(['like', 'ward_name', $this->ward_name]);

        return $dataProvider;
    }
}
