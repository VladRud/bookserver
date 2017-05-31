<?php

namespace app\modules\user\events;

use app\modules\user\models\Token;
use yii\base\Event;

/**
 * Class UserActivateEvent
 *
 * @author Stableflow
 */
class UserActivateEvent extends Event {

    /* @var Token */
    protected $token;
    protected $user;

    /**
     * @param Token $token
     */
    public function setToken($token) {
        $this->token = $token;
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

    /**
     * @return Token|null
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * UserActivateEvent constructor.
     * @param Token $token
     * @param \app\modules\user\models\User|null $user
     */
    public function __construct(Token $token, \app\modules\user\models\User $user = null) {
        $this->token = $token;
        $this->user = $user;
    }

}
