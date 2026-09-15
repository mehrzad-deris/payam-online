<?php

/* Theme Setup */
function theme_setup() {
	add_theme_support( 'post-thumbnails' );

	register_nav_menus( [
		'main_menu' => 'فهرست سربرگ',
		'article_useful_links' => 'مقاله — لینک‌های مفید',
	] );
}

add_action( 'after_setup_theme', 'theme_setup' );

add_action( 'after_setup_theme', function () {
	register_nav_menus( [
		'footer_company'     => 'فوتر — پیام آنلاین',
		'footer_services'    => 'فوتر — خدمات',
		'footer_solutions'   => 'فوتر — راهکارها',
		'footer_quick_links' => 'فوتر — دسترسی‌های سریع',
	] );
} );

require_once get_theme_file_path('/inc/asset_registry.php');
require_once get_theme_file_path('/inc/security.php');
require_once get_theme_file_path('/inc/image_resize.php');
require_once get_theme_file_path('/inc/editor_shortcodes.php');
require_once get_theme_file_path('/inc/whmcs_products.php');
require_once get_theme_file_path('/inc/blog_archive.php');
require_once get_theme_file_path('/inc/single_post.php');
require_once get_theme_file_path('/inc/contact_forms.php');
require_once get_theme_file_path('/inc/faqs.php');

/* Remove Gutenberg */
add_filter( 'use_block_editor_for_post', '__return_false' );
add_action( 'wp_enqueue_scripts', function () {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
}, 100 );


/* Icon Component */
function icon( $name, $class = '' ) {
	$sprite_path = '/assets/images/icons/icon-pack.svg';
	$sprite      = add_query_arg(
		'ver',
		payam_asset_version( $sprite_path ),
		get_theme_file_uri( $sprite_path )
	);
	$icon_name   = sanitize_key( $name );

	return '<svg class="' . esc_attr( $class ) . '">
                <use href="' . esc_url( $sprite ) . '#' . esc_attr( $icon_name ) . '"></use>
            </svg>';
}

/*
 * Flexible Content
 * */
function theme_render_block( $layout ) {
	$layout = sanitize_key( (string) $layout );
	$layout_aliases = [
		'hero_section'         => 'domain_check_section',
		'hero_section_on_page' => 'server_card_section',
	];
	$template_layout = $layout_aliases[ $layout ] ?? $layout;
	$allowed_layouts = array_keys( payam_get_section_config() );

	if ( ! in_array( $layout, $allowed_layouts, true ) && ! array_key_exists( $layout, $layout_aliases ) ) {
		return;
	}

	if ( ! in_array( $template_layout, $allowed_layouts, true ) ) {
		return;
	}
	$path            = 'template-parts/blocks/' . $template_layout;

	if ( locate_template( $path . '.php' ) ) {
		get_template_part( $path );
	}
}

/**
 * Renders the reusable section heading component.
 *
 * @param array $args Component data and optional presentation classes.
 */
function section_heading( array $args = [] ): void {
	get_template_part(
		'template-parts/components/section_heading',
		null,
		$args
	);
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
