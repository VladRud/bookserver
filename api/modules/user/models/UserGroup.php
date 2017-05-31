<?php

namespace app\modules\user\models;

use Yii;

/**
 * This is the model class for table "{{%user_group}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $name
 *
 * @property UserGroupRelation[] $userGroupRelations
 * @property User[] $users
 */
class UserGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_group}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['name'], 'required'],
            [['name'], 'unique'],
            [['name'], 'trim'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGroupRelations()
    {
        return $this->hasMany(UserGroupRelation::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('{{%user_group_relation}}', ['group_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\modules\user\models\queries\UserGroupQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\user\models\queries\UserGroupQuery(get_called_class());
    }
}
