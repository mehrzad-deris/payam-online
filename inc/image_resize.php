<?php
defined( 'ABSPATH' ) || exit;

/* Image Resize */
add_action( 'after_setup_theme', function () {
	/* Tab Image */
	add_image_size( 'tab_image', 290, 290, true );
	add_image_size( 'tab_image_2x', 580, 580, true ); // x2
	add_image_size( 'tab_image_style_2', 561, 0, false );
	add_image_size( 'tab_image_style_2_2x', 1122, 0, false ); // x2

	/* About Us Section */
	add_image_size( 'about_image_section', 1280, 400, true );
	add_image_size( 'about_image_section_2x', 2560, 800, true ); // x2
	add_image_size( 'about_image_section_mobile', 358, 358, true ); // mobile
	add_image_size( 'about_image_section_mobile_2x', 716, 716, true ); // mobile x2

	/* Brand Section Section */
	add_image_size( 'brand_logo_center', 64, 64 );
	add_image_size( 'brand_logo_center_x2', 128, 128 ); // x2
	add_image_size( 'brand_section_logo_center', '', 36, true );
	add_image_size( 'brand_section_logo_center_x2', '', 72, true ); // x2

	/* Testimonials Section */
	add_image_size( 'testimonial_avatar', 56, 56, true );
	add_image_size( 'testimonial_avatar_x2', 112, 112, true ); // x2

	/* Blog Section */
	add_image_size( 'blog_card', 389, 218, true );
	add_image_size( 'blog_card_x2', 778, 436, true ); // x2

	/* Banner Section */
	add_image_size( 'banner_desktop', 1144, 591, true );
	add_image_size( 'banner_desktop_x2', 2288, 1182, true ); // x2
	add_image_size( 'banner_mobile', 358, 585, true );
	add_image_size( 'banner_mobile_x2', 716, 1170, true ); // x2
	add_image_size( 'banner_full', 1280, 0, false );
	add_image_size( 'banner_full_x2', 2560, 0, false ); // x2
	add_image_size( 'banner_full_mobile', 358, 0, false );
	add_image_size( 'banner_full_mobile_x2', 716, 0, false ); // mobile x2

	/* Content Image Section */
	add_image_size( 'content_image', 630, 420, true );
	add_image_size( 'content_image_x2', 1260, 840, true ); // x2

	/* OS Logo Image */
	add_image_size( 'os_logo_image', 96, 0, false);
	add_image_size( 'os_logo_image_2', 192, 0, false);
});

/**
 * Check whether an uploaded file is a valid SVG document.
 *
 * SVG is an XML document, so WordPress cannot validate it with
 * wp_get_image_mime() like raster images.
 */
function payam_is_valid_svg( string $file ): bool {
	if ( ! is_readable( $file ) || filesize( $file ) > wp_max_upload_size() ) {
		return false;
	}

	$contents = file_get_contents( $file );

	if ( false === $contents || '' === trim( $contents ) || false !== stripos( $contents, '<!DOCTYPE' ) ) {
		return false;
	}

	if ( ! class_exists( 'DOMDocument' ) ) {
		return (bool) preg_match( '/<svg(?:\s|>)/i', $contents );
	}

	$previousErrors = libxml_use_internal_errors( true );
	$document       = new DOMDocument();
	$isLoaded       = $document->loadXML( $contents, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );
	libxml_clear_errors();
	libxml_use_internal_errors( $previousErrors );

	if ( ! $isLoaded || ! $document->documentElement || 'svg' !== strtolower( $document->documentElement->localName ) ) {
		return false;
	}

	$blockedElements = [ 'script', 'foreignObject', 'iframe', 'object', 'embed' ];

	foreach ( $blockedElements as $elementName ) {
		if ( $document->getElementsByTagName( $elementName )->length ) {
			return false;
		}
	}

	foreach ( $document->getElementsByTagName( '*' ) as $element ) {
		foreach ( $element->attributes ?? [] as $attribute ) {
			$name  = strtolower( $attribute->nodeName );
			$value = trim( $attribute->nodeValue );

			if ( str_starts_with( $name, 'on' ) ) {
				return false;
			}

			if ( in_array( $name, [ 'href', 'xlink:href', 'src' ], true ) && preg_match( '/^(?:javascript:|data:|https?:|\/\/)/i', $value ) ) {
				return false;
			}
		}
	}

	return true;
}

/** Allow trusted administrators to upload SVG icons. */
add_filter( 'upload_mimes', function ( array $mimes ): array {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}

	return $mimes;
} );

/**
 * Supply the SVG type after validating its XML contents.
 *
 * fileinfo commonly reports SVG files as text/plain or image/svg, causing
 * WordPress to clear the extension and MIME returned for the upload.
 */
add_filter( 'wp_check_filetype_and_ext', function ( array $data, string $file, string $filename ): array {
	if (
		current_user_can( 'manage_options' )
		&& 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) )
		&& payam_is_valid_svg( $file )
	) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}, 10, 3 );

/**
 * ACF 6.8.7+ checks uploads with wp_get_image_mime(), which does not support
 * SVG. Remove only that image-validation error after our SVG validation passes.
 */
add_filter( 'acf/validate_is_image_attachment', function ( array $errors, array $file, array $attachment, array $field, string $context ): array {
	if ( 'upload' !== $context || ! current_user_can( 'manage_options' ) ) {
		return $errors;
	}

	$filename = (string) ( $attachment['name'] ?? $file['name'] ?? '' );
	$tempFile = (string) ( $attachment['tmp_name'] ?? '' );

	if (
		isset( $errors['invalid_image'] )
		&& 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) )
		&& payam_is_valid_svg( $tempFile )
	) {
		unset( $errors['invalid_image'] );
	}

	return $errors;
}, 10, 5 );
