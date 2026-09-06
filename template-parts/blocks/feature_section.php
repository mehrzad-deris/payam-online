<?php
/**
 * Feature section block.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor      = get_sub_field( 'section_color' ) ?: '';
$sectionStyle      = get_sub_field( 'section_style' ) ?: 'light';
$sectionIcon       = absint( get_sub_field( 'section_icon' ) );
$sectionTitle      = get_sub_field( 'section_title' );
$sectionTitleTag   = get_sub_field( 'title_tag' ) ?: 'h2';
$sectionSubtitle   = get_sub_field( 'section_subtitle' );
$featureItems      = get_sub_field( 'features' );
$marginTopField    = get_sub_field( 'section_margin_top' );
$marginBottomField = get_sub_field( 'section_margin_bottom' );
$sectionStyles     = [];

if ( '' !== $sectionColor ) {
	$sectionStyles[] = 'background-color: ' . $sectionColor;
}

if ( is_numeric( $marginTopField ) ) {
	$sectionStyles[] = 'margin-top: ' . max( -1000, min( 1000, (int) $marginTopField ) ) . 'px';
}

if ( is_numeric( $marginBottomField ) ) {
	$sectionStyles[] = 'margin-bottom: ' . max( -1000, min( 1000, (int) $marginBottomField ) ) . 'px';
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
