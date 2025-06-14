<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
?>

<div class="banner-section text-white d-flex align-items-center">
    <div class="container">
        <div class="banner-content text-start">
            <h1 class="mb-3">Свежие фрукты и овощи с доставкой</h1>
            <p class="lead">
                Мы — онлайн-магазин, работающий только на доставку. Без очередей, без хлопот — просто закажите, и мы привезём всё к вашей двери.
            </p>
            <a href="https://vk.com/im?entrypoint=community_page&media=&sel=-185268138" class="btn btn-warning mt-4">Связаться с нами</a>
        </div>
    </div>
</div>




<h2 class="mb-5 text-center">Недавно добавленные товары</h2>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'row justify-content-center'],
    'itemOptions' => ['class' => 'col-12 col-sm-10 col-md-6 col-lg-4 d-flex mb-4'],
    'layout' => "{items}\n{pager}",
    'itemView' => function ($model) {
        /** @var \app\models\Product $model */
        $images = $model->productImages;

        $imageHtml = '';
        $dotsHtml = '';
        
        if (!empty($images)) {
            foreach ($images as $index => $img) {
                $imageSrc = (preg_match('/^https?:\/\//', $img->image_path))
                    ? $img->image_path
                    : Yii::getAlias('@web') . '/' . ltrim($img->image_path, '/');

                $isActive = $img->is_main || (!$model->mainImage && $index === 0);

                $imageHtml .= Html::img($imageSrc, [
                    'class' => 'product-image rounded-top' . ($isActive ? ' active' : ''),
                    'data-index' => $index,
                    'alt' => $model->name
                ]);

                $dotsHtml .= Html::tag('div', '', [
                    'class' => 'dot' . ($isActive ? ' active' : ''),
                    'data-index' => $index
                ]);
            }
        } else {
            $imageSrc = Yii::getAlias('@web') . '/img/noimage.jpg';
            $imageHtml .= Html::img($imageSrc, [
                'class' => 'product-image rounded-top active',
                'alt' => $model->name
            ]);
        }

        $dotsContainer = Html::tag(
            'div',
            count($images) > 1 ? $dotsHtml : Html::tag('span', '•', ['class' => 'dot invisible']),
            ['class' => 'product-dots d-flex justify-content-center mt-2']
        );

        $priceText = $model->price_per_box ? 'Цена за ящик: <strong>' . $model->price_per_box . ' ₽</strong>' :
            ($model->price_per_kg ? 'Цена за кг: <strong>' . $model->price_per_kg . ' ₽</strong>' : 'Цена не указана');

        $weightText = $model->avg_weight
            ? 'Примерный вес: <strong>' . number_format($model->avg_weight, 2) . ' кг</strong>'
            : 'Примерный вес: не указан';

        return Html::tag('div',
            Html::tag('div', $imageHtml, ['class' => 'product-images position-relative text-center']) .
            $dotsContainer .
            Html::tag('div',
                Html::tag('h5', Html::encode($model->name), ['class' => 'card-title mb-2']) . // ⬅️ отступ снизу
                Html::tag('p', $weightText, ['class' => 'card-text mb-1']) .                  // ⬅️ отступ снизу
                Html::tag('p', $priceText, ['class' => 'card-text mb-3']) .                   // ⬅️ отступ снизу
                Html::tag('div',
                    Html::button('Добавить в корзину', [
                        'class' => 'btn btn-warning w-100 add-to-cart',
                        'data-id' => $model->id,
                        'type' => 'button'
                    ]) .
                    Html::tag('div',
                        Html::button('-', [
                            'class' => 'btn btn-sm btn-outline-danger decrease-qty',
                            'data-id' => $model->id
                        ]) .
                        Html::tag('span', '1', [
                            'class' => 'mx-2 quantity-value'
                        ]) .
                        Html::button('+', [
                            'class' => 'btn btn-sm btn-outline-success increase-qty',
                            'data-id' => $model->id
                        ]),
                        ['class' => 'd-flex justify-content-between align-items-center w-100 quantity-controls d-none']
                    ),
                    ['class' => 'cart-action-wrapper mt-auto mb-2', 'data-id' => $model->id]
                ),
                ['class' => 'card-body d-flex flex-column'] // ⬅️ обязательно!
            ),
            ['class' => 'card h-100 w-100 d-flex flex-column'] // ⬅️ обеспечиваем полную высоту и колонку
        );
    }
    ]) ?>
</div>

<div class="text-center mt-4">
    <?= Html::a('Перейти в каталог', ['product/catalog'], ['class' => 'btn btn-outline-primary']) ?>
</div>






<section class="reviews-section text-white py-5 bg-success mt-5">
    <div class="container">
        <h2 class="mb-5 text-center">Отзывы наших клиентов</h2>

        <div class="swiper-container-wrapper position-relative">
            <div class="swiper-button-prev text-white"></div>
            <div class="swiper-button-next text-white"></div>

            <div class="swiper reviews-slider">
                <div class="swiper-wrapper">

                    <!-- Отзыв 1 -->
                    <div class="swiper-slide">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?= Yii::getAlias('@web') ?>/img/user.jpg" class="rounded-circle me-3" alt="Игорь Смирнов" width="48" height="48">
                                    <h5 class="mb-0">Елизавета Боярская</h5>
                                </div>
                                <p class="card-text text-dark">Спасибо за заказ, отдельное спасибо за комплимент, мята в самый раз.</p>
                                <div class="review-date mt-auto text-muted small">Опубликовано: 28.04.2025</div>
                            </div>
                        </div>
                    </div>

                    <!-- Отзыв 2 -->
                    <div class="swiper-slide">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?= Yii::getAlias('@web') ?>/img/user.jpg" class="rounded-circle me-3" alt="Анна Петрова" width="48" height="48">
                                    <h5 class="mb-0">Варвара Королева</h5>
                                </div>
                                <p class="card-text text-dark">Хотела поблагодарить за качество мандаринов и оперативность! Выше всех похвал! Все остались очень довольны👍🏻👍🏻👍🏻</p>
                                <div class="review-date mt-auto text-muted small">Опубликовано: 24.12.2024</div>
                            </div>
                        </div>
                    </div>

                    <!-- Отзыв 3 -->
                    <div class="swiper-slide">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?= Yii::getAlias('@web') ?>/img/user.jpg" class="rounded-circle me-3" alt="Марина К." width="48" height="48">
                                    <h5 class="mb-0">Александра Гончарова</h5>
                                </div>
                                <p class="card-text text-dark">Заказ привезли на следующий день вовремя, очень красивая и вкусная малина, спасибо.</p>
                                <div class="review-date mt-auto text-muted small">Опубликовано: 13.09.2024</div>
                            </div>
                        </div>
                    </div>

                    <!-- Отзыв 4 -->
                    <div class="swiper-slide">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?= Yii::getAlias('@web') ?>/img/user.jpg" class="rounded-circle me-3" alt="Сергей Т." width="48" height="48">
                                    <h5 class="mb-0">Светлана Кошелева</h5>
                                </div>
                                <p class="card-text text-dark">Почитала отрицательные отзывы - очень удивлена. Заказываю здесь на протяжении 2х лет. Все всегда наисвежайшее, очень вкусное.</p>
                                <div class="review-date mt-auto text-muted small">Опубликовано: 15.08.2024</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="https://vk.com/reviews-185268138" target="_blank" class="btn btn-outline-light">
                Смотреть все отзывы в группе Вконтакте
            </a>
        </div>
    </div>
</section>


<section class="faq-section pt-5 pb-3">
    <div class="container">
        <h2 class="mb-5 text-center">Часто задаваемые вопросы</h2>

        <div class="accordion custom-accordion" id="faqAccordion">
            <?php
            $questions = [
                ['q' => 'Как оформить заказ?', 'a' => 'Чтобы оформить заказ, добавьте товары в корзину и следуйте инструкциям на странице оформления заказа.'],
                ['q' => 'Как осуществляется доставка?', 'a' => 'Доставка осуществляется курьером на следующий день после оформления заказа.'],
                ['q' => 'Можно ли выбрать время доставки?', 'a' => 'Да, после оформления заказа мы свяжемся с вами для уточнения деталей и времени доставки.'],
                ['q' => 'Какие способы оплаты доступны?', 'a' => 'Вы можете оплатить заказ наличными или переводом при получении.'],
                ['q' => 'Что делать, если товар оказался некачественным?', 'a' => 'Вы можете связаться с нашей службой поддержки, и мы оперативно решим проблему.'],
            ];

            foreach ($questions as $index => $item): ?>
                <div class="accordion-item border-0 border-bottom pb-2 mb-3" style="border-color: #ddd !important;">
                    <h2 class="accordion-header" id="heading<?= $index ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse<?= $index ?>" aria-expanded="false"
                                aria-controls="collapse<?= $index ?>">
                            <?= Html::encode($item['q']) ?>
                        </button>
                    </h2>
                    <div id="collapse<?= $index ?>" class="accordion-collapse collapse"
                         aria-labelledby="heading<?= $index ?>" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <?= Html::encode($item['a']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<section class="advantages-section py-4 mb-5">
  <div class="container">
    <h2 class="mb-5 text-start">Наши преимущества</h2>
    <div class="row gx-5 gy-4">
      <div class="col-12 col-md-4 d-flex align-items-start">
        <img src="<?= Yii::getAlias('@web') ?>/img/icons/adv1.png" alt="Свежие продукты" width="70" height="70" class="me-3">
        <div>
          <h5 class="mb-2">Свежие продукты</h5>
          <p class="mb-0 text-muted">Мы закупаем только свежие товары<br>от проверенных поставщиков.</p>
        </div>
      </div>
      <div class="col-12 col-md-4 d-flex align-items-start">
        <img src="<?= Yii::getAlias('@web') ?>/img/icons/adv2.png" alt="Быстрая доставка" width="70" height="70" class="me-3">
        <div>
          <h5 class="mb-2">Быстрая доставка</h5>
          <p class="mb-0 text-muted">Оперативная доставка прямо к вашей двери в удобное время.</p>
        </div>
      </div>
      <div class="col-12 col-md-4 d-flex align-items-start">
        <img src="<?= Yii::getAlias('@web') ?>/img/icons/adv3.png" alt="Экологичность" width="70" height="70" class="me-3">
        <div>
          <h5 class="mb-2">Экологичность</h5>
          <p class="mb-0 text-muted">Мы заботимся об экологии, предлагая продукты без вредных добавок.</p>
        </div>
      </div>
    </div>
  </div>
</section>


<style>
.accordion .accordion-button:not(.collapsed) {
    color: #f57c00 !important;
}
</style>


<?php
$this->registerJs(<<<JS
function initializeDots() {
    document.querySelectorAll('.product-dots').forEach(dotGroup => {
        const card = dotGroup.closest('.card');
        const dots = dotGroup.querySelectorAll('.dot');
        const images = card.querySelectorAll('.product-image');

        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                const index = dot.getAttribute('data-index');

                images.forEach(img => img.classList.remove('active'));
                dots.forEach(d => d.classList.remove('active'));

                images[index].classList.add('active');
                dot.classList.add('active');
            });
        });
    });
}

initializeDots();
JS);
?>