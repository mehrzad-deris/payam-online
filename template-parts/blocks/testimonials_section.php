<?php
/**
 * Testimonials slider section.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor    = get_sub_field( 'section_color' ) ?: '#f6f8fe';
$sectionStyle    = get_sub_field( 'section_style' ) ?: 'light';
$testimonials    = get_sub_field( 'testimonials' );
$transparent     = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
$sectionStyles   = [ '--testimonials-background: ' . ( sanitize_hex_color( $sectionColor ) ?: '#f6f8fe' ) ];

foreach ( [ 'padding_top', 'padding_top_mobile', 'padding_bottom', 'padding_bottom_mobile' ] as $fieldName ) {
	$fieldValue = get_sub_field( $fieldName );
	if ( is_numeric( $fieldValue ) ) {
		$sectionStyles[] = '--testimonials-' . str_replace( '_', '-', $fieldName ) . ': ' . absint( $fieldValue ) . 'px';
	}
}

if ( ! is_array( $testimonials ) ) {
	$testimonials = [];
}

$testimonials = array_values(
	array_filter(
		$testimonials,
		static fn( array $item ): bool => ! empty( $item['text'] )
	)
);

?>

<section
	class="testimonials-section testimonials-<?= esc_attr( $sectionStyle ); ?>"
	data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
	style="<?= esc_attr( implode( '; ', $sectionStyles ) ); ?>"
>
	<div class="container testimonials-container">
		<?php if ( ! empty( $testimonials ) ) : ?>
			<div class="swiper testimonials-slider" data-swiper="testimonials">
				<div class="swiper-wrapper">
					<?php foreach ( $testimonials as $item ) :
						$authorImage = absint( $item['author_image'] ?? 0 );
						$authorUrl   = $authorImage ? wp_get_attachment_image_url( $authorImage, 'testimonial_avatar' ) : false;
						$author2x    = $authorImage ? wp_get_attachment_image_url( $authorImage, 'testimonial_avatar_x2' ) : false;
						$authorAlt   = $authorImage ? get_post_meta( $authorImage, '_wp_attachment_image_alt', true ) : '';
						?>
						<article class="swiper-slide testimonial-card">
								<img class="testimonial-background" src="<?= esc_attr( $transparent ); ?>" data-lazy-src="<?= esc_url( get_theme_file_uri( '/assets/images/customer-bg.svg' ) ); ?>" alt="" width="979" height="416" decoding="async">

							<div class="testimonial-content">
								<div class="testimonial-quote" aria-hidden="true">
                                    <?= icon('quote') ?>
								</div>

								<div class="testimonial-body">
									<p><?= esc_html( $item['text'] ); ?></p>
								</div>

								<div class="testimonial-author">
									<?php if ( $authorUrl ) : ?>
                                        <span class="testimonial-avatar"><img src="<?= esc_url( $authorUrl ); ?>"<?= $author2x ? ' srcset="' . esc_url( $authorUrl ) . ' 1x, ' . esc_url( $author2x ) . ' 2x"' : ''; ?> alt="<?= esc_attr( $authorAlt ?: ( $item['author_name'] ?? '' ) ); ?>" width="59" height="59" loading="lazy" decoding="async"></span>
									<?php endif; ?>
									<div class="testimonial-copy">
										<strong><?= esc_html( $item['author_name'] ?? '' ); ?></strong>
										<span><?= esc_html( $item['author_role'] ?? '' ); ?></span>
									</div>

								</div>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<?php if ( count( $testimonials ) > 1 ) : ?>
					<div class="swiper-pagination testimonials-pagination" data-swiper-pagination></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
