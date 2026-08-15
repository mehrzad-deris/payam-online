<?php
/**
 * Operating systems logo slider section.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor        = get_sub_field( 'section_color' ) ?: '#07041d';
$sectionStyle        = (string) ( get_sub_field( 'section_style' ) ?: 'dark' );
$sectionIcon         = absint( get_sub_field( 'section_icon' ) );
$sectionTitle        = (string) ( get_sub_field( 'section_title' ) ?: '' );
$sectionTitleTag     = (string) ( get_sub_field( 'title_tag' ) ?: 'h2' );
$sectionSubtitle     = (string) ( get_sub_field( 'section_subtitle' ) ?: '' );
$marginTopField      = get_sub_field( 'section_margin_top' );
$marginBottomField   = get_sub_field( 'section_margin_bottom' );
$logoRows            = get_sub_field( 'logo_image_list' );
$transparentPixel    = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
$sectionStyles       = [ 'background-color: ' . ( sanitize_hex_color( $sectionColor ) ?: '#07041d' ) ];
$logos               = [];

if ( is_numeric( $marginTopField ) ) {
	$sectionStyles[] = 'margin-top: ' . max( -1000, min( 1000, (int) $marginTopField ) ) . 'px';
}

if ( is_numeric( $marginBottomField ) ) {
	$sectionStyles[] = 'margin-bottom: ' . max( -1000, min( 1000, (int) $marginBottomField ) ) . 'px';
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
		<?php
		section_heading( [
			'icon'        => $sectionIcon,
			'title'       => $sectionTitle,
			'title_tag'   => $sectionTitleTag,
			'subtitle'    => $sectionSubtitle,
			'title_class' => 'dark' === $sectionStyle ? 'text-white' : '',
		] );
		?>

		<?php if ( ! empty( $logos ) ) : ?>
			<div class="swiper os-logo-slider" data-swiper="os-logos" aria-label="<?= esc_attr( $sectionTitle ); ?>">
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
