<?php
/**
 * Trusted brands section block.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor    = get_sub_field( 'section_color' ) ?: '#f6f8fe';
$sectionStyle    = get_sub_field( 'section_style' ) ?: 'light';
$centerLogo      = absint( get_sub_field( 'center_logo' ) );
$brands          = get_sub_field( 'brands' );
$transparent     = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
$sectionStyles   = [ '--brands-background: ' . ( sanitize_hex_color( $sectionColor ) ?: '#f6f8fe' ) ];

foreach ( [ 'padding_top', 'padding_top_mobile', 'padding_bottom', 'padding_bottom_mobile' ] as $fieldName ) {
	$fieldValue = get_sub_field( $fieldName );
	if ( is_numeric( $fieldValue ) ) {
		$sectionStyles[] = '--brands-' . str_replace( '_', '-', $fieldName ) . ': ' . absint( $fieldValue ) . 'px';
	}
}

if ( ! is_array( $brands ) ) {
	$brands = [];
}

$brands = array_values(
	array_filter(
		$brands,
		static fn( array $brand ): bool => ! empty( $brand['logo'] )
	)
);

$brandAssets = [];
foreach ( $brands as $brand ) {
	$logoId  = absint( $brand['logo'] );
	$logoUrl = wp_get_attachment_image_url( $logoId, 'brand_section_logo_center' );
	if ( ! $logoUrl ) { continue; }
	$logo2x        = wp_get_attachment_image_url( $logoId, 'brand_section_logo_center_x2' );
	$brandAssets[] = [
		'url'    => $logoUrl,
		'url_2x' => $logo2x ?: '',
		'alt'    => (string) get_post_meta( $logoId, '_wp_attachment_image_alt', true ),
	];
}

if ( ! empty( $brandAssets ) ) {
	$minimumItems = 8;
	$repeatCount  = max( 1, (int) ceil( $minimumItems / count( $brandAssets ) ) );
	$loopBrands   = [];

	for ( $repeat = 0; $repeat < $repeatCount; $repeat++ ) {
		$loopBrands = array_merge( $loopBrands, $brandAssets );
	}
} else {
	$loopBrands = [];
}

$centerLogo1x = $centerLogo ? wp_get_attachment_image_url( $centerLogo, 'brand_logo_center' ) : false;
$centerLogo2x = $centerLogo ? wp_get_attachment_image_url( $centerLogo, 'brand_logo_center_x2' ) : false;
?>

<section
	class="brands-section brands-<?= esc_attr( $sectionStyle ); ?>"
	data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
	data-feature-module
	data-brands-section
	style="<?= esc_attr( implode( '; ', $sectionStyles ) ); ?>"
>
		<?php if ( $centerLogo1x && ! empty( $loopBrands ) ) : ?>
			<div class="brands-stage" data-brand-stage>
				<div class="brands-window">
					<div class="brands-track" data-brand-track>
						<?php for ( $copy = 0; $copy < 2; $copy++ ) : ?>
							<div class="brands-group"<?= 1 === $copy ? ' aria-hidden="true"' : ''; ?>>
								<?php foreach ( $loopBrands as $brandIndex => $brand ) : ?>
									<div class="brand-item" data-brand-item>
										<img
											src="<?= esc_attr( $transparent ); ?>"
											data-lazy-desktop-src="<?= esc_url( $brand['url'] ); ?>"
											<?= $brand['url_2x'] ? 'data-lazy-desktop-srcset="' . esc_url( $brand['url'] ) . ' 1x, ' . esc_url( $brand['url_2x'] ) . ' 2x"' : ''; ?>
											alt="<?= 0 === $copy && $brandIndex < count( $brandAssets ) ? esc_attr( $brand['alt'] ) : ''; ?>"
											width="173"
											height="36"
											decoding="async"
										>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endfor; ?>
					</div>
				</div>

				<div class="brand-center" data-brand-center>
					<span class="brand-ring ring-one"></span>
					<span class="brand-ring ring-two"></span>
					<span class="brand-ring ring-three"></span>
					<img
						src="<?= esc_attr( $transparent ); ?>"
						data-lazy-desktop-src="<?= esc_url( $centerLogo1x ); ?>"
						<?= $centerLogo2x ? 'data-lazy-desktop-srcset="' . esc_url( $centerLogo1x ) . ' 1x, ' . esc_url( $centerLogo2x ) . ' 2x"' : ''; ?>
						alt=""
						width="64"
						height="64"
						decoding="async"
					>
				</div>
			</div>
		<?php endif; ?>
</section>
