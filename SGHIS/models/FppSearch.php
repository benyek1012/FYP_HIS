<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Fpp;

/**
 * FppSearch represents the model behind the search form of `app\models\Fpp`.
 */
class FppSearch extends Fpp
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kod', 'name', 'additional_details'], 'safe'],
            [['min_cost_per_unit', 'max_cost_per_unit', 'total_cost'], 'number'],
            [['number_of_units'], 'integer'],
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
        $query = Fpp::find();

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
            'min_cost_per_unit' => $this->min_cost_per_unit,
            'max_cost_per_unit' => $this->max_cost_per_unit,
            'number_of_units' => $this->number_of_units,
            'total_cost' => $this->total_cost,
        ]);

        $query->andFilterWhere(['like', 'kod', $this->kod])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'additional_details', $this->additional_details]);

        return $dataProvider;
    }
}
