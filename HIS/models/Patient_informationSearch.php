<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Patient_information;

/**
 * Patient_informationSearch represents the model behind the search form of `app\models\Patient_information`.
 */
class Patient_informationSearch extends Patient_information
{
    /**
     * {@inheritdoc}
     */
    public $globalSearch;

    public function rules()
    {
        return [
            [['patient_uid','globalSearch', 'first_reg_date', 'nric', 'nationality', 'name', 'sex', 'phone_number', 'email', 'address1', 'address2', 'address3', 'job'], 'safe'],
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
        $query = Patient_information::find();

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

        $query->orFilterWhere(['like', 'patient_uid', $this->globalSearch])
            ->orWhere(['nric' => $this->globalSearch]);
            // ->orFilterWhere(['like', 'nationality', $this->globalSearch])
            // ->orFilterWhere(['like', 'name', $this->globalSearch])
            // ->orFilterWhere(['like', 'sex', $this->globalSearch])
            // ->orFilterWhere(['like', 'phone_number', $this->globalSearch])
            // ->orFilterWhere(['like', 'email', $this->globalSearch])
            // ->orFilterWhere(['like', 'address1', $this->globalSearch])
            // ->orFilterWhere(['like', 'address2', $this->globalSearch])
            // ->orFilterWhere(['like', 'address3', $this->globalSearch])
            // ->orFilterWhere(['like', 'job', $this->globalSearch]);

        return $dataProvider;
    }
}
