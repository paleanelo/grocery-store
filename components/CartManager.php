<?php
namespace app\components;

use Yii;
use app\models\Cart;
use app\models\CartItem;

class CartManager
{
    public static function getSessionKey()
    {
        return '_guest_cart';
    }

    // Добавить товар в гостевую корзину (сессия)
    public static function addToGuestCart($productId, $quantity = 1)
    {
        $session = Yii::$app->session;
        $cart = $session->get(self::getSessionKey(), []);
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }
        $session->set(self::getSessionKey(), $cart);
    }

    // Получить содержимое гостевой корзины
    public static function getGuestCart()
    {
        return Yii::$app->session->get(self::getSessionKey(), []);
    }

    // Очистить гостевую корзину
    public static function clearGuestCart()
    {
        Yii::$app->session->remove(self::getSessionKey());
    }

    // Слить корзину из сессии в корзину пользователя
    public static function mergeGuestCartToUser($userId)
    {
        $guestCart = self::getGuestCart();
        if (empty($guestCart)) return;

        $cart = Cart::find()->where(['user_id' => $userId])->one();
        if (!$cart) {
            $cart = new Cart();
            $cart->user_id = $userId;
            $cart->save(false);
        }

        foreach ($guestCart as $productId => $quantity) {
            $item = CartItem::findOne(['cart_id' => $cart->id, 'product_id' => $productId]);
            if ($item) {
                $item->quantity += $quantity;
            } else {
                $item = new CartItem([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }
            $item->save(false);
        }

        self::clearGuestCart();
    }

    // Сохранить гостевую корзину в сессии
    public static function saveGuestCart($cart)
    {
        Yii::$app->session->set(self::getSessionKey(), $cart);
    }
}
