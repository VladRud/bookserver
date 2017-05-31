<?php

namespace app\modules\user\models;

use Yii;

/**
 * This is the model class for table "{{%queue_mail}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $mail_id
 * @property integer $status
 *
 * @property User $user
 */
class QueueMail extends \yii\db\ActiveRecord
{

    const USER_REQUEST_INVITATION_CODE = 0;
    const ADMIN_APPROVES_INVITATION_REQUEST = 1;
    const USER_SINGS_UP_WITH_HIS_INVITATION_CODE = 2;
    const USER_SIGNS_UP_WITH_GENERAL_SIGN_UP_FORM = 3;
    const USER_FOLLOWS_CONFIRMATION_LINK_AND_CONFIRMS_HIS_EMAIL = 4;
    const GUEST_IS_REGISTERED_WITH_REFERRAL_CODE = 5;
    const ADMIN_BLOCKS_USER = 6;
    const ADMIN_UNBLOCKS_OR_ACTIVATES_USER = 7;
    const USER_CREATES_NEW_ORDER = 8;
    const ADMIN_DECLINES_NEW_ORDER = 9;
    const ADMIN_APPROVES_NEW_ORDER = 10;

    const STATUS_WAIT = 0;
    const STATUS_SUCCESS = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%queue_mail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'mail_id', 'status'], 'required'],
            [['user_id', 'mail_id', 'status'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'mail_id' => 'Mail ID',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
