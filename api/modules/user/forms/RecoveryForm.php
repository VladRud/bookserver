<?php

namespace app\modules\user\forms;

use Yii;
use yii\base\Model;
use app\modules\user\models\User;
use app\modules\user\models\Token;

/**
 * Class RecoveryForm
 *
 * @author Stableflow
 */
class RecoveryForm extends Model {

    const REQUEST_SCENARIO = 'request';
    const RESET_SCENARIO = 'reset';

    public $reCaptcha;
    public $email;
    public $password;
    public $confirmPassword;
    protected $_user;

    /** @inheritdoc */
    public function scenarios() {
        return [
            static::REQUEST_SCENARIO => ['email', 'reCaptcha'],
            static::RESET_SCENARIO => ['password', 'confirmPassword']
        ];
    }

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',                'targetClass' => User::className(),
                'message' => Yii::t('user', 'There is no user with this email address')
            ],
            ['email', function ($attribute) {
                $userModel = new User();
                $this->_user = $userModel->findUserByEmail($this->email);
                if ($this->_user !== null && !$this->_user->getIsConfirmed()) {
                    $this->addError($attribute, \Yii::t('user', 'You need to confirm your email address'));
                }
            }],
            ['email', 'trim'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6, 'max' => 64],
            ['confirmPassword', 'required'],
            ['confirmPassword', 'string', 'min' => 6, 'max' => 64],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password'],
            [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'secret' => Yii::$app->params['reCaptchaSecretKey'], 'uncheckedMessage' => 'Please confirm that you are not a bot.']
        ];
    }

    /** @inheritdoc */
    public function attributeLabels() {
        return [
            'email' => Yii::t('user', 'Email'),
            'password' => Yii::t('user', 'Password'),
            'confirmPassword' => Yii::t('user', 'Confirm password'),
        ];
    }
}
