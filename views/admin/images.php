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
                    'style' => 'max-width: 500px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;',
                ],
                'label' => 'Путь к фото',
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
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="bi bi-trash"></i>', ['product-image/delete', 'id' => $model->id], [
                            'class' => 'btn btn-outline-danger btn-sm',
                            'title' => 'Удалить',
                            'aria-label' => 'Удалить',
                            'data' => [
                                'confirm' => 'Вы уверены, что хотите удалить это изображение?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
            ],
        ],
    ]) ?>
</div>
