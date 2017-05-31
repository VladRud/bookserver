<?php

namespace app\modules\user\controllers;

use app\modules\user\models\User;
use app\modules\user\models\UserGroupRelation;
use Yii;
use app\modules\user\models\UserGroup;
use app\modules\user\models\search\UserGroupSearch;
use app\modules\core\components\controllers\BackController;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserGroupBackendController implements the CRUD actions for UserGroup model.
 */
class UserGroupBackendController extends BackController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all UserGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserGroup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserGroup();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $transaction = Yii::$app->db->beginTransaction();
            try {

                if (!$model->save()) {
                    throw new ErrorException();
                }

                if ($usersEmails = ArrayHelper::getValue(Yii::$app->request->post(), 'UserGroupRelation.user_id')) {
                    foreach ($usersEmails as $id => $email) {
                        $groupRelationModel = new UserGroupRelation();
                        $groupRelationModel->user_id = $id;
                        $groupRelationModel->group_id = $model->id;
                        if (!$groupRelationModel->save()) {
                            throw new ErrorException();
                        }
                    }
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Group has been saved');
            } catch (\ErrorException $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Could not save');
            }

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $transaction = Yii::$app->db->beginTransaction();
            try {

                if (!$model->save()) {
                    throw new ErrorException();
                }

                foreach ($model->userGroupRelations as $groupRelation) {
                    $groupRelation->delete();
                }

                if ($usersEmails = ArrayHelper::getValue(Yii::$app->request->post(), 'UserGroupRelation.user_id')) {
                    foreach ($usersEmails as $id => $email) {
                        $groupRelationModel = new UserGroupRelation();
                        $groupRelationModel->user_id = $id;
                        $groupRelationModel->group_id = $model->id;
                        if (!$groupRelationModel->save()) {
                            throw new ErrorException();
                        }
                    }
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Group has been saved');
            } catch (\ErrorException $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Could not save');
            }

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionGetUser()
    {
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException();
        }

        $collection = User::find()->select(['id', 'email'])->where(['like', 'email', Yii::$app->request->get('q')])->all();
        return Json::encode($collection);
    }


    /**
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionAddUser()
    {
        if (!Yii::$app->request->isAjax) {
            throw new ForbiddenHttpException();
        }

        $user = User::find()->select(['id', 'email'])->where(['id' => Yii::$app->request->post('user_id')])->one();

        if (!$user) {
            $response = [
                'status' => 'error',
            ];
        } else {
            $response = [
                'status' => 'success',
                'html' => $this->renderAjax('_user-group-item', [
                    'model' => new UserGroupRelation(),
                    'user' => $user
                ])
            ];
        }

        return Json::encode($response);
    }
}
