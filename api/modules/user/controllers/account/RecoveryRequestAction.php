<?php

namespace app\modules\user\controllers\account;

use Yii;
use yii\base\Action;
use app\modules\user\forms\RecoveryForm;

/**
 * Class RecoveryRequestAction
 *
 * @author Stableflow
 */
class RecoveryRequestAction extends Action
{
    public $layout;

    public function run()
    {
        $form = new RecoveryForm([
            'scenario' => RecoveryForm::REQUEST_SCENARIO
        ]);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            if (Yii::$app->userManager->passwordRecovery($form->email)) {
                Yii::$app->session->setFlash('success', Yii::t('user', 'Check out your email to continue!'));
                return $this->controller->redirect(['/user/account/login']);
            }

            Yii::$app->session->setFlash('error', Yii::t('user', 'Password recovery error.'));
            return $this->controller->redirect(['/user/account/recovery-request']);
        }

        if (!empty($this->layout)) {
            $this->controller->layout = $this->layout;
        }

        return $this->controller->render($this->id, [
            'model' => $form
        ]);
    }

}
