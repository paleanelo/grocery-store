<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\OrdersItem $model */

$this->title = 'Create Orders Item';
$this->params['breadcrumbs'][] = ['label' => 'Orders Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
