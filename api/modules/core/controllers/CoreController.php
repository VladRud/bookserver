<?php

namespace api\modules\core\controllers;

use yii\rest\Controller;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\Cors;

class CoreController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin'                           => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age'           => 3600,
                'Access-Control-Request-Headers'   => ['*'],
            ]
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
        ];

        return $behaviors;
    }
}
