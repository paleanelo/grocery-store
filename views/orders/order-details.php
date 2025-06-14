<?php
/** @var app\models\Orders $order */
/** @var array $products */
use yii\helpers\Html;
use yii\helpers\Url;
?>

<h3>Детали заказа №<?= $order->id ?></h3>
<p><strong>Имя:</strong> <?= Html::encode($order->customer_name) ?></p>
<p><strong>Телефон:</strong> <?= Html::encode($order->customer_phone) ?></p>
<p><strong>Адрес:</strong> <?= Html::encode($order->customer_address) ?></p>
<p><strong>Статус:</strong> <?= Html::encode($order->status->name ?? 'Неизвестно') ?></p>
<p><strong>Итоговая сумма:</strong> <?= number_format($order->total_price, 2, ',', ' ') . ' ₽' ?></p>

<hr>
<h4 class="mb-4">Товары:</h4>
<div class="order-items">
    <?php foreach ($order->items as $item): ?>
        <?php
            $images = \app\models\ProductImage::find()
                ->where(['product_id' => $item->product_id])
                ->all();

            $mainImage = null;
            foreach ($images as $img) {
                if ($img->is_main) {
                    $mainImage = $img;
                    break;
                }
            }

            if (!$mainImage && !empty($images)) {
                $mainImage = $images[0];
            }

            // Нормализуем путь к изображению
            $imageSrc = $mainImage && $mainImage->image_path
                ? (preg_match('/^https?:\/\//', $mainImage->image_path)
                    ? $mainImage->image_path
                    : Yii::getAlias('@web') . '/' . ltrim($mainImage->image_path, '/\\'))
                : Yii::getAlias('@web') . '/img/noimage.jpg';
        ?>

        <div class="order-item d-flex align-items-start gap-3 mb-3" style="border-bottom: 1px solid #ccc; padding-bottom: 10px;">
            <img src="<?= Html::encode($imageSrc) ?>" alt="Фото товара" width="60" height="60" style="object-fit: cover; border-radius: 4px; flex-shrink: 0;">

            <div class="d-flex flex-column justify-content-start" style="margin: 0; padding: 0;">
                <strong class="mb-1"><?= Html::encode($item->product_name) ?></strong>
                <div class="d-flex gap-4">
                    <span>Цена:<br> <?= number_format($item->price, 2, ',', ' ') ?> ₽</span>
                    <span>Количество:<br> <?= Html::encode($item->quantity) ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
