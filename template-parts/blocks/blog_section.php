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
                'show_shapes' => (bool) get_sub_field( 'section_heading_shapes' ),
        ] );
        ?>

        <?php if ( ! empty( $blogPosts ) ) : ?>
            <div class="swiper blog-slider" data-swiper="blog">
                <div class="swiper-wrapper">
                    <?php foreach ( $blogPosts as $blogPost ) :
	                    get_template_part( 'template-parts/components/blog_card', null, [
		                    'post_id' => $blogPost->ID,
		                    'class'   => 'swiper-slide',
	                    ] );
                    endforeach; ?>
                </div>

                <?php if ( count( $blogPosts ) > 1 ) : ?>
                    <div class="swiper-pagination blog-pagination swiper-pagination-card-style bottom-7! relative" data-swiper-pagination></div>
                <?php endif; ?>

                <div class="justify-center flex mt-10">
                    <?php if ( $sectionLoadMore ) : ?>
                        <a href="<?= esc_html( $sectionLoadMore['url'] ) ?: '' ?>" class="cta-link cta-btn-primary cta-has-icon">
                            <?= esc_html( $sectionLoadMore['title'] ) ?: '' ?>
                            <span class="icon"><?= icon( 'arrow-linear-2', 'w-5 h-5' ) ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
