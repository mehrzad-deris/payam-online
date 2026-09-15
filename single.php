<?php
/** Single blog post. */
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) : the_post();
    $post_id      = get_the_ID();
    $prepared     = payam_prepare_post_toc( apply_filters( 'the_content', get_the_content() ) );
    $related      = payam_get_related_posts( $post_id, 4 );
    $thumbnail_id = get_post_thumbnail_id( $post_id );
    $thumbnail_alt = $thumbnail_id ? trim( (string) get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) : '';
    $thumbnail_desktop = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'single_article' ) : false;
    $thumbnail_desktop_x2 = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'single_article_x2' ) : false;
    $thumbnail_mobile = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'single_article_mobile' ) : false;
    $thumbnail_mobile_x2 = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'single_article_mobile_x2' ) : false;
    ?>
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
                    <?php if ( $thumbnail_desktop && $thumbnail_mobile ) : ?>
                        <figure class="article-featured">
                            <picture>
                                <source
                                    media="(min-width: 1280px)"
                                    srcset="<?= esc_url( $thumbnail_desktop ); ?> 1x<?= $thumbnail_desktop_x2 ? ', ' . esc_url( $thumbnail_desktop_x2 ) . ' 2x' : ''; ?>"
                                >
                                <img
                                    src="<?= esc_url( $thumbnail_mobile ); ?>"
                                    <?= $thumbnail_mobile_x2 ? 'srcset="' . esc_url( $thumbnail_mobile ) . ' 1x, ' . esc_url( $thumbnail_mobile_x2 ) . ' 2x"' : ''; ?>
                                    alt="<?= esc_attr( $thumbnail_alt ?: get_the_title() ); ?>"
                                    width="630"
                                    height="354"
                                    fetchpriority="high"
                                    decoding="async"
                                >
                            </picture>
                        </figure>
                    <?php endif; ?>
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
				<div class="article-related-scroll"><?php foreach ( $related as $related_post ) : get_template_part( 'template-parts/components/blog_card', null, [ 'post_id' => $related_post->ID ] ); endforeach; ?></div>
				</section><?php endif; ?>
            <div class="article-useful-mobile">
                <div class="article-sticky-card"><h2>لینک‌های مفید</h2><?php if ( has_nav_menu( 'article_useful_links' ) ) {
                        wp_nav_menu( [ 'theme_location' => 'article_useful_links', 'container' => 'nav', 'menu_class' => 'article-useful-menu', 'depth' => 1, 'fallback_cb' => false ] );
                    } ?></div>
            </div>
        </div>

        <?php
        $builder_source = payam_article_builder_source( $post_id );
        payam_render_builder( $builder_source, 'mt-10' );
        ?>

    </main>
<?php endwhile;
get_footer();
