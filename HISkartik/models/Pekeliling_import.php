<?php

namespace app\models;

use Yii;
use GpsLab\Component\Base64UID\Base64UID;

/**
 * This is the model class for table "pekeliling_import".
 *
 * @property string $pekeliling_uid
 * @property string $upload_datetime
 * @property string|null $approval1_responsible_uid
 * @property string|null $approval2_responsible_uid
 * @property string $file_import
 * @property string $lookup_type
 * @property string|null $error
 * @property string|null $scheduled_datetime
 * @property string|null $executed_datetime
 * @property string|null $execute_responsible_uid
 * @property string $update_type
 */
class Pekeliling_import extends \yii\db\ActiveRecord
{
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pekeliling_import';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'csv', 'checkExtensionByMimeType' => false],
            [['pekeliling_uid', 'upload_datetime', 'file_import', 'lookup_type', 'update_type'], 'required'],
            [['upload_datetime', 'scheduled_datetime', 'executed_datetime'], 'safe'],
            [['pekeliling_uid', 'approval1_responsible_uid', 'approval2_responsible_uid', 'execute_responsible_uid'], 'string', 'max' => 64],
            [['file_import'], 'string', 'max' => 100],
            [['lookup_type'], 'string', 'max' => 32],
            [['error'], 'string', 'max' => 500000],
            [['update_type'], 'string', 'max' => 12],
            [['pekeliling_uid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pekeliling_uid' => 'Pekeliling Uid',
            'upload_datetime' => Yii::t('app','Upload Datetime'),
            'approval1_responsible_uid' => Yii::t('app','Approval 1 Responsible'),
            'approval2_responsible_uid' => Yii::t('app','Approval 2 Responsible'),
            'file_import' => Yii::t('app','File Import'),
            'lookup_type' => Yii::t('app','Lookup Type'),
            'error' => Yii::t('app','Error'),
            'scheduled_datetime' => Yii::t('app','Scheduled Datetime'),
            'executed_datetime' => Yii::t('app','Executed Datetime'),
            'execute_responsible_uid' => Yii::t('app','Execute Responsible'),
            'update_type' => Yii::t('app','Update Type'),
        ];
    }

    public static function chooseLookupType($type)
    {
        switch ($type) {
            case "status":
                return new Lookup_status();
            case "treatment":
                return new Lookup_treatment();
            case "ward":
                return new Lookup_ward();
            case "department":
                return new Lookup_department();
            case "fpp":
                return new Lookup_fpp();
        }
    }

    public static function validateSpecialCharacter($line, $header)
    {
        $string_special_char = "";
        foreach ($line as $key => $val) {
            // loop over array and check all special characters found in string, only    allow ' () - / \ ,'
            if(preg_match('/[\'^£$%&*};"{@#~?!><>|=_+¬]/', $val)) {
                if($string_special_char == "")
                    $string_special_char = $header[$key].' '.$val;
                else $string_special_char .= "  ,  ".$header[$key].' '.$val;
            } 
        }
        if($string_special_char != "")
            $string_special_char .=  Yii::t('app', ' contains special characters').".";
        return $string_special_char;
    }

    public static function validateModel($type, $line)
    {
        foreach ($line as $key => $val) {
            if(!empty($line[$key]))
            {
                // replaces all tabs, new lines, double spaces to simple 1 space
                $line[$key] = trim(preg_replace('/[\t\n\r\s]+/', ' ', $val));
            }
        }

        switch ($type) {
            case "status":
                $model_lookup = Lookup_status::findOne(['status_code' =>  $line[0]]);
                if(empty($model_lookup))
                {
                    $model_lookup = new Lookup_status();
                    $model_lookup->status_uid =  Base64UID::generate(32);
                    $model_lookup->status_code = $line[0];
                }  
                $model_lookup->status_description = $line[1];
                $model_lookup->class_1a_ward_cost = $line[2];
                $model_lookup->class_1b_ward_cost = $line[3];
                $model_lookup->class_1c_ward_cost = $line[4];
                $model_lookup->class_2_ward_cost = $line[5];
                $model_lookup->class_3_ward_cost = $line[5];
                return $model_lookup;

            case "treatment":
                $model_lookup = Lookup_treatment::findOne(['treatment_code' =>  $line[0]]);
                if(empty($model_lookup))
                {
                    $model_lookup = new Lookup_treatment();
                    $model_lookup->treatment_uid =  Base64UID::generate(32);
                    $model_lookup->treatment_code = $line[0];
                }
                $model_lookup->treatment_name = $line[1];
                $model_lookup->class_1_cost_per_unit = $line[2];
                $model_lookup->class_2_cost_per_unit = $line[3];
                $model_lookup->class_3_cost_per_unit = $line[4];
                $model_lookup->class_Daycare_FPP_per_unit = $line[5];
                return $model_lookup;

            case "ward":
                $model_lookup = Lookup_ward::findOne(['ward_code' =>  $line[0]]);
                if(empty($model_lookup))
                {
                    $model_lookup = new Lookup_ward();
                    $model_lookup->ward_uid =  Base64UID::generate(32);
                    $model_lookup->ward_code = $line[0];
                }
                $model_lookup->ward_name = $line[1];
                $model_lookup->sex = $line[2];
                $model_lookup->min_age = $line[3];
                $model_lookup->max_age = $line[4];
                return $model_lookup;
              
            case "department":
                $model_lookup = Lookup_department::findOne(['department_code' =>  $line[0]]);
                if(empty($model_lookup))
                {
                    $model_lookup = new Lookup_department();
                    $model_lookup->department_uid =  Base64UID::generate(32);
                    $model_lookup->department_code = $line[0];
                }
                $model_lookup->department_name = $line[1];
                $model_lookup->phone_number = $line[2];
                $model_lookup->address1 = $line[3];
                $model_lookup->address2 = $line[4];
                $model_lookup->address3 = $line[5];
                return $model_lookup;

            case "fpp":
                $model_lookup = Lookup_fpp::findOne(['kod' =>  $line[0]]);
                if(empty($model_lookup))
                {
                    $model_lookup = new Lookup_fpp();
                    $model_lookup->kod = $line[0];
                }
                $model_lookup->name = $line[1];
                $model_lookup->min_cost_per_unit = $line[2];
                $model_lookup->max_cost_per_unit = $line[3];
                return $model_lookup;
        }
    }

    public static function validateHeader($first_column_csv, $col)
    {
        foreach ($first_column_csv as $key => $val) {
            if(!empty($first_column_csv[$key]))
            {
                // replaces all tabs, new lines, double spaces, comma to simple 1 space
                $first_column_csv[$key] = trim(preg_replace('/[\t\n\r\s]+/', ' ', $val));
            }
        }

        // remove first element from database header
        array_shift($col);

        // filter empty column
        $first_column_csv = array_filter($first_column_csv);
         
        // Computes the different of array
        $result_different_first_row = array_diff($first_column_csv, $col);

        // Check first column of CSV and database column name equal
        if((count($first_column_csv) == count($col)) && (empty($result_different_first_row)))
            return true;
        else return false;
    }
}
