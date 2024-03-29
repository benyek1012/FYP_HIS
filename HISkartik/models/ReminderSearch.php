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
            [['batch_date', 'reminder1count', 'reminder2count', 'reminder3count', 'responsible_uid'], 'safe'],
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
            'sort'=> ['defaultOrder' => ['batch_date' => SORT_DESC,]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'batch_date' => $this->batch_date,
            'reminder1count' => $this->reminder1count,
            'reminder2count' => $this->reminder2count,
            'reminder3count' => $this->reminder3count,
        ]);

        $query->andFilterWhere(['like', 'batch_date', $this->batch_date])
            ->andFilterWhere(['like', 'responsible_uid', $this->responsible_uid]);

        return $dataProvider;
    }
}
