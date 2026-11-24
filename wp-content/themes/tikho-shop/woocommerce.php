<?php
/** WooCommerce page wrapper. */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main id="main" class="woocommerce-page-main">
    <div class="section-wrap woo-main">
        <?php woocommerce_content(); ?>
    </div>
</main>
<?php get_footer(); ?>
