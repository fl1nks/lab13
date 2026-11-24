<?php
/**
 * Theme setup and WooCommerce helpers for Тихо.
 *
 * @package Tikho_Shop
 */

if (!defined('ABSPATH')) {
    exit;
}

function tikho_shop_setup() {
    load_theme_textdomain('tikho-shop', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 180,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('woocommerce', array(
        'thumbnail_image_width' => 620,
        'single_image_width'    => 1000,
        'product_grid'          => array(
            'default_rows'    => 3,
            'min_rows'        => 1,
            'max_rows'        => 8,
            'default_columns' => 4,
            'min_columns'     => 2,
            'max_columns'     => 4,
        ),
    ));
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    register_nav_menus(array(
        'primary' => __('Основное меню', 'tikho-shop'),
        'footer'  => __('Меню в подвале', 'tikho-shop'),
    ));
}
add_action('after_setup_theme', 'tikho_shop_setup');

function tikho_shop_customize_register($wp_customize) {
    $wp_customize->add_section('tikho_shop_forms', array(
        'title'       => __('Формы Тихо', 'tikho-shop'),
        'description' => __('Добавьте шорткод Contact Form 7 для подключения реальной формы подписки.', 'tikho-shop'),
        'priority'    => 160,
    ));
    $wp_customize->add_setting('tikho_shop_newsletter_shortcode', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('tikho_shop_newsletter_shortcode', array(
        'label'       => __('Шорткод формы подписки', 'tikho-shop'),
        'description' => __('Например, шорткод формы, созданной в Contact Form 7.', 'tikho-shop'),
        'section'     => 'tikho_shop_forms',
        'type'        => 'textarea',
    ));
}
add_action('customize_register', 'tikho_shop_customize_register');

function tikho_shop_assets() {
    $theme = wp_get_theme();
    $version = $theme->get('Version') ? $theme->get('Version') : '1.0.0';
    wp_enqueue_style('tikho-shop-style', get_stylesheet_uri(), array(), $version);
    wp_enqueue_script('tikho-shop-script', get_template_directory_uri() . '/assets/js/shop.js', array('jquery'), $version, true);
}
add_action('wp_enqueue_scripts', 'tikho_shop_assets');

function tikho_shop_woocommerce_assets() {
    if (class_exists('WooCommerce') && is_front_page()) {
        wp_enqueue_script('wc-add-to-cart');
        wp_enqueue_script('wc-cart-fragments');
    }
}
add_action('wp_enqueue_scripts', 'tikho_shop_woocommerce_assets', 20);

/** Return category slugs that match the storefront filter controls. */
function tikho_shop_product_filter_slugs($product_id) {
    $terms = wp_get_post_terms($product_id, 'product_cat');
    if (is_wp_error($terms) || empty($terms)) {
        return array();
    }

    $aliases = array(
        'keramika'  => 'ceramics',
        'ceramics'  => 'ceramics',
        'textil'    => 'textile',
        'tekstil'   => 'textile',
        'textile'   => 'textile',
        'aromaty'   => 'scents',
        'aromates'  => 'scents',
        'scents'    => 'scents',
    );
    $slugs = array();
    foreach ($terms as $term) {
        $slug = isset($aliases[$term->slug]) ? $aliases[$term->slug] : sanitize_html_class($term->slug);
        if ($slug && !in_array($slug, $slugs, true)) {
            $slugs[] = $slug;
        }
    }
    return $slugs;
}

/** Render one WooCommerce product in the front-page editorial grid. */
function tikho_shop_product_card($product) {
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }

    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $product_url = $product->get_permalink();
    $filter_slugs = tikho_shop_product_filter_slugs($product_id);
    $terms = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'));
    $meta = (!is_wp_error($terms) && !empty($terms)) ? implode(' · ', array_slice($terms, 0, 2)) : __('Мастерская · ручная работа', 'tikho-shop');
    $short_description = wp_strip_all_tags($product->get_short_description());
    $price_data = $product->get_price();
    $price_text = wp_strip_all_tags(wc_price(wc_get_price_to_display($product)));
    $image_html = $product->get_image('woocommerce_thumbnail', array(
        'alt'     => $product_name,
        'loading' => 'lazy',
    ));
    $button_classes = array('quick-add', 'add_to_cart_button', 'product_type_' . $product->get_type());
    if ($product->supports('ajax_add_to_cart') && $product->is_purchasable() && $product->is_in_stock()) {
        $button_classes[] = 'ajax_add_to_cart';
    }
    $button_label = sprintf(__('Добавить %s в корзину', 'tikho-shop'), $product_name);
    ?>
    <article class="product-card" data-product-card data-category="<?php echo esc_attr(implode(' ', $filter_slugs)); ?>" data-name="<?php echo esc_attr($product_name); ?>">
        <div class="product-image-wrap">
            <a class="product-image-link" href="<?php echo esc_url($product_url); ?>" aria-label="<?php echo esc_attr($product_name); ?>">
                <?php echo wp_kses_post($image_html); ?>
            </a>
            <?php if ($product->is_featured()) : ?>
                <span class="product-label"><?php esc_html_e('Бестселлер', 'tikho-shop'); ?></span>
            <?php else : ?>
                <span class="product-label product-label-soft"><?php esc_html_e('Малая серия', 'tikho-shop'); ?></span>
            <?php endif; ?>
            <button class="wishlist-button" type="button" aria-label="<?php echo esc_attr(sprintf(__('Добавить %s в избранное', 'tikho-shop'), $product_name)); ?>" aria-pressed="false" data-wishlist="<?php echo esc_attr($product_id); ?>"><span aria-hidden="true">♡</span></button>
            <a href="<?php echo esc_url($product->add_to_cart_url()); ?>"
               class="<?php echo esc_attr(implode(' ', $button_classes)); ?>"
               aria-label="<?php echo esc_attr($button_label); ?>"
               data-add-to-cart
               data-product-id="<?php echo esc_attr($product_id); ?>"
               data-product_id="<?php echo esc_attr($product_id); ?>"
               data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
               data-quantity="1"
               data-price="<?php echo esc_attr($price_data); ?>"
               data-product-name="<?php echo esc_attr($product_name); ?>">
                <span aria-hidden="true">+</span><span class="visually-hidden"><?php esc_html_e('Добавить в корзину', 'tikho-shop'); ?></span>
            </a>
        </div>
        <div class="product-meta"><span><?php echo esc_html($meta); ?></span><span class="product-color-dot color-sand" aria-hidden="true"></span></div>
        <div class="product-title-row"><h3><a href="<?php echo esc_url($product_url); ?>"><?php echo esc_html($product_name); ?></a></h3><span class="product-price"><?php echo esc_html($price_text); ?></span></div>
        <p class="product-note"><?php echo esc_html($short_description ? wp_trim_words($short_description, 10) : __('Для простых, тёплых моментов дома.', 'tikho-shop')); ?></p>
    </article>
    <?php
}

/** Markup for the WooCommerce-aware side cart. */
function tikho_shop_cart_contents_inner() {
    if (!function_exists('WC') || !WC()->cart || WC()->cart->is_empty()) {
        ?>
        <div class="cart-empty">
            <span class="cart-empty-mark" aria-hidden="true">◌</span>
            <h3><?php esc_html_e('Пока здесь тихо.', 'tikho-shop'); ?></h3>
            <p><?php esc_html_e('Добавим что-нибудь красивое? Ваши находки будут ждать здесь.', 'tikho-shop'); ?></p>
            <a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/#catalog')); ?>" data-close-cart><?php esc_html_e('Перейти к каталогу ↗', 'tikho-shop'); ?></a>
        </div>
        <?php
        return;
    }

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product = isset($cart_item['data']) ? $cart_item['data'] : false;
        if (!$product || !$product->exists() || empty($cart_item['quantity'])) {
            continue;
        }
        $quantity = (int) $cart_item['quantity'];
        $remove_url = wc_get_cart_remove_url($cart_item_key);
        ?>
        <article class="cart-line">
            <?php echo wp_kses_post($product->get_image('woocommerce_thumbnail', array('class' => 'cart-line-image', 'alt' => '', 'loading' => 'lazy'))); ?>
            <div class="cart-line-info">
                <h3><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
                <p><?php echo esc_html(sprintf(_n('%s шт.', '%s шт.', $quantity, 'tikho-shop'), number_format_i18n($quantity))); ?></p>
                <a class="cart-remove" href="<?php echo esc_url($remove_url); ?>" aria-label="<?php echo esc_attr(sprintf(__('Удалить %s из корзины', 'tikho-shop'), $product->get_name())); ?>"><?php esc_html_e('Убрать', 'tikho-shop'); ?></a>
            </div>
            <span class="cart-line-price"><?php echo wp_kses_post(WC()->cart->get_product_subtotal($product, $quantity)); ?></span>
        </article>
        <?php
    }
}

function tikho_shop_cart_contents_fragment() {
    ob_start();
    ?>
    <div class="cart-content" data-cart-content><?php tikho_shop_cart_contents_inner(); ?></div>
    <?php
    return ob_get_clean();
}

function tikho_shop_cart_footer_fragment() {
    if (!function_exists('WC') || !WC()->cart || WC()->cart->is_empty()) {
        return '<div class="cart-footer" data-cart-footer hidden></div>';
    }

    $subtotal = (float) WC()->cart->get_displayed_subtotal();
    $threshold = 8000;
    $progress = min(100, ($subtotal / $threshold) * 100);
    ob_start();
    ?>
    <div class="cart-footer" data-cart-footer>
        <div class="shipping-progress">
            <div class="shipping-progress-top">
                <span><?php echo esc_html($subtotal >= $threshold ? __('Ура! Доставка за наш счёт', 'tikho-shop') : sprintf(__('До бесплатной доставки осталось %s', 'tikho-shop'), wp_strip_all_tags(wc_price(max(0, $threshold - $subtotal))))); ?></span>
                <span aria-hidden="true">✳</span>
            </div>
            <div class="shipping-track"><span style="width: <?php echo esc_attr($progress); ?>%"></span></div>
        </div>
        <div class="cart-subtotal"><span><?php esc_html_e('Итого', 'tikho-shop'); ?></span><strong><?php echo wp_kses_post(WC()->cart->get_cart_subtotal()); ?></strong></div>
        <p class="cart-shipping-note"><?php esc_html_e('Стоимость доставки рассчитается при оформлении.', 'tikho-shop'); ?></p>
        <a class="button button-dark checkout-trigger" href="<?php echo esc_url(wc_get_checkout_url()); ?>"><?php esc_html_e('Перейти к оформлению', 'tikho-shop'); ?> <span class="arrow">↗</span></a>
        <p class="checkout-secure-note"><span aria-hidden="true">▧</span> <?php esc_html_e('Безопасная оплата через подключённый платёжный сервис', 'tikho-shop'); ?></p>
    </div>
    <?php
    return ob_get_clean();
}

function tikho_shop_cart_count_fragment() {
    $count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
    return '<span class="cart-count" aria-live="polite">' . esc_html((string) $count) . '</span>';
}

function tikho_shop_drawer_count_fragment() {
    $count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
    return '<span class="drawer-count" data-drawer-count>(' . esc_html((string) $count) . ')</span>';
}

function tikho_shop_cart_fragments($fragments) {
    $fragments['span.cart-count'] = tikho_shop_cart_count_fragment();
    $fragments['[data-drawer-count]'] = tikho_shop_drawer_count_fragment();
    $fragments['[data-cart-content]'] = tikho_shop_cart_contents_fragment();
    $fragments['[data-cart-footer]'] = tikho_shop_cart_footer_fragment();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'tikho_shop_cart_fragments');
