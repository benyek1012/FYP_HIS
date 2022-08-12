<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use app\models\Receipt;
use yii\data\Sort;

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
            [['receipt_uid','kod_akaun', 'rn', 'receipt_type', 'receipt_content_bill_id', 'receipt_content_description', 'receipt_content_datetime_paid', 'receipt_content_payer_name', 'receipt_content_payment_method', 'card_no', 'cheque_number', 'receipt_responsible', 'receipt_serial_number'], 'safe'],
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
        // $query = Receipt::find()->where(['rn' => Yii::$app->request->get('rn')])
        //                         ->orderBy(['receipt_content_datetime_paid' => SORT_DESC, 'rn' => SORT_DESC]);
        
        $query = (new Bill()) -> getProcedureBillReceipt(Yii::$app->request->get('rn'));

        // echo '<pre>';
        // var_dump($query);
        // exit();
        // echo '</pre>';
       
        // add conditions that should always apply here

        $sort = new Sort([
            'defaultOrder' => ['receipt_content_datetime_paid'=>SORT_DESC],
            'attributes' => [
                'receipt_content_datetime_paid',
                //'bill_generation_datetime',
                // or any other attribute
            ],
        ]);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'sort' => $sort,
            //'pagination'=>['pageSize'=>5],
        ]);

        // $this->load($params);

        // if (!$this->validate()) {
        //     // uncomment the following line if you do not want to return any records when validation fails
        //     // $query->where('0=1');
        //     return $dataProvider;
        // }

        // // grid filtering conditions
        // $query->andFilterWhere([
        //     'receipt_content_sum' => $this->receipt_content_sum,
        //     'receipt_content_datetime_paid' => $this->receipt_content_datetime_paid,
        // ]);

        // $query->andFilterWhere(['like', 'receipt_uid', $this->receipt_uid])
        //     ->andFilterWhere(['like', 'rn', $this->rn])
        //     ->andFilterWhere(['like', 'receipt_type', $this->receipt_type])
        //     ->andFilterWhere(['like', 'receipt_content_bill_id', $this->receipt_content_bill_id])
        //     ->andFilterWhere(['like', 'receipt_content_description', $this->receipt_content_description])
        //     ->andFilterWhere(['like', 'receipt_content_payer_name', $this->receipt_content_payer_name])
        //     ->andFilterWhere(['like', 'receipt_content_payment_method', $this->receipt_content_payment_method])
        //     ->andFilterWhere(['like', 'card_no', $this->card_no])
        //     ->andFilterWhere(['like', 'cheque_number', $this->cheque_number])
        //     ->andFilterWhere(['like', 'receipt_responsible', $this->receipt_responsible])
        //     ->andFilterWhere(['like', 'receipt_serial_number', $this->receipt_serial_number]);

        return $dataProvider;
    }

        /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function transactionRecords($params)
    {
        // This is showing all RN from payment 
        $model_adm = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
        

        if(empty($model_adm))
           $model_adm = Patient_admission::findOne(['patient_uid' => Yii::$app->request->get('id')]);
      
        // $rn_array = array();
        // foreach($model_rn as $model)
        // {
        //     $rn_array[] = $model->rn;
        // }

        // echo '<pre>';
        // var_dump(implode(",", $rn_array));
        // exit();
        // echo '</pre>';

        // $rn_array = (implode(",", $rn_array));

        $query = (new Bill()) -> getProcedureTransactions($model_adm->patient_uid);

        // echo '<pre>';
        // var_dump($query);
        // exit();
        // echo '</pre>';

        $sort = new Sort([
            'defaultOrder' => ['receipt_content_datetime_paid'=>SORT_DESC],
            'attributes' => [
                'receipt_content_datetime_paid',
                //'bill_generation_datetime',
                // or any other attribute
            ],
        ]);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'sort' => $sort,
            'pagination'=>['pageSize'=>5],
        ]);

         // add conditions that should always apply here

        // $dataProvider = new ActiveDataProvider([
        //     'query' => $query,
        //     'pagination'=>['pageSize'=>10],
        // ]);

        // $this->load($params);

        // if (!$this->validate()) {
        //     // uncomment the following line if you do not want to return any records when validation fails
        //     // $query->where('0=1');
        //     return $dataProvider;
        // }

        // // grid filtering conditions
        // $query->andFilterWhere([
        //     'receipt_content_sum' => $this->receipt_content_sum,
        //     'receipt_content_datetime_paid' => $this->receipt_content_datetime_paid,
        // ]);

        // $query->andFilterWhere(['like', 'receipt_uid', $this->receipt_uid])
        //     ->andFilterWhere(['like', 'rn', $this->rn])
        //     ->andFilterWhere(['like', 'receipt_type', $this->receipt_type])
        //     ->andFilterWhere(['like', 'receipt_content_bill_id', $this->receipt_content_bill_id])
        //     ->andFilterWhere(['like', 'receipt_content_description', $this->receipt_content_description])
        //     ->andFilterWhere(['like', 'receipt_content_payer_name', $this->receipt_content_payer_name])
        //     ->andFilterWhere(['like', 'receipt_content_payment_method', $this->receipt_content_payment_method])
        //     ->andFilterWhere(['like', 'card_no', $this->card_no])
        //     ->andFilterWhere(['like', 'cheque_number', $this->cheque_number])
        //     ->andFilterWhere(['like', 'receipt_responsible', $this->receipt_responsible])
        //     ->andFilterWhere(['like', 'receipt_serial_number', $this->receipt_serial_number]);

        return $dataProvider;
    }
}
