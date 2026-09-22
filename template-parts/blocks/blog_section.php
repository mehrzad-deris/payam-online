<?php
/**
 * Blog slider section.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor    = get_sub_field( 'section_color' ) ?: '#f6f8fe';
$sectionStyle    = get_sub_field( 'section_style' ) ?: 'light';
$postsSource     = (string) ( get_sub_field( 'posts_source' ) ?: 'recent' );
$postsCount      = max( 1, absint( get_sub_field( 'posts_count' ) ?: 6 ) );
$selectedPosts   = get_sub_field( 'selected_posts' );
$blogPosts       = [];
$sectionLoadMore = get_sub_field( 'blog_more_link' );
$sectionStyles   = [ '--blog-background: ' . ( sanitize_hex_color( $sectionColor ) ?: '#f6f8fe' ) ];

foreach ( [ 'padding_top', 'padding_top_mobile', 'padding_bottom', 'padding_bottom_mobile' ] as $fieldName ) {
    $fieldValue = get_sub_field( $fieldName );
    if ( is_numeric( $fieldValue ) ) {
        $sectionStyles[] = '--blog-' . str_replace( '_', '-', $fieldName ) . ': ' . absint( $fieldValue ) . 'px';
    }
}

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
        style="<?= esc_attr( implode( '; ', $sectionStyles ) ); ?>"
>
    <div class="container blog-container">
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
                    <div class="swiper-pagination blog-pagination swiper-pagination-card-style bottom-13! relative" data-swiper-pagination></div>
                <?php endif; ?>

                <div class="justify-center flex mt-12">
                    <?php if ( is_array( $sectionLoadMore ) && ! empty( $sectionLoadMore['url'] ) ) : $moreTarget = (string) ( $sectionLoadMore['target'] ?? '_self' ); ?>
                        <a href="<?= esc_url( $sectionLoadMore['url'] ); ?>" target="<?= esc_attr( $moreTarget ); ?>"<?= '_blank' === $moreTarget ? ' rel="noopener noreferrer"' : ''; ?> class="cta-link cta-btn-primary cta-has-icon">
                            <?= esc_html( $sectionLoadMore['title'] ?? '' ); ?>
                            <span class="icon"><?= icon( 'arrow-linear-2', 'w-5 h-5' ) ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
