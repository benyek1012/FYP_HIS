<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Lookup_fpp;

/**
 * Lookup_fppSearch represents the model behind the search form of `app\models\Lookup_fpp`.
 */
class Lookup_fppSearch extends Lookup_fpp
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kod', 'name'], 'safe'],
            [['min_cost_per_unit', 'max_cost_per_unit'], 'number'],
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
        $query = Lookup_fpp::find();

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
        ]);

        $query->andFilterWhere(['like', 'kod', $this->kod])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
