<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Bill;
use yii\helpers\ArrayHelper;


/**
 * BillSearch represents the model behind the search form of `app\models\Bill`.
 */
class BillSearch extends Bill
{
    /**
     * {@inheritdoc}
     */
    public $date_from;
    public $date_to;
    public $race, $nationality;
    public $report_type;
    public $max;
    public function rules()
    {
        return [
            [['bill_uid', 'max','rn','race','nationality', 'report_type','status_code', 'status_description', 'class', 'department_code', 'department_name', 'collection_center_code', 'nurse_responsible', 'bill_generation_datetime', 'generation_responsible_uid', 'description', 'bill_print_responsible_uid', 'bill_print_datetime', 'bill_print_id'], 'safe'],
            [['daily_ward_cost', 'bill_generation_billable_sum_rm', 'bill_generation_final_fee_rm'], 'number'],
            [['is_free'], 'integer'],
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

        $query = Bill::find();

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
            'daily_ward_cost' => $this->daily_ward_cost,
            'is_free' => $this->is_free,
            'bill_generation_datetime' => $this->bill_generation_datetime,
            'bill_generation_billable_sum_rm' => $this->bill_generation_billable_sum_rm,
            'bill_generation_final_fee_rm' => $this->bill_generation_final_fee_rm,
            'bill_print_datetime' => $this->bill_print_datetime,
        ]);

        $query->andFilterWhere(['like', 'bill_uid', $this->bill_uid])
            ->andFilterWhere(['like', 'rn', $this->rn])
            ->andFilterWhere(['like', 'status_code', $this->status_code])
            ->andFilterWhere(['like', 'status_description', $this->status_description])
            ->andFilterWhere(['like', 'class', $this->class])
            ->andFilterWhere(['like', 'department_code', $this->department_code])
            ->andFilterWhere(['like', 'department_name', $this->department_name])
            ->andFilterWhere(['like', 'collection_center_code', $this->collection_center_code])
            ->andFilterWhere(['like', 'nurse_responsible', $this->nurse_responsible])
            ->andFilterWhere(['like', 'generation_responsible_uid', $this->generation_responsible_uid])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'bill_print_responsible_uid', $this->bill_print_responsible_uid])
            ->andFilterWhere(['like', 'bill_print_id', $this->bill_print_id])
            ->andFilterWhere(['between', 'DATE_FORMAT(bill_generation_datetime, "%Y-%m-%d")', $this->date_from, $this->date_to]);

        return $dataProvider;
    }

    public static function getReportData($params)
    {
        $bill = new BillSearch();
        $bill->load($params);
        // grid filtering conditions
        if($bill->report_type == 'monthly'){
            $query = Bill::find()
            ->select('bill.*, EXTRACT(YEAR_MONTH FROM bill_generation_datetime) as bill_generation_datetime,sum(bill_generation_billable_sum_rm) as bill_generation_billable_sum_rm')
            ->from('bill')
            ->joinWith('patient_information',true)
            ->groupBy('EXTRACT(YEAR_MONTH FROM bill_generation_datetime)')
            ->andFilterWhere(['like', 'bill_uid', $bill->bill_uid])
            ->andFilterWhere(['like', 'rn', $bill->rn])
            ->andFilterWhere(['like', 'status_code', $bill->status_code])
            ->andFilterWhere(['like', 'status_description', $bill->status_description])
            ->andFilterWhere(['like', 'class', $bill->class])
            ->andFilterWhere(['like', 'department_code', $bill->department_code])
            ->andFilterWhere(['like', 'department_name', $bill->department_name])
            ->andFilterWhere(['like', 'collection_center_code', $bill->collection_center_code])
            ->andFilterWhere(['like', 'nurse_responsible', $bill->nurse_responsible])
            ->andFilterWhere(['like', 'generation_responsible_uid', $bill->generation_responsible_uid])
            ->andFilterWhere(['like', 'description', $bill->description])
            ->andFilterWhere(['like', 'bill_print_responsible_uid', $bill->bill_print_responsible_uid])
            ->andFilterWhere(['like', 'bill_print_id', $bill->bill_print_id])
            //->andFilterWhere(['between', 'DATE_FORMAT(bill_generation_datetime, "%Y-%m-%d")', $bill->date_from, $this->date_to])
            ->andFilterWhere(['like', 'race', $bill->race])
            ->andFilterWhere(['like', 'nationality', $bill->nationality])
            ->all();
        } 
        else if($bill->report_type == 'weekly'){
            $query = Bill::find()
            ->select('bill.*, YEARWEEK(bill_generation_datetime) as bill_generation_datetime ,sum(bill_generation_billable_sum_rm) as bill_generation_billable_sum_rm')
            ->from('bill')
            ->joinWith('patient_information',true)
            ->groupBy('YEARWEEK(bill_generation_datetime)')
            ->andFilterWhere(['like', 'bill_uid', $bill->bill_uid])
            ->andFilterWhere(['like', 'rn', $bill->rn])
            ->andFilterWhere(['like', 'status_code', $bill->status_code])
            ->andFilterWhere(['like', 'status_description', $bill->status_description])
            ->andFilterWhere(['like', 'class', $bill->class])
            ->andFilterWhere(['like', 'department_code', $bill->department_code])
            ->andFilterWhere(['like', 'department_name', $bill->department_name])
            ->andFilterWhere(['like', 'collection_center_code', $bill->collection_center_code])
            ->andFilterWhere(['like', 'nurse_responsible', $bill->nurse_responsible])
            ->andFilterWhere(['like', 'generation_responsible_uid', $bill->generation_responsible_uid])
            ->andFilterWhere(['like', 'description', $bill->description])
            ->andFilterWhere(['like', 'bill_print_responsible_uid', $bill->bill_print_responsible_uid])
            ->andFilterWhere(['like', 'bill_print_id', $bill->bill_print_id])
            //->andFilterWhere(['between', 'DATE_FORMAT(bill_generation_datetime, "%Y-%m-%d")', $bill->date_from, $this->date_to])
            ->andFilterWhere(['like', 'race', $bill->race])
            ->andFilterWhere(['like', 'nationality', $bill->nationality])
            ->all();
        }
        else if($bill->report_type == 'quarterly'){
            $query = Bill::find()
            ->select('bill.*, EXTRACT(QUARTER FROM bill_generation_datetime)  as bill_generation_datetime ,sum(bill_generation_billable_sum_rm) as bill_generation_billable_sum_rm')
            ->from('bill')
            ->joinWith('patient_information',true)
            ->groupBy('EXTRACT(QUARTER FROM bill_generation_datetime),EXTRACT(YEAR FROM bill_generation_datetime)')
            ->andFilterWhere(['like', 'bill_uid', $bill->bill_uid])
            ->andFilterWhere(['like', 'rn', $bill->rn])
            ->andFilterWhere(['like', 'status_code', $bill->status_code])
            ->andFilterWhere(['like', 'status_description', $bill->status_description])
            ->andFilterWhere(['like', 'class', $bill->class])
            ->andFilterWhere(['like', 'department_code', $bill->department_code])
            ->andFilterWhere(['like', 'department_name', $bill->department_name])
            ->andFilterWhere(['like', 'collection_center_code', $bill->collection_center_code])
            ->andFilterWhere(['like', 'nurse_responsible', $bill->nurse_responsible])
            ->andFilterWhere(['like', 'generation_responsible_uid', $bill->generation_responsible_uid])
            ->andFilterWhere(['like', 'description', $bill->description])
            ->andFilterWhere(['like', 'bill_print_responsible_uid', $bill->bill_print_responsible_uid])
            ->andFilterWhere(['like', 'bill_print_id', $bill->bill_print_id])
            //->andFilterWhere(['between', 'DATE_FORMAT(bill_generation_datetime, "%Y-%m-%d")', $bill->date_from, $this->date_to])
            ->andFilterWhere(['like', 'race', $bill->race])
            ->andFilterWhere(['like', 'nationality', $bill->nationality])
            ->all();
        }
        else if($bill->report_type == 'yearly'){
            $query = Bill::find()
            ->select('bill.*, EXTRACT(YEAR FROM bill_generation_datetime)  as bill_generation_datetime ,sum(bill_generation_billable_sum_rm) as bill_generation_billable_sum_rm')
            ->from('bill')
            ->joinWith('patient_information',true)
            ->groupBy('EXTRACT(YEAR FROM bill_generation_datetime)')
            ->andFilterWhere(['like', 'bill_uid', $bill->bill_uid])
            ->andFilterWhere(['like', 'rn', $bill->rn])
            ->andFilterWhere(['like', 'status_code', $bill->status_code])
            ->andFilterWhere(['like', 'status_description', $bill->status_description])
            ->andFilterWhere(['like', 'class', $bill->class])
            ->andFilterWhere(['like', 'department_code', $bill->department_code])
            ->andFilterWhere(['like', 'department_name', $bill->department_name])
            ->andFilterWhere(['like', 'collection_center_code', $bill->collection_center_code])
            ->andFilterWhere(['like', 'nurse_responsible', $bill->nurse_responsible])
            ->andFilterWhere(['like', 'generation_responsible_uid', $bill->generation_responsible_uid])
            ->andFilterWhere(['like', 'description', $bill->description])
            ->andFilterWhere(['like', 'bill_print_responsible_uid', $bill->bill_print_responsible_uid])
            ->andFilterWhere(['like', 'bill_print_id', $bill->bill_print_id])
            //->andFilterWhere(['between', 'DATE_FORMAT(bill_generation_datetime, "%Y-%m-%d")', $bill->date_from, $this->date_to])
            ->andFilterWhere(['like', 'race', $bill->race])
            ->andFilterWhere(['like', 'nationality', $bill->nationality])
            ->all();
        }
        else if($bill->report_type == 'race'){
            $query = Bill::find()
            ->select('bill.*, race as bill_generation_datetime, sum(bill_generation_billable_sum_rm) as bill_generation_billable_sum_rm')
            ->from('bill')
            ->joinWith('patient_information',true)
            ->groupBy('race')
            ->andFilterWhere(['like', 'bill_uid', $bill->bill_uid])
            ->andFilterWhere(['like', 'rn', $bill->rn])
            ->andFilterWhere(['like', 'status_code', $bill->status_code])
            ->andFilterWhere(['like', 'status_description', $bill->status_description])
            ->andFilterWhere(['like', 'class', $bill->class])
            ->andFilterWhere(['like', 'department_code', $bill->department_code])
            ->andFilterWhere(['like', 'department_name', $bill->department_name])
            ->andFilterWhere(['like', 'collection_center_code', $bill->collection_center_code])
            ->andFilterWhere(['like', 'nurse_responsible', $bill->nurse_responsible])
            ->andFilterWhere(['like', 'generation_responsible_uid', $bill->generation_responsible_uid])
            ->andFilterWhere(['like', 'description', $bill->description])
            ->andFilterWhere(['like', 'bill_print_responsible_uid', $bill->bill_print_responsible_uid])
            ->andFilterWhere(['like', 'bill_print_id', $bill->bill_print_id])
            //->andFilterWhere(['between', 'DATE_FORMAT(bill_generation_datetime, "%Y-%m-%d")', $bill->date_from, $this->date_to])
            ->andFilterWhere(['like', 'race', $bill->race])
            ->andFilterWhere(['like', 'nationality', $bill->nationality])
            ->all();
        }
        else if($bill->report_type == 'nationality'){
            $query = Bill::find()
            ->select('bill.*, nationality as bill_generation_datetime, sum(bill_generation_billable_sum_rm) as bill_generation_billable_sum_rm')
            ->from('bill')
            ->joinWith('patient_information',true)
            ->groupBy('nationality')
            ->andFilterWhere(['like', 'bill_uid', $bill->bill_uid])
            ->andFilterWhere(['like', 'rn', $bill->rn])
            ->andFilterWhere(['like', 'status_code', $bill->status_code])
            ->andFilterWhere(['like', 'status_description', $bill->status_description])
            ->andFilterWhere(['like', 'class', $bill->class])
            ->andFilterWhere(['like', 'department_code', $bill->department_code])
            ->andFilterWhere(['like', 'department_name', $bill->department_name])
            ->andFilterWhere(['like', 'collection_center_code', $bill->collection_center_code])
            ->andFilterWhere(['like', 'nurse_responsible', $bill->nurse_responsible])
            ->andFilterWhere(['like', 'generation_responsible_uid', $bill->generation_responsible_uid])
            ->andFilterWhere(['like', 'description', $bill->description])
            ->andFilterWhere(['like', 'bill_print_responsible_uid', $bill->bill_print_responsible_uid])
            ->andFilterWhere(['like', 'bill_print_id', $bill->bill_print_id])
            //->andFilterWhere(['between', 'DATE_FORMAT(bill_generation_datetime, "%Y-%m-%d")', $bill->date_from, $this->date_to])
            ->andFilterWhere(['like', 'race', $bill->race])
            ->andFilterWhere(['like', 'nationality', $bill->nationality])
            ->all();
        }
        else{
            $query = Bill::find()
            ->select('bill.*, DATE(bill_generation_datetime) as bill_generation_datetime, sum(bill_generation_billable_sum_rm) as bill_generation_billable_sum_rm')
            ->from('bill')
            ->joinWith('patient_information',true)
            ->groupBy('DATE(bill_generation_datetime)')
            ->andFilterWhere(['like', 'bill_uid', $bill->bill_uid])
            ->andFilterWhere(['like', 'rn', $bill->rn])
            ->andFilterWhere(['like', 'status_code', $bill->status_code])
            ->andFilterWhere(['like', 'status_description', $bill->status_description])
            ->andFilterWhere(['like', 'class', $bill->class])
            ->andFilterWhere(['like', 'department_code', $bill->department_code])
            ->andFilterWhere(['like', 'department_name', $bill->department_name])
            ->andFilterWhere(['like', 'collection_center_code', $bill->collection_center_code])
            ->andFilterWhere(['like', 'nurse_responsible', $bill->nurse_responsible])
            ->andFilterWhere(['like', 'generation_responsible_uid', $bill->generation_responsible_uid])
            ->andFilterWhere(['like', 'description', $bill->description])
            ->andFilterWhere(['like', 'bill_print_responsible_uid', $bill->bill_print_responsible_uid])
            ->andFilterWhere(['like', 'bill_print_id', $bill->bill_print_id])
            //->andFilterWhere(['between', 'DATE_FORMAT(bill_generation_datetime, "%Y-%m-%d")', $bill->date_from, $this->date_to])
            ->andFilterWhere(['like', 'race', $bill->race])
            ->andFilterWhere(['like', 'nationality', $bill->nationality])
            ->all();

        }
        $data = ArrayHelper::toArray($query);
        return $data;
    }
}
