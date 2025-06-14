<?php

namespace app\controllers;
use Yii;

use app\models\Orders;
use app\models\OrdersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class OrdersController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Orders models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Orders model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Orders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Orders();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Orders model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Orders model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orders::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDetails($id)
    {
        $order = \app\models\Orders::find()->where(['id' => $id, 'user_id' => Yii::$app->user->id])->one();
        if (!$order) {
            return 'Заказ не найден.';
        }

        $items = \app\models\OrdersItem::find()->where(['orders_id' => $order->id])->all();

        $products = [];
        foreach ($items as $item) {
            $image = \app\models\ProductImage::find()->where(['product_id' => $item->product_id])->one();
            $products[] = [
                'name' => $item->product_name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'image' => $image ? Yii::getAlias('@web/' . $image->image_path) : null,
            ];
        }

        return $this->renderPartial('order-details', [
            'order' => $order,
            'products' => $products,
        ]);
    }

    public function actionAdminDetails($id)
    {
        $order = \app\models\Orders::findOne($id);
        if (!$order) {
            return 'Заказ не найден.';
        }

        $items = \app\models\OrdersItem::find()->where(['orders_id' => $order->id])->all();

        $products = [];
        foreach ($items as $item) {
            $image = \app\models\ProductImage::find()->where(['product_id' => $item->product_id])->one();
            $products[] = [
                'name' => $item->product_name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'image' => $image ? Yii::getAlias('@web/' . $image->image_path) : null,
            ];
        }

        return $this->renderPartial('order-details', [
            'order' => $order,
            'products' => $products,
        ]);
    }

    public function actionChangeStatus()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['id'], $data['status_id'])) {
            return ['success' => false, 'message' => 'Invalid input'];
        }

        $order = Orders::findOne($data['id']);
        if ($order) {
            $order->status_id = $data['status_id'];
            if ($order->save(false)) {
                return ['success' => true];
            }
        }

        return ['success' => false, 'message' => 'Order not found or failed to save'];
    }
}
