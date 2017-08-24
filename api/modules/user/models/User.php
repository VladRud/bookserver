<?php

namespace api\modules\user\models;

use yii\db\ActiveRecord;
use api\modules\user\helpers\Password;
use common\helpers\DateHelper;
use api\modules\user\models\Token;

/**
 * Description of User
 *
 * @author VladRud
 * 
 * Database fields:
 * @property integer $id
 * @property string  $username
 * @property string  $email
 * @property string  $password
 * @property string  $password_reset_token
 * @property string  $created_at
 * @property integer $status
 * @property string  $updated_at
 *
 */
class User extends ActiveRecord
{
    const SCENARIO_REGISTER = 'register';

    const STATUS_PENDING    = 0;
    const STATUS_APPROVED   = 1;
    const STATUS_BLOCKED    = 2;

    protected $metaData;

    /** @inheritdoc */
    public static function tableName() {
        return '{{%user}}';
    }

    /** @inheritdoc */
    public function scenarios() {
        return [
            static::SCENARIO_REGISTER               => ['username', 'email', 'password', 'created_at', 'status'],
        ];
    }

    /** @inheritdoc */
    public function rules() {
        return [

            // Username
            ['username', 'required'],
            ['username', 'unique'],
            ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@]+$/'],
            ['username', 'string', 'min' => 3, 'max' => 60],
            ['username', 'trim'],

            // Email
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 100],
//            ['email', 'unique', 'except' => static::SCENARIO_REGISTER_TEMP_OAUTH],
            ['email', 'trim'],

            // Password
            ['password', 'required'],
            ['password', 'string', 'min' => 6, 'max' => 64],
            
            // Status
            [['status'], 'integer'],
        ];
    }

    /**
     * Finds a user by the given username or email.
     *
     * @param  string      $usernameOrEmail Username or email to be used on search.
     * @return User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail) {
        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->findUserByEmail($usernameOrEmail);
        }
        return $this->findUserByUsername($usernameOrEmail);
    }

    public function getToken()
    {
        return $this->hasMany(Token::className(), ['user_id' => 'id']);
    }

    /**
     * Finds a user by the given email.
     *
     * @param  string      $email Email to be used on search.
     * @return User
     */
    public function findUserByEmail($email) {
        return self::findOne(['email' => $email]);
    }

    /**
     * Finds a user by the given username.
     *
     * @param  string      $username Username to be used on search.
     * @return User
     */
    public function findUserByUsername($username) {
        return self::findOne(['username' => $username]);
    }

    public function generateToken() {

    }

}
