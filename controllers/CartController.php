<?php

namespace app\controllers;
use Yii;
use yii\web\Response;
use app\components\CartManager;

use app\models\Cart;
use app\models\CartSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Product;
use app\models\CartItem;
use app\models\Orders;
use app\models\OrdersItem;

use yii\filters\AccessControl;

/**
 * CartController implements the CRUD actions for Cart model.
 */
class CartController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['checkout'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Только авторизованные пользователи
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Cart models.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            $guestCart = \app\components\CartManager::getGuestCart();
            $cartItems = [];

            foreach ($guestCart as $productId => $quantity) {
                $product = \app\models\Product::findOne($productId);
                if ($product) {
                    $cartItem = new \app\models\CartItem([
                        'product_id' => $productId,
                        'quantity' => $quantity,
                    ]);
                    $cartItem->populateRelation('product', $product);
                    $cartItems[] = $cartItem;
                }
            }

            $cart = null; // Не нужен объект Cart
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $product = $item->product;
                $price = $product->price_per_box ?? $product->price_per_kg ?? 0;
                $totalPrice += $item->quantity * $price;
            }

        } else {
            $cart = \app\models\Cart::find()
                ->where(['user_id' => Yii::$app->user->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->with('cartItems.product.productImages')
                ->one();

            $cartItems = $cart ? $cart->cartItems : [];
            $totalPrice = $cart ? $cart->getTotalPrice() : 0;
        }

        return $this->render('index', [
            'cart' => $cart,
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice,
        ]);
    }

    /**
     * Displays a single Cart model.
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
     * Creates a new Cart model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Cart();

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
     * Updates an existing Cart model.
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
     * Finds the Cart model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Cart the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Cart::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionAdd($id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;

        if ($request->isPost && $id === null) {
            $id = $request->bodyParams['id'] ?? null;
        }

        if (!$id || !($product = Product::findOne($id))) {
            return ['success' => false, 'message' => 'Товар не найден'];
        }

        // 👉 Для гостя — сохраняем в сессию
        if (Yii::$app->user->isGuest) {
            CartManager::addToGuestCart($id, 1);
            $guestCart = CartManager::getGuestCart();
            $quantity = $guestCart[$id] ?? 1;
            return ['success' => true, 'quantity' => $quantity];
        }

        // 👉 Для авторизованного — сохраняем в БД
        $userId = Yii::$app->user->id;

        $cart = Cart::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if (!$cart) {
            $cart = new Cart();
            $cart->user_id = $userId;
            $cart->save(false);
        }

        $item = CartItem::findOne(['cart_id' => $cart->id, 'product_id' => $id]);
        if ($item) {
            $item->quantity += 1;
        } else {
            $item = new CartItem([
                'cart_id' => $cart->id,
                'product_id' => $id,
                'quantity' => 1,
            ]);
        }

        $item->save(false);

        return ['success' => true, 'quantity' => $item->quantity];
    }
    
    
    public function actionDecrease($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // 👤 Гость
        if (Yii::$app->user->isGuest) {
            $guestCart = \app\components\CartManager::getGuestCart();

            if (!isset($guestCart[$id])) {
                return ['success' => false, 'message' => 'Товар не найден в корзине'];
            }

            // 👉 Удаление по флагу force=1
            if (Yii::$app->request->get('force') == 1) {
                unset($guestCart[$id]);
                \app\components\CartManager::saveGuestCart($guestCart);
                return ['success' => true, 'quantity' => 0];
            }

            // Уменьшение количества
            $guestCart[$id]--;
            if ($guestCart[$id] <= 0) {
                unset($guestCart[$id]);
                \app\components\CartManager::saveGuestCart($guestCart);
                return ['success' => true, 'quantity' => 0];
            }

            \app\components\CartManager::saveGuestCart($guestCart);
            return ['success' => true, 'quantity' => $guestCart[$id]];
        }

        // 👤 Авторизованный пользователь
        $userId = Yii::$app->user->id;

        $cart = \app\models\Cart::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        if (!$cart) {
            return ['success' => false, 'message' => 'Корзина не найдена'];
        }

        $item = \app\models\CartItem::findOne(['cart_id' => $cart->id, 'product_id' => $id]);

        if (!$item) {
            return ['success' => false, 'message' => 'Товар не найден в корзине'];
        }

        $item->quantity -= 1;

        if ($item->quantity <= 0) {
            $item->delete();
            return ['success' => true, 'quantity' => 0];
        }

        $item->save(false);
        return ['success' => true, 'quantity' => $item->quantity];
    }

    
    public function actionCheckout()
    {
        $userId = Yii::$app->user->id;
        $cart = Cart::find()->where(['user_id' => $userId])->orderBy(['created_at' => SORT_DESC])->one();

        if (!$cart) {
            Yii::$app->session->setFlash('error', 'Корзина пуста.');
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $order = new Orders();
            $order->user_id = $userId;
            $order->customer_name = $post['customer_name'] ?? '';
            $order->customer_phone = $post['customer_phone'] ?? '';
            $order->customer_address = $post['customer_address'] ?? '';
            $order->status_id = 1;
            $order->total_price = 0;

            if ($order->save()) {
                $total = 0;
                $itemsText = '';

                foreach ($cart->cartItems as $cartItem) {
                    $product = $cartItem->product;
                    $price = $product->price_per_box ?? $product->price_per_kg ?? 0;

                    $orderItem = new OrdersItem();
                    $orderItem->orders_id = $order->id;
                    $orderItem->product_id = $product->id;
                    $orderItem->quantity = $cartItem->quantity;
                    $orderItem->product_name = $product->name;
                    $orderItem->price = $price;
                    $orderItem->save(false);

                    $total += $price * $cartItem->quantity;

                    $itemsText .= "{$product->name} — {$cartItem->quantity} шт. × {$price} руб.\n";
                }

                $order->total_price = $total;
                $order->save(false);

                // Очистка корзины
                foreach ($cart->cartItems as $item) {
                    $item->delete();
                }
                $cart->delete();

                // Отправка сообщения в VK
                try {
                    $vkToken = Yii::$app->params['vkToken'];
                    $vkAdminId = Yii::$app->params['vkAdminId'];

                    $message = "🛒 Новый заказ от {$order->customer_name}\n"
                        . "📞 Телефон: {$order->customer_phone}\n"
                        . "📍 Адрес доставки: {$order->customer_address}\n\n"
                        . $itemsText
                        . "\n💰 Итого: {$total} руб.";

                    $params = [
                        'user_id' => $vkAdminId,
                        'message' => $message,
                        'access_token' => $vkToken,
                        'v' => '5.199',
                        'random_id' => time(),
                        'from_group' => 1,
                    ];

                    $vkResponse = file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($params));
                    $decoded = json_decode($vkResponse, true);

                    if (!isset($decoded['response'])) {
                        Yii::warning("VK message send failed: " . $vkResponse, __METHOD__);
                    }

                } catch (\Throwable $e) {
                    Yii::warning("VK message send exception: " . $e->getMessage(), __METHOD__);
                }

                Yii::$app->session->setFlash('success', '
                    Спасибо за заказ! Мы свяжемся с вами для уточнения суммы. 
                    Для быстрой связи: 
                    <a href="https://vk.com/sofia.delivery?from=groups" target="_blank">ВКонтакте</a> или 
                    <a href="https://t.me/sofia_delivery" target="_blank">Телеграм</a>.
                ');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Не удалось оформить заказ. Пожалуйста, попробуйте снова.');
            return $this->refresh();
        }

        return $this->render('checkout', [
            'cart' => $cart,
        ]);
    }

    public function actionDeleteItem($id)
    {
        $isAjax = Yii::$app->request->isAjax;
        if ($isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }

        if (Yii::$app->user->isGuest) {
            $guestCart = \app\components\CartManager::getGuestCart();
            if (isset($guestCart[$id])) {
                unset($guestCart[$id]);
                \app\components\CartManager::saveGuestCart($guestCart);
                return ['success' => true];
            }
            return ['success' => false, 'message' => 'Товар не найден в корзине'];
        }

        $item = \app\models\CartItem::findOne($id);
        if (!$item || $item->cart->user_id !== Yii::$app->user->id) {
            return ['success' => false, 'message' => 'Ошибка доступа'];
        }

        $deleted = $item->delete();
        return ['success' => $deleted];
    }

    
    public function actionUpdateQuantity($item_id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $quantity = Yii::$app->request->post('quantity');

        if (!$quantity || $quantity < 1) {
            return ['success' => false, 'message' => 'Неверное количество'];
        }

        // Для гостей — обновляем в сессии
        if (Yii::$app->user->isGuest) {
            $guestCart = \app\components\CartManager::getGuestCart();
            if (!isset($guestCart[$item_id])) {
                return ['success' => false, 'message' => 'Товар не найден в корзине'];
            }
            $guestCart[$item_id] = $quantity;
            \app\components\CartManager::saveGuestCart($guestCart);

            // Посчитаем сумму вручную
            $total = 0;
            foreach ($guestCart as $productId => $qty) {
                $product = \app\models\Product::findOne($productId);
                if ($product) {
                    $price = $product->price_per_box ?? $product->price_per_kg ?? 0;
                    $total += $qty * $price;
                }
            }

            return [
                'success' => true,
                'quantity' => $quantity,
                'totalPrice' => number_format($total, 0, '.', ' ') . ' ₽'
            ];
        }

        // Авторизованный пользователь
        $cartItem = \app\models\CartItem::findOne(['id' => $item_id]);
        if (!$cartItem) {
            return ['success' => false, 'message' => 'Товар не найден в корзине'];
        }

        $cartItem->quantity = $quantity;
        if (!$cartItem->save()) {
            return ['success' => false, 'message' => 'Не удалось обновить товар'];
        }

        $cart = \app\models\Cart::findOne(['id' => $cartItem->cart_id]);

        $total = 0;
        foreach ($cart->cartItems as $item) {
            $product = $item->product;
            $price = $product->price_per_box ?? $product->price_per_kg ?? 0;
            $total += $price * $item->quantity;
        }

        return [
            'success' => true,
            'quantity' => $cartItem->quantity,
            'totalPrice' => number_format($total, 0, '.', ' ') . ' ₽'
        ];
    }
}
