<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Lookup_treatment;

/**
 * Lookup_treatmentSearch represents the model behind the search form of `app\models\Lookup_treatment`.
 */
class Lookup_treatmentSearch extends Lookup_treatment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['treatment_uid', 'treatment_code', 'treatment_name'], 'safe'],
            [['class_1_cost_per_unit', 'class_2_cost_per_unit', 'class_3_cost_per_unit', 'class_Daycare_FPP_per_unit'], 'number'],
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
        $query = Lookup_treatment::find();

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
            'class_1_cost_per_unit' => $this->class_1_cost_per_unit,
            'class_2_cost_per_unit' => $this->class_2_cost_per_unit,
            'class_3_cost_per_unit' => $this->class_3_cost_per_unit,
            'class_Daycare_FPP_per_unit' => $this->class_Daycare_FPP_per_unit,
        ]);

        $query->andFilterWhere(['like', 'treatment_uid', $this->treatment_uid])
            ->andFilterWhere(['like', 'treatment_code', $this->treatment_code])
            ->andFilterWhere(['like', 'treatment_name', $this->treatment_name]);

        return $dataProvider;
    }
}
