<?php
/**
 * Feature section block.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor    = get_sub_field( 'section_color' ) ?: '';
$sectionStyle    = get_sub_field( 'section_style' ) ?: 'light';
$sectionIcon     = absint( get_sub_field( 'section_icon' ) );
$sectionTitle    = get_sub_field( 'section_title' );
$sectionTitleTag = get_sub_field( 'title_tag' ) ?: 'h2';
$sectionSubtitle = get_sub_field( 'section_subtitle' );
$featureItems    = get_sub_field( 'features' );

if ( is_array( $featureItems ) ) {
	$featureItems = array_values(
		array_slice(
			array_filter(
				$featureItems,
				static fn( $item ) => ! empty( $item['feature_title'] ) || ! empty( $item['feature_description'] )
			),
			0,
			4
		)
	);
}
?>

<section
	class="feature-section feature-section--<?= esc_attr( $sectionStyle ); ?>"
	data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
	<?php if ( '' !== $sectionColor ) : ?>style="background-color: <?= esc_attr( $sectionColor ); ?>"<?php endif; ?>
>
	<div class="container">
		<?php
		section_heading(
			[
				'icon'      => $sectionIcon,
				'title'     => $sectionTitle,
				'title_tag' => $sectionTitleTag,
				'subtitle'  => $sectionSubtitle,
			]
		);
		?>

		<?php if ( is_array( $featureItems ) && ! empty( $featureItems ) ) : ?>
			<div class="feature-section__grid">
				<?php foreach ( $featureItems as $featureItem ) :
					$featureIcon        = absint( $featureItem['feature_icon'] ?? 0 );
					$featureTitle       = $featureItem['feature_title'] ?? '';
					$featureDescription = $featureItem['feature_description'] ?? '';
					?>
					<article class="feature-section__item">
						<img
							class="feature-section__shape"
							src="<?= esc_url( get_theme_file_uri( '/assets/images/features-shape.svg' ) ); ?>"
							alt=""
							width="233"
							height="148"
							loading="lazy"
							decoding="async"
							aria-hidden="true"
						>

						<div class="feature-section__content">
							<?php if ( $featureIcon ) : ?>
								<div class="feature-section__icon" aria-hidden="true">
									<?= wp_get_attachment_image(
										$featureIcon,
										'full',
										false,
										[
											'alt'      => '',
											'loading'  => 'lazy',
											'decoding' => 'async',
										]
									); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php endif; ?>

							<div class="feature-section__text">
								<?php if ( '' !== $featureTitle ) : ?>
									<h3 class="feature-section__title"><?= esc_html( $featureTitle ); ?></h3>
								<?php endif; ?>

								<?php if ( '' !== $featureDescription ) : ?>
									<p class="feature-section__description"><?= esc_html( $featureDescription ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
