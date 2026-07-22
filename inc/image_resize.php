<?php
defined( 'ABSPATH' ) || exit;

/* Image Resize */
add_action( 'after_setup_theme', function () {
	/* Hero Section */
	add_image_size( 'hero_section', 385, 441, true ); // Hero
	add_image_size( 'hero_section_2x', 770, 882, true ); // Hero x2
} );

/* SVG Support */
add_filter( 'upload_mimes', function ( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';

	return $mimes;
} );
