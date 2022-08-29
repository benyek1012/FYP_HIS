<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Batch;

/**
 * BatchSearch represents the model behind the search form of `app\models\Batch`.
 */
class BatchSearch extends Batch
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['upload_datetime', 'scheduled_datetime', 'executed_datetime', 'file_import', 'lookup_type', 'update_type', 'error', 
                'approval1_responsible_uid', 'approval2_responsible_uid', 'execute_responsible_uid'], 'safe'],
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
        $query = Batch::find()->orderBy(['upload_datetime' => SORT_DESC]);

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
            'id' => $this->id,
            // 'batch' => $this->batch,
        ]);

        $query->andFilterWhere(['like', 'file_import', $this->file_import])
        ->andFilterWhere(['like', 'upload_datetime', $this->upload_datetime])
        ->andFilterWhere(['like', 'approval1_responsible_uid', $this->approval1_responsible_uid])
        ->andFilterWhere(['like', 'approval2_responsible_uid', $this->approval2_responsible_uid])
        ->andFilterWhere(['like', 'lookup_type', $this->lookup_type])
        ->andFilterWhere(['like', 'error', $this->error])
        ->andFilterWhere(['like', 'scheduled_datetime', $this->scheduled_datetime])
        ->andFilterWhere(['like', 'executed_datetime', $this->executed_datetime])
        ->andFilterWhere(['like', 'execute_responsible_uid', $this->execute_responsible_uid])
        ->andFilterWhere(['like', 'update_type', $this->update_type]);

        return $dataProvider;
    }
}
