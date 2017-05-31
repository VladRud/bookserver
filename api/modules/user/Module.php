<?php

namespace app\modules\user;

use app\modules\user\events\UserEvents;

class Module extends \yii\base\Module {

    public $controllerNamespace = 'app\modules\user\controllers';
    public $loginSuccess = '/';
    public $logoutSuccess = '/';
    
    public function init()
    {
        return parent::init();
    }

}
