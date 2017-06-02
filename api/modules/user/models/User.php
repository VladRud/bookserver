<?php

namespace api\modules\user\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use api\modules\user\helpers\Password;
use app\modules\user\models\UserMeta;
use yii\helpers\Url;

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
     * Register new user
     * @param User $referral
     * @return boolean
     */
    public function register($referral = null) {
        $this->status = static::STATUS_APPROVED;
        if ($this->save()) {
            if (null !== $referral) {
                Referral::linkReferral($referral->id, $this->id);
            }
            Yii::$app->user->login($this);
            return true;
        }
        return false;
    }

}
