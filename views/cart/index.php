<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var \app\models\Cart|null $cart */

$this->title = 'Корзина';
?>

<div class="container mt-5 pt-5"> 
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="<?= Url::to(['product/catalog']) ?>" class="text-success" style="text-decoration: none; font-size: 1.1rem;">
                <i class="bi bi-arrow-left me-1"></i> В каталог
            </a>
        </div>
        <h1 class="text-center mb-0">Корзина</h1>
        <div style="width: 100px;"></div>
    </div>

    <div id="cart-content">
    <?php if (!empty($cartItems)): ?>
        <div class="cart-items">
            <?php $totalSum = 0; ?>
            <?php foreach ($cartItems as $item): ?>
                <?php
                    $product = $item->product;
                    $weight = $product->avg_weight ?: 'не указан';

                    if ($product->price_per_box !== null) {
                        $price = $product->price_per_box . ' ₽ / ящик';
                        $itemTotal = $product->price_per_box * $item->quantity;
                    } elseif ($product->price_per_kg !== null) {
                        $price = $product->price_per_kg . ' ₽ / кг';
                        $itemTotal = $product->price_per_kg * $item->quantity;
                    } else {
                        $price = 'не указана';
                        $itemTotal = 0;
                    }

                    $totalSum += $itemTotal;

                    // Выбор изображения
                    $images = $product->productImages;
                    if (!empty($images)) {
                        $mainImage = null;
                        foreach ($images as $img) {
                            if ($img->is_main) {
                                $mainImage = $img;
                                break;
                            }
                        }
                        if (!$mainImage) {
                            $mainImage = $images[0];
                        }

                        $imagePath = $mainImage->image_path;
                        $imageSrc = (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://'))
                            ? $imagePath
                            : Yii::getAlias('@web') . '/' . ltrim($imagePath, '/');
                    } else {
                        $imageSrc = Yii::getAlias('@web') . '/img/noimage.jpg';
                    }
                ?>

                <div class="cart-item d-flex flex-wrap align-items-center py-3" style="border-bottom: 1px solid #ccc; gap: 20px;">
                    <!-- Изображение -->
                    <div style="flex: 0 0 100px;">
                        <?= Html::img($imageSrc, [
                            'style' => 'width: 100px; height: 100px; object-fit: cover; border-radius: 8px;',
                            'alt' => $product->name
                        ]) ?>
                    </div>

                    <!-- Название -->
                    <div style="flex: 1 1 150px; min-width: 150px;">
                        <strong><?= Html::encode($product->name) ?></strong>
                    </div>

                    <!-- Цена -->
                    <div style="flex: 1 1 120px; min-width: 120px;">
                        <strong data-price="<?= $product->price_per_box ?? $product->price_per_kg ?>">Цена:</strong> <?= Html::encode($price) ?>
                    </div>

                    <!-- Вес -->
                    <div style="flex: 1 1 120px; min-width: 120px;">
                        <strong>Вес:</strong>
                        <?= is_numeric($weight) ? Html::encode($weight . ' кг') : Html::encode($weight) ?>
                    </div>

                    <!-- Кнопки изменения количества -->
                    <?php $itemId = Yii::$app->user->isGuest ? $item->product_id : $item->id; ?>

                    <div class="d-flex align-items-center" style="flex: 1 1 160px; min-width: 160px;">
                        <div class="d-flex align-items-center flex-nowrap quantity-container">
                            <button class="btn btn-outline-secondary btn-sm quantity-btn minus" data-item-id="<?= $itemId ?>">−</button>
                            <span class="mx-2 fw-bold item-quantity" data-item-id="<?= $itemId ?>">
                                <?= $item->quantity ?>
                            </span>
                            <button class="btn btn-outline-secondary btn-sm quantity-btn plus" data-item-id="<?= $itemId ?>">+</button>
                        </div>
                    </div>

                    <!-- Кнопка удаления -->
                    <div style="flex: 0 0 auto;">
                        <button class="btn btn-danger btn-sm delete-item-btn" data-id="<?= $itemId ?>">Удалить</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Сумма заказа -->
        <div class="text-center mt-4">
            <h4>Сумма заказа: <span id="cart-total-price"><?= number_format($totalSum, 0, '.', ' ') . ' ₽' ?></span></h4>
        </div>

        <p class="mt-4 text-center text-muted">
            Доставляем всё кратно ящику, так как есть весовые позиции. Точная сумма будет известна после отгрузки у поставщиков.
        </p>

    <?php if (Yii::$app->user->isGuest): ?>
        <p class="text-danger text-center">Чтобы оформить заказ, необходимо войти в систему.</p>
    <?php else: ?>
        <div id="customer-data-section">
            <h2 class="mt-5 mb-4 text-center">Данные заказчика</h2>

        <form method="post" action="<?= Url::to(['cart/checkout']) ?>"
            class="mx-auto rounded"
            style="max-width: 500px; padding: 40px 30px; border: 1px solid green; background-color: #f8f9fa;">

            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>

            <div class="mb-4">
                <input type="text" class="form-control" name="customer_name" placeholder="Имя" required>
            </div>
            <div class="mb-4">
                <input type="text" class="form-control" name="customer_phone" id="customer-phone" placeholder="Телефон" required>
            </div>
            <div class="mb-4">
                <input type="text" class="form-control" name="customer_address" placeholder="Адрес доставки" required>
            </div>

            <div class="text-center">
                <button class="btn btn-warning px-4">Оформить заказ</button>
            </div>
        </form>

        <?php
            $this->registerJsFile(
                'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js',
                ['depends' => [\yii\web\JqueryAsset::class]]
            );

            $this->registerJs("
                $('#customer-phone').inputmask('+7(999)-999-99-99');
            ");
        ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <p class="text-center">Ваша корзина пуста.</p>
<?php endif; ?>
</div>
</div>


<div class="mt-5 mb-5">
    <h2 class="text-center mb-4">Зоны доставки</h2>
<div class="text-center">
    <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A99534d149efc5429f10f2e422fc779e8661c619e790dd651dd44b93811b2d36e&amp;width=100%25&amp;height=460&amp;lang=ru_RU&amp;scroll=true"></script>
</div>
</div>


<?php
$csrfToken = Yii::$app->request->getCsrfToken();
$updateQuantityUrl = Url::to(['cart/update-quantity']);
$decreaseUrl = Url::to(['cart/decrease']);
$deleteItemUrl = Url::to(['cart/delete-item']);
$isGuest = Yii::$app->user->isGuest ? 'true' : 'false';

$this->registerJs(<<<JS
const isGuest = $isGuest;

document.querySelectorAll('.quantity-btn').forEach(button => {
    button.addEventListener('click', function () {
        const itemId = this.dataset.itemId;
        const quantityElem = document.querySelector(`.item-quantity[data-item-id="\${itemId}"]`);
        let quantity = parseInt(quantityElem.textContent);

        if (this.classList.contains('plus')) {
            quantity++;
        } else if (this.classList.contains('minus')) {
            if (quantity <= 1) return;
            quantity--;
        }

        fetch('$updateQuantityUrl?item_id=' + itemId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': '$csrfToken'
            },
            body: 'quantity=' + quantity
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                quantityElem.textContent = data.quantity;
                const totalElem = document.querySelector('#cart-total-price');
                if (totalElem && data.totalPrice) {
                    totalElem.textContent = data.totalPrice;
                }
            } else {
                alert(data.message || 'Ошибка при обновлении количества');
            }
        })
        .catch(error => {
            console.error('Ошибка запроса:', error);
        });
    });
});

document.querySelectorAll('.delete-item-btn').forEach(button => {
    button.addEventListener('click', function () {
        const itemId = this.dataset.id;
        if (!confirm('Удалить товар из корзины?')) return;

        const deleteUrl = isGuest
            ? '$decreaseUrl?id=' + itemId + '&force=1'
            : '$deleteItemUrl?id=' + itemId;

        fetch(deleteUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '$csrfToken',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const itemElem = this.closest('.cart-item');
                itemElem.remove();
                recalculateTotalPrice();
            } else {
                alert(data.message || 'Ошибка при удалении товара');
            }
        })
        .catch(error => {
            console.error('Ошибка при удалении:', error);
        });
    });
});

function recalculateTotalPrice() {
    const cartItems = document.querySelectorAll('.cart-item');
    const totalElem = document.querySelector('#cart-total-price');
    const formElem = document.querySelector('form[action*="checkout"]');
    const orderSumText = document.querySelector('h4');
    const deliveryNote = document.querySelector('p.text-muted');
    const customerDataSection = document.getElementById('customer-data-section');

    let total = 0;
    cartItems.forEach(item => {
        const price = parseFloat(item.querySelector('[data-price]').dataset.price);
        const quantity = parseInt(item.querySelector('.item-quantity').textContent);
        total += price * quantity;
    });

    if (cartItems.length > 0) {
        if (totalElem) totalElem.textContent = total.toFixed(2) + ' ₽';
    } else {
        if (totalElem) totalElem.closest('div').remove();
        if (formElem) formElem.remove();
        if (orderSumText) orderSumText.remove();
        if (deliveryNote) deliveryNote.remove();
        if (customerDataSection) customerDataSection.remove();

        const cartContent = document.getElementById('cart-content');
        if (cartContent) {
            cartContent.innerHTML = '<p class="text-center cart-empty-text">Ваша корзина пуста.</p>';
        }
    }
}
JS);
?>
</div>


