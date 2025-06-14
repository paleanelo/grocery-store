<?php
use yii\helpers\Html;

$this->title = 'Панель администратора';
?>

<div class="container mt-5 pt-5 text-center">
    <h1 class="mb-4">Панель администратора</h1>
    <div class="d-flex flex-column align-items-center gap-3" style="max-width: 300px; margin: 0 auto;">
        <?= Html::a('Управлять заказами', ['orders/index'], ['class' => 'btn btn-primary w-100']) ?>
        <?= Html::a('Управлять товарами', ['admin/products'], ['class' => 'btn btn-primary w-100']) ?>
        <?= Html::a('Категории товаров', ['category/index'], ['class' => 'btn btn-secondary w-100']) ?>
        <?= Html::a('Изображения к товарам', ['admin/images'], ['class' => 'btn btn-secondary w-100']) ?>
    </div>
</div>

