<?php
/**
 * Card-based services section.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor             = sanitize_hex_color( (string) get_sub_field( 'section_color' ) );
$sectionStyle             = 'dark' === get_sub_field( 'section_style' ) ? 'dark' : 'light';
$sectionServices          = get_sub_field( 'services' );
$paddingTopValue          = get_sub_field( 'padding_top' );
$paddingTopMobileValue    = get_sub_field( 'padding_top_mobile' );
$paddingBottomValue       = get_sub_field( 'padding_bottom' );
$paddingBottomMobileValue = get_sub_field( 'padding_bottom_mobile' );
$sectionStyles            = [];
$sectionServices          = is_array( $sectionServices ) ? array_values( array_filter( $sectionServices, 'is_array' ) ) : [];

if ( ! $sectionServices ) {
	return;
}

if ( $sectionColor ) {
	$sectionStyles[] = 'background-color: ' . $sectionColor;
}

foreach (
	[
		'padding-top'           => $paddingTopValue,
		'padding-top-mobile'    => $paddingTopMobileValue,
		'padding-bottom'        => $paddingBottomValue,
		'padding-bottom-mobile' => $paddingBottomMobileValue,
	] as $property => $value
) {
	if ( is_numeric( $value ) ) {
		$sectionStyles[] = '--services-card-' . $property . ': ' . absint( $value ) . 'px';
	}
}
?>

<section
	class="services-card-section<?= 'dark' === $sectionStyle ? ' text-white' : ''; ?>"
	data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
	<?= $sectionStyles ? 'style="' . esc_attr( implode( '; ', $sectionStyles ) ) . '"' : ''; ?>
>
	<div class="container">
		<div class="services-items grid md:grid-cols-2 lg:grid-cols-4 gap-5">
			<?php foreach ( $sectionServices as $service ) :
				$title       = trim( (string) ( $service['title'] ?? '' ) );
				$subtitle    = trim( (string) ( $service['subtitle'] ?? '' ) );
				$price       = $service['price'] ?? '';
				$icon        = $service['icon'] ?? '';
				$iconId      = is_array( $icon ) ? absint( $icon['ID'] ?? $icon['id'] ?? 0 ) : absint( $icon );
				$iconUrl     = is_array( $icon ) ? (string) ( $icon['url'] ?? '' ) : ( is_string( $icon ) ? $icon : '' );
				$link        = $service['link'] ?? '';
				$linkUrl     = is_array( $link ) ? (string) ( $link['url'] ?? '' ) : (string) $link;
				$linkTarget  = is_array( $link ) ? (string) ( $link['target'] ?? '' ) : '';
				$linkTitle   = is_array( $link ) ? trim( (string) ( $link['title'] ?? '' ) ) : '';
				$ctaText     = $linkTitle ?: 'مشاهده و خرید';
				$priceOutput = is_numeric( $price ) ? number_format_i18n( (float) $price ) : trim( (string) $price );
				?>
				<article class="service-item group">
					<?php if ( '' !== $linkUrl ) : ?>
						<a href="<?= esc_url( $linkUrl ); ?>" class="inner"<?= '_blank' === $linkTarget ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
					<?php else : ?>
						<div class="inner">
					<?php endif; ?>
						<span class="item-inner flex-row md:flex-col p-5 pb-7 text-neutral-500">
							<span class="service-heading flex items-center gap-2">
								<?php if ( $iconId || '' !== $iconUrl ) : ?>
									<span class="service-icon">
										<?php if ( $iconId ) : ?>
											<?= wp_get_attachment_image( $iconId, 'thumbnail', false, [ 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php else : ?>
											<img src="<?= esc_url( $iconUrl ); ?>" alt="" loading="lazy" decoding="async">
										<?php endif; ?>
									</span>
								<?php endif; ?>
								<?php if ( '' !== $title ) : ?><span class="service-title text-desktop-h5 text-neutral-900"><?= esc_html( $title ); ?></span><?php endif; ?>
							</span>
							<?php if ( '' !== $priceOutput ) : ?>
								<span class="service-price flex items-center gap-1"><span class="service-price-label text-caption">شروع قیمت از:</span><span class="service-price-value text-desktop-h5 text-neutral-900"><?= esc_html( $priceOutput ); ?></span></span>
							<?php endif; ?>
							<?php if ( '' !== $subtitle ) : ?><span class="service-subtitle"><span class="text-caption-mobile"><?= esc_html( $subtitle ); ?></span></span><?php endif; ?>
							<?php if ( '' !== $linkUrl ) : ?>
								<span class="service-cta cta-link cta-btn-primary cta-opacity-style border-transparent! fill-transparent group-hover:fill-white group-hover:bg-primary-500! group-hover:text-white! text-body-3 gap-0!">
									<span class="service-cta-text"><?= esc_html( $ctaText ); ?></span><span class="service-cta-text-mobile ml-2">خرید</span><?= icon( 'arrow-linear-2', 'service-cta-icon w-0 h-5 group-hover:w-7 duration-300' ); ?>
								</span>
							<?php endif; ?>
						</span>
					<?php if ( '' !== $linkUrl ) : ?>
						</a>
					<?php else : ?>
						</div>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
