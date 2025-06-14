<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProductImage $model */

$this->title = 'Добавить фото к товару';
$this->params['breadcrumbs'][] = ['label' => 'Product Images', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-image-create mt-custom">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
