<?php

namespace api\controllers;

use Yii;
use yii\rest\Controller;
use api\models\LoginForm;
use api\models\RegistrationForm;
use api\controllers\CoreController;

class SiteController extends CoreController
{
    public function actionIndex(){
        return ['api'];
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if ($token = $model->auth()) {
            return $token;
        } else {
            return $model;
        }
    }

    public function actionRegistration(){
        $model = new RegistrationForm();
        $model->load(Yii::$app->request->getBodyParams(), '');
        return Yii::$app->request->getBodyParams();
    }
}
