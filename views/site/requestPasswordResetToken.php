<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Восстановление пароля';
?>

<h1 class="mt-5 pt-5"><?= Html::encode($this->title) ?></h1>

<p>Введите ваш email. Вам будет отправлена ссылка для сброса пароля.</p>

<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>
