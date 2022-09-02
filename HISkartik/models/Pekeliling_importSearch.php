<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pekeliling_import;

/**
 * Pekeliling_importSearch represents the model behind the search form of `app\models\Pekeliling_import`.
 */
class Pekeliling_importSearch extends Pekeliling_import
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pekeliling_uid', 'upload_datetime', 'approval1_responsible_uid', 'approval2_responsible_uid',
             'file_import', 'lookup_type', 'error', 'scheduled_datetime', 'executed_datetime',
              'execute_responsible_uid', 'update_type'], 'safe'],
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
        $query = Pekeliling_import::find()->orderBy(['upload_datetime' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>['pageSize'=>10],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'upload_datetime' => $this->upload_datetime,
            'scheduled_datetime' => $this->scheduled_datetime,
            'executed_datetime' => $this->executed_datetime,
        ]);

        $query->andFilterWhere(['like', 'pekeliling_uid', $this->pekeliling_uid])
            ->andFilterWhere(['like', 'approval1_responsible_uid', $this->approval1_responsible_uid])
            ->andFilterWhere(['like', 'approval2_responsible_uid', $this->approval2_responsible_uid])
            ->andFilterWhere(['like', 'file_import', $this->file_import])
            ->andFilterWhere(['like', 'lookup_type', $this->lookup_type])
            ->andFilterWhere(['like', 'error', $this->error])
            ->andFilterWhere(['like', 'execute_responsible_uid', $this->execute_responsible_uid])
            ->andFilterWhere(['like', 'update_type', $this->update_type]);

        return $dataProvider;
    }
}
