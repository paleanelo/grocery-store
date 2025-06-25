<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\web\YiiAsset;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerJsVar('baseUrl', \yii\helpers\Url::base());
$this->registerJsFile('@web/js/cart.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <meta name="csrf-token" content="<?= Yii::$app->request->getCsrfToken() ?>">
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column min-vh-100">
<?php $this->beginBody() ?>


<div id="flash-wrapper" style="position: relative; z-index: 1055;">
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <?php
            if ($type === 'error') {
                $bootstrapType = 'danger';
            } else {
                $bootstrapType = $type;
            }
        ?>
        <?php if (!empty($message) && is_string($message)): ?>
            <div class="alert alert-<?= $bootstrapType ?> alert-dismissible fade show flash-fixed" role="alert" data-autoclose="<?= $bootstrapType !== 'danger' ? 'true' : 'false' ?>">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>


<div class="flex-grow-1">
<header id="header" class="bg-success fixed-top">
    <div class="container d-flex justify-content-between align-items-center py-2">
        <!-- Левая часть: Вход / Выход (десктоп) -->
        <div class="d-none d-lg-block" style="white-space: nowrap;">
            <?php if (Yii::$app->user->isGuest): ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/site/login']) ?>" class="text-white text-decoration-none">
                    Войти
                </a>
            <?php else: ?>
                <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline', 'id' => 'logout-form']) ?>
                <a href="#" class="text-white text-decoration-none" style="white-space: nowrap;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Выйти (<?= Yii::$app->user->identity->username ?>)
                </a>
                <?= Html::endForm() ?>
            <?php endif; ?>
        </div>

        <!-- Центр: логотип + название -->
        <div class="text-center w-100 d-lg-flex justify-content-center d-none">
            <span class="text-white h5 d-flex align-items-center justify-content-center py-2 m-0">
                <a href="<?= Yii::$app->homeUrl ?>" class="d-inline-block me-2">
                    <img src="<?= Yii::getAlias('@web') ?>/img/logo-icon.png" alt="Логотип" style="height: 32px;">
                </a>
                Софийская база
            </span>
        </div>

        <!-- Мобилка: логотип и название по центру -->
        <div class="d-block d-lg-none text-center w-100">
            <a href="<?= Yii::$app->homeUrl ?>" class="d-inline-flex align-items-center justify-content-center text-white text-decoration-none">
                <img src="<?= Yii::getAlias('@web') ?>/img/logo-icon.png" alt="Логотип" style="height: 28px;" class="me-2">
                <span class="h5 m-0">Софийская база</span>
            </a>
        </div>

        <!-- Правая часть: иконки (десктоп) -->
        <div class="d-none d-lg-flex align-items-center gap-3">
            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role_id == 1): ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/user/lk']) ?>">
                    <img src="<?= Yii::getAlias('@web') ?>/img/lk-icon.png" alt="Личный кабинет" style="height: 24px;">
                </a>
            <?php endif; ?>

            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role_id == 2): ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/admin/index']) ?>" class="btn btn-light btn-sm">
                    Админка
                </a>
            <?php endif; ?>

            <a href="<?= Yii::$app->urlManager->createUrl(['/cart/index']) ?>">
                <img src="<?= Yii::getAlias('@web') ?>/img/cart-icon.png" alt="Корзина" style="height: 28px;">
            </a>
        </div>

        <!-- Гамбургер-кнопка (мобилка) -->
        <button class="btn btn-outline-light d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
            ☰
        </button>
    </div>

    <!-- Выпадающее меню (мобилка) -->
    <div class="collapse bg-success px-3 pb-4 d-lg-none" id="mobileMenu">
        <div class="d-flex flex-column align-items-center text-center">
            <?php if (Yii::$app->user->isGuest): ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/site/login']) ?>" class="text-white text-decoration-none py-1">Войти</a>
            <?php else: ?>
                <?= Html::beginForm(['/site/logout'], 'post', ['id' => 'logout-form-mobile']) ?>
                <a href="#" class="text-white text-decoration-none py-1" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                    Выйти (<?= Yii::$app->user->identity->username ?>)
                </a>
                <?= Html::endForm() ?>
            <?php endif; ?>

            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role_id == 1): ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/user/lk']) ?>" class="text-white text-decoration-none py-1">
                    <img src="<?= Yii::getAlias('@web') ?>/img/lk-icon.png" alt="ЛК" style="height: 20px;" class="me-1"> Личный кабинет
                </a>
            <?php endif; ?>

            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role_id == 2): ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/admin/index']) ?>" class="text-white text-decoration-none py-1">Админка</a>
            <?php endif; ?>

            <a href="<?= Yii::$app->urlManager->createUrl(['/cart/index']) ?>" class="text-white text-decoration-none py-1">
                <img src="<?= Yii::getAlias('@web') ?>/img/cart-icon.png" alt="Корзина" style="height: 20px;" class="me-1"> Корзина
            </a>
        </div>
    </div>
</header>


<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <!-- <?= Alert::widget() ?> -->




        
        <?= $content ?>
    </div>
</main>
</div>

<footer class="footer custom-footer mt-3 w-100">
    <div class="w-100" style="background-color: #f5f5f5;">
        <div class="container py-4 pb-3">
            <div class="row align-items-center text-center text-md-start">
                <!-- Логотип, название и адрес -->
                <div class="col-md-4 mb-3 mb-md-0 d-flex flex-column align-items-center align-items-md-start justify-content-center justify-content-md-start text-muted">
                    <div class="d-flex align-items-center">
                        <img src="<?= Yii::getAlias('@web') ?>/img/logo-icon-green.png" alt="Логотип" style="height: 36px;" class="me-2 footer-logo">
                        <span class="fw-semibold fs-5">Софийская база</span>
                    </div>
                    <div class="fs-6 mb-4">Софийская улица, 151, Санкт-Петербург</div>
                </div>

                <!-- Контакты (только соц.сети) -->
                <div class="col-md-4 mb-4 mb-md-0 text-muted fs-6 d-flex flex-column align-items-center">
                    <div class="d-flex gap-3 mt-2 mb-3">
                        <a href="https://vk.com/sofia.delivery?from=groups">
                            <img src="<?= Yii::getAlias('@web') ?>/img/icons/vkontakte.png" alt="ВКонтакте" style="height: 28px;">
                        </a>
                        <a href="https://t.me/sofia_delivery">
                            <img src="<?= Yii::getAlias('@web') ?>/img/icons/telegram.png" alt="Телеграм" style="height: 28px;">
                        </a>
                    </div>
                </div>

                <!-- Копирайт -->
                <div class="col-md-4 text-muted text-center text-md-end fs-6">
                    &copy; <?= date('Y') ?> | Все права защищены
                    <br>
                    <a href="<?= \yii\helpers\Url::to('@web/files/politika.pdf') ?>" class="text-decoration-none text-muted" target="_blank">
                        Политика конфиденциальности
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>






<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-..." crossorigin="anonymous"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const swiper = new Swiper('.reviews-slider', {
        slidesPerView: 1,
        spaceBetween: 30,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            }
        },
        on: {
            init: equalizeHeights,
            resize: equalizeHeights,
        }
    });

    function equalizeHeights() {
        const slides = document.querySelectorAll('.reviews-slider .swiper-slide');
        let maxHeight = 0;

        // Сброс высоты
        slides.forEach(slide => {
            slide.style.height = 'auto';
            const card = slide.querySelector('.card');
            if (card) {
                card.style.height = 'auto';
            }
        });

        // Определяем максимальную высоту
        slides.forEach(slide => {
            const card = slide.querySelector('.card');
            if (card) {
                maxHeight = Math.max(maxHeight, card.offsetHeight);
            }
        });

        // Применяем максимальную высоту ко всем карточкам
        slides.forEach(slide => {
            const card = slide.querySelector('.card');
            if (card) {
                card.style.height = maxHeight + 'px';
            }
        });
    }
});
</script>


<style>
  .flash-fixed {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1055;
    min-width: 300px;
    max-width: 90%;
    text-align: center;
  }
</style>

<script>
  // Автоматическое скрытие flash, кроме "Спасибо за заказ"
  setTimeout(() => {
    document.querySelectorAll('.flash-fixed').forEach(el => {
      if (!el.innerText.includes('Спасибо за заказ')) {
        el.classList.remove('show');
        el.classList.add('fade');
        setTimeout(() => el.remove(), 500);
      }
    });
  }, 2500);
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
