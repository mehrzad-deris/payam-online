<?php
/**
 * Operating systems logo slider section.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor        = get_sub_field( 'section_color' ) ?: '';
$sectionStyle        = (string) ( get_sub_field( 'section_style' ) ?: 'light' );
$logoRows            = get_sub_field( 'logo_image_list' );
$transparentPixel    = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
$sectionStyles       = [ 'background-color: ' . ( sanitize_hex_color( $sectionColor ) ?: '' ) ];
$logos               = [];

foreach ( [ 'padding_top', 'padding_top_mobile', 'padding_bottom', 'padding_bottom_mobile' ] as $fieldName ) {
	$fieldValue = get_sub_field( $fieldName );
	if ( is_numeric( $fieldValue ) ) {
		$sectionStyles[] = '--os-logo-' . str_replace( '_', '-', $fieldName ) . ': ' . absint( $fieldValue ) . 'px';
	}
}

if ( is_array( $logoRows ) ) {
	foreach ( $logoRows as $logoRow ) {
		if ( ! is_array( $logoRow ) ) {
			continue;
		}

		$logoField = $logoRow['logo_image'] ?? 0;
		$logoId    = absint( is_array( $logoField ) ? ( $logoField['ID'] ?? 0 ) : $logoField );

		if ( $logoId ) {
			$logos[] = $logoId;
		}
	}
}
?>

<section
	class="os-logo-section os-logo-section-<?= esc_attr( $sectionStyle ); ?>"
	data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
	style="<?= esc_attr( implode( '; ', $sectionStyles ) ); ?>"
>
	<div class="container os-logo-container">
		<?php if ( ! empty( $logos ) ) : ?>
			<div class="swiper os-logo-slider" data-swiper="os-logos" aria-label="لوگو سیستم‌عامل‌ها">
				<div class="swiper-wrapper">
					<?php foreach ( $logos as $logoId ) :
						$logo1x  = wp_get_attachment_image_src( $logoId, 'os_logo_image' );
						$logo2x  = wp_get_attachment_image_url( $logoId, 'os_logo_image_2' );
						$logoAlt = get_post_meta( $logoId, '_wp_attachment_image_alt', true );

						if ( ! $logo1x ) {
							continue;
						}
						?>
						<div class="swiper-slide os-logo-item">
							<img
								class="os-logo-image"
								src="<?= esc_attr( $transparentPixel ); ?>"
								data-lazy-src="<?= esc_url( $logo1x[0] ); ?>"
								<?= $logo2x ? 'data-lazy-srcset="' . esc_url( $logo1x[0] ) . ' 1x, ' . esc_url( $logo2x ) . ' 2x"' : ''; ?>
								alt="<?= esc_attr( $logoAlt ); ?>"
								width="<?= esc_attr( $logo1x[1] ); ?>"
								height="<?= esc_attr( $logo1x[2] ); ?>"
								decoding="async"
							>

                            <?= icon('top-shape', 'top-shape') ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
