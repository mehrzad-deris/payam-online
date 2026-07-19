<?php

/* Theme Setup */
function theme_setup() {
	register_nav_menus( [
		'main_menu' => 'فهرست سربرگ',
	] );
}

add_action( 'after_setup_theme', 'theme_setup' );

/* Remove Gutenberg */
add_filter( 'use_block_editor_for_post', '__return_false' );
add_action( 'wp_enqueue_scripts', function () {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
}, 100 );

/* Enqueue Styles */
function theme_styles() {
	wp_enqueue_style(
		'smo-theme',
		get_template_directory_uri() . '/assets/styles/theme.min.css',
		[],
		filemtime( get_template_directory() . '/assets/styles/theme.min.css' )
	);
	wp_enqueue_style(
		'swiper',
		get_template_directory_uri() . '/assets/scripts/swiper/swiper-bundle.min.css',
		[],
		filemtime( get_template_directory() . '/assets/scripts/swiper/swiper-bundle.min.css' )
	);
}

add_action( 'wp_enqueue_scripts', 'theme_styles' );

/* Enqueue Scripts */
function theme_scripts() {
	wp_enqueue_script(
		'swiper',
		get_template_directory_uri() . '/assets/scripts/swiper/swiper-bundle.min.js',
		[],
		null,
		true
	);

	wp_enqueue_script(
		'app',
//		get_template_directory_uri() . '/assets/js/app.min.js',
		get_template_directory_uri() . '/assets/js/app.js',
		[ 'swiper' ], // dependency
		null,
		true
	);

	wp_enqueue_script(
		'landing',
//		get_template_directory_uri() . '/assets/js/landing-page.min.js',
		get_template_directory_uri() . '/assets/js/landing-page.js',
		[ 'app' ], // dependency
		null,
		true
	);
}

add_action( 'wp_enqueue_scripts', 'theme_scripts' );

/* Components */
require_once get_template_directory() . '/inc/components.php';

/* Icon Component */
function icon( $name, $class = '' ) {
	$sprite = get_template_directory_uri() . '/assets/images/icons/icon-pack.svg';

	return '<svg class="' . $class . '">
                <use href="' . $sprite . '#' . $name . '"></use>
            </svg>';
}

/*
 * Flexible Content
 * */
function theme_render_block( $layout ) {
	$path = 'template-parts/blocks/' . $layout;

	if ( locate_template( $path . '.php' ) ) {
		get_template_part( $path );
	} else {
		echo "<!-- Block not found: {$layout} -->";
	}
}

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

/* Remove WP Version */
add_filter( 'the_generator', '__return_empty_string' );
function remove_version_from_assets( $src ) {
	$wp_version = get_bloginfo( 'version' );
	if ( strpos( $src, 'ver=' . $wp_version ) !== false ) {
		$src = remove_query_arg( 'ver', $src );
	}

	return $src;
}

add_filter( 'style_loader_src', 'remove_version_from_assets', 9999 );
add_filter( 'script_loader_src', 'remove_version_from_assets', 9999 );

/* Autoload */
$payamava_autoload = get_template_directory() . '/vendor/autoload.php';

if ( file_exists( $payamava_autoload ) ) {
	require_once $payamava_autoload;
}

/* Persian Date
 * $post_datetime = get_post_datetime();
 * echo esc_html(payamava_jalali_date('Y/m/d', $post_datetime);
 * */
function payamava_jalali_date(
	string $format = 'Y/m/d',
	$datetime = null
): string {

	if ( ! class_exists( '\Morilog\Jalali\Jalalian' ) ) {
		return '';
	}

	try {
		if ( null === $datetime ) {
			$datetime = current_datetime();
		}

		if ( is_string( $datetime ) ) {
			$datetime = new DateTimeImmutable(
				$datetime,
				wp_timezone()
			);
		}

		if ( ! $datetime instanceof DateTimeInterface ) {
			return '';
		}

		return \Morilog\Jalali\Jalalian::fromDateTime( $datetime )
		                               ->format( $format );

	} catch ( Throwable $exception ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log(
				'Jalali date conversion failed: ' .
				$exception->getMessage()
			);
		}

		return '';
	}
}