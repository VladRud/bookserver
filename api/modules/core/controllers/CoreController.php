<?php

namespace app\api\modules\core\controllers;

use yii\rest\Controller;

class CoreController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin'                           => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age'           => 3600,
            ]
        ];

        return $behaviors;
    }
}
