<?php
/**
 * Responsive linked banner section.
 */

defined( 'ABSPATH' ) || exit;

$bannerImage       = absint( get_sub_field( 'banner_image' ) );
$bannerMobileImage = absint( get_sub_field( 'banner_mobile_image' ) );
$bannerLink        = get_sub_field( 'cta_link' ) ?: get_sub_field( 'banner_link' );
$transparentPixel  = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

if ( ! $bannerImage ) {
	return;
}

$desktopUrl = wp_get_attachment_image_url( $bannerImage, 'banner_desktop' );
$desktop2x  = wp_get_attachment_image_url( $bannerImage, 'banner_desktop_x2' );
$mobileUrl  = $bannerMobileImage ? wp_get_attachment_image_url( $bannerMobileImage, 'banner_mobile' ) : false;
$mobile2x   = $bannerMobileImage ? wp_get_attachment_image_url( $bannerMobileImage, 'banner_mobile_x2' ) : false;
$imageAlt   = (string) get_post_meta( $bannerImage, '_wp_attachment_image_alt', true );
$linkUrl    = is_array( $bannerLink ) ? ( $bannerLink['url'] ?? '' ) : $bannerLink;
$linkTarget = is_array( $bannerLink ) ? ( $bannerLink['target'] ?? '_self' ) : '_self';
$linkTitle  = is_array( $bannerLink ) ? ( $bannerLink['title'] ?? '' ) : '';

if ( ! $desktopUrl ) {
	return;
}
?>

<section class="banner-section mb-24 md:mb-32" data-lazy-root>
	<div class="container banner-container flex justify-center">
		<?php if ( $linkUrl ) : ?>
			<a class="banner-link" href="<?= esc_url( $linkUrl ); ?>" target="<?= esc_attr( $linkTarget ); ?>"<?= '_blank' === $linkTarget ? ' rel="noopener noreferrer"' : ''; ?><?= $linkTitle ? ' aria-label="' . esc_attr( $linkTitle ) . '"' : ''; ?>>
		<?php endif; ?>

		<picture class="banner-picture">
			<?php if ( $mobileUrl ) : ?>
				<source
					media="(max-width: 767px)"
					data-lazy-srcset="<?= esc_url( $mobileUrl ); ?><?= $mobile2x ? ', ' . esc_url( $mobile2x ) . ' 2x' : ''; ?>"
					width="358"
					height="585"
				>
			<?php endif; ?>
			<img
				src="<?= esc_attr( $transparentPixel ); ?>"
				data-lazy-src="<?= esc_url( $desktopUrl ); ?>"
				<?= $desktop2x ? 'data-lazy-srcset="' . esc_url( $desktopUrl ) . ' 1x, ' . esc_url( $desktop2x ) . ' 2x"' : ''; ?>
				alt="<?= esc_attr( $imageAlt ); ?>"
				width="1144"
				height="591"
				decoding="async"
			>
		</picture>

		<?php if ( $linkUrl ) : ?>
			</a>
		<?php endif; ?>
	</div>
</section>
