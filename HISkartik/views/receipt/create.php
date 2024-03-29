<?php

use app\models\Patient_admission;
use app\models\Patient_information;
/* @var $this yii\web\View */
/* @var $model app\models\Receipt */

if($cancellation == false){
    $temp = Patient_admission::findOne(['rn'=> Yii::$app->request->get('rn')]);
    $temp2 = Patient_information::findOne(['patient_uid'=> $temp->patient_uid]);

    if(Yii::$app->request->get('rn') == Yii::$app->params['other_payment_rn']){
        $this->title = Yii::t('app','Create Other Payment').' : '.Yii::$app->request->get('rn');
        
        if(Yii::$app->request->get('rn') == Yii::$app->params['other_payment_rn'])
        {
            $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Other Payments'), 'url' => ['index', 'rn'=> Yii::$app->request->get('rn')]];
            $this->params['breadcrumbs'][] = Yii::t('app','Create Other Payment');
        }
        else
        {
            if($temp2->name != "")
                $this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
            else 
                $this->params['breadcrumbs'][] = ['label' => "Unknown", 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
            $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Payments'), 'url' => ['index', 'rn'=> Yii::$app->request->get('rn')]];
            $this->params['breadcrumbs'][] = Yii::t('app','Create Payment');
        }
    }
    else if(empty(Yii::$app->request->get('outside'))){
        $this->title = Yii::t('app','Create Payment').' : '.Yii::$app->request->get('rn');
        if($temp2->name != "")
            $this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
        else 
            $this->params['breadcrumbs'][] = ['label' => "Unknown", 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
        $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Payments'), 'url' => ['index', 'rn'=> Yii::$app->request->get('rn')]];
        $this->params['breadcrumbs'][] = Yii::t('app','Create Payment');
    }
    else{
        $this->title = Yii::t('app','Payment Outside SGH').' : '.Yii::$app->request->get('rn');
        if($temp2->name != "")
            $this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
        else 
            $this->params['breadcrumbs'][] = ['label' => "Unknown", 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
        $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Payments'), 'url' => ['index', 'rn'=> Yii::$app->request->get('rn')]];
        $this->params['breadcrumbs'][] = Yii::t('app','Payment Outside SGH');
    }
    // if($temp2->name != "")
    //     $this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
    // else 
    //     $this->params['breadcrumbs'][] = ['label' => "Unknown", 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
    // $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Payments'), 'url' => ['index', 'rn'=> Yii::$app->request->get('rn')]];
    // $this->params['breadcrumbs'][] = Yii::t('app','Create Payment');

    // if(Yii::$app->request->get('rn') == Yii::$app->params['other_payment_rn'])
    //     $this->title = Yii::t('app','Create Other Payment').' : '.Yii::$app->request->get('rn');
    // else $this->title = Yii::t('app','Create Payment').' : '.Yii::$app->request->get('rn');

    // if($temp2->name != "")
    //     $this->params['breadcrumbs'][] = ['label' => $temp2->name, 'url' => ['site/admission', 'id' => $temp2->patient_uid]];
    // else 
    //     $this->params['breadcrumbs'][] = ['label' => "Unknown", 'url' => ['site/admission', 'id' => $temp2->patient_uid]];

    // if(Yii::$app->request->get('rn') == Yii::$app->params['other_payment_rn'])
    // {
    //     $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Other Payments'), 'url' => ['index', 'rn'=> Yii::$app->request->get('rn')]];
    //     $this->params['breadcrumbs'][] = Yii::t('app','Create Other Payment');
    // }
    // else
    // {
    //     $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Payments'), 'url' => ['index', 'rn'=> Yii::$app->request->get('rn')]];
    //     $this->params['breadcrumbs'][] = Yii::t('app','Create Payment');
    // }
}

?>
<div class="receipt-create">

    <?= $this->render('_form', [
        'model' => $model,
        'cancellation' => $cancellation,
        'model_bill' => $model_bill,
        'index' => $index,
    ]) ?>

</div>