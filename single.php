<?php
/** Single blog post. */
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) : the_post();
    $post_id      = get_the_ID();
    $prepared     = payam_prepare_post_toc( apply_filters( 'the_content', get_the_content() ) );
    $related      = payam_get_related_posts( $post_id, 4 );
    $thumbnail_id = get_post_thumbnail_id( $post_id );
    $post_date    = get_post_datetime( $post_id ); ?>
    <main class="single-article" data-header-theme="light" data-single-article>
        <div class="container">
            <div class="article-layout">
                <aside class="article-sidebar article-useful" aria-label="لینک‌های مفید">
                    <div class="article-sticky-card"><h2>لینک‌های مفید</h2><?php if ( has_nav_menu( 'article_useful_links' ) ) {
                            wp_nav_menu( [ 'theme_location' => 'article_useful_links', 'container' => 'nav', 'menu_class' => 'article-useful-menu', 'depth' => 1, 'fallback_cb' => false ] );
                        } ?></div>
                </aside>
                <article class="article-main">
                    <header class="article-header"><h1><?= esc_html( get_the_title() ); ?></h1></header>
                    <?php if ( $thumbnail_id ) : ?>
                        <figure class="article-featured"><?= wp_get_attachment_image( $thumbnail_id, 'single_article', false, [ 'alt' => get_the_title(), 'fetchpriority' => 'high', 'decoding' => 'async' ] ); ?></figure><?php endif; ?>
                    <?php if ( $prepared['headings'] ) : ?>
                        <section class="article-toc" data-article-toc>
                        <button type="button" aria-expanded="false" aria-controls="article-toc-list"><span>آن‌چه در این مقاله می‌خوانید</span><?= icon( 'arrow-down-2', 'article-toc-arrow' ); ?></button>
                        <div class="article-toc-panel" id="article-toc-list" hidden>
                            <ol><?php foreach ( $prepared['headings'] as $heading ) : ?>
                                <li class="toc-level-<?= esc_attr( $heading['level'] ); ?>"><a href="#<?= esc_attr( $heading['id'] ); ?>"><?= esc_html( $heading['title'] ); ?></a></li><?php endforeach; ?></ol>
                        </div></section><?php endif; ?>
                    <div class="article-content text-body-3"><?= $prepared['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped   ?></div>
                    <?php $tags = get_the_tags();
                    if ( $tags ) : ?>
                        <div class="article-tags"><strong>برچسب‌ها:</strong>
                        <div><?php foreach ( $tags as $tag ) : ?><a href="<?= esc_url( get_tag_link( $tag ) ); ?>"><?= esc_html( $tag->name ); ?></a><?php endforeach; ?></div></div><?php endif; ?>
                    <?php comments_template(); ?>
                </article>
                <aside class="article-sidebar article-related" aria-label="آموزش‌های مرتبط">
                    <div class="article-sticky-card"><h2>آموزش‌های مرتبط</h2>
                        <div class="related-posts-compact"><?php foreach ( $related as $related_post ) : ?><a href="<?= esc_url( get_permalink( $related_post ) ); ?>"><?= payam_article_sidebar_image( $related_post->ID ); ?><span><strong><?= esc_html( get_the_title( $related_post ) ); ?></strong><time><?= esc_html( payamava_jalali_date( 'j F Y', get_post_datetime( $related_post ) ) ); ?></time></span></a><?php endforeach; ?></div>
                    </div>
                </aside>
            </div>
            <?php if ( $related ) : ?>
                <section class="article-related-mobile" aria-label="آموزش‌های مرتبط"><h2>آموزش‌های مرتبط</h2>
                <div class="swiper" data-swiper data-swiper-options='{"slidesPerView":1,"spaceBetween":20,"loop":false,"autoplay":false}'>
                    <div class="swiper-wrapper"><?php foreach ( $related as $related_post ) : get_template_part( 'template-parts/components/blog_card', null, [ 'post_id' => $related_post->ID, 'class' => 'swiper-slide' ] ); endforeach; ?></div>
                    <div class="swiper-pagination swiper-pagination-card-style" data-swiper-pagination></div>
                </div></section><?php endif; ?>
            <div class="article-useful-mobile">
                <div class="article-sticky-card"><h2>لینک‌های مفید</h2><?php if ( has_nav_menu( 'article_useful_links' ) ) {
                        wp_nav_menu( [ 'theme_location' => 'article_useful_links', 'container' => 'nav', 'menu_class' => 'article-useful-menu', 'depth' => 1, 'fallback_cb' => false ] );
                    } ?></div>
            </div>
        </div>

        <?php
        $builder_source = payam_article_builder_source( $post_id );
        if ( function_exists( 'have_rows' ) && have_rows( $builder_source['field'], $builder_source['post_id'] ) ):
            while ( have_rows( $builder_source['field'], $builder_source['post_id'] ) ): the_row(); ?>
                <div class="mt-10">
                    <?php theme_render_block( get_row_layout() ); ?>
                </div>
            <?php endwhile;
        endif;
        ?>

    </main>
<?php endwhile;
get_footer();
