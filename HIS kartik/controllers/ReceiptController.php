<?php

namespace app\controllers;
<<<<<<< Updated upstream
=======

require 'vendor/autoload.php';
>>>>>>> Stashed changes
use Yii;
use app\models\Receipt;
use app\models\ReceiptSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
<<<<<<< Updated upstream
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
=======
use app\models\Patient_admission;
use yii\data\ActiveDataProvider;
use app\models\Patient_information;
use yii\helpers\ArrayHelper;
>>>>>>> Stashed changes

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

/*
class clearText {

    public function clearVar($text)
    {
        $updateTxt = ltrim($text,"Array ( [0] => ");
        $updateTxt = rtrim($updateTxt," )");

        return $updateTxt;
        
    }
}
*/
/**
 * ReceiptController implements the CRUD actions for Receipt model.
 */
class ReceiptController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Receipt models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ReceiptSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Receipt model.
     * @param string $receipt_uid Receipt Uid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($receipt_uid)
    {
        return $this->render('view', [
            'model' => $this->findModel($receipt_uid),
        ]);
    }

    /**
     * Creates a new Receipt model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('+0800')); //GMT
        $model = new Receipt();

        if ($this->request->isPost && $model->load($this->request->post())) {
            if(empty($model->receipt_content_datetime_paid))
            {
                $model->receipt_content_datetime_paid =  $date->format('Y-m-d H:i');
<<<<<<< Updated upstream
=======

            if($model->validate() && $model->save()){
                // return Yii::$app->getResponse()->redirect(array('/receipt/update', 
                // 'receipt_uid' => $model->receipt_uid, 'rn' => $model->rn));   
                

                //$pa2 = new Patient_admission();

                $pa2 = receipt::find()->orderBy(['rn' => SORT_DESC])->limit(1)->all();
                
               $pa3 = Patient_information::find()->all();
              // $pa = ArrayHelper::getColumn(Patient_admission::find()->limit(1)->all(), 'rn');
    
                $rn1 = ArrayHelper::getColumn($pa2,'rn');
                
                $ed1 = ArrayHelper::getColumn($pa2,'receipt_content_datetime_paid');
                $piname = ArrayHelper::map($pa3,'name','nric');
               
                $result = implode($rn1);
                $result2 = implode($ed1);
               
    
               $blankfront = str_repeat("\x20", 15); // adds 14 spaces
                $fixbackblank = str_repeat("\x20", 33);
                $fixbackblank2 = str_repeat("\x20", 31);
                $fixbackblank3 = str_repeat("\x20", 32);
    
                $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
                $printer = new Printer($connector);
                $printer -> text("\n\n\x20\n\n\x20\n\n");
                $printer -> text($blankfront); // space= 0.3cm， receipt column 1
                $printer -> text($resitno); // receipt number
                $printer -> text($fixbackblank); //receipt column 2
                $printer -> text($ic."\n"); // no.K/P
                $printer -> text($blankfront);
                $printer -> text(date("d/m/Y", strtotime($result2))."  ");
                $printer -> text($fixbackblank2);
                $printer -> text($result."\n"); // rn
                $printer -> text($blankfront);
                $printer -> text(date("H:i:s", strtotime($result2))."\n");
                $printer -> text($fixbackblank);
                $printer -> text($bil."\n"); //no.Bil
                $printer -> text($blankfront);
                $printer -> text(""); // Akaun
                $printer -> text($fixbackblank3 ."         ");
                $printer -> text($total."\n"); //total price
                $printer -> text($blankfront);
                $printer -> text("  \n"); // Op (example required)
                $printer -> text($blankfront);
                $printer -> text($cagaran); // No.Cagaran
                $printer -> text($fixbackblank);
                $printer -> text(mb_strimwidth($payer_name,0, 50)."\n\n"); // guarrantor name
                $printer -> text($blankfront);
                $blankback = str_repeat("\x20", 55 - 14 - strlen($patientname));
                $printer -> text($patientname); // patient name
                $printer -> text($blankback);
                $printer -> text("cash"."\n\n"); //Cara Bayaran
                $printer -> text(str_repeat("\x20" , 7)."Penjelasan :");
                
                
                $printer -> close(); 
                
                //put print
                return Yii::$app->getResponse()->redirect(array('/receipt/index', 
                'rn' => $model->rn));   
                

                
>>>>>>> Stashed changes
            }
            $model->save();
            return Yii::$app->getResponse()->redirect(array('/receipt/update', 
            'receipt_uid' => $model->receipt_uid, 'rn' => $model->rn));   
        } else {
            $model->receipt_content_datetime_paid = date("Y-m-d H:i");
            $model->loadDefaultValues();
        }


        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Receipt model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $receipt_uid Receipt Uid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($receipt_uid)
    {
        $model = $this->findModel($receipt_uid);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return Yii::$app->getResponse()->redirect(array('/receipt/update', 
            'receipt_uid' => $model->receipt_uid));   
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
/*
    public function actionPrint($receipt_uid)
    {

        $model = $this->findModel($receipt_uid);
        if ($this->request->isPost && $model->load($this->request->post())) {

           $pa2 = new Patient_admission();

                $pa2 = Patient_admission::find()->all();
                $pa3 = Patient_information::find()->all();
    
                $rn1 = ArrayHelper::getColumn($pa2,'rn');
                $ed1 = ArrayHelper::getColumn($pa2,'entry_datetime');
                $piname = ArrayHelper::map($pa3,'name','nric');
               
                $result = implode($rn1);
                $result2 = implode($ed1);
    
                $x = new clearText();
    
                $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
                $printer = new Printer($connector);
                $printer -> text($result."\n"); // receipt number
                $printer -> text($result2."\n");
    
                $printer -> close(); 
        }
    } */

    /**
     * Deletes an existing Receipt model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $receipt_uid Receipt Uid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($receipt_uid)
    {
        $this->findModel($receipt_uid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Receipt model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $receipt_uid Receipt Uid
     * @return Receipt the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($receipt_uid)
    {
        if (($model = Receipt::findOne(['receipt_uid' => $receipt_uid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionPrint()
    {

        /*
        $rn1 = ArrayHelper::getColumn($pa2,'rn');
        $ed1 = ArrayHelper::getColumn($pa2,'entry_datetime');
        $piname = ArrayHelper::map($pa3,'name','nric');

         $result = implode($rn1);
        $result2 = implode($ed1);
    */
       // $pa = Patient_admission::find()->select(['rn','entry_datetime'])->one();
        //print_r($pa);
        //$getrn = Patient_admission::findOne(['rn'=> $receipt->rn]);

        $connector = new WindowsPrintConnector("smb://DESKTOP-7044BNO/Epson");
        $printer = new Printer($connector);
       // $printer -> text(($x->clearVar($result));
        $printer -> text("2022/008023". "\n");
        $printer -> text("Did we sucessfully print from here?". "\n");



        $printer -> cut();
        $printer -> close(); 


        return "";
    }
}
