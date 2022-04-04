<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Patient_informationSearch;

class SidebarController extends \yii\web\Controller
{
    public function actionIndex($IC)
    {
        $model = new Patient_informationSearch();

        if ($model->load(Yii::$app->request->post()) && $model->findByIC($IC)){
            Yii::$app->session->addFlash('SIGNUP', 'You have successfully registered');
            return $this->redirect(Yii::$app->homeUrl);
        }

        return $this->render('signup', [
            'model' => $model
        ]);
    }

}
