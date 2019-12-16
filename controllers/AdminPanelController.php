<?php

namespace app\controllers;

use Throwable;
use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * AdminPanelController implements the CRUD actions for User model.
 */
class AdminPanelController extends Controller
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'roles'   => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider(
            [
                'query' => User::find(),
            ]
        );

        return $this->render(
            'index',
            [
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single User model.
     *
     * @param integer $id
     *
     * @return string
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): string
    {
        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return Response|string
     *
     * @throws Exception
     */
    public function actionCreate()
    {
        $model = new User(['scenario' => User::SCENARIO_CREATE]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->editPerms($model);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render(
            'create',
            [
                'model' => $model,
                'perms' => Yii::$app->authManager->getPermissions(),
            ]
        );
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return Response|string
     *
     * @throws NotFoundHttpException if the model cannot be found
     * @throws Exception
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->editPerms($model);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render(
            'update',
            [
                'model' => $model,
                'perms' => Yii::$app->authManager->getPermissions(),
            ]
        );
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return Response
     *
     * @throws NotFoundHttpException if the model cannot be found
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete(int $id): Response
    {
        /** @var User $model */
        $model = $this->findModel($id);
        $model->removeAllPermissions();
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return User the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): User
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param User $model
     *
     * @return void
     *
     * @throws Exception
     */
    private function editPerms(User $model): void
    {
        $perms = Yii::$app->request->post()['User']['permissions'] === '' ? []
            : Yii::$app->request->post()['User']['permissions'];
        $userPerms = Yii::$app->authManager->getPermissionsByUser($model->getId());
        $allPerms = Yii::$app->authManager->getPermissions();

        $valUserPerms = [];
        foreach ($userPerms as $value) {
            $valUserPerms[$value->name] = $value->name;
        }

        $valPerms = [];
        foreach ($perms as $value) {
            $valPerms[$value] = $value;
        }

        $valAllPerms = [];
        foreach ($allPerms as $value) {
            $valAllPerms[$value->name] = $value->name;
        }

        foreach ($valPerms as $value) {
            if (!isset($valUserPerms[$value]) && isset($valAllPerms[$value])) {
                $model->addPermission($value);
            }
        }

        foreach ($valUserPerms as $value) {
            if (!isset($valPerms[$value])) {
                $model->removePermission($value);
            }
        }
    }
}
