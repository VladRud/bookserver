<?php

namespace app\modules\user\models\queries;

/**
 * This is the ActiveQuery class for [[\app\modules\user\models\UserGroup]].
 *
 * @see \app\modules\user\models\UserGroup
 */
class UserGroupQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\modules\user\models\UserGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\user\models\UserGroup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
