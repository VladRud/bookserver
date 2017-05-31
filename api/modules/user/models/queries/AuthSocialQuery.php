<?php

namespace app\modules\user\models\queries;

/**
 * This is the ActiveQuery class for [[\app\modules\user\models\AuthSocial]].
 *
 * @see \app\modules\user\models\AuthSocial
 */
class AuthSocialQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\modules\user\models\AuthSocial[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\user\models\AuthSocial|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
