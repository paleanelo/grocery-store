<?php

use app\models\Orders;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\Status;

/** @var yii\web\View $this */
/** @var app\models\OrdersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Заказы';
?>
<div class="container mt-custom">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'summary' => 'Показано {begin}–{end} из {totalCount} заказов',
    'emptyText' => 'По вашему запросу ничего не найдено.',
    'pager' => [
        'firstPageLabel' => 'Первая',
        'lastPageLabel' => 'Последняя',
        'prevPageLabel' => '«',
        'nextPageLabel' => '»',
    ],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        ['attribute' => 'id', 'label' => 'Номер заказа'],
        ['attribute' => 'customer_name', 'label' => 'Имя'],
        ['attribute' => 'customer_phone', 'label' => 'Контактный телефон'],
        ['attribute' => 'customer_address', 'label' => 'Адрес доставки'],
        // ['attribute' => 'user_id', 'label' => 'ID учётной записи'],
        ['attribute' => 'total_price', 'label' => 'Сумма'],
        ['attribute' => 'created_at', 'label' => 'Дата'],
        [
            'attribute' => 'status_id',
            'value' => 'status.name',
            'label' => 'Статус',
        ],

        [
            'label' => 'Изменить статус',
            'format' => 'raw',
            'value' => function ($model) {
                $statuses = ArrayHelper::map(Status::find()->all(), 'id', 'name');
                return Html::dropDownList('status', $model->status_id, $statuses, [
                    'class' => 'form-select status-dropdown',
                    'data-id' => $model->id,
                ]);
            },
        ],

        [
            'label' => 'Детали',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::button('Подробнее', [
                    'class' => 'btn btn-outline-primary btn-sm btn-detail-warning',
                    'data-order-id' => $model->id,
                ]);
            },
        ],
    ],
]); ?>


<?php
$detailsUrl = Url::to(['/orders/admin-details']);
$statusUrl = Url::to(['/orders/change-status']);
$csrf = Yii::$app->request->getCsrfToken();

$this->registerJs(<<<JS
// Открытие модального окна с деталями заказа
document.querySelectorAll('.btn-detail-warning').forEach(btn => {
    btn.addEventListener('click', () => {
        const orderId = btn.getAttribute('data-order-id');
        const modal = document.getElementById('orderModal');
        const content = document.getElementById('modalContent');
        modal.style.display = 'block';
        content.innerHTML = 'Загрузка...';

        fetch('$detailsUrl?id=' + orderId, {
            headers: {'X-CSRF-Token': '$csrf'}
        })
        .then(response => response.text())
        .then(html => content.innerHTML = html)
        .catch(err => content.innerHTML = 'Ошибка загрузки');
    });
});

// Закрытие модального окна
document.querySelector('.close').onclick = function() {
    document.getElementById('orderModal').style.display = 'none';
};
window.onclick = function(event) {
    if (event.target === document.getElementById('orderModal')) {
        document.getElementById('orderModal').style.display = 'none';
    }
};

// Обработка смены статуса
document.querySelectorAll('.status-dropdown').forEach(select => {
    select.addEventListener('change', function() {
        const orderId = this.getAttribute('data-id');
        const statusId = this.value;

        fetch('$statusUrl', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '$csrf'
            },
            body: JSON.stringify({id: orderId, status_id: statusId})
        }).then(response => {
            if (!response.ok) alert('Ошибка при обновлении статуса');
        });
    });
});
JS);
?>

<!-- Модальное окно -->
<div id="orderModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="modalContent">Загрузка...</div>
    </div>
</div>
</div>

