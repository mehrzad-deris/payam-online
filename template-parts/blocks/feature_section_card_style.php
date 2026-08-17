<?php
/**
 * Feature section card-style block.
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
		array_filter(
			$featureItems,
			static fn( $item ) => ! empty( $item['feature_title'] ) || ! empty( $item['feature_description'] )
		)
	);
}

$extraItemsCount = is_array( $featureItems ) ? max( 0, count( $featureItems ) - 3 ) : 0;
?>

<section
	class="feature-card feature-card--<?= esc_attr( $sectionStyle ); ?><?= $extraItemsCount > 0 ? ' feature-card--collapsible' : ''; ?>"
	data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
	data-feature-card
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
			<div class="feature-card__grid">
				<?php foreach ( $featureItems as $featureIndex => $featureItem ) :
					$featureIcon        = absint( $featureItem['feature_icon'] ?? 0 );
					$featureTitle       = $featureItem['feature_title'] ?? '';
					$featureDescription = $featureItem['feature_description'] ?? '';
					?>
					<article class="feature-card__item<?= $featureIndex >= 3 ? ' feature-card__item--extra' : ''; ?>"<?= $featureIndex >= 3 ? ' data-feature-card-extra' : ''; ?>>
						<div class="feature-card__content">
							<?php if ( $featureIcon ) : ?>
								<div class="feature-card__icon" aria-hidden="true">
									<?= wp_get_attachment_image(
										$featureIcon,
										'full',
										false,
										[
											'alt'      => '',
											'loading'  => 'lazy',
											'decoding' => 'async',
										]
									); ?>
								</div>
							<?php endif; ?>

							<div class="feature-card__text">
								<?php if ( '' !== $featureTitle ) : ?>
									<h3 class="feature-card__title md:text-body-2 text-body-mobile-2"><?= esc_html( $featureTitle ); ?></h3>
								<?php endif; ?>

								<?php if ( '' !== $featureDescription ) : ?>
									<p class="feature-card__description md:text-caption text-caption-mobile"><?= esc_html( $featureDescription ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<?php if ( $extraItemsCount > 0 ) : ?>
				<button
					type="button"
					class="feature-card__toggle text-body-3"
					data-feature-card-toggle
					data-collapsed-label="<?= esc_attr( sprintf( 'مشاهده %s مورد دیگر', number_format_i18n( $extraItemsCount ) ) ); ?>"
					data-expanded-label="نمایش کمتر"
					aria-expanded="false"
				>
					<span data-feature-card-label><?= esc_html( sprintf( 'مشاهده %s مورد دیگر', number_format_i18n( $extraItemsCount ) ) ); ?></span>
					<span class="icon feature-card__toggle-icon" aria-hidden="true"><?= icon( 'arrow-down' ); ?></span>
				</button>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
