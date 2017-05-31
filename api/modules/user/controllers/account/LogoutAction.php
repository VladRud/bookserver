<?php

namespace app\modules\user\controllers\account;

use Yii;
use yii\base\Action;
use yii\helpers\Url;

/**
 * Class LogOutAction
 *
 * @author Stableflow
 */
class LogoutAction extends Action
{
    public function run()
    {
        if (!Yii::$app->user->isGuest) {
             Yii::$app->authenticationManager->logout(Yii::$app->getUser()->getIdentity());
        }

        return $this->controller->redirect(Url::to([Yii::$app->getModule('user')->logoutSuccess]));
    }

}
