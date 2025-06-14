<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Category;

/** @var yii\web\View $this */
/** @var app\models\Product $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'category_id')->dropDownList(
        ArrayHelper::map(Category::find()->orderBy('name')->all(), 'id', 'name'),
        ['prompt' => 'Выберите категорию']
    )->label('Категория') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Название товара') ?>

    <?= $form->field($model, 'price_per_kg')->textInput()->label('Цена за кг') ?>

    <?= $form->field($model, 'price_per_box')->textInput()->label('Цена за коробку') ?>

    <?= $form->field($model, 'avg_weight')->textInput()->label('Средний вес (кг)') ?>

    <!-- <?= $form->field($model, 'description')->textarea(['rows' => 6])->label('Описание') ?> -->

    <?= $form->field($model, 'is_active')->checkbox([
        'label' => 'Отображение',
        'uncheck' => 0,
        'value' => 1
    ]) ?>

    <?= $form->field($model, 'expire_days')->textInput()->label('Автоудаление через (дней)') ?>

    <?= $form->field($model, 'mainImageFile')->fileInput()->label('Главное фото') ?>

    <?= $form->field($model, 'extraImageFiles[]')->fileInput([
        'multiple' => true,
        'accept' => 'image/*'
    ])->label('Дополнительные фото (до 4)') ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
