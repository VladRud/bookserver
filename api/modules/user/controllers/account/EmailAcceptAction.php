<?php

namespace app\modules\user\controllers\account;

use app\modules\user\components\TokenStorage;
use app\modules\user\helpers\Password;
use app\modules\user\models\Token;
use Yii;
use yii\base\Action;
use app\modules\user\forms\RegistrationForm;
use yii\web\NotFoundHttpException;

/**
 * Class EmailAcceptAction
 *
 * @author Stableflow
 */
class EmailAcceptAction extends Action
{
    public $layout;

    public function run($token = null)
    {
        $tokenStorage = new TokenStorage();
        if (!$tokenModel = $tokenStorage->get($token, Token::TYPE_OAUTH_TEMP_USER, Token::STATUS_NEW)) {
            throw new NotFoundHttpException();
        }

        $form = new RegistrationForm([
            'scenario' => RegistrationForm::SCENARIO_OAUTH
        ]);

        $user = $tokenModel->user;
        $auth = $user->authSocial;
        $form->getDefaultReferralCode();
        $form->email = $user->getOauthTempEmail();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {

            $form->first_name = $user->first_name;
            $form->last_name = $user->last_name;
            $form->password = Password::hash($form->password);

            $form->externalID = $auth->external_id;
            $form->clientID = $auth->client_id;
            $form->tempUserID = $user->id;

            if ($user = Yii::$app->userManager->createUser($form)) {
                $tokenStorage->deleteByTypeAndUser($tokenModel->type, $user);
                Yii::$app->session->setFlash('success', Yii::t('user', 'Account was created! Check your email!'));
                return $this->controller->redirect('/');
            }

            Yii::$app->session->setFlash('error', Yii::t('user', 'Error creating account!'));
        }

        if (!empty($this->layout)) {
            $this->controller->layout = $this->layout;
        }

        return $this->controller->render('email-accept', [
            'model' => $form,
        ]);
    }

}
