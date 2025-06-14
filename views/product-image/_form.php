<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Product;

/** @var yii\web\View $this */
/** @var app\models\ProductImage $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="product-image-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'product_id')->dropDownList(
        ArrayHelper::map(Product::find()->orderBy('name')->all(), 'id', 'name'),
        ['prompt' => 'Выберите товар']
    )->label('Товар') ?>

    <?= $form->field($model, 'imageFile')->fileInput()->label('Загрузить фото') ?>

    <?= $form->field($model, 'is_main')->checkbox([
        'label' => 'Сделать фото заглавным',
        'uncheck' => 0,
        'value' => 1,
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
