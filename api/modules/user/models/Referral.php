<?php

namespace app\modules\user\models;

use Yii;

/**
 * This is the model class for table "{{%referral}}".
 *
 * @property integer $source_user_id
 * @property integer $target_user_id
 *
 * @property User $sourceUser
 * @property User $targetUser
 */
class Referral extends \yii\db\ActiveRecord
{
    const COOKIES_REQUEST_ID = 'rb_referral_code';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%referral}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source_user_id', 'target_user_id'], 'required'],
            [['source_user_id', 'target_user_id'], 'integer'],
            [['source_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['source_user_id' => 'id']],
            [['target_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['target_user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'source_user_id' => 'Source User ID',
            'target_user_id' => 'Target User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSourceUser()
    {
        return $this->hasOne(User::className(), ['id' => 'source_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTargetUser()
    {
        return $this->hasOne(User::className(), ['id' => 'target_user_id']);
    }

    /**
     * Create referral link
     * @param User $source_user
     * @param User $target_user
     * @return boolean
     */
    public static function linkReferral(User $source_user, User $target_user) {
        $model = new static();
        $model->source_user_id = $source_user->id;
        $model->target_user_id = $target_user->id;
        return $model->save();
    }

    /**
     * Create referral link
     * @param User $source_user
     * @param User $target_user
     * @return boolean
     */
    public function unlinkReferral(User $source_user, User $target_user) {
        if (null !== $model = static::find()->where(['source_user_id' => $source_user->id, 'target_user_id' => $target_user->id])->one()) {
            return $model->delete();
        }

        return false;
    }
}
