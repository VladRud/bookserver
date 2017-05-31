<?php

namespace app\modules\user\models;

use Yii;
use app\modules\user\models\User;

/**
 * This is the model class for table "sf_token".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $code
 * @property integer $create_date
 * @property integer $type
 * @property string $ip
 * @property integer $status
 * @property datetime $expire
 *
 * @property User $user
 */
class Token extends \yii\db\ActiveRecord {
    
    const SCENARIO_DEFAULT = 'default';
    
    const TYPE_RECOVERY = 1;
    const TYPE_ACTIVATE = 2;
    const TYPE_CHANGE_PASSWORD = 3;
    const TYPE_ACCESS = 4;
    const TYPE_OAUTH_TEMP_USER = 5;

    const STATUS_NEW = 0;
    const STATUS_ACTIVATE = 1;
    const STATUS_FAIL = 2;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%token}}';
    }

    /** @inheritdoc */
    public static function primaryKey() {
        return ['user_id', 'code', 'type'];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'code', 'create_date', 'type', 'expire', 'status', 'ip'], 'required'],
            [['user_id', 'type'], 'integer'],
            [['code'], 'string', 'max' => 32],
            [['user_id', 'code', 'type'], 'unique', 'targetAttribute' => ['user_id', 'code', 'type'], 'message' => 'The combination of User ID, Code and Type has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        return [
            'default' => ['*']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'code' => Yii::t('app', 'Code'),
            'create_date' => Yii::t('app', 'Create Date'),
            'type' => Yii::t('app', 'Type'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    public function updateTokenExpiredTime($expire){
        return $this->updateAttributes([
            'expire' => \app\helpers\DateHelper::getGTMDatetime(time() + $expire)
        ]);
    }

}
