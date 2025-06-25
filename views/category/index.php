<?php

use app\models\Category;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\CategorySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Категории';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index mt-custom">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить категорию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => 'Показано {begin}–{end} из {totalCount} категорий',
        'emptyText' => 'По вашему запросу ничего не найдено.',
        'pager' => [
            'firstPageLabel' => 'Первая',
            'lastPageLabel' => 'Последняя',
            'prevPageLabel' => '«',
            'nextPageLabel' => '»',
        ],
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'name',
                'label' => 'Название'
            ],
            [
                'attribute' => 'slug',
                'label' => 'Слаг'
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}', // можно указать только {delete}, если нужно
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        return Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-outline-danger btn-sm',
                            'title' => 'Удалить',
                            'aria-label' => 'Удалить',
                            'data' => [
                                'confirm' => 'Вы уверены, что хотите удалить эту категорию?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
                'urlCreator' => function ($action, Category $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
            ],
        ],
    ]); ?>

</div>
