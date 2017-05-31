<?php

namespace app\modules\user\forms;

use Yii;
use yii\base\Model;
use app\modules\user\models\User;
use app\modules\user\helpers\Password;

/**
 * LoginForm is the model behind the login form.
 */
class ProfileForm extends Model {

    const SCENARIO_CHANGE_PERSONAL_INFO = 'scenario_change_personal_info';
    const SCENARIO_CHANGE_AVATAR = 'scenario_change_avatar';
    const SCENARIO_CHANGE_PASSWORD = 'scenario_change_password';

    public $firstName;
    public $lastName;
    public $phone;
    public $about;
    public $interests;
    
    public $fileAvatar;
    
    public $currentPassword;
    public $newPassword;
    public $confirmPassword;
    
    /** @inheritdoc */
    public function scenarios() {
        return\yii\helpers\ArrayHelper::merge([
                static::SCENARIO_CHANGE_AVATAR => ['fileAvatar'],
                static::SCENARIO_CHANGE_PERSONAL_INFO => ['firstName', 'lastName', 'phone', 'about', 'interests'],
                static::SCENARIO_CHANGE_PASSWORD => ['currentPassword', 'newPassword', 'confirmPassword'],
            
            ], parent::scenarios());
    }
    
    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['firstName', 'lastName', 'phone', 'about', 'interests', 'fileAvatar', 'currentPassword', 'newPassword', 'confirmPassword'], 'required'],
            [['firstName', 'lastName'], 'trim'],
            [['currentPassword'], 'validateCurrentPassword'],
            [['newPassword'], 'validateNewPassword'],
            ['confirmPassword', 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    public function attributeLabels() {
        return[
            'lastName'          => Yii::t('user', 'Last name'),
            'firstName'         => Yii::t('user', 'First name'),
            'currentPassword'   => Yii::t('user', 'Current Password'),
            'newPassword'       => Yii::t('user', 'New Password'),
            'confirmPassword'   => Yii::t('user', 'Re-type New Password'),
            'phone'             => Yii::t('user', 'Phone'),
            'about'             => Yii::t('user', 'About'),
            'interests'         => Yii::t('user', 'Interests'),
            
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
            if(empty($this->currentPassword)){
                $this->addError('currentPassword', "Current Password cannot be blank.");
            }
        }
    }

    public function updateProfile($user){
        $user->newPassword = $this->newPassword;
        
        if($user->save()){
            \app\modules\user\models\UserMeta::updateUserMeta($user->id, 'last_name', $this->lastName);
            \app\modules\user\models\UserMeta::updateUserMeta($user->id, 'first_name', $this->firstName);
            
            \app\modules\user\models\UserMeta::updateUserMeta($user->id, 'phone', $this->phone);
            \app\modules\user\models\UserMeta::updateUserMeta($user->id, 'about', $this->state);
            \app\modules\user\models\UserMeta::updateUserMeta($user->id, 'interests', $this->city);
            
            return true;
        }
        
        return false;
    }
    
}
