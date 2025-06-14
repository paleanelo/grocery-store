<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Личный кабинет';
?>

<div class="container mt-5 pt-5 text-center">
    <h2 class="mb-4"><?= Html::encode($this->title) ?></h2>

    <div class="d-flex justify-content-center">
        <?php $form = ActiveForm::begin([
            'action' => ['user/update-profile'],
            'options' => ['style' => 'max-width: 400px; width: 100%; text-align: left;']
        ]); ?>
            <?= $form->field($user, 'username')->textInput(['class' => 'form-control'])->label('Логин') ?>
            <?= $form->field($user, 'full_name')->textInput(['class' => 'form-control']) ?>
            <?= $form->field($user, 'email')->input('email', ['class' => 'form-control'])->label('Адрес электронной почты') ?>

            <div class="text-center mt-4">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success me-2']) ?>
                <?= Html::a('Удалить профиль', ['user/delete-profile'], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить профиль?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="orders mt-5">
        <h3 class="mb-4">История заказов</h3>

        <?php if (empty($orders)): ?>
            <p class="text-muted">Ваша история заказов пуста.</p>
            <?= Html::a('Перейти в каталог', ['product/catalog'], ['class' => 'text-success catalog-link', 'style' => 'font-weight: 500;']) ?>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-info">
                        <div><strong>Заказ №<?= $order->id ?></strong></div>
                        <div>Сумма: <?= number_format($order->total_price, 2, ',', ' ') . ' ₽' ?></div>
                        <div>Дата: <?= Yii::$app->formatter->asDatetime($order->created_at, 'php:d.m.Y H:i') ?></div>
                        <div class="order-status">Статус: <?= Html::encode($order->status->name ?? 'Неизвестно') ?></div>
                    </div>
                    <button class="btn-detail-warning" data-order-id="<?= $order->id ?>">Детали заказа</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Модальное окно -->
<div id="orderModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="modalContent">Загрузка...</div>
    </div>
</div>

<?php
$detailsUrl = Url::to(['/orders/details']);
$csrf = Yii::$app->request->getCsrfToken();
$script = <<<JS
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

document.querySelector('.close').onclick = function() {
    document.getElementById('orderModal').style.display = 'none';
};

window.onclick = function(event) {
    if (event.target === document.getElementById('orderModal')) {
        document.getElementById('orderModal').style.display = 'none';
    }
};
JS;
$this->registerJs($script);
?>
