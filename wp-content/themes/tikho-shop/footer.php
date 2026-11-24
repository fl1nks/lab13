<?php
/** Site footer and overlays. */
if (!defined('ABSPATH')) {
    exit;
}
$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/#catalog');
?>
<footer class="site-footer" id="footer">
    <div class="footer-main section-wrap">
        <div class="footer-brand-block">
            <a class="brand footer-brand" href="<?php echo esc_url(home_url('/')); ?>">тихо<span class="brand-dot">.</span><span class="brand-caption"><?php esc_html_e('дом начинается с деталей', 'tikho-shop'); ?></span></a>
            <p><?php esc_html_e('Вещи для дома', 'tikho-shop'); ?><br><?php esc_html_e('и для себя.', 'tikho-shop'); ?></p>
        </div>
        <div class="footer-column">
            <h2><?php esc_html_e('Исследовать', 'tikho-shop'); ?></h2>
            <a href="<?php echo esc_url($shop_url); ?>"><?php esc_html_e('Каталог', 'tikho-shop'); ?></a>
            <a href="<?php echo esc_url(home_url('/#story')); ?>"><?php esc_html_e('О нас', 'tikho-shop'); ?></a>
            <a href="<?php echo esc_url(home_url('/#approach')); ?>"><?php esc_html_e('Наш подход', 'tikho-shop'); ?></a>
        </div>
        <div class="footer-column">
            <h2><?php esc_html_e('Помощь', 'tikho-shop'); ?></h2>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Задать вопрос', 'tikho-shop'); ?></a>
            <a href="<?php echo esc_url(home_url('/delivery/')); ?>"><?php esc_html_e('Доставка и оплата', 'tikho-shop'); ?></a>
            <a href="<?php echo esc_url(home_url('/returns/')); ?>"><?php esc_html_e('Обмен и возврат', 'tikho-shop'); ?></a>
        </div>
        <div class="footer-contact">
            <span class="footer-kicker"><?php esc_html_e('Напишите нам', 'tikho-shop'); ?></span>
            <a class="footer-email" href="mailto:hello@tiho-store.ru">hello@tiho-store.ru</a>
            <div class="social-links"><a href="#footer" aria-label="Telegram">tg ↗</a><a href="#footer" aria-label="ВКонтакте">vk ↗</a><a href="#footer" aria-label="Pinterest">pin ↗</a></div>
        </div>
    </div>
    <div class="footer-bottom section-wrap">
        <span>© <?php echo esc_html(wp_date('Y')); ?> <?php esc_html_e('тихо. Сделано с заботой.', 'tikho-shop'); ?></span>
        <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('Политика конфиденциальности', 'tikho-shop'); ?></a>
        <span><?php esc_html_e('С любовью к простым вещам', 'tikho-shop'); ?>&nbsp; ✳</span>
    </div>
</footer>

<div class="drawer-backdrop" data-cart-backdrop></div>
<aside class="cart-drawer" id="cart-drawer" aria-labelledby="cart-heading" aria-hidden="true" tabindex="-1">
    <div class="drawer-header">
        <div><p class="eyebrow"><?php esc_html_e('Ваши находки', 'tikho-shop'); ?></p><h2 id="cart-heading"><?php esc_html_e('Корзина', 'tikho-shop'); ?> <span class="drawer-count" data-drawer-count>(<?php echo esc_html((string) ((function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0)); ?>)</span></h2></div>
        <button class="icon-button drawer-close" type="button" aria-label="<?php esc_attr_e('Закрыть корзину', 'tikho-shop'); ?>" data-cart-close>×</button>
    </div>
    <?php echo tikho_shop_cart_contents_fragment(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    <?php echo tikho_shop_cart_footer_fragment(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</aside>

<dialog class="search-dialog" id="search-dialog" aria-labelledby="search-heading">
    <div class="search-dialog-inner">
        <div class="search-dialog-heading"><p class="eyebrow"><?php esc_html_e('Найдём вашу вещь', 'tikho-shop'); ?></p><button class="icon-button" type="button" aria-label="<?php esc_attr_e('Закрыть поиск', 'tikho-shop'); ?>" data-search-close>×</button></div>
        <h2 id="search-heading"><?php esc_html_e('Что ищем', 'tikho-shop'); ?> <em><?php esc_html_e('сегодня?', 'tikho-shop'); ?></em></h2>
        <form class="search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" data-search-form>
            <label class="visually-hidden" for="site-search"><?php esc_html_e('Поиск по каталогу', 'tikho-shop'); ?></label>
            <input id="site-search" type="search" name="s" placeholder="<?php esc_attr_e('Например, ваза или лён', 'tikho-shop'); ?>" autocomplete="off">
            <input type="hidden" name="post_type" value="product">
            <button type="submit" aria-label="<?php esc_attr_e('Искать', 'tikho-shop'); ?>"><span class="arrow">↗</span></button>
        </form>
        <p class="search-hint"><?php esc_html_e('Популярное:', 'tikho-shop'); ?> <button type="button" data-search-suggestion="керамика">керамика</button> <button type="button" data-search-suggestion="лён">лён</button> <button type="button" data-search-suggestion="свеча">свеча</button></p>
        <p class="search-result-message" data-search-message aria-live="polite"></p>
    </div>
</dialog>

<?php wp_footer(); ?>
</body>
</html>
