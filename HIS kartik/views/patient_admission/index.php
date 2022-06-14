<?php

use app\models\Patient_admission;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Patient_admissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="patient-admission-index">
    
    <!-- This is the gridview that shows patient admission summary-->   
    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'showOnEmpty' => false,
        'emptyText' => Yii::t('app','Patient admission record is not founded'),
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'rn',
                    'format' => 'raw',
                    'value'=>function ($data) {
                        return Html::a($data['rn'], \yii\helpers\Url::to(['/patient_admission/update', 'rn' => $data['rn']]));
                    },
                ],
                [
                    'attribute' => 'entry_datetime',
                    "format"=>"raw",
                    'value'=>function ($data) {
                        $date = new DateTime($data['entry_datetime']);
                        $tag = Html::tag ( 'span' , $date->format('Y-m-d') , [
                            // title
                            'title' => $date->format('Y-m-d H:i A') ,
                            'data-placement' => 'top' ,
                            'data-toggle'=>'tooltip',
                            'style' => 'white-space:pre;'
                        ] );
                        return $tag;
                    },
                ],
                'initial_ward_code',
                'initial_ward_class',
                'reference',
                'medical_legal_code',
                'reminder_given',
                'guarantor_name',
              //  'guarantor_nric',
              //  'guarantor_phone_number',
                //'guarantor_email:email',
                [
                    'attribute' => 'billable_sum',
                    'label' => Yii::t('app','Billable Total').' (RM)',
                    'value' => function($data){
                        return  (new Patient_admission()) -> get_billable_sum($data->rn);
                    },
                ],
                [
                    'attribute' => 'final_fee',
                    'label' => Yii::t('app','Amount Due').' / '.Yii::t('app','Unclaimed Balance').' (RM)',
                    'value' => function($data){
                        return (new Patient_admission()) ->get_bill($data->rn);
                    },
                ],
            ],
    ]) ?>

</div>

<?php
    $js = <<<SCRIPT
    /* To initialize BS3 tooltips set this below */
    $(function () { 
       $('body').tooltip({
        selector: '[data-toggle="tooltip"]',
            html:true
        });
    });
SCRIPT;
    // Register tooltip/popover initialization javascript
    $this->registerJs ( $js );
?>