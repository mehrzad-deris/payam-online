<?php
/**
 * Testimonials slider section.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor    = get_sub_field( 'section_color' ) ?: '#f6f8fe';
$sectionStyle    = get_sub_field( 'section_style' ) ?: 'light';
$sectionIcon     = absint( get_sub_field( 'section_icon' ) );
$sectionTitle    = (string) ( get_sub_field( 'section_title' ) ?: '' );
$sectionTitleTag = get_sub_field( 'title_tag' ) ?: 'h2';
$sectionSubtitle = (string) ( get_sub_field( 'section_subtitle' ) ?: '' );
$testimonials    = get_sub_field( 'testimonials' );
$transparent     = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

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
	style="--testimonials-background: <?= esc_attr( $sectionColor ); ?>"
>
	<div class="container testimonials-container">
		<?php
		section_heading(
			[
				'icon'           => $sectionIcon,
				'title'          => $sectionTitle,
				'title_tag'      => $sectionTitleTag,
				'subtitle'       => $sectionSubtitle,
				'class'          => 'testimonials-heading',
				'title_class'    => 'testimonials-title',
				'subtitle_class' => 'testimonials-subtitle',
			]
		);
		?>

		<?php if ( ! empty( $testimonials ) ) : ?>
			<div class="swiper testimonials-slider" data-testimonials-slider>
				<div class="swiper-wrapper">
					<?php foreach ( $testimonials as $item ) :
						$authorImage = absint( $item['author_image'] ?? 0 );
						$authorUrl   = $authorImage ? wp_get_attachment_image_url( $authorImage, 'testimonial_avatar' ) : false;
						$author2x    = $authorImage ? wp_get_attachment_image_url( $authorImage, 'testimonial_avatar_x2' ) : false;
						$authorAlt   = $authorImage ? get_post_meta( $authorImage, '_wp_attachment_image_alt', true ) : '';
						?>
						<article class="swiper-slide testimonial-card">
							<picture class="testimonial-background" aria-hidden="true">
								<source media="(max-width: 767px)" data-testimonial-srcset="<?= esc_url( get_theme_file_uri( '/assets/images/customer-bg-mobile.webp' ) ); ?>">
								<img src="<?= esc_attr( $transparent ); ?>" data-testimonial-src="<?= esc_url( get_theme_file_uri( '/assets/images/customer-bfg.webp' ) ); ?>" alt="" width="979" height="416" decoding="async">
							</picture>

							<div class="testimonial-content">
								<div class="testimonial-quote" aria-hidden="true">
                                    <?= icon('quote') ?>
								</div>

								<div class="testimonial-body">
									<p><?= esc_html( $item['text'] ); ?></p>

									<div class="testimonial-rating">
										<?php for ( $star = 0; $star < 5; $star++ ) : ?>
                                            <?= icon('star') ?>
										<?php endfor; ?>
									</div>
								</div>

								<div class="testimonial-author">
									<?php if ( $authorUrl ) : ?>
										<img class="testimonial-avatar" src="<?= esc_url( $authorUrl ); ?>"<?= $author2x ? ' srcset="' . esc_url( $authorUrl ) . ' 1x, ' . esc_url( $author2x ) . ' 2x"' : ''; ?> alt="<?= esc_attr( $authorAlt ?: ( $item['author_name'] ?? '' ) ); ?>" width="56" height="56" loading="lazy" decoding="async">
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
					<div class="swiper-pagination testimonials-pagination"></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
