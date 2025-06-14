<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Сброс пароля';
?>

<h1 class="mt-5 pt-5"><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'password')->passwordInput()->label('Введите новый пароль') ?>
    <div class="form-group">
        <?= Html::submitButton('Сменить пароль', ['class' => 'btn btn-success']) ?>
    </div>
<?php ActiveForm::end(); ?>
