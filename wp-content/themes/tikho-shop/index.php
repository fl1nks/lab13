<?php
/** Fallback archive template. */
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main id="main" class="section-wrap page-content">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class('archive-entry'); ?>>
                <p class="eyebrow"><?php echo esc_html(get_the_date()); ?></p>
                <h1 class="page-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                <div class="entry-content"><?php the_excerpt(); ?></div>
            </article>
        <?php endwhile; ?>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e('Пока здесь тихо.', 'tikho-shop'); ?></p>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
