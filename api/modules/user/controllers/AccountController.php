<?php

namespace api\modules\user\controllers;

use api\modules\core\controllers\CoreController;
use api\modules\user\controllers\account\LoginAction;
use api\modules\user\controllers\account\RegisterAction;

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
            ],
        ];
    }
}
