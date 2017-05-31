<?php

namespace app\modules\user\models;

use Yii;

/**
 * This is the model class for table "{{%user_meta}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $meta_key
 * @property string $meta_value
 *
 * @property User $user
 */
class UserMeta extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user_meta}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id'], 'integer'],
            [['meta_key', 'meta_value'], 'required'],
            [['meta_key'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'            => Yii::t('user', 'ID'),
            'user_id'       => Yii::t('user', 'User ID'),
            'meta_key'      => Yii::t('user', 'Meta Key'),
            'meta_value'    => Yii::t('user', 'Meta Value'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * Update user meta
     */
    public static function updateUserMeta($userId, $key, $value){
        if(null === $model = static::find()->where(['user_id' => $userId, 'meta_key' => $key ])->one()){
            $model = new static();
            $model->user_id = $userId;
            $model->meta_key = $key;
            $model->meta_value = $value;
            return $model->save();
        }else{
            return $model->updateAttributes([
                'meta_key' => $key,
                'meta_value' => $value,
            ]);
        }
    }
    
}
