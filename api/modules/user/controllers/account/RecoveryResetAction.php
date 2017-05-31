<?php

namespace app\modules\user\controllers\account;

use Yii;
use yii\base\Action;
use app\modules\user\forms\RecoveryForm;
use app\modules\user\models\Token;

/**
 * Class RecoveryResetAction
 *
 * @author Stableflow
 */
class RecoveryResetAction extends Action {

    public $layout;

    public function run($code)
    {
        $tokenModel = Yii::$app->userManager->tokenStorage->get($code, Token::TYPE_CHANGE_PASSWORD);

        if (null === $tokenModel) {
            throw new \yii\web\NotFoundHttpException();
        }

        $form = new RecoveryForm([
            'scenario' => RecoveryForm::RESET_SCENARIO
        ]);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            if (Yii::$app->userManager->resetPassword($code, $form->password)) {
                Yii::$app->session->setFlash(
                    'success', Yii::t('user', 'Password recover successfully')
                );
                return $this->controller->redirect(['/user/account/login']);
            } else {
                Yii::$app->session->setFlash(
                    'error', Yii::t('user', 'Error when changing password!')
                );
            }
        }

        if (!empty($this->layout)) {
            $this->controller->layout = $this->layout;
        }

        return $this->controller->render($this->id, [
            'model' => $form
        ]);
    }

}
