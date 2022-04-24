<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Lookup_status;

/**
 * Lookup_statusSearch represents the model behind the search form of `app\models\Lookup_status`.
 */
class Lookup_statusSearch extends Lookup_status
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_uid', 'status_code', 'status_description'], 'safe'],
            [['class_1a_ward_cost', 'class_1b_ward_cost', 'class_1c_ward_cost', 'class_2_ward_cost', 'class_3_ward_cost'], 'number'],
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
        $query = Lookup_status::find();

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
            'class_1a_ward_cost' => $this->class_1a_ward_cost,
            'class_1b_ward_cost' => $this->class_1b_ward_cost,
            'class_1c_ward_cost' => $this->class_1c_ward_cost,
            'class_2_ward_cost' => $this->class_2_ward_cost,
            'class_3_ward_cost' => $this->class_3_ward_cost,
        ]);

        $query->andFilterWhere(['like', 'status_uid', $this->status_uid])
            ->andFilterWhere(['like', 'status_code', $this->status_code])
            ->andFilterWhere(['like', 'status_description', $this->status_description]);

        return $dataProvider;
    }
}
