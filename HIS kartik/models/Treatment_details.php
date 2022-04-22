<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "treatment_details".
 *
 * @property string $treatment_details_uid
 * @property string $bill_uid
 * @property string $treatment_code
 * @property string $treatment_name
 * @property float $item_per_unit_cost_rm
 * @property int $item_count
 * @property float $item_total_unit_cost_rm
 *
 * @property Bill $billU
 */
class Treatment_details extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'treatment_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['treatment_details_uid', 'bill_uid', 'treatment_code', 'treatment_name', 'item_per_unit_cost_rm', 'item_count', 'item_total_unit_cost_rm'], 'required'],
            [['item_per_unit_cost_rm', 'item_total_unit_cost_rm'], 'number'],
            [['item_count'], 'integer'],
            [['treatment_details_uid', 'bill_uid'], 'string', 'max' => 64],
            [['treatment_code'], 'string', 'max' => 20],
            [['treatment_name'], 'string', 'max' => 50],
            [['treatment_details_uid'], 'unique'],
            [['bill_uid'], 'exist', 'skipOnError' => true, 'targetClass' => Bill::className(), 'targetAttribute' => ['bill_uid' => 'bill_uid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'treatment_details_uid' => Yii::t('app','Treatment Details Uid'),
            'bill_uid' => Yii::t('app','Bill Uid'),
            'treatment_code' => Yii::t('app','Treatment Code'),
            'treatment_name' => Yii::t('app','Treatment Name'),
            'item_per_unit_cost_rm' => Yii::t('app','Item Per Unit Cost Rm'),
            'item_count' => Yii::t('app','Item Count'),
            'item_total_unit_cost_rm' => Yii::t('app','Item Total Unit Cost Rm'),
        ];
    }

    /**
     * Gets query for [[BillU]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBillU()
    {
        return $this->hasOne(Bill::className(), ['bill_uid' => 'bill_uid']);
    }
}
