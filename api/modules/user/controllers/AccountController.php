<?php

namespace app\modules\user\controllers;

use api\modules\core\controllers\CoreController;
use app\modules\user\controllers\account\LoginAction;
use app\modules\user\controllers\account\RegisterAction;


use app\modules\user\components\AuthHandler;
use app\modules\user\controllers\account\ActivateAction;
use app\modules\user\controllers\account\EmailAcceptAction;
use app\modules\user\controllers\account\LogoutAction;
use app\modules\user\controllers\account\RecoveryRequestAction;
use app\modules\user\controllers\account\RecoveryResetAction;
use app\modules\user\forms\RegistrationForm;
use yii\filters\AccessControl;

class AccountController extends CoreController
{



    public function actions()
    {
        return [
            'sign-up' => [
                'class' => RegisterAction::className(),
            ],
            'login' => [
                'class' => LoginAction::className(),
                'layout' => '/frontend/main'
            ],
//
//            'logout' => [
//                'class' => LogoutAction::className(),
//            ],
//            'activate' => [
//                'class' => ActivateAction::className(),
//            ],
//            'invitation-request' => [
//                'class' => InvitationRequestAction::className(),
//            ],
//            'recovery-request' => [
//                'class' => RecoveryRequestAction::className(),
//                'layout' => '/frontend/main'
//            ],
//            'recovery-reset' => [
//                'class' => RecoveryResetAction::className(),
//                'layout' => '/frontend/main'
//            ],
//            'auth' => [
//                'class' => 'yii\authclient\AuthAction',
//                'successCallback' => [$this, 'onAuthSuccess'],
//            ],
//            'email-accept' => [
//                'class' => EmailAcceptAction::className(),
//                'layout' => '/frontend/main'
//            ]
        ];
    }
}
