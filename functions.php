<?php

/* Theme Setup */
function theme_setup() {
	register_nav_menus( [
		'main_menu' => 'فهرست سربرگ',
	] );
}

add_action( 'after_setup_theme', 'theme_setup' );

require_once get_theme_file_path('/inc/asset_registry.php');
require_once get_theme_file_path('/inc/image_resize.php');

/* Remove Gutenberg */
add_filter( 'use_block_editor_for_post', '__return_false' );
add_action( 'wp_enqueue_scripts', function () {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
}, 100 );


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