<?php

namespace app\controllers;

use app\models\Product;
use app\models\Category;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\User;
use yii\web\BadRequestHttpException;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        Product::deactivateExpired();
    
        // Получаем только 6 последних активных товаров с изображением
        $query = Product::find()
            ->where(['is_active' => 1])
            ->with('mainImage')
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(6);
    
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false, // отключаем пагинацию для главной страницы
        ]);
    
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // 👉 Слияние корзины гостя с корзиной пользователя
            \app\components\CartManager::mergeGuestCartToUser(Yii::$app->user->id);
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionRequestPasswordReset()
    {
        $model = new \app\models\PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте вашу почту для сброса пароля.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось отправить письмо.');
            }
        }

        return $this->render('requestPasswordResetToken', ['model' => $model]);
    }

    public function actionResetPassword($token)
    {
        $user = User::findOne(['password_reset_token' => $token]);

        if (!$user || !$this->isTokenValid($token)) {
            throw new BadRequestHttpException('Неверный или устаревший токен.');
        }

        $model = new \app\models\ResetPasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user->password = Yii::$app->security->generatePasswordHash($model->password);
            $user->password_reset_token = null;
            $user->save(false);
            Yii::$app->session->setFlash('success', 'Пароль успешно изменён.');
            return $this->goHome();
        }

        return $this->render('resetPassword', ['model' => $model]);
    }

    private function isTokenValid($token)
    {
        if (!is_string($token) || !str_contains($token, '_')) {
            return false;
        }

        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        return $timestamp + 3600 * 24 >= time(); // токен действителен 24 часа
    }
}
