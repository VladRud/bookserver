<?php

namespace api\modules\user\forms;

use Yii;
use yii\base\Model;
use api\modules\user\models\User;
use api\modules\user\helpers\Password;
use yii\web\IdentityInterface;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model {

    public $username;
    public $password;
    public $rememberMe = true;
    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            // Username
            [['username'], 'required'],
            [['username'], 'trim'],

            // Password
            [['password'], 'validatePassword'],
            [['password'], 'required'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params) {
        $user = $this->getUser();
        if (!$this->hasErrors()) {
            if (!$user || (!Password::validate($this->password, $user->password))) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }
    
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser() {
        if ($this->_user === false) {
            $user = new User();
            $this->_user = $user->findUserByUsernameOrEmail($this->username);
        }

        return $this->_user;
    }

    public function setUser(IdentityInterface $user)
    {
        $this->_user = $user;
    }
}
