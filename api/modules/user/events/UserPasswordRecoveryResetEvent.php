<?php

namespace app\modules\user\events;
use app\modules\user\models\User;

/**
 * Class UserPasswordRecoveryEvent
 *
 * @author Stableflow
 */
class UserPasswordRecoveryResetEvent extends \yii\base\Event {

    /**
     * @var
     */
    protected $token;

    /**
     * @var
     */
    protected $password;

    /**
     * @var
     */
    protected $user;

    /**
     * @param $token
     * @param $password
     * @param $user
     */
    public function __construct($token, $password = null, \app\modules\user\models\User $user = null) {
        $this->token = $token;
        $this->password = $password;
        $this->user = $user;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token) {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param User $user
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }

}
