<?php

namespace app\modules\user\components;

use \Yii;
use \api\modules\user\models\User;
use \api\modules\user\forms\LoginForm;
use \api\modules\user\events\UserEvents;
use \api\modules\user\events\UserLoginEvent;
use \api\modules\user\events\UserLogoutEvent;
use yii\base\Component;
use yii\web\IdentityInterface;

/**
 * Class AuthenticationManager
 *
 * @author Stableflow
 */
class AuthenticationManager extends Component
{
    public function logout(IdentityInterface $user)
    {
        Yii::$app->eventManager->fire(UserEvents::BEFORE_LOGOUT, new UserLogoutEvent($user));
        Yii::$app->getUser()->logout();
        Yii::$app->eventManager->fire(UserEvents::AFTER_LOGOUT, new UserLogoutEvent($user));

        return true;
    }

    public function login(LoginForm $form)
    {
        $user = $form->getUser();




        return false;
    }

}
