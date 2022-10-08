<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Cancellation;

/**
 * CancellationSearch represents the model behind the search form of `app\models\Cancellation`.
 */
class CancellationSearch extends Cancellation
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cancellation_uid', 'table', 'reason', 'replacement_uid'], 'safe'],
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
        $query = Cancellation::find();

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
        $query->andFilterWhere(['like', 'cancellation_uid', $this->cancellation_uid])
            ->andFilterWhere(['like', 'table', $this->table])
            ->andFilterWhere(['like', 'reason', $this->reason])
            ->andFilterWhere(['like', 'replacement_uid', $this->replacement_uid]);

        return $dataProvider;
    }
}
