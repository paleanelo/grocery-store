<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Product $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-view mt-custom">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить запись?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php if (!empty($model->images)): ?>
        <div class="mb-4 d-flex flex-wrap gap-3">
            <?php foreach ($model->images as $image): ?>
                <?php
                    $imageSrc = preg_match('/^https?:\/\//', $image->image_path)
                        ? $image->image_path
                        : Yii::getAlias('@web') . '/' . ltrim($image->image_path, '/\\');
                ?>
                <div style="width: 160px; height: 180px; position: relative;">
                    <img src="<?= Html::encode($imageSrc) ?>"
                        alt="Фото товара"
                        class="img-thumbnail"
                        style="width: 100%; height: 160px; object-fit: cover;">

                    <?php if ($image->is_main): ?>
                        <div class="text-center text-success small fw-bold mt-1 mb-2">Заглавное</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

        </div>
    <?php endif; ?>          

    <?= DetailView::widget([
        'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'nullDisplay' => 'не указано',
        ],
    'model' => $model,
    'attributes' => [
        [
            'attribute' => 'category_id',
            'label' => 'Категория',
            'value' => $model->category->name ?? '(не указано)',
        ],
        [
            'attribute' => 'name',
            'label' => 'Название',
        ],
        [
            'attribute' => 'slug',
            'label' => 'Слаг (ЧПУ)',
        ],
        [
            'attribute' => 'price_per_kg',
            'label' => 'Цена за кг',
        ],
        [
            'attribute' => 'price_per_box',
            'label' => 'Цена за коробку',
        ],
        [
            'attribute' => 'avg_weight',
            'label' => 'Средний вес (кг)',
        ],
        [
            'attribute' => 'description',
            'label' => 'Описание',
            'format' => 'ntext',
        ],
        [
            'attribute' => 'is_active',
            'label' => 'Активен',
            'value' => $model->is_active ? 'Да' : 'Нет',
        ],
        [
            'attribute' => 'expire_days',
            'label' => 'Скрыть через (дней)',
        ],
        [
            'attribute' => 'created_at',
            'label' => 'Создано',
        ],
        [
            'attribute' => 'updated_at',
            'label' => 'Обновлено',
        ],
    ],
]) ?>


</div>
