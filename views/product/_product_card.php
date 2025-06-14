<?php
use yii\helpers\Html;

/** @var \app\models\Product $model */
$product = $model;

$images = $model->productImages;

$imageHtml = '';
$dotsHtml = '';

if (!empty($images)) {
    foreach ($images as $index => $img) {
        // Проверяем, внешний это URL или относительный путь
        $imageSrc = (preg_match('/^https?:\/\//', $img->image_path))
            ? $img->image_path
            : Yii::getAlias('@web') . '/' . ltrim($img->image_path, '/');

        $isActive = $img->is_main || (!$model->mainImage && $index === 0);

        $imageHtml .= Html::img($imageSrc, [
            'class' => 'product-image rounded-top' . ($isActive ? ' active' : ''),
            'data-index' => $index,
            'alt' => $model->name
        ]);

        $dotsHtml .= Html::tag('div', '', [
            'class' => 'dot' . ($isActive ? ' active' : ''),
            'data-index' => $index
        ]);
    }
} else {
    // Если нет изображений — заглушка
    $imageSrc = Yii::getAlias('@web') . '/img/noimage.jpg';
    $imageHtml .= Html::img($imageSrc, [
        'class' => 'product-image rounded-top active',
        'alt' => $model->name
    ]);
}

// Цена
$priceText = $model->price_per_box
    ? 'Цена за ящик: <strong>' . $model->price_per_box . ' ₽</strong>'
    : ($model->price_per_kg
        ? 'Цена за кг: <strong>' . $model->price_per_kg . ' ₽</strong>'
        : 'Цена не указана');

// Вес
$weightText = $model->avg_weight
    ? 'Примерный вес: <strong>' . number_format($model->avg_weight, 2) . ' кг</strong>'
    : 'Примерный вес: не указан';
?>

<div class="card h-100 d-flex flex-column">
    <div class="product-images position-relative text-center">
        <?= $imageHtml ?>
    </div>

    <div class="product-dots d-flex justify-content-center mt-2">
        <?php if (count($images) > 1): ?>
            <?= $dotsHtml ?>
        <?php else: ?>
            <span class="dot invisible">•</span>
        <?php endif; ?>
    </div>

    <div class="card-body d-flex flex-column">
        <h5 class="card-title mb-2"><?= Html::encode($model->name) ?></h5>

        <p class="card-text mb-1"><?= $weightText ?></p>
        <p class="card-text mb-3"><?= $priceText ?></p>

        <div class="cart-action-wrapper mt-auto mb-2 position-relative" data-id="<?= $product->id ?>">
            <button class="btn btn-warning w-100 add-to-cart" data-id="<?= $product->id ?>" type="button">
                Добавить в корзину
            </button>
        </div>
    </div>
</div>

