<?php

namespace app\controllers;
use Yii;
use app\models\Receipt;
use app\models\ReceiptSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

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
