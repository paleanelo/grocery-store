<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

$this->title = 'Каталог товаров';
?>

<div class="container mt-5 pt-5">
    <h1 class="text-center mb-4"><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'catalog-container', 'timeout' => 5000]); ?>

    <!-- Поиск -->
    <div class="search-bar pt-3 mb-3 d-flex flex-column align-items-center">

        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['product/catalog'],
            'options' => [
                'data-pjax' => true,
                'class' => 'w-100',
                'id' => 'product-search-form',
                'style' => 'max-width: 700px;',
            ],
            'fieldConfig' => ['template' => "{input}\n{error}"],
        ]); ?>

        <!-- Скрытое поле для передачи выбранной категории -->
        <?= Html::hiddenInput('ProductSearch[categoryName]', $searchModel->categoryName, ['id' => 'category-hidden-input']) ?>

        <div class="d-flex w-100 mb-2" style="gap: 10px;">
            <?= Html::activeTextInput($searchModel, 'name', [
                'class' => 'form-control',
                'placeholder' => 'Поиск по названию',
                'style' => 'flex-grow:1; height: 38px;',
            ]) ?>
            <?= Html::submitButton('Найти', [
                'class' => 'btn btn-success',
                'style' => 'height: 38px; white-space: nowrap;',
            ]) ?>
        </div>

        <!-- Сортировка по центру -->
        <div class="sort-bar d-flex justify-content-center align-items-center w-100 mt-4" style="gap: 10px;">
            <label for="sort-select" class="mb-0">Сортировать по:</label>
            <?= Html::dropDownList('ProductSearch[sort]', $searchModel->sort, [
                '' => 'По умолчанию',
                'price_asc' => 'Цене (возрастание)',
                'price_desc' => 'Цене (убывание)',
                'date' => 'Дате добавления',
            ], [
                'class' => 'form-select',
                'id' => 'sort-select',
                'style' => 'max-width: 200px;',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <!-- Категории -->
    <div class="category-buttons pt-3 mb-4 text-center">
        <button class="<?= empty($searchModel->categoryName) ? 'btn btn-success m-1' : 'btn btn-outline-success m-1' ?>"
                data-category="">
            Смотреть все
        </button>
        <?php foreach ($categories as $category): ?>
            <button class="<?= ($searchModel->categoryName === $category->name) ? 'btn btn-success m-1' : 'btn btn-outline-success m-1' ?>"
                    data-category="<?= Html::encode($category->name) ?>">
                <?= Html::encode($category->name) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Список товаров -->
    <div id="product-list" class="mb-5">
        <?= $this->render('_product_list', ['dataProvider' => $dataProvider]) ?>
    </div>

    <?php Pjax::end(); ?>
</div>

<?php
$this->registerJs(<<<JS
// Переключение изображений
document.addEventListener('click', function (event) {
    if (event.target.classList.contains('dot')) {
        const dot = event.target;
        const index = dot.getAttribute('data-index');
        const card = dot.closest('.card');
        const dots = card.querySelectorAll('.dot');
        const images = card.querySelectorAll('.product-image');

        if (!images[index]) return;

        images.forEach(img => img.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        images[index].classList.add('active');
        dot.classList.add('active');
    }
});

// При выборе сортировки — отправить форму (с сохранённой категорией)
document.addEventListener('change', function (event) {
    if (event.target.id === 'sort-select') {
        const form = document.getElementById('product-search-form');
        if (form) {
            form.submit();
        }
    }
});

// При выборе категории — обновить скрытое поле и отправить форму
document.addEventListener('click', function (event) {
    if (event.target.matches('button[data-category]')) {
        const category = event.target.getAttribute('data-category');
        const categoryInput = document.getElementById('category-hidden-input');
        if (categoryInput) {
            categoryInput.value = category;
        }

        const form = document.getElementById('product-search-form');
        if (form) {
            form.submit();
        }
    }
});
JS);
?>
