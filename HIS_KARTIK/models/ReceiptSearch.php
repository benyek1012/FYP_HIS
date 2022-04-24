<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Receipt;

/**
 * ReceiptSearch represents the model behind the search form of `app\models\Receipt`.
 */
class ReceiptSearch extends Receipt
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receipt_uid', 'rn', 'receipt_type', 'receipt_content_bill_id', 'receipt_content_description', 'receipt_content_datetime_paid', 'receipt_content_payer_name', 'receipt_content_payment_method', 'card_no', 'cheque_number', 'receipt_responsible', 'receipt_serial_number'], 'safe'],
            [['receipt_content_sum'], 'number'],
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
        $query = Receipt::find();

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
            'receipt_content_sum' => $this->receipt_content_sum,
            'receipt_content_datetime_paid' => $this->receipt_content_datetime_paid,
        ]);

        $query->andFilterWhere(['like', 'receipt_uid', $this->receipt_uid])
            ->andFilterWhere(['like', 'rn', $this->rn])
            ->andFilterWhere(['like', 'receipt_type', $this->receipt_type])
            ->andFilterWhere(['like', 'receipt_content_bill_id', $this->receipt_content_bill_id])
            ->andFilterWhere(['like', 'receipt_content_description', $this->receipt_content_description])
            ->andFilterWhere(['like', 'receipt_content_payer_name', $this->receipt_content_payer_name])
            ->andFilterWhere(['like', 'receipt_content_payment_method', $this->receipt_content_payment_method])
            ->andFilterWhere(['like', 'card_no', $this->card_no])
            ->andFilterWhere(['like', 'cheque_number', $this->cheque_number])
            ->andFilterWhere(['like', 'receipt_responsible', $this->receipt_responsible])
            ->andFilterWhere(['like', 'receipt_serial_number', $this->receipt_serial_number]);

        return $dataProvider;
    }
}
