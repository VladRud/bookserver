<?php

namespace app\modules\user\controllers;

use app\modules\user\forms\BackUsersForm;
use app\modules\user\models\UsersSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

use app\modules\core\components\controllers\BackController;
use app\modules\user\models\User;
use app\modules\user\forms\ProfileForm;
use app\modules\user\models\UserMeta;
use app\modules\core\helpers\FileUploaderHelper;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Class IndexBackendController
 *
 * @author Stableflow
 * 
 */
class IndexBackendController extends BackController {

    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'user-to-blacklist' => ['POST'],
                ],
            ],
        ]);
    }

    public function actionProfile() {

        if (($user = User::findOne(Yii::$app->user->id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new ProfileForm([
            'phone' => $user->phone,
            'lastName' => $user->last_name,
            'firstName' => $user->first_name,
            'about' => $user->about,
            'interests' => $user->interests,
        ]);
        $post = Yii::$app->request->post();
        if($post !== null && ($model->load($post) || isset($_FILES['ProfileForm']))){
            if(isset($post['ProfileForm']['firstName'])){
                $model->scenario = ProfileForm::SCENARIO_CHANGE_PERSONAL_INFO;
            }elseif(isset($post['ProfileForm']['currentPassword'])){
                $model->scenario = ProfileForm::SCENARIO_CHANGE_PASSWORD;
            }else{
                $model->scenario = ProfileForm::SCENARIO_CHANGE_AVATAR;
                $model->fileAvatar = UploadedFile::getInstance($model, 'fileAvatar');
            }
            
            if($model->validate()){
                switch ($model->scenario){
                    case ProfileForm::SCENARIO_CHANGE_PASSWORD:
                        $user->newPassword = $model->newPassword;
                        $user->save();
                        break;
                    case ProfileForm::SCENARIO_CHANGE_PERSONAL_INFO:
                        UserMeta::updateUserMeta($user->id, 'phone', $model->phone);
                        $user->last_name = $model->lastName;
                        $user->first_name = $model->firstName;
                        $user->update();
//                        UserMeta::updateUserMeta($user->id, 'last_name', $model->lastName);
//                        UserMeta::updateUserMeta($user->id, 'first_name', $model->firstName);
                        UserMeta::updateUserMeta($user->id, 'about', $model->about);
                        UserMeta::updateUserMeta($user->id, 'interests', $model->interests);
                        break;
                    case ProfileForm::SCENARIO_CHANGE_AVATAR:
                        if ($model->fileAvatar instanceof UploadedFile) {
                            $basePath = Yii::getAlias('@webroot');
                            if (null !== $user->avatar && file_exists($basePath . $user->avatar)) {
                                unlink($basePath . $user->avatar);
                            }

                            if(null !== $avatar = FileUploaderHelper::saveImage($model->fileAvatar, 'avatar')){
                                UserMeta::updateUserMeta($user->id, 'avatar', $avatar);
                            }
                        }
                        break;
                }
                
                return $this->redirect(['profile']);
            }
        }
        
        
        
        return $this->render('profile', [
                    'model' => $model,
                    'user' => $user
        ]);
    }

    public function actionIndex() {

        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'statusList' => $searchModel->getStatusList(),
            'roleList' => $searchModel->getRoleList()
        ]);
    }

    public function actionUserToBlacklist() {
        if (Yii::$app->request->isAjax) {

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $id = Yii::$app->request->post('id');

            if (null === $user = User::findOne($id)) {
                return false;
            }

            if ($user->status != User::STATUS_BLACKLIST) {
                $status = User::STATUS_BLACKLIST;
            } else {
                $status = User::STATUS_APPROVED;
            }

            $user->status = $status;
            $user->update();

            return true;
        }
        return false;
    }

    public function actionEdit($id) {

        if (($model = BackUsersForm::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model->scenario = BackUsersForm::EDIT_SCENARIO;

        $post = Yii::$app->request->post();

        if ($post != null && $model->load($post) && $model->validate()) {
            if ($model->update()) {
                $this->redirect('index');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);

    }

    public function actionCreate(){
        $model = new BackUsersForm();

        $model->scenario = BackUsersForm::SIGNUP_SCENARIO;

        $post = Yii::$app->request->post();

        if ($post != null && $model->load($post) && $model->validate()) {
            if ($model->save()) {
                $this->redirect('index');
            }
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

}
