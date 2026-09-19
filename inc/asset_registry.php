<?php
/**
 * File Name: asset_registry.php
 */

defined( 'ABSPATH' ) || exit;

/**
 * ACF Flexible Content field name.
 */
const PAYAM_PAGE_BUILDER_FIELD = 'page_builder';

/** Resolve only this clone's namespace; never fall back to shared Options data. */
function payam_options_builder_source( string $name ): array {
	$field = function_exists( 'get_field_object' ) ? get_field_object( $name, 'option', false, false ) : false;
	$group = is_array( $field ) && 'clone' === ( $field['type'] ?? '' ) && 'group' === ( $field['display'] ?? '' );
	return [ 'field' => $group ? $name : $name . '_page_builder', 'post_id' => 'option', 'child' => $group ? 'page_builder' : '' ];
}

function payam_builder_sections( array $source ): array {
	if ( ! function_exists( 'get_field' ) ) { return []; }
	$rows = get_field( $source['field'], $source['post_id'] );
	if ( ! empty( $source['child'] ) ) { $rows = is_array( $rows ) ? ( $rows[ $source['child'] ] ?? [] ) : []; }
	return is_array( $rows ) ? $rows : [];
}

function payam_render_builder( array $source, string $wrapper_class = '' ): void {
	if ( ! function_exists( 'have_rows' ) ) { return; }
	$render = static function () use ( $wrapper_class ): void {
		if ( $wrapper_class ) { echo '<div class="' . esc_attr( $wrapper_class ) . '">'; }
		theme_render_block( get_row_layout() );
		if ( $wrapper_class ) { echo '</div>'; }
	};
	while ( have_rows( $source['field'], $source['post_id'] ) ) {
		the_row();
		if ( ! empty( $source['child'] ) ) {
			while ( have_rows( $source['child'] ) ) {
				the_row();
				$render();
			}
		} else { $render(); }
	}
}

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
	return file_exists( get_theme_file_path( $relative_path ) );
}

/** Select readable CSS in development and minified CSS in production. */
function payam_css_asset_path( string $relative_path ): string {
	static $use_minified = null;

	if ( null === $use_minified ) {
		$environment  = function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production';
		$is_debug     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
		$use_minified = ! $is_debug && ! in_array( $environment, [ 'local', 'development' ], true );
	}

	if ( ! $use_minified || ! str_ends_with( $relative_path, '.css' ) ) {
		return $relative_path;
	}

	$minified = substr( $relative_path, 0, -4 ) . '.min.css';

	return payam_asset_exists( $minified ) ? $minified : $relative_path;
}

/**
 * Converts an ACF layout name to an asset slug.
 */
function payam_get_section_slug( string $layout ): string {
	$slug = preg_replace( '/_section$/', '', $layout );

	return sanitize_key( str_replace( '_', '-', (string) $slug ) );
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
		// Legacy layout name: keep existing page-builder rows and load the renamed section assets.
		'hero_section' => [
			'styles'  => [
				'payam-section-domain-check',
			],
			'scripts' => [
				'payam-section-domain-check',
			],
		],

		'domain_check_section' => [],

		'section_heading_section' => [],

		'compare_section' => [],
		'contact_section' => [],

		'hero_section_on_page' => [
			'styles'  => [
				'payam-bundle-product-cards',
			],
			'scripts' => [
				'payam-bundle-product-cards',
				'payam-bundle-tabs',
			],
		],

		'banner_section' => [],

		'seo_box_section' => [],
		'domain_products_section' => [],

		'os_logo_section' => [
			'styles'  => [
				'payam-vendor-swiper',
				'payam-bundle-sliders',
			],
			'scripts' => [
				'payam-vendor-swiper',
				'payam-bundle-sliders',
			],
		],

		'server_card_section' => [
			'styles'  => [
				'payam-bundle-product-cards',
			],
			'scripts' => [
				'payam-bundle-product-cards',
				'payam-bundle-tabs',
			],
		],

		'ssl_products_section' => [
			'styles'  => [
				'payam-bundle-product-cards',
			],
			'scripts' => [
				'payam-bundle-tabs',
			],
		],

		'faq_section' => [
			'styles' => [
				'payam-bundle-faq',
			],
			'scripts' => [
				'payam-bundle-faq',
			],
		],

		'feature_section' => [
			'styles' => [
				'payam-section-feature',
			],
		],

		'feature_section_card_style' => [
			'styles' => [
				'payam-section-feature',
			],
		],

		'statistics_section' => [
			'styles'  => [
				'payam-section-feature',
			],
			'scripts' => [
				'payam-bundle-feature',
			],
		],

		'content_image_section' => [
			'styles' => [
				'payam-section-feature',
			],
		],

		'brands_section' => [
			'styles'  => [
				'payam-section-feature',
			],
			'scripts' => [
				'payam-bundle-feature',
			],
		],

		'testimonials_section' => [
			'styles'  => [
				'payam-vendor-swiper',
				'payam-bundle-sliders',
			],
			'scripts' => [
				'payam-vendor-swiper',
				'payam-bundle-sliders',
			],
		],

		'services_section' => [
			'styles'  => [
				'payam-bundle-services-tabs',
			],
			'scripts' => [
				'payam-bundle-tabs',
			],
		],

		'services_card_section' => [
			'styles' => [
				'payam-bundle-service-cards',
			],
		],

		'blog_section' => [
			'styles'  => [
				'payam-vendor-swiper',
				'payam-bundle-content-cards',
				'payam-bundle-sliders',
			],
			'scripts' => [
				'payam-vendor-swiper',
				'payam-bundle-sliders',
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

		$style_path = sprintf( '/assets/styles/scss/sections/%s.css', $slug );

		$script_path = sprintf( '/assets/js/sections/%s.min.js', $slug );

		$styles  = $config['styles'] ?? [];
		$scripts = $config['scripts'] ?? [];

		if ( payam_asset_exists( payam_css_asset_path( $style_path ) ) ) {
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
	string $handle, string $relative_path, array $dependencies = []
): bool {
	$relative_path = payam_css_asset_path( $relative_path );

	if ( ! payam_asset_exists( $relative_path ) ) {
		return false;
	}

	return wp_register_style( $handle, get_theme_file_uri( $relative_path ), $dependencies, payam_asset_version( $relative_path ) );
}

/**
 * Registers a theme script when its file exists.
 */
function payam_register_theme_script(
	string $handle, string $relative_path, array $dependencies = []
): bool {
	if ( ! payam_asset_exists( $relative_path ) ) {
		return false;
	}

	return wp_register_script( $handle, get_theme_file_uri( $relative_path ), $dependencies, payam_asset_version( $relative_path ), [
			'strategy'  => 'defer',
			'in_footer' => true,
		] );
}

/**
 * Marks an asset for DS Cache without coupling the theme to the plugin.
 */
function payam_set_asset_cache_role( string $handle, string $type, string $role, bool $safe_to_merge = false ): void {
	if ( 'style' === $type ) {
		wp_style_add_data( $handle, 'ds_asset_group', $role );
		return;
	}

	wp_script_add_data( $handle, 'ds_asset_group', $role );
	if ( $safe_to_merge ) {
		wp_script_add_data( $handle, 'ds_asset_safe', true );
	}
}

/**
 * The global theme stylesheet defines the initial geometry of every page and
 * must never be converted to preload/onload by DS Cache. An async app shell
 * produces a full-page FOUC and a severe cumulative layout shift.
 */
add_filter( 'option_ds_cache_asset_optimizer', static function ( $options ) {
	if ( ! is_array( $options ) || empty( $options['async_styles'] ) ) {
		return $options;
	}

	$normalize = static function ( string $value ): string {
		$value = strtolower( basename( trim( $value ) ) );
		$value = preg_replace( '/\.css$/', '', $value );

		return (string) preg_replace( '/\.min$/', '', (string) $value );
	};
	$protected = [ 'payam-app', 'theme' ];

	global $wp_styles;
	if ( $wp_styles instanceof WP_Styles ) {
		foreach ( $wp_styles->registered as $handle => $asset ) {
			$role = (string) ( $asset->extra['ds_asset_group'] ?? '' );
			if ( ! in_array( $role, [ 'global', 'page' ], true ) ) {
				continue;
			}

			$protected[] = $normalize( (string) $handle );
			$protected[] = $normalize( (string) wp_parse_url( (string) $asset->src, PHP_URL_PATH ) );
		}
	}
	$protected = array_values( array_unique( array_filter( $protected ) ) );

	$rules = preg_split( '/[\s,]+/', (string) $options['async_styles'], -1, PREG_SPLIT_NO_EMPTY ) ?: [];
	$rules = array_values( array_filter( $rules, static function ( string $rule ) use ( $normalize, $protected ): bool {
		return ! in_array( $normalize( $rule ), $protected, true );
	} ) );
	$options['async_styles'] = implode( "\n", $rules );

	return $options;
} );

/** Prioritize body text and the above-the-fold heading; other weights load on demand. */
add_action( 'wp_head', static function (): void {
	foreach ( [
		'/assets/fonts/peyda/PeydaWebFaNum-Regular.woff2',
		'/assets/fonts/peyda/PeydaWebFaNum-Bold.woff2',
	] as $font_path ) {
		if ( ! payam_asset_exists( $font_path ) ) {
			continue;
		}
		?>
		<link rel="preload" href="<?= esc_url( get_theme_file_uri( $font_path ) ); ?>" as="font" type="font/woff2" crossorigin>
		<?php
	}
}, 2 );

/**
 * Swiper instances initialize near the viewport, so its full vendor stylesheet
 * can load non-blocking. The small geometry-critical subset lives in input.css.
 */
add_filter( 'style_loader_tag', static function ( string $html, string $handle ): string {
	if ( 'payam-vendor-swiper' !== $handle || is_admin() ) {
		return $html;
	}

	$preload = preg_replace_callback(
		'/\srel=(["\'])stylesheet\1/i',
		static function ( array $match ): string {
			$quote = $match[1];
			return ' rel=' . $quote . 'preload' . $quote
				. ' as=' . $quote . 'style' . $quote
				. ' onload="this.onload=null;this.rel=\'stylesheet\'"';
		},
		$html,
		1
	);

	return is_string( $preload ) && $preload !== $html
		? $preload . '<noscript>' . $html . '</noscript>'
		: $html;
}, 15, 2 );

/**
 * Registers global, vendor, bundle and section assets.
 */
function payam_register_assets(): void {

	/*
	|--------------------------------------------------------------------------
	| Global assets
	|--------------------------------------------------------------------------
	*/

	payam_register_theme_style( 'payam-app', '/assets/styles/theme.min.css' );

	payam_register_theme_script( 'payam-app', '/assets/js/app.min.js' );

	/*
	|--------------------------------------------------------------------------
	| Swiper vendor
	|--------------------------------------------------------------------------
	*/

	payam_register_theme_style( 'payam-vendor-swiper', '/assets/scripts/swiper/swiper-bundle.min.css' );

	payam_register_theme_script( 'payam-vendor-swiper', '/assets/scripts/swiper/swiper-bundle.min.js' );

	/*
	|--------------------------------------------------------------------------
	| Cards bundle
	|--------------------------------------------------------------------------
	*/

	payam_register_theme_style( 'payam-bundle-cards', '/assets/styles/scss/bundles/cards.css' );
	payam_register_theme_style( 'payam-bundle-product-cards', '/assets/styles/scss/bundles/product-cards.css' );
	payam_register_theme_style( 'payam-bundle-service-cards', '/assets/styles/scss/bundles/service-cards.css' );
	payam_register_theme_style( 'payam-bundle-services-tabs', '/assets/styles/scss/bundles/services-tabs.css' );
	payam_register_theme_style( 'payam-bundle-content-cards', '/assets/styles/scss/bundles/content-cards.css' );
	payam_register_theme_style( 'payam-bundle-sliders', '/assets/styles/scss/bundles/sliders.css' );
	payam_register_theme_style( 'payam-bundle-faq', '/assets/styles/scss/bundles/faq.css' );

	payam_register_theme_script( 'payam-bundle-cards', '/assets/js/bundles/cards.min.js', [
			'payam-app',
		] );

	payam_register_theme_script( 'payam-bundle-product-cards', '/assets/js/bundles/product-cards.min.js', [ 'payam-app' ] );
	payam_register_theme_script( 'payam-bundle-tabs', '/assets/js/bundles/tabs.min.js', [ 'payam-app' ] );
	payam_register_theme_script( 'payam-bundle-faq', '/assets/js/bundles/faq.min.js', [ 'payam-app' ] );
	payam_register_theme_script( 'payam-bundle-sliders', '/assets/js/bundles/sliders.min.js', [ 'payam-app', 'payam-vendor-swiper' ] );

	payam_register_theme_script( 'payam-bundle-feature', '/assets/js/bundles/feature.min.js', [
			'payam-app',
		] );

	/*
	|--------------------------------------------------------------------------
	| Section assets
	|--------------------------------------------------------------------------
	*/

	foreach ( array_keys( payam_get_section_config() ) as $layout ) {
		$slug = payam_get_section_slug( $layout );

		$style_path = payam_css_asset_path( sprintf( '/assets/styles/scss/sections/%s.css', $slug ) );

		$script_path = sprintf( '/assets/js/sections/%s.min.js', $slug );

		payam_register_theme_style( 'payam-section-' . $slug, $style_path, [
				'payam-app',
			] );

		payam_register_theme_script( 'payam-section-' . $slug, $script_path, [
				'payam-app',
			] );

		payam_set_asset_cache_role( 'payam-section-' . $slug, 'style', 'page' );
		payam_set_asset_cache_role( 'payam-section-' . $slug, 'script', 'page', true );
	}

	payam_set_asset_cache_role( 'payam-app', 'style', 'global' );
	payam_set_asset_cache_role( 'payam-app', 'script', 'global' );
	payam_set_asset_cache_role( 'payam-vendor-swiper', 'style', 'vendor' );
	payam_set_asset_cache_role( 'payam-vendor-swiper', 'script', 'vendor' );

	$bundle_styles = [ 'cards', 'product-cards', 'service-cards', 'services-tabs', 'content-cards', 'sliders', 'faq', 'feature' ];
	foreach ( $bundle_styles as $bundle ) {
		payam_set_asset_cache_role( 'payam-bundle-' . $bundle, 'style', 'page' );
	}
	$bundle_scripts = [ 'cards', 'product-cards', 'tabs', 'faq', 'sliders', 'feature' ];
	foreach ( $bundle_scripts as $bundle ) {
		payam_set_asset_cache_role( 'payam-bundle-' . $bundle, 'script', 'page', true );
	}
}

add_action( 'wp_enqueue_scripts', 'payam_register_assets', 5 );

/**
 * Returns the layouts used in the current page builder.
 */
function payam_get_current_page_layouts(): array {
	if ( ( ! is_singular() && ! is_404() ) || ! function_exists( 'get_field' ) ) {
		return [];
	}

	$post_id = get_queried_object_id();

	if ( ! $post_id && ! is_404() ) {
		return [];
	}

	if ( is_404() ) {
		$sections = payam_builder_sections( payam_options_builder_source( 'error_404_builder' ) );
	} elseif ( is_singular( 'post' ) ) {
		$source = payam_article_builder_source( $post_id );
		$sections = payam_builder_sections( $source );
	} else {
		$sections = get_field( PAYAM_PAGE_BUILDER_FIELD, $post_id );
	}

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

		$style_handles = array_merge( $style_handles, $registry[ $layout ]['styles'] ?? [] );

		$script_handles = array_merge( $script_handles, $registry[ $layout ]['scripts'] ?? [] );
	}

	$style_handles  = array_values( array_unique( $style_handles ) );
	$script_handles = array_values( array_unique( $script_handles ) );

	// Vendor assets must precede bundles even when another section requested the bundle first.
	if ( in_array( 'payam-vendor-swiper', $style_handles, true ) ) {
		$style_handles = array_values( array_diff( $style_handles, [ 'payam-vendor-swiper' ] ) );
		array_unshift( $style_handles, 'payam-vendor-swiper' );
	}

	if ( in_array( 'payam-vendor-swiper', $script_handles, true ) ) {
		$script_handles = array_values( array_diff( $script_handles, [ 'payam-vendor-swiper' ] ) );
		array_unshift( $script_handles, 'payam-vendor-swiper' );
	}

	foreach ( $style_handles as $handle ) {
		if ( wp_style_is( $handle, 'registered' ) ) {
			wp_enqueue_style( $handle );
		}
	}

	foreach ( $script_handles as $handle ) {
		if ( wp_script_is( $handle, 'registered' ) ) {
			wp_enqueue_script( $handle );
		}
	}
}

add_action( 'wp_enqueue_scripts', 'payam_enqueue_assets', 20 );
