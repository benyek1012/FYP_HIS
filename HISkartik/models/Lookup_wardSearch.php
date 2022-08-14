<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Lookup_ward;

/**
 * Lookup_wardSearch represents the model behind the search form of `app\models\Lookup_ward`.
 */
class Lookup_wardSearch extends Lookup_ward
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ward_uid', 'ward_code', 'ward_name', 'sex'], 'safe'],
            [['min_age', 'max_age', 'batch'], 'integer'],
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
        $query = Lookup_ward::find();

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
            'min_age' => $this->min_age,
            'max_age' => $this->max_age,
        ]);

        $query->andFilterWhere(['like', 'ward_uid', $this->ward_uid])
            ->andFilterWhere(['like', 'ward_code', $this->ward_code])
            ->andFilterWhere(['like', 'ward_name', $this->ward_name])
            ->andFilterWhere(['like', 'sex', $this->sex])
            ->andFilterWhere(['like', 'batch', $this->batch]);

        return $dataProvider;
    }
}
