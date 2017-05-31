<?php

namespace app\modules\user\events;

use yii\base\Event;
use app\modules\user\forms\RegistrationForm;
use app\modules\user\models\User;
use app\modules\user\models\Token;

/**
 * Class UserRegistrationEvent
 *
 * @author Stableflow
 */
class UserRegistrationEvent extends Event {

    /**
     * @var User;
     */
    protected $user;

    /**
     * @var RegistrationForm
     */
    protected $form;

    /**
     * @var Token
     */
    protected $token;

    public function __construct(RegistrationForm $form, User $user, Token $token = null) {
        $this->form = $form;
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * @param RegistrationForm $form
     */
    public function setForm($form) {
        $this->form = $form;
    }

    /**
     * @return RegistrationForm
     */
    public function getForm() {
        return $this->form;
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

}
