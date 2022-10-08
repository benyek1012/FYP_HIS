<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Lookup_general;

/**
 * Lookup_generalSearch represents the model behind the search form of `app\models\Lookup_general`.
 */
class Lookup_generalSearch extends Lookup_general
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lookup_general_uid', 'code', 'category', 'name', 'long_description'], 'safe'],
            [['recommend'], 'integer'],
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
        $query = Lookup_general::find();

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
            'recommend' => $this->recommend,
        ]);

        $query->andFilterWhere(['like', 'lookup_general_uid', $this->lookup_general_uid])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'category', $this->category])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'long_description', $this->long_description]);

        return $dataProvider;
    }
}
