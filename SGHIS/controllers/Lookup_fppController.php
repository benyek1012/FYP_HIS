<?php

namespace app\controllers;

use Yii;
use app\models\Lookup_fpp;
use app\models\Lookup_fppSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\grid\EditableColumnAction;
use yii\helpers\ArrayHelper;
use app\models\New_user;

/**
 * Lookup_fppController implements the CRUD actions for Lookup_fpp model.
 */
class Lookup_fppController extends Controller
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

    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'fpp' => [                                                              // identifier for your editable action
                'class' => EditableColumnAction::className(),                       // action class name
                'modelClass' => Lookup_fpp::className(),                   // the update model class
                'outputValue' => function ($model, $attribute, $key, $index) {
                    $value = $model->$attribute;  
                }
            ]
        ]);
    }

    /**
     * Lists all Lookup_fpp models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new Lookup_fpp();
        $searchModel = new Lookup_fppSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if(!(new New_user()) -> isCashierorAdminorClerk()) echo $this->render('/site/no_access');
        if ($this->request->isPost && $model->load($this->request->post()))
        {
            $checkDuplicatedCode = Lookup_fpp::findOne((['kod' => $model->kod]));

            if($model->validate() &&  empty( $checkDuplicatedCode))
            {
                try{
                    $model->save();
                }catch(\yii\db\Exception $e){
                    var_dump($e->getMessage()); //Get the error messages accordingly.
                }
                return $this->redirect(['index', 'kod' => $model->kod]);
            }
            else
            {
                Yii::$app->session->setFlash('error_fpp', '
                    <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <strong>Validation error! </strong> Kod '.$model->kod.' is duplicated. !</div>'
                );
                //$message = 'Code should not be duplicated.';
                //$model->addError('ward_code', $message);
            }
        }
        else
        {
            $model->loadDefaultValues();
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Lookup_fpp model.
     * @param string $kod Kod
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($kod)
    {
        return $this->render('view', [
            'model' => $this->findModel($kod),
        ]);
    }

    /**
     * Creates a new Lookup_fpp model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Lookup_fpp();
        $searchModel = new Lookup_fppSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $checkDuplicatedCode = Lookup_fpp::findOne((['kod' => $model->kod]));

            if (empty($checkDuplicatedCode))
            {
                try{
                    $model->save();
                }catch(\yii\db\Exception $e){
                    var_dump($e->getMessage()); //Get the error messages accordingly.
                }
                return $this->redirect(['index', 'kod' => $model->kod]);
            }
            else
            {
                Yii::$app->session->setFlash('error_fpp', '
                <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                <strong>Validation error! </strong> Kod '.$model->kod.' is duplicated. !</div>'
                );
                return $this->redirect(['index', 'kod' => $model->kod]);
            }
        } 

        $model->loadDefaultValues();

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Lookup_fpp model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $kod Kod
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($kod)
    {
        $model = $this->findModel($kod);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'kod' => $model->kod]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Lookup_fpp model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $kod Kod
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($kod)
    {
        $this->findModel($kod)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Lookup_fpp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $kod Kod
     * @return Lookup_fpp the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($kod)
    {
        if (($model = Lookup_fpp::findOne(['kod' => $kod])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
