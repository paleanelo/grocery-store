<?php

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Изображения товаров';
?>

<div class="container mt-custom">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- <?= Html::a('Добавить изображение', ['product-image/create'], ['class' => 'btn btn-success my-3']) ?> -->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => 'Показано {begin}–{end} из {totalCount} изображений',
        'emptyText' => 'По вашему запросу ничего не найдено.',
        'pager' => [
            'firstPageLabel' => 'Первая',
            'lastPageLabel' => 'Последняя',
            'prevPageLabel' => '«',
            'nextPageLabel' => '»',
        ],
        'columns' => [
            [
                'attribute' => 'product_id',
                'label' => 'Товар',
                'value' => fn($model) => $model->product->name ?? '(не найден)',
                'filter' => Html::activeTextInput($searchModel, 'productName', ['class' => 'form-control']),
            ],
            [
                'attribute' => 'image_path',
                'contentOptions' => [
                    'style' => 'max-width: 600px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;',
                ],
            ],
            [
                'attribute' => 'is_main',
                'format' => 'boolean',
                'label' => 'Заглавное фото',
            ],            
            [
                'class' => 'yii\grid\ActionColumn',
                'controller' => 'product-image',
                'template' => '{update} {delete}',
            ],
        ],
    ]) ?>
</div>
