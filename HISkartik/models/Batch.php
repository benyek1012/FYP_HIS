<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "batch".
 *
 * @property int $id
 * @property string $upload_datetime
 * @property string|null $approval1_responsible_uid
 * @property string|null $approval2_responsible_uid
 * @property string $file_import
 * @property string $lookup_type
 * @property string|null $error
 * @property string|null $scheduled_datetime
 * @property string|null $executed_datetime
 * @property string|null $execute_responsible_uid
 * @property string|null $update_type
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
            [['file'], 'file', 'extensions' => 'csv', 'checkExtensionByMimeType' => false],
            [['upload_datetime', 'file_import', 'lookup_type', 'update_type'], 'required'],
            [['upload_datetime', 'scheduled_datetime', 'executed_datetime'], 'safe'],
            [['approval1_responsible_uid', 'approval2_responsible_uid', 'execute_responsible_uid'], 'string', 'max' => 64],
            [['file_import'], 'string', 'max' => 100],
            [['lookup_type'], 'string', 'max' => 32],
            [['error'], 'string', 'max' => 2000],
            [['update_type'], 'string', 'max' => 12],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'upload_datetime' => 'Upload Datetime',
            'approval1_responsible_uid' => 'Approval 1 Responsible',
            'approval2_responsible_uid' => 'Approval 2 Responsible',
            'file_import' => 'File Import',
            'lookup_type' => 'Lookup Type',
            'error' => 'Error',
            'scheduled_datetime' => 'Scheduled Datetime',
            'executed_datetime' => 'Executed Datetime',
            'execute_responsible_uid' => 'Execute Responsible',
            'update_type' => 'Update Type',
        ];
    }

    public static function chooseLookupType($type)
    {
        switch ($type) {
            case "status":
                return new Lookup_status();
                break;
            case "treatment":
                return new Lookup_treatment();
                break;
            case "ward":
                return new Lookup_ward();
                break;
            case "department":
                return new Lookup_department();
                break;
            case "fpp":
                return new Lookup_fpp();
                break;
        }
    }
}