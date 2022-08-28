<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Reminder;

/**
 * ReminderSearch represents the model behind the search form of `app\models\Reminder`.
 */
class ReminderSearch extends Reminder
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['batch_uid', 'batch_datetime', 'reminder1', 'reminder2', 'reminder3', 'responsible'], 'safe'],
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
        $query = Reminder::find();

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
            'batch_datetime' => $this->batch_datetime,
            'reminder1' => $this->reminder1,
            'reminder2' => $this->reminder2,
            'reminder3' => $this->reminder3,
        ]);

        $query->andFilterWhere(['like', 'batch_uid', $this->batch_uid])
            ->andFilterWhere(['like', 'responsible', $this->responsible]);

        return $dataProvider;
    }
}