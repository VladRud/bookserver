<?php

namespace app\modules\user\models;

use Yii;
use yii\authclient\ClientInterface;

/**
 * This is the model class for table "{{%auth_social}}".
 *
 * @property integer $user_id
 * @property integer $client_id
 * @property string $external_id
 *
 * @property User $user
 */
class AuthSocial extends \yii\db\ActiveRecord
{
    const CLIENT_ID_FACEBOOK    = 1;
    const CLIENT_ID_TWITTER     = 2;
    const CLIENT_ID_GOOGLE      = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_social}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'client_id'], 'integer'],
            [['client_id', 'external_id'], 'required'],
            [['external_id'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('user', 'User ID'),
            'client_id' => Yii::t('user', 'Client ID'),
            'external_id' => Yii::t('user', 'External ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return \app\modules\user\models\queries\AuthSocialQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\user\models\queries\AuthSocialQuery(get_called_class());
    }

    public static function getClientIDList()
    {
        return [
            static::CLIENT_ID_FACEBOOK,
            static::CLIENT_ID_TWITTER,
            static::CLIENT_ID_GOOGLE,
        ];
    }

    /**
     * @param ClientInterface $client
     * @return int
     */
    public static function getClientID(ClientInterface $client)
    {
        $clientID = $client->getId();
        $clientName = $client->getName();

        if (is_integer($clientID) && in_array($clientID, static::getClientIDList())) {
            return $clientID;
        }

        switch (trim(strtolower($clientName))) {
            case 'facebook':
                return static::CLIENT_ID_FACEBOOK;
            case 'twitter':
                return static::CLIENT_ID_TWITTER;
            case 'google':
                return static::CLIENT_ID_GOOGLE;
            default:
                return 0;
        }
    }
}
