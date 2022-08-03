<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "batch".
 *
 * @property int $id
 * @property int $batch
 * @property string $file_import
 */
class Batch extends \yii\db\ActiveRecord
{
    public $file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'batch';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['batch', 'file_import'], 'required'],
            [['batch'], 'integer'],
            [['file_import'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'batch' => Yii::t('app','Batch'),
            'file_import' => Yii::t('app','File Import'),
        ];
    }
}
