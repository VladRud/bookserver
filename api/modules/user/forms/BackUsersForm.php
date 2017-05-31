<?php

namespace app\modules\user\forms;

use Yii;
use app\modules\user\models\User;
use yii\base\Security;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use app\modules\user\helpers\Password;

/**
 * This is the model class for table "sf_users".
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property integer $role
 * @property string $password
 * @property string $referral_code
 * @property string $create_date
 * @property integer $status
 * @property string $update_date
 * @property string $first_name
 * @property string $last_name
 * @property string $confirmPassword
 * @property string $newPassword
 *
 * @property Token[] $tokens
 * @property UserMeta[] $userMetas
 */
class BackUsersForm extends User
{

    public $confirmPassword;

    const SIGNUP_SCENARIO = 'signup';
    const EDIT_SCENARIO = 'edit';

    public function scenarios() {
        return [
            self::SIGNUP_SCENARIO => ['username', 'email', 'newPassword', 'confirmPassword', 'role', 'status', 'last_name', 'first_name'],
            self::EDIT_SCENARIO => ['username', 'email', 'role', 'status', 'last_name', 'first_name']
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_date','update_date'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update_date',
                ],
                'value' => function ($event) {
                    return gmdate('Y-m-d H:i:s', time());
                },
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role', 'status'], 'required'],
            [['role', 'status'], 'integer'],

            [['create_date', 'update_date', 'confirmPassword', 'newPassword'], 'safe'],

            ['username', 'required'],
            ['username', 'unique'],
            ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@]+$/'],
            ['username', 'string', 'min' => 3, 'max' => 60],
            ['username', 'trim'],

            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 100],
            ['email', 'unique'],
            ['email', 'trim'],

            [['password'], 'string', 'max' => 64, 'min' => 3],
            [['newPassword'], 'validateNewPassword'],
            ['confirmPassword', 'compare', 'compareAttribute' => 'newPassword'],

            [['referral_code'], 'string', 'max' => 12],
            [['referral_code'], 'unique'],

            [['first_name', 'last_name'], 'string', 'max' => 255],

        ];
    }

    public function validateCurrentPassword($attribute, $params){
        if (!$this->hasErrors()) {
            if(!Password::validate($this->$attribute, Yii::$app->user->identity->password)){
                $this->addError($attribute, 'Incorrect password.');
            }
        }
    }

    public function validateNewPassword($attribute, $params){
        if (!$this->hasErrors()) {
            $this->password = Password::hash($this->$attribute);
//            if(empty($this->currentPassword)){
//                $this->addError('currentPassword', "Current Password cannot be blank.");
//            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'role' => 'Role',
            'password' => 'Password',
            'referral_code' => 'Referral Code',
            'create_date' => 'Create Date',
            'status' => 'Status',
            'update_date' => 'Update Date',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
        ];
    }
}
