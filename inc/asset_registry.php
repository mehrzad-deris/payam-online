<?php
/**
 * File Name: asset_registry.php
 */

defined( 'ABSPATH' ) || exit;

/**
 * ACF Flexible Content field name.
 */
const PAYAM_PAGE_BUILDER_FIELD = 'page_builder';

/**
 * Returns an asset version based on the file modification time.
 */
function payam_asset_version( string $relative_path ): string {
	$absolute_path = get_theme_file_path( $relative_path );

	if ( file_exists( $absolute_path ) ) {
		return (string) filemtime( $absolute_path );
	}

	return wp_get_theme()->get( 'Version' );
}

/**
 * Checks whether a theme asset exists.
 */
function payam_asset_exists( string $relative_path ): bool {
	return file_exists(
		get_theme_file_path( $relative_path )
	);
}

/**
 * Converts an ACF layout name to an asset slug.
 */
function payam_get_section_slug( string $layout ): string {
	$slug = preg_replace( '/_section$/', '', $layout );

	return sanitize_key(
		str_replace( '_', '-', (string) $slug )
	);
}

/**
 * Section configuration.
 *
 * Section CSS and JS are detected automatically using this convention:
 *
 * assets/styles/scss/sections/{section-slug}.css
 * assets/js/sections/{section-slug}.js
 *
 * Only additional shared bundles need to be declared here.
 */
function payam_get_section_config(): array {
	return [
		'hero_section' => [],

		'services_section' => [
			'scripts' => [
				'payam-bundle-cards-slider',
			],
		],
	];
}

/**
 * Builds the final section asset registry automatically.
 */
function payam_get_section_assets(): array {
	$registry = [];

	foreach ( payam_get_section_config() as $layout => $config ) {
		$slug = payam_get_section_slug( $layout );

		$style_path = sprintf(
			'/assets/styles/scss/sections/%s.css',
			$slug
		);

		$script_path = sprintf(
			'/assets/js/sections/%s.js',
			$slug
		);

		$styles  = $config['styles'] ?? [];
		$scripts = $config['scripts'] ?? [];

		if ( payam_asset_exists( $style_path ) ) {
			$styles[] = 'payam-section-' . $slug;
		}

		if ( payam_asset_exists( $script_path ) ) {
			$scripts[] = 'payam-section-' . $slug;
		}

		$registry[ $layout ] = [
			'styles'  => array_values( array_unique( $styles ) ),
			'scripts' => array_values( array_unique( $scripts ) ),
		];
	}

	return $registry;
}

/**
 * Registers a theme stylesheet when its file exists.
 */
function payam_register_theme_style(
	string $handle,
	string $relative_path,
	array $dependencies = []
): bool {
	if ( ! payam_asset_exists( $relative_path ) ) {
		return false;
	}

	return wp_register_style(
		$handle,
		get_theme_file_uri( $relative_path ),
		$dependencies,
		payam_asset_version( $relative_path )
	);
}

/**
 * Registers a theme script when its file exists.
 */
function payam_register_theme_script(
	string $handle,
	string $relative_path,
	array $dependencies = []
): bool {
	if ( ! payam_asset_exists( $relative_path ) ) {
		return false;
	}

	return wp_register_script(
		$handle,
		get_theme_file_uri( $relative_path ),
		$dependencies,
		payam_asset_version( $relative_path ),
		[
			'strategy'  => 'defer',
			'in_footer' => true,
		]
	);
}

/**
 * Registers global, vendor, bundle and section assets.
 */
function payam_register_assets(): void {

	/*
	|--------------------------------------------------------------------------
	| Global assets
	|--------------------------------------------------------------------------
	*/

	payam_register_theme_style(
		'payam-app',
		'/assets/styles/theme.min.css'
	);

	payam_register_theme_script(
		'payam-app',
		'/assets/js/app.js'
	);

	/*
	|--------------------------------------------------------------------------
	| Swiper vendor
	|--------------------------------------------------------------------------
	*/

	payam_register_theme_style(
		'payam-vendor-swiper',
		'/assets/scripts/swiper/swiper-bundle.min.css'
	);

	payam_register_theme_script(
		'payam-vendor-swiper',
		'/assets/scripts/swiper/swiper-bundle.min.js'
	);

	/*
	|--------------------------------------------------------------------------
	| Cards slider bundle
	|--------------------------------------------------------------------------
	*/

	payam_register_theme_script(
		'payam-bundle-cards-slider',
		'/assets/js/bundles/cards-slider.js',
		[
			'payam-app',
			'payam-vendor-swiper',
		]
	);

	/*
	|--------------------------------------------------------------------------
	| Section assets
	|--------------------------------------------------------------------------
	*/

	foreach ( array_keys( payam_get_section_config() ) as $layout ) {
		$slug = payam_get_section_slug( $layout );

		$style_path = sprintf(
			'/assets/styles/scss/sections/%s.css',
			$slug
		);

		$script_path = sprintf(
			'/assets/js/sections/%s.js',
			$slug
		);

		payam_register_theme_style(
			'payam-section-' . $slug,
			$style_path,
			[
				'payam-app',
			]
		);

		payam_register_theme_script(
			'payam-section-' . $slug,
			$script_path,
			[
				'payam-app',
			]
		);
	}
}

add_action( 'wp_enqueue_scripts', 'payam_register_assets', 5 );

/**
 * Returns the layouts used in the current page builder.
 */
function payam_get_current_page_layouts(): array {
	if (
		! is_singular() ||
		! function_exists( 'get_field' )
	) {
		return [];
	}

	$post_id = get_queried_object_id();

	if ( ! $post_id ) {
		return [];
	}

	$sections = get_field(
		PAYAM_PAGE_BUILDER_FIELD,
		$post_id
	);

	if ( ! is_array( $sections ) ) {
		return [];
	}

	$layouts = [];

	foreach ( $sections as $section ) {
		$layout = $section['acf_fc_layout'] ?? '';

		if ( '' !== $layout ) {
			$layouts[] = $layout;
		}
	}

	return array_values( array_unique( $layouts ) );
}

/**
 * Enqueues global and section-specific assets.
 */
function payam_enqueue_assets(): void {
	wp_enqueue_style( 'payam-app' );
	wp_enqueue_script( 'payam-app' );

	$layouts = payam_get_current_page_layouts();

	if ( empty( $layouts ) ) {
		return;
	}

	$registry = payam_get_section_assets();

	$style_handles  = [];
	$script_handles = [];

	foreach ( $layouts as $layout ) {
		if ( ! isset( $registry[ $layout ] ) ) {
			continue;
		}

		$style_handles = array_merge(
			$style_handles,
			$registry[ $layout ]['styles'] ?? []
		);

		$script_handles = array_merge(
			$script_handles,
			$registry[ $layout ]['scripts'] ?? []
		);
	}

	foreach ( array_unique( $style_handles ) as $handle ) {
		if ( wp_style_is( $handle, 'registered' ) ) {
			wp_enqueue_style( $handle );
		}
	}

	foreach ( array_unique( $script_handles ) as $handle ) {
		if ( wp_script_is( $handle, 'registered' ) ) {
			wp_enqueue_script( $handle );
		}
	}
}

add_action( 'wp_enqueue_scripts', 'payam_enqueue_assets', 20 );