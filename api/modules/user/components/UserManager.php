<?php

namespace api\modules\user\components;

use common\helpers\DateHelper;
//use api\modules\core\components\EventManager;
use \Yii;
use \api\modules\user\forms\RegistrationForm;
use \api\modules\user\models\User;
use \api\modules\user\helpers\Password;
use \api\modules\user\components\TokenStorage;
use yii\base\Component;

/**
 * Class UserManager
 *
 * @author Stableflow
 */
class UserManager extends Component
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    public function init()
    {
        parent::init();
        $this->tokenStorage = Yii::createObject('api\modules\user\components\TokenStorage');
    }

    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }


    public function createUser(RegistrationForm $form)
    {
        $user = new User();
        $user->setScenario(User::SCENARIO_REGISTER);

        $data = $form->getAttributes();

        $user->setAttributes($data);

        if ($user->validate() && $user->register()) {
            return [
                'success' => true
            ];
        }

        return $user->errors;
    }

}
