<?php

namespace app\modules\user\listeners;

use app\components\MandrillMailer;
use app\models\EmailTemplate;
use app\modules\core\components\IPNormalizer;
use app\modules\core\components\VirtualCurrency;
use app\modules\invitation\models\Invitation;
use app\modules\user\events\UserPasswordRecoveryEvent;
use app\modules\user\models\User;
use app\modules\user\models\UserIpLog;
use Yii;
use yii\helpers\Html;
use \yii\helpers\Url;
use app\modules\user\events\UserRegistrationEvent;

/**
 * Class UserListener
 *
 * @author Stableflow
 */
class UserListener {

    /**
     * After logout event handler
     * @param \app\modules\user\events\UserLogoutEvent $event Description
     */
    public static function onAfterLogout(\app\modules\user\events\UserLogoutEvent $event) {
        
    }

    /**
     * Before logout event handler
     * @param \app\modules\user\events\UserLogoutEvent $event Description
     */
    public static function onBeforeLogout(\app\modules\user\events\UserLogoutEvent $event) {
        
    }

    /**
     * Before login event handler
     * @param \app\modules\user\events\UserLoginEvent $event Description
     */
    public static function onBeforeLogin(\app\modules\user\events\UserLoginEvent $event) {
        
    }

    /**
     * Success login event handler
     * @param \app\modules\user\events\UserLoginEvent $event Description
     */
    public static function onSuccessLogin(\app\modules\user\events\UserLoginEvent $event) {
        
    }
    
    public static function onSuccessAutoLogin(\yii\web\UserEvent $event) {
        $userID = Yii::$app->user->id;

        $ip = (new IPNormalizer())->getIP();

        if ($ip) {
            UserIpLog::add($userID, $ip);
        }
    }

    /**
     * Failure login event handler
     * @param \app\modules\user\events\UserLoginEvent $event Description
     */
    public static function onFailureLogin(\app\modules\user\events\UserLoginEvent $event) {
        
    }

    public static function onBeforePasswordRecovery(\app\modules\user\events\UserPasswordRecoveryEvent $event) {
        
    }

    public static function onSuccessPasswordRecovery(\app\modules\user\events\UserPasswordRecoveryEvent $event) {
        $user = $event->getUser();
        $token = $event->getToken();
        $mandrillMailer = \Yii::$app->get('mandrillMailer');
        /* @var MandrillMailer $mandrillMailer */

        $mandrillMailer->addToQueue(
            $user->email,
            EmailTemplate::TEMPLATE_USER_PASSWORD_RECOVERY, [
            'link' => Html::a('Link', Url::toRoute([
                $event->getUser()->role == User::ROLE_ADMIN ? '/user/account/back-recovery-reset' : '/user/account/recovery-reset',
                'code' => $event->getToken()->code
            ], true), [
                'target' => '_blank',
                'style' => 'word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #2A9AE7;font-weight: bold;text-decoration: none;',
            ]),
            'username' => $event->getUser()->username
        ]);
    }

    public static function onFailurePasswordRecovery(UserPasswordRecoveryEvent $event) {
        
    }

    public static function onBeforePasswordRecoveryReset(\app\modules\user\events\UserPasswordRecoveryResetEvent $event) {
        
    }

    public static function onSuccessPasswordRecoveryReset(\app\modules\user\events\UserPasswordRecoveryResetEvent $event) {

    }

    public static function onFailurePasswordRecoveryReset(\app\modules\user\events\UserPasswordRecoveryResetEvent $event) {

    }

    /**
     * Success registration event handler
     *
     * @param UserRegistrationEvent $event
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function onSuccessRegistration(UserRegistrationEvent $event) {
        $user   = $event->getUser();
        $token  = $event->getToken();
        $form   = $event->getForm();

        $mandrillMailer = \Yii::$app->mandrillMailer;
        $keyStorage = \Yii::$app->keyStorage;
        $virtualCurrency = \Yii::$app->virtualCurrency;

        if ($user->status !== User::STATUS_PENDING) {
            return false;
        }

        $mandrillMailer->addToQueue(
            $user->email,
            EmailTemplate::TEMPLATE_REGISTER_CONFIRMATION, [
            'username' => $user->username,
            'confirmation_link' => Html::a('Confirmation link', Url::toRoute(['/user/account/activate', 'token' => $token->code], true), [
                'target' => '_blank',
                'style' => 'word-wrap: break-word;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #2A9AE7;font-weight: bold;text-decoration: none;',
            ]),
        ]);

        $referralCode = $form->referralCode;
        $referralPercents = $keyStorage->get('referral_percents');

        if (!empty($referralCode) && (($sourceUser = User::getUserByReferralCode($referralCode)) !== null) && (int)$referralPercents > 0) {
            $mandrillMailer->addToQueue(
                $sourceUser->email,
                EmailTemplate::TEMPLATE_REGISTER_REFERRAL_BONUS, [
                'source_username' => $sourceUser->username,
                'target_username' => $user->username,
                'referral_percents' => $referralPercents
            ]);
        }

        // Free points crediting
        $freePointsAmount = $keyStorage->get('free_points_register');

        if (!empty($freePointsAmount) && $freePointsAmount > 0) {
            $virtualCurrency->setUser($user);
            $virtualCurrency->crediting($freePointsAmount);
        }
    }

    /**
     * Failure registration event handler
     * @param \app\modules\user\events\UserRegistrationEvent $event Description
     */
    public static function onFailureRegistration(\app\modules\user\events\UserRegistrationEvent $event) {
        
    }

    /**
     * Success activate event handler
     * @param \app\modules\user\events\UserRegistrationEvent $event Description
     */
    public static function onSuccessActivateAccount(\app\modules\user\events\UserActivateEvent $event) {
        $token = $event->getToken();
        $user = $event->getUser();

        if ($inv = Invitation::find()->email($user->email)->status(Invitation::STATUS_APPROVED)->one()) {
            $inv->delete();
        }

//        $token->delete();
    }

    /**
     * Failure activate event handler
     * @param \app\modules\user\events\UserRegistrationEvent $event Description
     */
    public static function onFailureActivateAccount(\app\modules\user\events\UserActivateEvent $event) {
        
    }
    
}
