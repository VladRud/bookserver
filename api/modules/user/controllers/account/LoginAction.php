<?php

namespace api\modules\user\controllers\account;

use Yii;
use yii\base\Action;
use api\modules\user\forms\LoginForm;
//use app\modules\setting\helpers\SettingHelper;
use yii\base\ErrorException;

class LoginAction extends Action
{
    public $layout;

    public function run()
    {
        $form = new LoginForm();
        $data = Yii::$app->request->getBodyParams();

        if ($form->load($data, '') && $form->validate()) {

            if (Yii::$app->authenticationManager->login($form)) {
                return $form;
            }
        } else {
            return [
                'errors' => $form->errors
            ];
        }

    }

}
