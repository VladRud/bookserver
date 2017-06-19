<?php

namespace api\modules\user\forms;

use yii\base\Model;
use api\modules\user\models\User;

/**
 * RegisterForm is the model behind the register form.
 */
class RegistrationForm extends Model
{
    public $username;
    public $email;
    public $password;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            ['username', 'required', 'message' => 'required'],
            ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@]+$/', 'message' => 'valid'],
            ['username', 'string', 'min' => 3, 'message' => 'minlength'],
            ['username', 'string', 'max' => 25, 'message' => 'maxlength'],
            ['username', 'unique', 'targetClass' => User::className(), 'message' => 'unique'],

            ['email', 'required', 'message' => 'required'],
            ['email', 'email', 'message' => 'valid'],
            ['email', 'string', 'max' => 100, 'message' => 'max'],
            ['email', 'trim'],
            ['email', 'unique', 'targetClass' => User::className(), 'message' => 'unique'],

            ['password', 'required', 'message' => 'required'],
        ];
    }

}
