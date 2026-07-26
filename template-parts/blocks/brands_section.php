<?php
/**
 * Trusted brands section block.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor    = get_sub_field( 'section_color' ) ?: '#f6f8fe';
$sectionStyle    = get_sub_field( 'section_style' ) ?: 'light';
$sectionIcon     = absint( get_sub_field( 'section_icon' ) );
$sectionTitle    = (string) ( get_sub_field( 'section_title' ) ?: '' );
$sectionTitleTag = get_sub_field( 'title_tag' ) ?: 'h2';
$sectionSubtitle = (string) ( get_sub_field( 'section_subtitle' ) ?: '' );
$centerLogo      = absint( get_sub_field( 'center_logo' ) );
$brands          = get_sub_field( 'brands' );
$transparent     = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

if ( ! is_array( $brands ) ) {
	$brands = [];
}

$brands = array_values(
	array_filter(
		$brands,
		static fn( array $brand ): bool => ! empty( $brand['logo'] )
	)
);

if ( ! empty( $brands ) ) {
	$minimumItems = 8;
	$repeatCount  = max( 1, (int) ceil( $minimumItems / count( $brands ) ) );
	$loopBrands   = [];

	for ( $repeat = 0; $repeat < $repeatCount; $repeat++ ) {
		$loopBrands = array_merge( $loopBrands, $brands );
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
	style="--brands-background: <?= esc_attr( $sectionColor ); ?>"
>
	<div class="container brands-container">
		<?php
		section_heading(
			[
				'icon'           => $sectionIcon,
				'title'          => $sectionTitle,
				'title_tag'      => $sectionTitleTag,
				'subtitle'       => $sectionSubtitle,
				'class'          => 'brands-heading',
				'title_class'    => 'brands-title',
				'subtitle_class' => 'brands-subtitle',
			]
		);
		?>
    </div>

		<?php if ( $centerLogo1x && ! empty( $loopBrands ) ) : ?>
			<div class="brands-stage" data-brand-stage>
				<div class="brands-window">
					<div class="brands-track" data-brand-track>
						<?php for ( $copy = 0; $copy < 2; $copy++ ) : ?>
							<div class="brands-group"<?= 1 === $copy ? ' aria-hidden="true"' : ''; ?>>
								<?php foreach ( $loopBrands as $brandIndex => $brand ) :
									$logoId  = absint( $brand['logo'] );
									$logoUrl = wp_get_attachment_image_url( $logoId, 'brand_section_logo_center' );
									$logo2x  = wp_get_attachment_image_url( $logoId, 'brand_section_logo_center_x2' );
									$logoAlt = get_post_meta( $logoId, '_wp_attachment_image_alt', true );

									if ( ! $logoUrl ) {
										continue;
									}
									?>
									<div class="brand-item" data-brand-item>
										<img
											src="<?= esc_url( $transparent ); ?>"
											data-feature-desktop-src="<?= esc_url( $logoUrl ); ?>"
											<?= $logo2x ? 'data-feature-desktop-srcset="' . esc_url( $logoUrl ) . ' 1x, ' . esc_url( $logo2x ) . ' 2x"' : ''; ?>
											alt="<?= 0 === $copy && $brandIndex < count( $brands ) ? esc_attr( $logoAlt ) : ''; ?>"
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
						src="<?= esc_url( $transparent ); ?>"
						data-feature-desktop-src="<?= esc_url( $centerLogo1x ); ?>"
						<?= $centerLogo2x ? 'data-feature-desktop-srcset="' . esc_url( $centerLogo1x ) . ' 1x, ' . esc_url( $centerLogo2x ) . ' 2x"' : ''; ?>
						alt=""
						width="64"
						height="64"
						decoding="async"
					>
				</div>
			</div>
		<?php endif; ?>
</section>
