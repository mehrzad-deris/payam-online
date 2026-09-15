<?php
/**
 * Feature section block.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor      = get_sub_field( 'section_color' ) ?: '';
$sectionStyle      = get_sub_field( 'section_style' ) ?: 'light';
$featureItems      = get_sub_field( 'features' );
$sectionStyles     = [];

if ( '' !== $sectionColor ) {
	$sectionStyles[] = 'background-color: ' . $sectionColor;
}

foreach ( [ 'padding_top', 'padding_top_mobile', 'padding_bottom', 'padding_bottom_mobile' ] as $fieldName ) {
	$fieldValue = get_sub_field( $fieldName );
	if ( is_numeric( $fieldValue ) ) {
		$sectionStyles[] = '--feature-' . str_replace( '_', '-', $fieldName ) . ': ' . absint( $fieldValue ) . 'px';
	}
}

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
	<?= $sectionStyles ? 'style="' . esc_attr( implode( '; ', $sectionStyles ) ) . '"' : ''; ?>
>
	<div class="container">
		<?php if ( is_array( $featureItems ) && ! empty( $featureItems ) ) : ?>
			<div class="feature-section__grid">
				<?php foreach ( $featureItems as $featureItem ) :
					$featureIcon        = absint( $featureItem['feature_icon'] ?? 0 );
					$featureTitle       = $featureItem['feature_title'] ?? '';
					$featureDescription = $featureItem['feature_description'] ?? '';
					?>
					<article class="feature-section__item">
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
									<h3 class="feature-section__title md:text-desktop-h5"><?= esc_html( $featureTitle ); ?></h3>
								<?php endif; ?>

								<?php if ( '' !== $featureDescription ) : ?>
									<p class="feature-section__description text-body-3 text-neutral-500"><?= esc_html( $featureDescription ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
