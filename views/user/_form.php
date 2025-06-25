<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .help-block {
        color: red !important;
    }

    .has-error .form-control {
        border-color: red;
    }

    .btn-orange {
        background-color: #fd7e14;
        color: #fff;
        border: none;
    }

    .btn-orange:hover {
        background-color: #e96b06;
    }

    .form-control:focus {
        border-color: #fd7e14;
        box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25);
    }

    a.orange-link {
        color: #fd7e14;
        text-decoration: none;
    }

    a.orange-link:hover {
        text-decoration: underline;
        color: #e96b06;
    }
</style>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="w-100" style="max-width: 400px;">
        <h2 class="text-center mb-4"><?= Html::encode($this->title) ?></h2>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'username')->textInput(['maxlength' => true])->label('Логин') ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true])->label('Пароль') ?>

        <?= $form->field($model, 'agree')->checkbox([
            'id' => 'agree-checkbox',
            'label' => 'Даю согласие на <a href="' . \yii\helpers\Url::to('@web/files/soglasiye-polzovatelya-sayta.pdf') . '" target="_blank">обработку персональных данных</a>',
            'labelOptions' => ['style' => 'font-weight: normal'],
        ])->label(false) ?>

        <div class="d-grid mb-3">
            <?= Html::submitButton('Зарегистрироваться', [
                'id' => 'submit-button',
                'disabled' => true,
                'class' => 'btn btn-orange']) ?>
        </div>
        
        <?php
        $script = <<< JS
            const checkbox = document.getElementById('agree-checkbox');
            const submitButton = document.getElementById('submit-button');

            checkbox.addEventListener('change', function(){
                submitButton.disabled = !this.checked;
            });
            JS;
            $this->registerJs($script);
        ?>
        <?php ActiveForm::end(); ?>

        <div class="text-center">
            <p class="text-muted mb-1">Уже есть аккаунт?</p>
            <a href="<?= \yii\helpers\Url::to(['/site/login']) ?>" class="orange-link">Войти</a>
        </div>
    </div>
</div>
