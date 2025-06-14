<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Войти в аккаунт';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    /* Оранжевая тема */
    .btn-orange {
        background-color: #fd7e14;
        color: #fff;
        border: none;
    }

    .btn-orange:hover {
        background-color: #e96b06;
    }

    .form-check-input:checked {
        background-color: #fd7e14;
        border-color: #fd7e14;
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

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'labelOptions' => ['class' => 'form-label'],
                'inputOptions' => ['class' => 'form-control mb-2'],
                'errorOptions' => ['class' => 'invalid-feedback d-block'],
            ],
        ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Логин') ?>

        <?= $form->field($model, 'password')->passwordInput()->label('Пароль') ?>

        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"form-check mb-3\">{input} {label}</div>\n{error}",
            'labelOptions' => ['class' => 'form-check-label'],
            'inputOptions' => ['class' => 'form-check-input'],
        ])->label('Запомнить меня') ?>

        <div class="d-grid mb-3">
            <?= Html::submitButton('Войти', ['class' => 'btn btn-orange']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <div class="text-center">
            <a href="<?= \yii\helpers\Url::to(['/site/request-password-reset']) ?>" class="orange-link">Забыли пароль?</a>
            <p class="text-muted mb-1 mt-4">Ещё нет аккаунта?</p>
            <a href="<?= \yii\helpers\Url::to(['/user/create']) ?>" class="orange-link">Зарегистрируйтесь</a>
        </div>
    </div>
</div>
