<?php
/** Front page: editorial landing page and WooCommerce product grid. */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
$theme_uri = get_template_directory_uri();
$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/#catalog');
$front_page_id = get_queried_object_id();
$elementor_built = class_exists('\Elementor\Plugin') && $front_page_id && \Elementor\Plugin::instance()->db->is_built_with_elementor($front_page_id);
if ($elementor_built) :
    ?>
    <main id="main" class="elementor-content">
        <?php while (have_posts()) : the_post(); the_content(); endwhile; ?>
    </main>
    <?php
    get_footer();
    return;
endif;
$products = function_exists('wc_get_products') ? wc_get_products(array(
    'status'  => 'publish',
    'limit'   => 4,
    'orderby' => 'date',
    'order'   => 'DESC',
)) : array();
?>
<main id="main">
    <section class="hero section-wrap" id="top" aria-labelledby="hero-heading">
        <div class="hero-copy">
            <p class="eyebrow"><span class="eyebrow-mark"></span><?php esc_html_e('Маленький магазин вашего дома', 'tikho-shop'); ?></p>
            <h1 id="hero-heading"><?php esc_html_e('Дом, в котором', 'tikho-shop'); ?><br><?php esc_html_e('хочется', 'tikho-shop'); ?> <em><?php esc_html_e('остаться.', 'tikho-shop'); ?></em></h1>
            <p class="hero-description"><?php esc_html_e('Керамика, текстиль и маленькие ритуалы — вещи, которые делают обычный день немного теплее.', 'tikho-shop'); ?></p>
            <div class="hero-actions"><a class="button button-dark" href="#catalog"><?php esc_html_e('Найти свою вещь', 'tikho-shop'); ?> <span class="arrow">↗</span></a><a class="text-link" href="#story"><?php esc_html_e('Узнать о нас', 'tikho-shop'); ?> <span class="arrow">↗</span></a></div>
            <div class="hero-note"><div class="avatar-stack" aria-hidden="true"><span>А</span><span>М</span><span>И</span></div><span><?php esc_html_e('Создаём и выбираем', 'tikho-shop'); ?><br><strong><?php esc_html_e('небольшими партиями', 'tikho-shop'); ?></strong></span></div>
        </div>
        <div class="hero-visual">
            <img src="<?php echo esc_url($theme_uri . '/assets/images/hero-home.webp'); ?>" alt="<?php esc_attr_e('Солнечный домашний интерьер с керамической вазой и льняной скатертью', 'tikho-shop'); ?>" fetchpriority="high">
            <div class="hero-sticker" aria-label="<?php esc_attr_e('Создано с заботой', 'tikho-shop'); ?>"><svg viewBox="0 0 100 100" aria-hidden="true"><defs><path id="sticker-circle" d="M50,50 m-35,0 a35,35 0 1,1 70,0 a35,35 0 1,1 -70,0"/></defs><text><textPath href="#sticker-circle">создано с заботой · создано с заботой · </textPath></text></svg><span class="sticker-center">✳</span></div>
            <div class="hero-image-caption"><span>01 / 04</span><span><?php esc_html_e('Тихие вещи для живого дома', 'tikho-shop'); ?></span></div>
        </div>
        <div class="hero-side-note" aria-hidden="true">СОЗДАНО НЕ СПЕША · С ЛЮБОВЬЮ · В РОССИИ</div>
    </section>

    <section class="values-strip section-wrap" aria-label="<?php esc_attr_e('Наши ценности', 'tikho-shop'); ?>">
        <div class="value-item"><span class="value-icon">✳</span><span><?php esc_html_e('Небольшие мастерские', 'tikho-shop'); ?></span></div><span class="value-divider" aria-hidden="true"></span>
        <div class="value-item"><span class="value-icon">◌</span><span><?php esc_html_e('Природные материалы', 'tikho-shop'); ?></span></div><span class="value-divider" aria-hidden="true"></span>
        <div class="value-item"><span class="value-icon">♡</span><span><?php esc_html_e('Упакуем как подарок', 'tikho-shop'); ?></span></div>
    </section>

    <section class="catalog-section section-wrap" id="catalog" aria-labelledby="catalog-heading">
        <div class="section-heading catalog-heading"><div><p class="eyebrow"><?php esc_html_e('Найдено с любовью', 'tikho-shop'); ?></p><h2 id="catalog-heading"><?php esc_html_e('Вещи, к которым', 'tikho-shop'); ?><br class="desktop-break"> <?php esc_html_e('хочется', 'tikho-shop'); ?> <em><?php esc_html_e('прикасаться.', 'tikho-shop'); ?></em></h2></div><p class="section-intro"><?php esc_html_e('Чуть неровные. Очень настоящие.', 'tikho-shop'); ?><br><?php esc_html_e('И у каждой — свой характер.', 'tikho-shop'); ?></p></div>
        <div class="catalog-toolbar">
            <div class="filter-list" role="group" aria-label="<?php esc_attr_e('Фильтр товаров', 'tikho-shop'); ?>" data-filter-list>
                <button class="filter-chip is-active" type="button" data-filter="all" aria-pressed="true"><?php esc_html_e('Всё', 'tikho-shop'); ?></button>
                <button class="filter-chip" type="button" data-filter="ceramics" aria-pressed="false"><?php esc_html_e('Керамика', 'tikho-shop'); ?></button>
                <button class="filter-chip" type="button" data-filter="textile" aria-pressed="false"><?php esc_html_e('Текстиль', 'tikho-shop'); ?></button>
                <button class="filter-chip" type="button" data-filter="scents" aria-pressed="false"><?php esc_html_e('Ароматы', 'tikho-shop'); ?></button>
            </div>
            <a class="sort-trigger" href="<?php echo esc_url($shop_url); ?>"><?php esc_html_e('Весь каталог', 'tikho-shop'); ?> <span aria-hidden="true">↗</span></a>
        </div>
        <div class="product-grid" data-product-grid>
            <?php if (!empty($products)) : ?>
                <?php foreach ($products as $product) : ?>
                    <?php tikho_shop_product_card($product); ?>
                <?php endforeach; ?>
            <?php else : ?>
                <article class="product-card" data-product-card data-category="ceramics" data-name="Ваза Туман">
                    <div class="product-image-wrap"><a class="product-image-link" href="<?php echo esc_url($shop_url); ?>"><img src="<?php echo esc_url($theme_uri . '/assets/images/product-vase.webp'); ?>" alt="<?php esc_attr_e('Керамическая ваза Туман ручной работы', 'tikho-shop'); ?>" loading="lazy"></a><span class="product-label"><?php esc_html_e('Бестселлер', 'tikho-shop'); ?></span><button class="wishlist-button" type="button" aria-label="<?php esc_attr_e('Добавить вазу в избранное', 'tikho-shop'); ?>" aria-pressed="false" data-wishlist="sample-vase"><span aria-hidden="true">♡</span></button><a class="quick-add" href="<?php echo esc_url($shop_url); ?>" aria-label="<?php esc_attr_e('Перейти в каталог', 'tikho-shop'); ?>"><span>↗</span></a></div>
                    <div class="product-meta"><span><?php esc_html_e('Керамика · ручная работа', 'tikho-shop'); ?></span><span class="product-color-dot color-cream" aria-hidden="true"></span></div><div class="product-title-row"><h3><?php esc_html_e('Ваза «Туман»', 'tikho-shop'); ?></h3><span class="product-price">3 850 ₽</span></div><p class="product-note"><?php esc_html_e('Та самая, для одной веточки.', 'tikho-shop'); ?></p>
                </article>
                <article class="product-card" data-product-card data-category="textile" data-name="Льняная дорожка Пауза">
                    <div class="product-image-wrap"><a class="product-image-link" href="<?php echo esc_url($shop_url); ?>"><img src="<?php echo esc_url($theme_uri . '/assets/images/product-linen.webp'); ?>" alt="<?php esc_attr_e('Дорожка из натурального льна Пауза', 'tikho-shop'); ?>" loading="lazy"></a><span class="product-label product-label-soft"><?php esc_html_e('Натуральный лён', 'tikho-shop'); ?></span><button class="wishlist-button" type="button" aria-label="<?php esc_attr_e('Добавить дорожку в избранное', 'tikho-shop'); ?>" aria-pressed="false" data-wishlist="sample-linen"><span aria-hidden="true">♡</span></button><a class="quick-add" href="<?php echo esc_url($shop_url); ?>" aria-label="<?php esc_attr_e('Перейти в каталог', 'tikho-shop'); ?>"><span>↗</span></a></div>
                    <div class="product-meta"><span><?php esc_html_e('Текстиль · 100% лён', 'tikho-shop'); ?></span><span class="product-color-dot color-sand" aria-hidden="true"></span></div><div class="product-title-row"><h3><?php esc_html_e('Дорожка «Пауза»', 'tikho-shop'); ?></h3><span class="product-price">2 900 ₽</span></div><p class="product-note"><?php esc_html_e('Собирает за столом самых близких.', 'tikho-shop'); ?></p>
                </article>
                <article class="product-card" data-product-card data-category="scents" data-name="Свеча Сумерки">
                    <div class="product-image-wrap"><a class="product-image-link" href="<?php echo esc_url($shop_url); ?>"><img src="<?php echo esc_url($theme_uri . '/assets/images/product-candle.webp'); ?>" alt="<?php esc_attr_e('Свеча Сумерки в керамическом подсвечнике', 'tikho-shop'); ?>" loading="lazy"></a><span class="product-label product-label-dark"><?php esc_html_e('Малая серия', 'tikho-shop'); ?></span><button class="wishlist-button" type="button" aria-label="<?php esc_attr_e('Добавить свечу в избранное', 'tikho-shop'); ?>" aria-pressed="false" data-wishlist="sample-candle"><span aria-hidden="true">♡</span></button><a class="quick-add" href="<?php echo esc_url($shop_url); ?>" aria-label="<?php esc_attr_e('Перейти в каталог', 'tikho-shop'); ?>"><span>↗</span></a></div>
                    <div class="product-meta"><span><?php esc_html_e('Ароматы · соевый воск', 'tikho-shop'); ?></span><span class="product-color-dot color-clay" aria-hidden="true"></span></div><div class="product-title-row"><h3><?php esc_html_e('Свеча «Сумерки»', 'tikho-shop'); ?></h3><span class="product-price">1 650 ₽</span></div><p class="product-note"><?php esc_html_e('Запах тёплого дерева и тишины.', 'tikho-shop'); ?></p>
                </article>
                <article class="product-card" data-product-card data-category="ceramics" data-name="Кружка Мята">
                    <div class="product-image-wrap"><a class="product-image-link" href="<?php echo esc_url($shop_url); ?>"><img src="<?php echo esc_url($theme_uri . '/assets/images/product-mug.webp'); ?>" alt="<?php esc_attr_e('Керамическая кружка Мята ручной работы', 'tikho-shop'); ?>" loading="lazy"></a><span class="product-label product-label-soft"><?php esc_html_e('Любимая форма', 'tikho-shop'); ?></span><button class="wishlist-button" type="button" aria-label="<?php esc_attr_e('Добавить кружку в избранное', 'tikho-shop'); ?>" aria-pressed="false" data-wishlist="sample-mug"><span aria-hidden="true">♡</span></button><a class="quick-add" href="<?php echo esc_url($shop_url); ?>" aria-label="<?php esc_attr_e('Перейти в каталог', 'tikho-shop'); ?>"><span>↗</span></a></div>
                    <div class="product-meta"><span><?php esc_html_e('Керамика · ручная работа', 'tikho-shop'); ?></span><span class="product-color-dot color-sage" aria-hidden="true"></span></div><div class="product-title-row"><h3><?php esc_html_e('Кружка «Мята»', 'tikho-shop'); ?></h3><span class="product-price">1 450 ₽</span></div><p class="product-note"><?php esc_html_e('Для долгих завтраков и пауз.', 'tikho-shop'); ?></p>
                </article>
            <?php endif; ?>
        </div>
        <p class="no-results" data-no-results hidden><?php esc_html_e('Пока ничего не нашлось. Попробуйте другой раздел.', 'tikho-shop'); ?></p>
        <a class="all-products-link" href="<?php echo esc_url($shop_url); ?>"><?php esc_html_e('Смотреть весь каталог', 'tikho-shop'); ?> <span class="arrow">↗</span></a>
    </section>

    <section class="story-section section-wrap" id="story" aria-labelledby="story-heading">
        <div class="story-image"><img src="<?php echo esc_url($theme_uri . '/assets/images/hero-home.webp'); ?>" alt="<?php esc_attr_e('Светлая гостиная и предметы ручной работы', 'tikho-shop'); ?>" loading="lazy"><span class="story-image-label"><?php esc_html_e('Вещи, которые остаются', 'tikho-shop'); ?></span></div>
        <div class="story-copy"><p class="eyebrow"><?php esc_html_e('Привет, мы «тихо»', 'tikho-shop'); ?></p><h2 id="story-heading"><?php esc_html_e('Дом — это не', 'tikho-shop'); ?><br><?php esc_html_e('картинка. Это', 'tikho-shop'); ?><br><em><?php esc_html_e('как вам в нём.', 'tikho-shop'); ?></em></h2><p><?php esc_html_e('Мы верим в маленькие радости: любимую чашку, солнечный след на стене, ужин без повода. Поэтому собираем вещи, которые хочется хранить — и передавать дальше.', 'tikho-shop'); ?></p><a class="text-link text-link-underlined" href="#approach"><?php esc_html_e('Больше о нашей философии', 'tikho-shop'); ?> <span class="arrow">↗</span></a><div class="signature"><span class="signature-mark">Т.</span><span><?php esc_html_e('С теплом,', 'tikho-shop'); ?><br><strong><?php esc_html_e('команда тихо', 'tikho-shop'); ?></strong></span></div></div>
    </section>

    <section class="approach-section section-wrap" id="approach" aria-labelledby="approach-heading">
        <div class="approach-intro"><p class="eyebrow"><?php esc_html_e('Медленно — значит внимательно', 'tikho-shop'); ?></p><h2 id="approach-heading"><?php esc_html_e('Меньше вещей.', 'tikho-shop'); ?><br><em><?php esc_html_e('Больше смысла.', 'tikho-shop'); ?></em></h2><p><?php esc_html_e('Мы не гонимся за количеством. Ищем мастерские, где помнят, кто сделал вещь и из чего она создана.', 'tikho-shop'); ?></p><a class="button button-outline" href="#catalog"><?php esc_html_e('Выбрать свою', 'tikho-shop'); ?> <span class="arrow">↗</span></a></div>
        <div class="approach-list"><article class="approach-item"><span class="approach-number">01</span><div><h3><?php esc_html_e('С характером', 'tikho-shop'); ?></h3><p><?php esc_html_e('Ручная работа не бывает идеально одинаковой. И в этом её красота.', 'tikho-shop'); ?></p></div><span class="approach-symbol">↗</span></article><article class="approach-item"><span class="approach-number">02</span><div><h3><?php esc_html_e('На каждый день', 'tikho-shop'); ?></h3><p><?php esc_html_e('Не для особого случая, а для вашей обычной, настоящей жизни.', 'tikho-shop'); ?></p></div><span class="approach-symbol">↗</span></article><article class="approach-item"><span class="approach-number">03</span><div><h3><?php esc_html_e('С заботой о завтра', 'tikho-shop'); ?></h3><p><?php esc_html_e('Натуральные материалы, короткие тиражи, упаковка без лишнего.', 'tikho-shop'); ?></p></div><span class="approach-symbol">↗</span></article></div>
    </section>

    <section class="newsletter-section section-wrap" id="journal" aria-labelledby="newsletter-heading">
        <div class="newsletter-flower" aria-hidden="true">✳</div><div class="newsletter-copy"><p class="eyebrow"><?php esc_html_e('Письма из дома', 'tikho-shop'); ?></p><h2 id="newsletter-heading"><?php esc_html_e('Немного тишины', 'tikho-shop'); ?><br><?php esc_html_e('в вашей', 'tikho-shop'); ?> <em><?php esc_html_e('почте.', 'tikho-shop'); ?></em></h2><p><?php esc_html_e('Новые вещи, маленькие истории и идеи для дома — редко и по любви.', 'tikho-shop'); ?></p></div>
        <?php
        $newsletter_shortcode = get_theme_mod('tikho_shop_newsletter_shortcode', '');
        if ($newsletter_shortcode && shortcode_exists('contact-form-7')) :
            ?>
            <div class="newsletter-cf7"><?php echo do_shortcode($newsletter_shortcode); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <?php else : ?>
            <form class="newsletter-form" data-newsletter-form><label class="visually-hidden" for="newsletter-email"><?php esc_html_e('Ваш email', 'tikho-shop'); ?></label><input id="newsletter-email" name="email" type="email" placeholder="<?php esc_attr_e('Ваш email', 'tikho-shop'); ?>" autocomplete="email" required><button type="submit"><span><?php esc_html_e('Подписаться', 'tikho-shop'); ?></span><span class="arrow">↗</span></button><small><?php esc_html_e('Нажимая «Подписаться», вы соглашаетесь с политикой конфиденциальности.', 'tikho-shop'); ?></small></form>
        <?php endif; ?>
    </section>
</main>
<?php get_footer(); ?>
