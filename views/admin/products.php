<?php

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Управление товарами';
?>

<div class="container mt-custom">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::a('Добавить товар', ['product/create'], ['class' => 'btn btn-success my-3']) ?>
    <?= Html::a('Загрузить товары из ВК', ['vk-parser/parse'], ['class' => 'btn btn-secondary']) ?>

    <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'formatter' => [
        'class' => 'yii\i18n\Formatter',
        'nullDisplay' => 'не указано',
    ],
    'summary' => 'Показано {begin}–{end} из {totalCount} товаров',
    'emptyText' => 'По вашему запросу ничего не найдено.',
    'pager' => [
        'firstPageLabel' => 'Первая',
        'lastPageLabel' => 'Последняя',
        'prevPageLabel' => '«',
        'nextPageLabel' => '»',
    ],
    'columns' => [
        [
            'attribute' => 'category_id',
            'label' => 'Категория',
            'value' => fn($model) => $model->category->name ?? '(не указана)',
            'filter' => Html::activeTextInput($searchModel, 'categoryName', ['class' => 'form-control']),
        ],
        [
            'attribute' => 'name',
            'label' => 'Название',
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
            'attribute' => 'is_active',
            'label' => 'Активен',
            'format' => 'boolean',
        ],
        [
            'attribute' => 'expire_days',
            'label' => 'Скрыть через (дней)',
        ],
        [
            'attribute' => 'created_at',
            'format' => ['date', 'php:Y-m-d H:i'],
            'label' => 'Создано',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'controller' => 'product',
            'template' => '{view} {update} {delete}',
        ],
    ],
]) ?>
</div>
