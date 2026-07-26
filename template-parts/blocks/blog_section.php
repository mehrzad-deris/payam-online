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

if ( 'selected' === $postsSource && is_array( $selectedPosts ) ) {
	$selectedPostIds = array_values(
		array_filter(
			array_map(
				static fn( $post ): int => absint( $post instanceof WP_Post ? $post->ID : $post ),
				$selectedPosts
			)
		)
	);

	if ( ! empty( $selectedPostIds ) ) {
		$blogPosts = get_posts(
			[
				'post_type'              => 'post',
				'post_status'            => 'publish',
				'post__in'               => $selectedPostIds,
				'orderby'                => 'post__in',
				'posts_per_page'         => count( $selectedPostIds ),
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => false,
			]
		);
	}
} else {
	$blogPosts = get_posts(
		[
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => $postsCount,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
		]
	);
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
						$postId      = $blogPost->ID;
						$thumbnailId = get_post_thumbnail_id( $postId );
						$thumbnail   = $thumbnailId ? wp_get_attachment_image_url( $thumbnailId, 'blog_card' ) : false;
						$thumbnail2x = $thumbnailId ? wp_get_attachment_image_url( $thumbnailId, 'blog_card_x2' ) : false;
						$thumbnailAlt = $thumbnailId ? get_post_meta( $thumbnailId, '_wp_attachment_image_alt', true ) : '';
						?>
						<article class="swiper-slide blog-card">
							<a class="blog-link" href="<?= esc_url( get_permalink( $postId ) ); ?>">
								<?php if ( $thumbnail ) : ?>
									<img
										class="blog-image"
										src="<?= esc_url( $thumbnail ); ?>"
										<?= $thumbnail2x ? 'srcset="' . esc_url( $thumbnail ) . ' 1x, ' . esc_url( $thumbnail2x ) . ' 2x"' : ''; ?>
										alt="<?= esc_attr( $thumbnailAlt ?: get_the_title( $postId ) ); ?>"
										width="389"
										height="218"
										loading="lazy"
										decoding="async"
									>
								<?php endif; ?>

								<h3 class="blog-title"><?= esc_html( get_the_title( $postId ) ); ?></h3>
								<time class="blog-date" datetime="<?= esc_attr( get_the_date( DATE_W3C, $postId ) ); ?>">
									<?= esc_html( get_the_date( '', $postId ) ); ?>
								</time>
							</a>
						</article>
					<?php endforeach; ?>
                </div>

                <?php if ( count( $blogPosts ) > 1 ) : ?>
                    <div class="swiper-pagination blog-pagination" data-swiper-pagination></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
