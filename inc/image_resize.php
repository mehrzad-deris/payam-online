<?php
defined( 'ABSPATH' ) || exit;

/* Image Resize */
add_action( 'after_setup_theme', function () {
	/* Tab Image */
	add_image_size( 'tab_image', 290, 290, true );
	add_image_size( 'tab_image_2x', 580, 580, true ); // x2
} );

/* SVG Support */
add_filter( 'upload_mimes', function ( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';

	return $mimes;
} );
