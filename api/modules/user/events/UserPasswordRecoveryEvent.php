<?php

namespace app\modules\user\events;

/**
 * Class UserPasswordRecoveryEvent
 *
 * @author Stableflow
 */
class UserPasswordRecoveryEvent extends \yii\base\Event {

    /**
     * @var
     */
    protected $email;

    /**
     * @var \yii\web\User
     */
    protected $user;

    /**
     * @var \app\modules\user\models\Token
     */
    protected $token;

    /**
     * @param $email
     * @param User $user
     * @param UserToken $token
     */
    public function __construct($email, \app\modules\user\models\User $user = null, \app\modules\user\models\Token $token = null) {
        $this->email = $email;
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * @param \app\modules\user\models\Token $token
     */
    public function setToken($token) {
        $this->token = $token;
    }

    /**
     * @return \app\modules\user\models\Token
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser() {
        return $this->user;
    }

}
