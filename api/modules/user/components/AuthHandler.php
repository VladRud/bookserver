<?php

namespace app\modules\user\components;

use app\modules\user\forms\LoginForm;
use app\modules\user\forms\RegistrationForm;
use app\modules\user\models\AuthSocial;
use app\modules\user\models\Token;
use app\modules\user\models\User;
use yii\authclient\ClientInterface;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class AuthHandler
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handle()
    {
        if (!Yii::$app->getUser()->getIsGuest()) {
            return false;
        }

        $keyStorage = Yii::$app->keyStorage;
        $inviteSignup = $keyStorage->get('invite_only_signup');

        if ($inviteSignup) {
            return Yii::$app->response->redirect('/user/account/invitation-request');
        }

        $clientID = AuthSocial::getClientID($this->client);
        $userAttributes = $this->getUserAttributes($clientID);

        $auth = AuthSocial::find()->with(['user.token'])->where([
            'client_id' => $clientID,
            'external_id' => $userAttributes->externalID,
        ])->one();

        if ($auth) { // login
            $user = $auth->user;
            $oauthTempUserToken = (new TokenStorage())->getUserToken($user, Token::TYPE_OAUTH_TEMP_USER, Token::STATUS_NEW);

            if ($oauthTempUserToken) {
                return Yii::$app->response->redirect(['/user/account/email-accept', 'token' => $oauthTempUserToken->code]);
            }

            $form = new LoginForm();
            $form->username = $user->email;
            $form->setUser($user);

            Yii::$app->authenticationManager->login($form);

            return Yii::$app->response->redirect('/');
        } else { // signup

            $form = new RegistrationForm();
            $form->scenario = RegistrationForm::SCENARIO_OAUTH;
            $form->getDefaultReferralCode();
            $form->first_name   = $userAttributes->firstName;
            $form->last_name    = $userAttributes->lastName;
            $form->email        = $userAttributes->email;
            $form->gender       = $userAttributes->gender;

            // Oauth
            $form->externalID   = $userAttributes->externalID;
            $form->clientID     = $clientID;

            if ($token = Yii::$app->userManager->createTempUser($form)) {
                return Yii::$app->response->redirect(['/user/account/email-accept', 'token' => $token->code]);
            }
        }

        Yii::$app->session->setFlash('error', 'Unexpected errors occurred. Please contact with us');
        return false;
    }

    public function getUserAttributes($clientID)
    {
        $data = new \stdClass();
        $attributes = $this->client->getUserAttributes();

        switch ($clientID) {
            case AuthSocial::CLIENT_ID_FACEBOOK:
                $data->externalID   = ArrayHelper::getValue($attributes, 'id');
                $data->email        = ArrayHelper::getValue($attributes, 'email');
                $data->firstName    = ArrayHelper::getValue($attributes, 'first_name');
                $data->lastName     = ArrayHelper::getValue($attributes, 'last_name');
                $data->gender       = ArrayHelper::getValue($attributes, 'gender') == 'female' ? User::FEMALE : User::MALE;
                break;
            case AuthSocial::CLIENT_ID_TWITTER:
                $data->externalID   = ArrayHelper::getValue($attributes, 'id_str');
                $data->email        = ArrayHelper::getValue($attributes, 'email');
                $data->firstName    = null;
                $data->lastName     = null;
                $data->gender       = null;
                break;
            case AuthSocial::CLIENT_ID_GOOGLE:
                $data->externalID   = ArrayHelper::getValue($attributes, 'id');
                $data->email        = ArrayHelper::getValue($attributes, 'emails.0.value');
                $data->firstName    = ArrayHelper::getValue($attributes, 'name.givenName');
                $data->lastName     = ArrayHelper::getValue($attributes, 'name.familyName');
                $data->gender       = ArrayHelper::getValue($attributes, 'gender') == 'female' ? User::FEMALE : User::MALE;
                break;
            default:
                throw new InvalidConfigException('Wrong client id');
        }

        return $data;
    }
}
