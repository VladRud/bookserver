<?php

namespace app\modules\user\forms;

use app\modules\user\models\Referral;
use Yii;
use yii\base\Model;
use app\modules\user\models\User;
use app\modules\invitation\models\Invitation;

/**
 * RegisterForm is the model behind the register form.
 */
class RegistrationForm extends Model
{
    const SCENARIO_SIGNUP = 'signup';
    const SCENARIO_INVITATION = 'inviteOnly';
    const SCENARIO_INVITATION_REQUEST = 'invitationRequest';
    const SCENARIO_OAUTH = 'oauth';

    public $username;
    public $email;
    public $password;
    public $confirmPassword;
    public $invitationCode;
    public $referralCode;

    public $first_name;
    public $last_name;
    public $birthday;
    public $gender;

    public $reCaptcha;
    public $terms;

    // Oauth
    public $externalID;
    public $clientID;
    public $tempUserID;

    public $isWidget;

    protected $referralUser;

    public function scenarios() {
        return [
            static::SCENARIO_SIGNUP                 => ['username', 'gender', 'birthday', 'email', 'password',
                                                        'confirmPassword', 'first_name', 'last_name', 'referralCode',
                                                        'isWidget', 'reCaptcha', 'terms'],
            static::SCENARIO_INVITATION             => ['username', 'gender', 'birthday', 'email', 'password',
                                                        'confirmPassword', 'first_name', 'last_name', 'reCaptcha',
                                                        'referralCode', 'invitationCode'],
            static::SCENARIO_INVITATION_REQUEST     => ['email', 'reCaptcha'],
            static::SCENARIO_OAUTH                  => ['email', 'username', 'reCaptcha'],
        ];
    }

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [

            // Username
            ['username', 'required'],
            ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@]+$/'],
            ['username', 'string', 'min' => 3, 'max' => 60],
            ['username', 'unique', 'targetClass' => User::className()],
            ['username', 'trim'],

            // Email
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 100],
            ['email', 'unique', 'targetClass' => User::className()],
            ['email', 'unique', 'targetClass' => Invitation::className(), 'on' => static::SCENARIO_INVITATION_REQUEST],
            ['email', 'trim'],

            // Password
            ['password', 'required'],
            ['password', 'string', 'min' => 6, 'max' => 64],

            // Confirm Password
            ['confirmPassword', 'required'],
            ['confirmPassword', 'string', 'min' => 6, 'max' => 64],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password'],

            // Meta
            [['first_name', 'last_name'], 'string', 'max' => 255],
            [['first_name', 'last_name'], 'trim'],
            [['gender'], 'in', 'range' => [User::MALE, User::FEMALE]],
            ['birthday', 'date', 'format' => 'Y-m-d'],

            // Invite code
            ['invitationCode', 'required'],
            ['email', 'validateInvitationCode', 'on' => self::SCENARIO_INVITATION],

            // Referral code
            ['referralCode', 'validateReferralCode', 'skipOnEmpty' => true],

            // Captcha
            [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'secret' => Yii::$app->params['reCaptchaSecretKey'], 'uncheckedMessage' => 'Please confirm that you are not a bot.'],

            // Terms
            ['terms', 'required', 'requiredValue' => 1, 'message' => 'Terms are required'],
        ];
    }

    public function attributeLabels() {
        return [
            'username' => Yii::t('user', 'Username'),
            'email' => Yii::t('user', 'Email'),
            'password' => Yii::t('user', 'Password'),
            'confirmPassword' => Yii::t('user', 'Confirm Password'),
            'invitationCode' => Yii::t('user', 'Invitation Code'),
            'referralCode' => Yii::t('user', 'Referral Code'),
        ];
    }

    /**
     * Referral code validation
     */
    public function validateReferralCode($attribute, $params) {
        if (null === $this->referralUser = User::getUserByReferralCode($this->{$attribute})) {
            $this->addError($attribute, Yii::t('app', 'Incorrect referral code'));
        }
    }

    /**
     * Invite code validation
     */
    public function validateInvitationCode($attribute, $params) {
        if (false === Invitation::checkInvite($this->{$attribute}, $this->invitationCode)) {
            $this->addError($attribute, Yii::t('app', 'Incorrect email for specified code'));
        }
    }

    public function getGender () {
        return User::getGender();
    }

    /**
     * After the referral link App stores referral code in cookie and then could auto put code in form
     *
     * @return mixed
     */
    public function getDefaultReferralCode()
    {
        $cookies = Yii::$app->request->cookies;
        return $this->referralCode = $cookies->getValue(Referral::COOKIES_REQUEST_ID, null);
    }
}
