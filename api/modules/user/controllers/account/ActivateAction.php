<?php

namespace app\modules\user\controllers\account;

use Yii;
use yii\base\Action;

/**
 * Class ActivateAction
 *
 * @author Stableflow
 */
class ActivateAction extends Action
{
    public function run($token)
    {
        if (Yii::$app->userManager->activateUser($token)) {
            Yii::$app->session->setFlash(
                    'success', Yii::t('user', 'You have successfully activated the account.')
            );

            return $this->controller->redirect('/');
        }

        Yii::$app->session->setFlash(
                'error', Yii::t('user', 'There was a problem with the activation of the account. Please refer to the site\'s administration.')
        );

        return  $this->controller->redirect(['/user/account/login']);
    }

}
