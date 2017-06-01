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
            ['username', 'required'],
            ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@]+$/'],
            ['username', 'string', 'min' => 3, 'max' => 60],
            ['username', 'unique', 'targetClass' => User::className()],

            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 100],
            ['email', 'trim'],
            ['email', 'unique', 'targetClass' => User::className()],

            ['password', 'required'],
        ];
    }

}
