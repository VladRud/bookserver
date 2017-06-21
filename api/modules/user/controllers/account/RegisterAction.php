<?php

namespace api\modules\user\controllers\account;

use Yii;
use yii\base\Action;
use api\modules\user\forms\RegistrationForm;

/**
 * Class RegisterAction
 */
class RegisterAction extends Action
{

    public function run()
    {
        $model = new RegistrationForm();
        $data = Yii::$app->request->getBodyParams();

        if ($model->load($data, '') && $model->validate()) {
            if ($user = Yii::$app->userManager->createUser($model)) {
                return $user;
            }

        } else {
            return [
                'errors' => $model->errors
            ];
        }

    }

}
