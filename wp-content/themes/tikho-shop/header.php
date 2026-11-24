<?php
/** Site header. */
if (!defined('ABSPATH')) {
    exit;
}
$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/#catalog');
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f5ef">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e('Перейти к содержимому', 'tikho-shop'); ?></a>

<div class="announcement-bar" id="announcement-bar">
    <span><?php esc_html_e('Бережная доставка по России', 'tikho-shop'); ?> <span class="announcement-dot">✳</span> <?php esc_html_e('Бесплатно от 8 000 ₽', 'tikho-shop'); ?></span>
    <button class="announcement-close" type="button" aria-label="<?php esc_attr_e('Скрыть объявление', 'tikho-shop'); ?>" data-dismiss-announcement>×</button>
</div>

<header class="site-header">
    <button class="icon-button mobile-menu-toggle" type="button" aria-label="<?php esc_attr_e('Открыть меню', 'tikho-shop'); ?>" aria-expanded="false" data-menu-toggle>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" /></svg>
    </button>
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('Тихо — на главную', 'tikho-shop'); ?>">тихо<span class="brand-dot">.</span><span class="brand-caption"><?php esc_html_e('дом начинается с деталей', 'tikho-shop'); ?></span></a>
    <nav class="primary-nav" aria-label="<?php esc_attr_e('Основная навигация', 'tikho-shop'); ?>" data-mobile-nav>
        <?php if (has_nav_menu('primary')) : ?>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container'      => false,
                'items_wrap'     => '%3$s',
                'depth'          => 1,
                'fallback_cb'    => false,
            ));
            ?>
        <?php else : ?>
            <a href="<?php echo esc_url($shop_url); ?>"><?php esc_html_e('Каталог', 'tikho-shop'); ?></a>
            <a href="<?php echo esc_url(home_url('/#story')); ?>"><?php esc_html_e('Наша история', 'tikho-shop'); ?></a>
            <a href="<?php echo esc_url(home_url('/#approach')); ?>"><?php esc_html_e('Наш подход', 'tikho-shop'); ?></a>
            <a href="<?php echo esc_url(home_url('/#footer')); ?>"><?php esc_html_e('Контакты', 'tikho-shop'); ?></a>
        <?php endif; ?>
    </nav>
    <div class="header-actions">
        <button class="icon-button search-trigger" type="button" aria-label="<?php esc_attr_e('Открыть поиск', 'tikho-shop'); ?>" data-search-open>
            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.3"/><path d="m15.5 15.5 4.2 4.2"/></svg>
            <span class="header-action-label"><?php esc_html_e('Поиск', 'tikho-shop'); ?></span>
        </button>
        <button class="icon-button cart-trigger" type="button" aria-label="<?php esc_attr_e('Открыть корзину', 'tikho-shop'); ?>" data-cart-open>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8h16l-1.3 11H5.3L4 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg>
            <span class="header-action-label"><?php esc_html_e('Корзина', 'tikho-shop'); ?></span>
            <?php echo tikho_shop_cart_count_fragment(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </button>
    </div>
</header>
