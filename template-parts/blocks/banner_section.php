<?php
/**
 * Responsive linked banner section.
 */

defined( 'ABSPATH' ) || exit;

$bannerImage       = absint( get_sub_field( 'banner_image' ) );
$bannerMobileImage = absint( get_sub_field( 'banner_mobile_image' ) );
$bannerLink        = get_sub_field( 'cta_link' ) ?: get_sub_field( 'banner_link' );
$bannerSize        = (string) ( get_sub_field( 'banner_size' ) ?: 'medium' );
$transparentPixel  = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

if ( ! in_array( $bannerSize, [ 'medium', 'full' ], true ) ) {
	$bannerSize = 'medium';
}

if ( ! $bannerImage ) {
	return;
}

$isFull            = 'full' === $bannerSize;
$desktopImageSize  = $isFull ? 'banner_full' : 'banner_desktop';
$desktopImage2x    = $isFull ? 'banner_full_x2' : 'banner_desktop_x2';
$mobileImageSize   = $isFull ? 'banner_full_mobile' : 'banner_mobile';
$mobileImage2x     = $isFull ? 'banner_full_mobile_x2' : 'banner_mobile_x2';
$desktopImage      = wp_get_attachment_image_src( $bannerImage, $desktopImageSize );
$desktopImage2xSrc = wp_get_attachment_image_src( $bannerImage, $desktopImage2x );
$mobileImage       = $bannerMobileImage ? wp_get_attachment_image_src( $bannerMobileImage, $mobileImageSize ) : false;
$mobileImage2xSrc  = $bannerMobileImage ? wp_get_attachment_image_src( $bannerMobileImage, $mobileImage2x ) : false;
$imageAlt   = (string) get_post_meta( $bannerImage, '_wp_attachment_image_alt', true );
$linkUrl    = is_array( $bannerLink ) ? ( $bannerLink['url'] ?? '' ) : $bannerLink;
$linkTarget = is_array( $bannerLink ) ? ( $bannerLink['target'] ?? '_self' ) : '_self';
$linkTitle  = is_array( $bannerLink ) ? ( $bannerLink['title'] ?? '' ) : '';

if ( ! $desktopImage ) {
	return;
}

[ $desktopUrl, $desktopWidth, $desktopHeight ] = $desktopImage;
$desktop2x = $desktopImage2xSrc ? $desktopImage2xSrc[0] : false;
$mobileUrl = $mobileImage ? $mobileImage[0] : false;
$mobile2x  = $mobileImage2xSrc ? $mobileImage2xSrc[0] : false;
?>

<section class="banner-section banner-section-<?= esc_attr( $bannerSize ); ?> mb-24 md:mb-32" data-lazy-root>
	<div class="container banner-container flex justify-center">
		<?php if ( $linkUrl ) : ?>
			<a class="banner-link block w-full <?= $isFull ? 'max-w-[1280px]' : 'max-w-[1144px]'; ?>" href="<?= esc_url( $linkUrl ); ?>" target="<?= esc_attr( $linkTarget ); ?>"<?= '_blank' === $linkTarget ? ' rel="noopener noreferrer"' : ''; ?><?= $linkTitle ? ' aria-label="' . esc_attr( $linkTitle ) . '"' : ''; ?>>
		<?php endif; ?>

		<picture class="banner-picture block w-full <?= $isFull ? 'max-w-[1280px]' : 'max-w-[1144px]'; ?>">
			<?php if ( $mobileUrl ) : ?>
				<source
					media="(max-width: 767px)"
					data-lazy-srcset="<?= esc_url( $mobileUrl ); ?><?= $mobile2x ? ', ' . esc_url( $mobile2x ) . ' 2x' : ''; ?>"
					width="<?= esc_attr( $mobileImage[1] ); ?>"
					height="<?= esc_attr( $mobileImage[2] ); ?>"
				>
			<?php endif; ?>
			<img
				class="block h-auto w-full"
				src="<?= esc_attr( $transparentPixel ); ?>"
				data-lazy-src="<?= esc_url( $desktopUrl ); ?>"
				<?= $desktop2x ? 'data-lazy-srcset="' . esc_url( $desktopUrl ) . ' 1x, ' . esc_url( $desktop2x ) . ' 2x"' : ''; ?>
				alt="<?= esc_attr( $imageAlt ); ?>"
				width="<?= esc_attr( $desktopWidth ); ?>"
				height="<?= esc_attr( $desktopHeight ); ?>"
				decoding="async"
			>
		</picture>

		<?php if ( $linkUrl ) : ?>
			</a>
		<?php endif; ?>
	</div>
</section>
