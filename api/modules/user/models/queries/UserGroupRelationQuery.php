<?php

namespace app\modules\user\models\queries;

/**
 * This is the ActiveQuery class for [[\app\modules\user\models\UserGroupRelation]].
 *
 * @see \app\modules\user\models\UserGroupRelation
 */
class UserGroupRelationQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\modules\user\models\UserGroupRelation[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\user\models\UserGroupRelation|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
