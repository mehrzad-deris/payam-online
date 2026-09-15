<?php
/** Automatic ACF Flexible Content previews for Page Builder layouts. */

defined( 'ABSPATH' ) || exit;

/** Find a desktop preview by layout name without storing anything in ACF. */
function payam_section_preview_asset( string $layout_name ): array {
	$layout_name = sanitize_key( $layout_name );
	$filenames  = array_unique( [ $layout_name, str_replace( '_', '-', $layout_name ) ] );
	$extensions = [ 'webp', 'avif', 'png', 'jpg', 'jpeg' ];

	foreach ( $filenames as $filename ) {
		foreach ( $extensions as $extension ) {
			$relative = '/assets/images/example/' . $filename . '.' . $extension;
			if ( is_file( get_theme_file_path( $relative ) ) ) {
				return [
					'path' => $relative,
					'url'  => get_theme_file_uri( $relative ),
					'ext'  => $extension,
				];
			}
		}
	}

	return [];
}

/** Build the authenticated URL used for an admin preview image. */
function payam_section_preview_url( string $layout_name, array $preview ): string {
	if ( empty( $preview['path'] ) ) {
		return '';
	}

	return add_query_arg( [
		'action' => 'payam_section_preview',
		'layout' => sanitize_key( $layout_name ),
		'nonce'  => wp_create_nonce( 'payam_section_preview' ),
		'ver'    => payam_asset_version( $preview['path'] ),
	], admin_url( 'admin-ajax.php' ) );
}

/** Collect the current Persian labels directly from ACF layout definitions. */
function payam_section_preview_layout_labels(): array {
	if ( ! function_exists( 'acf_get_field_groups' ) || ! function_exists( 'acf_get_fields' ) ) {
		return [];
	}

	$labels = [];
	$walk   = static function ( array $fields ) use ( &$labels, &$walk ): void {
		foreach ( $fields as $field ) {
			if ( 'flexible_content' === ( $field['type'] ?? '' ) ) {
				foreach ( (array) ( $field['layouts'] ?? [] ) as $layout ) {
					$name = sanitize_key( (string) ( $layout['name'] ?? '' ) );
					if ( $name && ! isset( $labels[ $name ] ) ) {
						$labels[ $name ] = (string) ( $layout['label'] ?? $name );
					}
				}
			}

			if ( ! empty( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) {
				$walk( $field['sub_fields'] );
			}
		}
	};

	foreach ( acf_get_field_groups() as $group ) {
		$fields = acf_get_fields( $group );
		if ( is_array( $fields ) ) {
			$walk( $fields );
		}
	}

	return $labels;
}

/** Append a non-persistent preview tab to registered Page Builder layouts. */
add_filter( 'acf/load_field/type=flexible_content', static function ( array $field ): array {
	if ( ! is_admin() || empty( $field['layouts'] ) || ! is_array( $field['layouts'] ) || ! function_exists( 'payam_get_section_config' ) ) {
		return $field;
	}

	$registered_layouts = array_keys( payam_get_section_config() );

	foreach ( $field['layouts'] as &$layout ) {
		$layout_name = sanitize_key( (string) ( $layout['name'] ?? '' ) );
		if ( ! in_array( $layout_name, $registered_layouts, true ) ) {
			continue;
		}

		$preview = payam_section_preview_asset( $layout_name );
		if ( empty( $preview['path'] ) ) {
			continue;
		}

		$identity  = substr( md5( (string) ( $field['key'] ?? $field['name'] ?? '' ) . '|' . $layout_name ), 0, 16 );
		$subfields = is_array( $layout['sub_fields'] ?? null ) ? $layout['sub_fields'] : [];
		$tab_key   = 'field_payam_preview_tab_' . $identity;

		foreach ( $subfields as $subfield ) {
			if ( $tab_key === ( $subfield['key'] ?? '' ) ) {
				continue 2;
			}
		}

		$subfields[] = [
			'key'       => $tab_key,
			'label'     => 'پیش‌نمایش',
			'name'      => 'payam_preview_tab_' . $identity,
			'type'      => 'tab',
			'placement' => 'top',
			'endpoint'  => 0,
		];
		$subfields[] = [
			'key'          => 'field_payam_preview_image_' . $identity,
			'label'        => '',
			'name'         => 'payam_preview_image_' . $identity,
			'type'         => 'message',
			'message'      => sprintf(
				'<div class="payam-section-preview"><img data-section-preview-src="%1$s" alt="%2$s" loading="lazy" decoding="async"></div>',
				esc_url( payam_section_preview_url( $layout_name, $preview ) ),
				esc_attr( 'پیش‌نمایش سکشن ' . ( $layout['label'] ?? $layout_name ) )
			),
			'new_lines'    => '',
			'esc_html'     => 0,
			'wrapper'      => [ 'class' => 'payam-section-preview-field' ],
		];

		$layout['sub_fields'] = $subfields;
	}
	unset( $layout );

	return $field;
}, 30 );

/** Serve previews through WordPress so hosts with a missing WebP MIME still render inline. */
add_action( 'wp_ajax_payam_section_preview', static function (): void {
	if ( ! current_user_can( 'edit_pages' ) ) {
		status_header( 403 );
		exit;
	}

	check_ajax_referer( 'payam_section_preview', 'nonce' );
	$layout_name = sanitize_key( (string) ( $_GET['layout'] ?? '' ) );

	if ( ! in_array( $layout_name, array_keys( payam_get_section_config() ), true ) ) {
		status_header( 404 );
		exit;
	}

	$preview = payam_section_preview_asset( $layout_name );
	$file    = ! empty( $preview['path'] ) ? get_theme_file_path( $preview['path'] ) : '';
	$mimes   = [
		'webp' => 'image/webp',
		'avif' => 'image/avif',
		'png'  => 'image/png',
		'jpg'  => 'image/jpeg',
		'jpeg' => 'image/jpeg',
	];

	if ( ! $file || ! is_readable( $file ) || empty( $mimes[ $preview['ext'] ?? '' ] ) ) {
		status_header( 404 );
		exit;
	}

	nocache_headers();
	header( 'Content-Type: ' . $mimes[ $preview['ext'] ] );
	header( 'Content-Disposition: inline; filename="' . sanitize_file_name( basename( $file ) ) . '"' );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Content-Length: ' . (string) filesize( $file ) );
	readfile( $file );
	exit;
} );

/** Register the central section guide under Appearance. */
add_action( 'admin_menu', static function (): void {
	add_theme_page(
		'راهنمای سکشن‌ها',
		'راهنمای سکشن‌ها',
		'edit_pages',
		'payam-section-guide',
		'payam_render_section_preview_guide'
	);
} );

/** Render all current layouts; compatibility aliases are intentionally hidden. */
function payam_render_section_preview_guide(): void {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'شما اجازه مشاهده این صفحه را ندارید.' ) );
	}

	$labels  = payam_section_preview_layout_labels();
	$layouts = array_diff( array_keys( payam_get_section_config() ), [ 'hero_section', 'hero_section_on_page' ] );
	?>
	<div class="wrap payam-section-guide">
		<h1>راهنمای سکشن‌ها</h1>
		<div class="payam-section-guide-grid">
			<?php foreach ( $layouts as $layout_name ) :
				$preview = payam_section_preview_asset( $layout_name );
				$label   = $labels[ $layout_name ] ?? str_replace( '_', ' ', $layout_name );
				$url     = payam_section_preview_url( $layout_name, $preview );
				?>
				<article class="payam-section-guide-card">
					<div class="payam-section-guide-image">
						<?php if ( $url ) : ?>
							<img src="<?= esc_url( $url ); ?>" alt="<?= esc_attr( 'پیش‌نمایش ' . $label ); ?>" loading="lazy" decoding="async">
						<?php else : ?>
							<span>تصویر پیش‌نمایش موجود نیست</span>
						<?php endif; ?>
					</div>
					<header>
						<h2><?= esc_html( $label ); ?></h2>
						<code><?= esc_html( $layout_name ); ?></code>
					</header>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/** Load the tiny preview UI only on ACF input screens. */
add_action( 'acf/input/admin_enqueue_scripts', static function (): void {
	$script = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG
		? '/assets/js/admin/section-previews.js'
		: '/assets/js/admin/section-previews.min.js';
	$style  = '/assets/styles/admin/section-previews.css';

	wp_enqueue_style( 'payam-section-previews', get_theme_file_uri( $style ), [], payam_asset_version( $style ) );
	wp_enqueue_script( 'payam-section-previews', get_theme_file_uri( $script ), [ 'acf-input' ], payam_asset_version( $script ), true );
} );

add_action( 'admin_enqueue_scripts', static function ( string $hook ): void {
	if ( 'appearance_page_payam-section-guide' !== $hook ) {
		return;
	}

	$style = '/assets/styles/admin/section-previews.css';
	wp_enqueue_style( 'payam-section-previews', get_theme_file_uri( $style ), [], payam_asset_version( $style ) );
} );
