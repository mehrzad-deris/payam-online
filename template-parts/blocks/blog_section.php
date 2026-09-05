<?php
/**
 * Blog slider section.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor    = get_sub_field( 'section_color' ) ?: '#f6f8fe';
$sectionStyle    = get_sub_field( 'section_style' ) ?: 'light';
$sectionIcon     = absint( get_sub_field( 'section_icon' ) );
$sectionTitle    = (string) ( get_sub_field( 'section_title' ) ?: '' );
$sectionTitleTag = get_sub_field( 'title_tag' ) ?: 'h2';
$sectionSubtitle = (string) ( get_sub_field( 'section_subtitle' ) ?: '' );
$postsSource     = (string) ( get_sub_field( 'posts_source' ) ?: 'recent' );
$postsCount      = max( 1, absint( get_sub_field( 'posts_count' ) ?: 6 ) );
$selectedPosts   = get_sub_field( 'selected_posts' );
$blogPosts       = [];
$sectionLoadMore = get_sub_field( 'blog_more_link' );

if ( 'selected' === $postsSource && is_array( $selectedPosts ) ) {
    $selectedPostIds = array_values( array_filter( array_map( static fn( $post ): int => absint( $post instanceof WP_Post ? $post->ID : $post ), $selectedPosts ) ) );

    if ( ! empty( $selectedPostIds ) ) {
        $blogPosts = get_posts( [
                'post_type'              => 'post',
                'post_status'            => 'publish',
                'post__in'               => $selectedPostIds,
                'orderby'                => 'post__in',
                'posts_per_page'         => count( $selectedPostIds ),
                'no_found_rows'          => true,
                'ignore_sticky_posts'    => true,
                'update_post_meta_cache' => true,
                'update_post_term_cache' => false,
        ] );
    }
} else {
    $blogPosts = get_posts( [
            'post_type'              => 'post',
            'post_status'            => 'publish',
            'posts_per_page'         => $postsCount,
            'orderby'                => 'date',
            'order'                  => 'DESC',
            'no_found_rows'          => true,
            'ignore_sticky_posts'    => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => false,
    ] );
}

?>

<section
        class="blog-section blog-<?= esc_attr( $sectionStyle ); ?>"
        data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
        style="--blog-background: <?= esc_attr( $sectionColor ); ?>"
>
    <div class="container blog-container">
        <?php
        section_heading( [
                'icon'        => $sectionIcon,
                'title'       => $sectionTitle,
                'title_tag'   => $sectionTitleTag,
                'subtitle'    => $sectionSubtitle,
                'title_class' => $sectionStyle === 'dark' ? 'text-white' : '',
        ] );
        ?>

        <?php if ( ! empty( $blogPosts ) ) : ?>
            <div class="swiper blog-slider" data-swiper="blog">
                <div class="swiper-wrapper">
                    <?php foreach ( $blogPosts as $blogPost ) :
                        $postId = $blogPost->ID;
                        $thumbnailId = get_post_thumbnail_id( $postId );
                        $thumbnail = $thumbnailId ? wp_get_attachment_image_url( $thumbnailId, 'blog_card' ) : false;
                        $thumbnail2x = $thumbnailId ? wp_get_attachment_image_url( $thumbnailId, 'blog_card_x2' ) : false;
                        $thumbnailAlt = $thumbnailId ? get_post_meta( $thumbnailId, '_wp_attachment_image_alt', true ) : '';
                        ?>
                        <article class="swiper-slide blog-card">
                            <a class="blog-link group" href="<?= esc_url( get_permalink( $postId ) ); ?>">
                                <?php if ( $thumbnail ) : ?>
                                    <span class="blog-image">
									<img
                                            src="<?= esc_url( $thumbnail ); ?>"
										<?= $thumbnail2x ? 'srcset="' . esc_url( $thumbnail ) . ' 1x, ' . esc_url( $thumbnail2x ) . ' 2x"' : ''; ?>
										alt="<?= esc_attr( $thumbnailAlt ?: get_the_title( $postId ) ); ?>"
                                            width="389"
                                            height="218"
                                            loading="lazy"
                                            decoding="async"
                                    >
                                    <span class="shape"><?= icon( 'top-shape' ) ?></span>
                                </span>
                                <?php endif; ?>

                                <span class="card-caption">
                                    <span class="blog-title text-neutral-900 md:text-body-2 text-body-mobile-2"><?= esc_html( get_the_title( $postId ) ); ?></span>
                                    <span class="flex justify-between items-center gap-2">
                                        <time class="blog-date text-neutral-500" datetime="<?= esc_html( payamava_jalali_date( 'Y/m/d', get_post_datetime() ) ); ?>">
                                            <?= esc_html( payamava_jalali_date( 'Y.m.d', get_post_datetime() ) ); ?>
                                        </time>
                                        <span class="read-more text-yellow-primary text-body-3 group-hover:text-secondry-700">
                                            <span>مطالعه بیشتر</span>
                                            <span class="icon group-hover:rotate-45" aria-hidden="true"><?= icon( 'arrow-linear' ) ?></span>
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ( count( $blogPosts ) > 1 ) : ?>
                    <div class="swiper-pagination blog-pagination swiper-pagination-card-style bottom-7! relative" data-swiper-pagination></div>
                <?php endif; ?>

                <div class="justify-center hidden xl:flex mt-10">
                    <?php if ( $sectionLoadMore ) : ?>
                        <a href="<?= esc_html( $sectionLoadMore['url'] ) ?: '' ?>" class="cta-link primary-cta">
                            <?= esc_html( $sectionLoadMore['title'] ) ?: '' ?>
                            <span class="icon"><?= icon( 'arrow-linear', 'w-3.5 h-2.5' ) ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
