<?php

namespace api\models;

use common\models\Token;
use common\models\User;
use yii\base\Model;

/**
 * Registration form
 */
class RegistrationForm extends Model
{
    public $username;
    public $password;
    public $email;
    public $create_at;
    public $password_hash;

    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
        ];
    }
}
