<?php

namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\ProductSearch;
use app\models\Category;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\models\ProductImage;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
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
     * Lists all Product models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['product.is_active' => 1]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Displays a single Product model.
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
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Product();
    
        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->mainImageFile = UploadedFile::getInstance($model, 'mainImageFile');
            $model->extraImageFiles = UploadedFile::getInstances($model, 'extraImageFiles');
    
            // Проверка: не больше 5 фото
            $totalImages = 0;
            if ($model->mainImageFile) {
                $totalImages++;
            }
            $totalImages += count($model->extraImageFiles);
    
            if ($totalImages > 5) {
                Yii::$app->session->setFlash('error', 'Нельзя загрузить больше 5 изображений к товару.');
                return $this->render('create', ['model' => $model]);
            }
    
            if ($model->save()) {
                // Главное фото
                if ($model->mainImageFile) {
                    $mainImage = new ProductImage();
                    $mainImage->product_id = $model->id;
                    $mainImage->imageFile = $model->mainImageFile;
                    $mainImage->is_main = 1;
                    $mainImage->uploadAndSave();
                }
    
                // Дополнительные фото
                foreach ($model->extraImageFiles as $file) {
                    $extra = new ProductImage();
                    $extra->product_id = $model->id;
                    $extra->imageFile = $file;
                    $extra->is_main = 0;
                    $extra->uploadAndSave();
                }
    
                return $this->redirect(['/admin/products']);
            }
        } else {
            $model->loadDefaultValues();
        }
    
        return $this->render('create', ['model' => $model]);
    }       

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
    
        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->mainImageFile = UploadedFile::getInstance($model, 'mainImageFile');
            $model->extraImageFiles = UploadedFile::getInstances($model, 'extraImageFiles');
    
            // Подсчёт существующих и новых фото
            $existing = ProductImage::find()->where(['product_id' => $model->id])->count();
            $new = (int) !empty($model->mainImageFile) + count($model->extraImageFiles);
    
            if (($existing + $new) > 5) {
                Yii::$app->session->setFlash('error', 'Общее количество изображений не может превышать 5.');
                return $this->render('update', ['model' => $model]);
            }
    
            if ($model->save()) {
                // 🔄 Заменить главное фото, если загружено новое
                if ($model->mainImageFile) {
                    $oldMain = ProductImage::find()
                        ->where(['product_id' => $model->id, 'is_main' => 1])
                        ->one();
    
                    if ($oldMain) {
                        $oldMain->delete();
                    }
    
                    $mainImage = new ProductImage();
                    $mainImage->product_id = $model->id;
                    $mainImage->imageFile = $model->mainImageFile;
                    $mainImage->is_main = 1;
                    $mainImage->uploadAndSave();
                }
    
                // 🔄 Заменить дополнительные фото, если загружены новые
                if (!empty($model->extraImageFiles)) {
                    // Удаляем все старые дополнительные фото
                    $oldExtras = ProductImage::find()
                        ->where(['product_id' => $model->id, 'is_main' => 0])
                        ->all();
    
                    foreach ($oldExtras as $old) {
                        $old->delete();
                    }
    
                    // Сохраняем новые дополнительные фото
                    foreach ($model->extraImageFiles as $file) {
                        $extra = new ProductImage();
                        $extra->product_id = $model->id;
                        $extra->imageFile = $file;
                        $extra->is_main = 0;
                        $extra->uploadAndSave();
                    }
                }
    
                return $this->redirect(['/admin/products']);
            }
        }
    
        return $this->render('update', ['model' => $model]);
    }      

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['/admin/products']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }



    public function actionCatalog()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 12;

        $categories = Category::find()->all();

        return $this->render('catalog', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'categories' => $categories,
        ]);
    }
}
