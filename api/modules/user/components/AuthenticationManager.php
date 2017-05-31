<?php

namespace app\modules\user\components;

use \Yii;
use \app\modules\user\models\User;
use \app\modules\user\forms\LoginForm;
use \app\modules\user\events\UserEvents;
use \app\modules\user\events\UserLoginEvent;
use \app\modules\user\events\UserLogoutEvent;
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

    public function login(LoginForm $form, $request = null)
    {
        $user = $form->getUser();

        if ($form->hasErrors()) {
            Yii::$app->eventManager->fire(UserEvents::FAILURE_LOGIN, new UserLoginEvent($form, $form->getUser()));
            return false;
        }

        if (!$user) {
            $form->addError('email', Yii::t('user', 'The username and password you entered did not match our records. Please double-check and try again.'));
            Yii::$app->eventManager->fire(UserEvents::FAILURE_LOGIN, new UserLoginEvent($form));
        }

        switch ($user->status) {
            case User::STATUS_BLOCKED:
                Yii::$app->session->setFlash('error', 'Your account was blocked.');
                return false;
                break;
            case User::STATUS_PENDING:
                Yii::$app->session->setFlash('error', 'Please activate your account.');
                return false;
                break;
            case User::STATUS_APPROVED:
                Yii::$app->eventManager->fire(UserEvents::BEFORE_LOGIN, new UserLoginEvent($form, $user));
                Yii::$app->getUser()->login($user, $form->rememberMe ? strtotime('+ 1 year', time()) - time() : 0);
                Yii::$app->eventManager->fire(UserEvents::SUCCESS_LOGIN, new UserLoginEvent($form, $user));
                return true;
                break;
        }

        return false;
    }

}
