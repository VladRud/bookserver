<?php

namespace api\modules\user\events;

use app\modules\user\models\User;
use yii\base\Event;

/**
 * Class UserLogoutEvent
 *
 * @author Stableflow
 */
class UserLogoutEvent extends Event {

    /**
     * @var User|null
     */
    protected $user;

    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * @param $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

}
