<?php

namespace api\modules\user\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use app\modules\user\helpers\Password;
use app\modules\user\models\UserMeta;
use yii\helpers\Url;

/**
 * Description of User
 *
 * @author Stableflow
 * 
 * Database fields:
 * @property integer $id
 * @property string  $username
 * @property integer $auth_key
 * @property string  $email
 * @property integer $role
 * @property string  $password
 * @property string  $create_date
 * @property string  $referral_code
 * @property integer $status
 * @property string  $first_name
 * @property string  $last_name
 * @property string  $birthday
 * @property string  $gender
 * @property string  $virtual_currency
 *
 * @property User $referrals
 * @property User $sourceReferral
 * @property Token $token
 * @property AuthSocial $authSocial
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const ROLE_ADMIN        = 1;
    const ROLE_USER         = 2;
    const ROLE_PARTNER      = 3;
    const ROLE_MOBILE_USER  = 4;
    
    const STATUS_PENDING    = 0;
    const STATUS_APPROVED   = 1;
    const STATUS_BLOCKED    = 2;
    const STATUS_TRANSFER   = 3;
    const STATUS_BLACKLIST  = 4;
    const STATUS_TEMP       = 5;

    const SCENARIO_REGISTER             = 'register';
    const SCENARIO_REGISTER_TEMP_OAUTH  = 'register_temp_oauth';
    const SCENARIO_REGISTER_OAUTH       = 'register_oauth';
    const SCENARIO_UPDATE_STATUS        = 'update_status';
    const SCENARIO_UPDATE_LOGIN         = 'update_login';
    const SCENARIO_UPDATE_PASSWORD      = 'update_password';
    const SCENARIO_UPDATE_PERSONAL      = 'update_personal';
    const SCENARIO_UPDATE_CURRENCY      = 'update_currency';

    protected $metaData;
    public $newPassword;

    const MALE = 1;
    const FEMALE = 2;


    /** @inheritdoc */
    public static function tableName() {
        return '{{%users}}';
    }

    /** @inheritdoc */
    public function attributeLabels() {
        return [
            'username'          => Yii::t('app', 'Username'),
            'email'             => Yii::t('app', 'Email'),
            'role'              => Yii::t('app', 'Role'),
            'password'          => Yii::t('app', 'Password'),
            'create_date'       => Yii::t('app', 'Registration time'),
            'status'            => Yii::t('app', 'Status'),
        ];
    }

    /** @inheritdoc */
    public function scenarios() {
        return [
            static::SCENARIO_REGISTER               => ['username', 'email', 'first_name', 'last_name', 'role', 'password',
                                                        'create_date', 'status', 'gender', 'birthday'],
            static::SCENARIO_REGISTER_TEMP_OAUTH    => ['first_name', 'last_name', 'role', 'create_date', 'status', 'gender'],
            static::SCENARIO_REGISTER_OAUTH         => ['username', 'email', 'first_name', 'last_name', 'role',
                                                        'create_date', 'status', 'password'],
            static::SCENARIO_UPDATE_STATUS          => ['status'],
            static::SCENARIO_UPDATE_LOGIN           => ['username', 'email'],
            static::SCENARIO_UPDATE_PASSWORD        => ['password'],
            static::SCENARIO_UPDATE_PERSONAL        => ['first_name', 'last_name', 'birthday', 'gender'],
            static::SCENARIO_UPDATE_CURRENCY        => ['virtual_currency'],
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
            ['email', 'unique', 'except' => static::SCENARIO_REGISTER_TEMP_OAUTH],
            ['email', 'trim'],

            // Password
            ['password', 'required'],
            ['password', 'string', 'min' => 6, 'max' => 64],
            
            // Status
            [['status'], 'integer'],

            // Role
            [['role'], 'integer'],

            // Meta
            [['first_name', 'last_name'], 'string', 'max' => 255],
            [['first_name', 'last_name'], 'trim'],
            [['gender'], 'in', 'range' => [User::MALE, User::FEMALE]],
            ['birthday', 'date', 'format' => 'Y-m-d'],

            // Virtual Currency
            ['virtual_currency', 'number']      // TODO: Right decimal format
        ];
    }
    
    /** @inheritdoc */
    public function beforeValidate() {
        if (!empty($this->newPassword)) {
            $this->password = $this->newPassword;
        }
        return parent::beforeValidate();
    }

    public function getQueueMail()
    {
        return $this->hasOne(QueueMail::className(), ['user_id' => 'id']);
    }

    public function getToken()
    {
        return $this->hasOne(Token::className(), ['user_id' => 'id']);
    }

    /** @inheritdoc */
    public function getId() {
        return $this->getAttribute('id');
    }

    /** @inheritdoc */
    public function getAuthKey() {
        return $this->getAttribute('auth_key');
    }

    /** @inheritdoc */
    public function validateAuthKey($authKey) {
        return $this->getAttribute('auth_key') == $authKey;
    }

    /** @inheritdoc */
    public static function findIdentity($id) {
        return static::findOne($id);
    }

    /** @inheritdoc */
    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @return boolean Whether the user is blocked or not.
     */
    public function getIsBlocked() {
        return $this->status == self::STATUS_BLOCKED;
    }

    /**
     * @return boolean Whether the user is approved or not.
     */
    public function getIsApproved() {
        return $this->status == self::STATUS_APPROVED;
    }
    
    /**
     * @return boolean Whether the user is approved or not.
     */
    public function getIsBlacklist() {
        return $this->status == self::STATUS_BLACKLIST;
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

    /**
     * Get status list
     * @return array
     */
    public static function getStatusList() {
        return [
            static::STATUS_APPROVED     => Yii::t('app', 'Approved'),
            static::STATUS_BLOCKED      => Yii::t('app', 'Blocked'),
            static::STATUS_PENDING      => Yii::t('app', 'Pending'),
            static::STATUS_TRANSFER     => Yii::t('app', 'Transfer'),
            static::STATUS_BLACKLIST    => Yii::t('app', 'Blacklist'),
        ];
    }

    /**
     * Get status
     * @param boolean $html 
     * @return string 
     */
    public function getStatus($html = false) {
        $data = $this->getStatusList();
        if (isset($data[$this->status])) {
            if (false !== $html) {
                switch ($this->status) {
                    case static::STATUS_APPROVED :
                        $status = 'success';
                        break;
                    case static::STATUS_BLOCKED:
                        $status = 'danger';
                        break;
                    case static::STATUS_PENDING:
                        $status = 'info';
                        break;
                    case static::STATUS_TRANSFER:
                        $status = 'primary';
                        break;
                    case static::STATUS_BLACKLIST:
                        $status = 'default';
                        break;
                }
                return "<span class=\"label label-sm label-$status\">{$data[$this->status]}</span>";
            }
            return $data[$this->status];
        }

        return 'unknown';
    }

    /**
     * Get role list
     * @return array
     */
    public static function getRoleList() {
        return [
            static::ROLE_USER   => Yii::t('app', 'User'),
            static::ROLE_ADMIN  => Yii::t('app', 'Admin'),
        ];
    }

    /**
     * Get role
     * @return string Description
     */
    public function getRoles() {
        $roles = static::getRoleList();

        if (isset($roles[$this->role]))
            return $roles[$this->role];

        return 'unknown';
    }

    /**
     * Resets password.
     * 
     * @param string $password
     * @return boolean
     */
    public function resetPassword($password) {
        return (bool) $this->updateAttributes(['password' => Password::hash($password)]);
    }

    /**
     * @return bool Whether the user is confirmed or not.
     */
    public function getIsConfirmed() {
        return $this->status !== static::STATUS_PENDING;
    }

    /**
     * Get user by referral code
     * @param string $code 
     * @return User
     */
    public static function getUserByReferralCode($code) {
        return self::findOne(['referral_code' => $code]);
    }

    /**
     * Register new user
     * @param User $referral
     * @return boolean
     */
    public function register($referral = null) {
        $this->role = static::ROLE_USER;
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

    /**
     * @return string
     */
    public function getReturnUrl() {
        switch ($this->role) {
            case static::ROLE_ADMIN:
                $url = Url::toRoute(['/dashboard/index-backend/index']);
                break;
            case static::ROLE_USER:
            case static::ROLE_MOBILE_USER:
            default :
                $url = Url::toRoute(['/profile/offer/wall']);
                break;
        }

        return $url;
    }

    /**
     */
    public function active() {
        return $this->andWhere('status != :status', [':status' => static::STATUS_APPROVED]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeta(){
        return $this->hasMany(UserMeta::className(), ['user_id' => 'id']);
    }
    
    /** @inheritdoc */
    public function afterFind() {
        $this->metaData = new \stdClass();
        foreach ($this->meta as $key => $value) {
            $this->metaData->{$value->meta_key} = $value->meta_value;
        }
    }
    
    /**
     * Get user meta data
     * @return object
     */
    public function getMetaData() {
        return $this->metaData;
    }

    public function getOauthTempEmail() {
        return isset($this->metaData->oauth_temp_mail) ? $this->metaData->oauth_temp_mail : null;
    }

    public function getAvatar() {
        return isset($this->metaData->avatar) ? $this->metaData->avatar : null;
    }
    
    public function getAbout() {
        return isset($this->metaData->about) ? $this->metaData->about : null;
    }
    
    public function getName() {
        return isset($this->last_name, $this->first_name) ? "{$this->last_name}  {$this->first_name}" : $this->username;
    }
    
    public function getPhone() {
        return isset($this->metaData->phone) ? $this->metaData->phone : null;
    }
    
    public function getInterests() {
        return isset($this->metaData->interests) ? $this->metaData->interests : null;
    }

    public static function getGender(){
        return [
            static::MALE => Yii::t('app', 'Male'),
            static::FEMALE => Yii::t('app', 'Female')
        ];
    }

    public function getGenderName()
    {
        $list = static::getGender();

        switch ($this->gender) {
            case static::MALE:
                return $list[static::MALE];
            case static::FEMALE:
                return $list[static::FEMALE];
            default:
                return 'unknown';
        }
    }

    public function getReferrals() {
        return $this->hasMany(User::className(), ['id' => 'target_user_id'])
            ->viaTable(Referral::tableName(), ['source_user_id' => 'id']);
    }

    public function getSourceReferral()
    {
        return $this->hasOne(User::className(), ['id' => 'source_user_id'])
            ->viaTable(Referral::tableName(), ['target_user_id' => 'id']);
    }

    public function getAuthSocial()
    {
        return $this->hasOne(AuthSocial::className(), ['user_id' => 'id']);
    }

    public function getVC()
    {
        return number_format($this->virtual_currency, 0);
    }

    public function getExchangedCurrency()
    {
        return bcdiv($this->virtual_currency, 100, 2);
    }
}
