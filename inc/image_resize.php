<?php
defined( 'ABSPATH' ) || exit;

/* Image Resize */
add_action( 'after_setup_theme', function () {
	/* Tab Image */
	add_image_size( 'tab_image', 290, 290, true );
	add_image_size( 'tab_image_2x', 580, 580, true ); // x2

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
} );

/* SVG Support */
add_filter( 'upload_mimes', function ( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
} );
