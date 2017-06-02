<?php

namespace app\modules\user\components;

use \Yii;
use \api\modules\user\models\User;
use \app\modules\user\models\Token;
use \common\helpers\DateHelper;
use yii\base\ErrorException;

/**
 * Class TokenStorage
 *
 * @author Stableflow
 */
class TokenStorage extends \yii\base\Component
{

    public function init()
    {
        parent::init();
//        $this->deleteExpired();
    }

    /**
     * @param User $user
     * @param int $expire
     * @param int $type
     * @return Token
     */
    public function create(User $user, $expire, $type)
    {
        $expire = (int)$expire;
        $model = new Token();
        $model->user_id = $user->id;
        $model->type = (int)$type;
        $model->code = Yii::$app->security->generateRandomString(rand(8, 12));
        $model->ip = Yii::$app->getRequest()->getUserIP();
        $model->create_date = DateHelper::getCurrentDateTime();
        $model->status = Token::STATUS_NEW;
        $model->expire = DateHelper::getGTMDatetime(time() + $expire);
        if ($model->save()) {
            return $model;
        }

        return false;
    }

    /**
     * Create activation token
     *
     * @param User $user
     * @param int $expire
     * @return Token
     */
    public function createAccountActivationToken(User $user, $expire = 86400)
    {
        $this->deleteByTypeAndUser(Token::TYPE_ACTIVATE, $user);
        return $this->create($user, $expire, Token::TYPE_ACTIVATE);
    }

    /**
     * Create recovery token
     *
     * @param User $user
     * @param int $expire
     * @return Token
     */
    public function createPasswordRecoveryToken(User $user, $expire = 86400)
    {
        $this->deleteByTypeAndUser(Token::TYPE_CHANGE_PASSWORD, $user);
        return $this->create($user, $expire, Token::TYPE_CHANGE_PASSWORD);
    }

    /**
     * Create access token
     *
     * @param User $user
     * @param int $expire
     * @return Token
     */
    public function createAccessToken(User $user, $expire = 18000)
    {
        $this->deleteByTypeAndUser(Token::TYPE_ACCESS, $user);
        return $this->create($user, $expire, Token::TYPE_ACCESS);
    }

    /**
     * Delete dublicate token
     *
     * @param int $type
     * @param User $user
     * @return boolean
     */
    public function deleteByTypeAndUser($type, User $user)
    {
        return Token::deleteAll(
            'type = :type AND user_id = :user_id',
            [
                ':type' => (int)$type,
                ':user_id' => $user->id
            ]
        );
    }

    /**
     * Delete all expired token
     */
    public function deleteExpired()
    {
        $deleted = Token::deleteAll('expire < :expire', [':expire' => gmdate("Y-m-d H:i:s", time())]);
        return $deleted;
    }

    /**
     * Get user token
     *
     * @param string
     * @param int $type
     * @param int $status
     * @return Token
     */
    public function get($token, $type, $status = Token::STATUS_NEW)
    {
        return Token::find()->where(
            'code = :code AND type = :type AND status = :status',
            [
                ':code' => $token,
                ':type' => (int)$type,
                ':status' => (int)$status
            ])->one();
    }

    public function getUserToken(User $user, $type, $status)
    {
        return Token::find()->where([
            'user_id' => $user->id,
            'type' => $type,
            'status' => $status
        ])->one();
    }

    /**
     * Activete user token
     *
     * @param Token $token
     * @param bool $invalidate
     * @return bool
     * @throws ErrorException
     */
    public function activate(Token $token, $invalidate = true)
    {
        $token->status = Token::STATUS_ACTIVATE;
        $token->scenario = Token::SCENARIO_DEFAULT;
        if ($token->save()) {
            if ($invalidate) {
                Token::deleteAll(
                    'id = :id AND user_id = :user_id AND type = :type',
                    [
                        ':user_id' => $token->user_id,
                        ':type' => $token->type,
                        ':id' => $token->id
                    ]
                );
            }
            return true;
        }
        throw new ErrorException(Yii::t('user', 'Error activate token!'));
    }

    public function createOauthTempUserToken(User $user, $expire = null)
    {
        $expire = (int)$expire;
        $model = new Token();
        $model->user_id = $user->id;
        $model->type = Token::TYPE_OAUTH_TEMP_USER;
        $model->code = Yii::$app->security->generateRandomString(rand(8, 12));
        $model->ip = Yii::$app->getRequest()->getUserIP();
        $model->create_date = DateHelper::getCurrentDateTime();
        $model->status = Token::STATUS_NEW;
        $model->expire = DateHelper::getGTMDatetime(time() + $expire);
        if ($model->save()) {
            return $model;
        }

        return false;
    }
}
