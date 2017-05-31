<?php

namespace app\modules\user\events;

/**
 * Description of UserEvents
 *
 * @author Stableflow
 */
class UserEvents {

    const SUCCESS_ACTIVATE_ACCOUNT  = 'user.success.activate';
    const FAILURE_ACTIVATE_ACCOUNT  = 'user.failure.activate';
    const SUCCESS_EMAIL_CONFIRM     = 'user.success.email.confirm';
    const FAILURE_EMAIL_CONFIRM     = 'user.failure.email.confirm';
    const SUCCESS_LOGIN             = 'user.success.login';
    const FAILURE_LOGIN             = 'user.failure.login';
    const BEFORE_LOGIN              = 'user.before.login';
    const AFTER_LOGIN               = 'user.after.login';
    const BEFORE_LOGOUT             = 'user.before.logout';
    const AFTER_LOGOUT              = 'user.after.logout';
    const BEFORE_PASSWORD_RECOVERY  = 'user.before.password.recovery';
    const SUCCESS_PASSWORD_RECOVERY = 'user.success.password.recovery';
    const FAILURE_PASSWORD_RECOVERY = 'user.failure.password.recovery';
    const BEFORE_PASSWORD_RESET     = 'user.before.password.reset';
    const FAILURE_PASSWORD_RESET    = 'user.failure.password.reset';
    const SUCCESS_PASSWORD_RESET    = 'user.success.password.reset';
    const SUCCESS_REGISTRATION      = 'user.success.registration';
    const FAILURE_REGISTRATION      = 'user.failure.registration';

}
