<?php
use yii\widgets\ListView;
use yii\widgets\LinkPager;

echo ListView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => ''],
    'itemOptions' => ['class' => ''],
    'layout' => "
        <div class='row g-4 row-cols-1 row-cols-sm-2 row-cols-lg-3'>
            {items}
        </div>
        <div class='d-flex justify-content-center mt-5 mb-5'>
            {pager}
        </div>
    ",
    'itemView' => '_product_card',
    'emptyText' => 'Товары не найдены.',
        'pager' => [
        'class' => LinkPager::class,
        'prevPageLabel' => '<i class="bi bi-chevron-left"></i>',
        'nextPageLabel' => '<i class="bi bi-chevron-right"></i>',
        'maxButtonCount' => 5,
        'options' => ['class' => 'pagination'],
        'linkOptions' => ['class' => 'page-link'],
        'disabledPageCssClass' => 'disabled',
        'activePageCssClass' => 'active',
        'pageCssClass' => 'page-item',
        'prevPageCssClass' => 'page-item',
        'nextPageCssClass' => 'page-item',
    ],
]);
