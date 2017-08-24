<?php

namespace api\modules\user\models;

use Yii;
use api\modules\user\models\User;
use common\helpers\DateHelper;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "vr_token".
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
class Token extends ActiveRecord {
    
    public static function tableName() {
        return '{{%token}}';
    }


    public function rules() {
        return [
            [['user_id', 'code', 'create_date', 'type', 'expire', 'status', 'ip'], 'required'],
            [['user_id', 'type'], 'integer'],
            [['code'], 'string', 'max' => 32],
            [['user_id', 'code', 'type'], 'unique', 'targetAttribute' => ['user_id', 'code', 'type'], 'message' => 'The combination of User ID, Code and Type has already been taken.']
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    public function updateTokenExpiredTime($expire){
        return $this->updateAttributes([
            'expire' => DateHelper::getGTMDatetime(time() + $expire)
        ]);
    }

}
