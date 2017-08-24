<?php

namespace api\modules\user\events;

use app\modules\user\forms\LoginForm;
use app\modules\user\models\User;
use yii\base\Event;

/**
 * Class UserLoginEvent
 *
 * @author Stableflow
 */
class UserLoginEvent extends Event
{
    /**
     * @var LoginForm;
     */
    protected $loginForm;
    /**
     * @var User|null
     */
    protected $user;


    public function __construct(LoginForm $loginForm, User $user = null)
    {
        $this->loginForm = $loginForm;
        $this->user = $user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $loginForm
     */
    public function setLoginForm($loginForm)
    {
        $this->loginForm = $loginForm;
    }

    /**
     * @return mixed
     */
    public function getLoginForm()
    {
        return $this->loginForm;
    }

}
