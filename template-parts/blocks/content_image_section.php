<?php
/**
 * Content and image split section.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor       = (string) ( get_sub_field( 'section_color' ) ?: '' );
$sectionStyle       = (string) ( get_sub_field( 'section_style' ) ?: 'light' );
$sectionImageField  = get_sub_field( 'section_image' );
$sectionImage       = absint( is_array( $sectionImageField ) ? ( $sectionImageField['ID'] ?? 0 ) : $sectionImageField );
$imagePosition      = get_sub_field( 'image_on_right' ) ? 'right' : 'left';
$sectionTitle       = trim( (string) ( get_sub_field( 'section_title' ) ?: '' ) );
$sectionTitleTag    = strtolower( (string) ( get_sub_field( 'title_tag' ) ?: 'h2' ) );
$sectionDescription = (string) ( get_sub_field( 'section_description' ) ?: '' );
$transparentPixel   = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

if ( ! in_array( $sectionStyle, [ 'light', 'dark' ], true ) ) {
	$sectionStyle = 'light';
}

if ( ! in_array( $sectionTitleTag, [ 'h2', 'h3', 'h4', 'h5', 'h6' ], true ) ) {
	$sectionTitleTag = 'h2';
}

$imageUrl    = $sectionImage ? wp_get_attachment_image_url( $sectionImage, 'content_image' ) : false;
$imageUrl2x  = $sectionImage ? wp_get_attachment_image_url( $sectionImage, 'content_image_x2' ) : false;
$imageAlt    = $sectionImage ? (string) get_post_meta( $sectionImage, '_wp_attachment_image_alt', true ) : '';
$hasContent  = '' !== $sectionTitle || '' !== trim( wp_strip_all_tags( $sectionDescription ) );

if ( ! $imageUrl && ! $hasContent ) {
	return;
}
?>

<section
	class="content-image-section content-image-section-<?= esc_attr( $imagePosition ); ?> content-image-section-<?= esc_attr( $sectionStyle ); ?>"
	data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
	data-lazy-root
	<?php if ( '' !== $sectionColor ) : ?>style="background-color: <?= esc_attr( $sectionColor ); ?>"<?php endif; ?>
>
	<div class="container content-image-container">
		<?php if ( $imageUrl ) : ?>
			<figure class="content-image-media">
				<img
					src="<?= esc_attr( $transparentPixel ); ?>"
					data-lazy-src="<?= esc_url( $imageUrl ); ?>"
					<?= $imageUrl2x ? 'data-lazy-srcset="' . esc_url( $imageUrl ) . ' 1x, ' . esc_url( $imageUrl2x ) . ' 2x"' : ''; ?>
					alt="<?= esc_attr( $imageAlt ); ?>"
					width="630"
					height="420"
					decoding="async"
				>
			</figure>
		<?php endif; ?>

		<?php if ( $hasContent ) : ?>
			<div class="content-image-copy">
				<?php if ( '' !== $sectionTitle ) : ?>
					<<?= esc_attr( $sectionTitleTag ); ?> class="content-image-title">
						<?= esc_html( $sectionTitle ); ?>
					</<?= esc_attr( $sectionTitleTag ); ?>>
				<?php endif; ?>

				<?php if ( '' !== trim( wp_strip_all_tags( $sectionDescription ) ) ) : ?>
					<div class="content-image-description">
						<?= wp_kses_post( $sectionDescription ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
