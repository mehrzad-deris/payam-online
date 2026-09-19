<?php
/**
 * Local mirror of products that will eventually be synchronized from WHMCS.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the editable catalogue categories used by the local WHMCS mirror.
 *
 * These terms describe the business category (hosting, SSL, DNS, and so on),
 * while the ACF `whmcs_source_type` field identifies the source API entity.
 */
function payam_register_whmcs_product_taxonomy(): void {
	register_taxonomy( 'whmcs_product_category', [ 'whmcs_product' ], [
		'labels' => [
			'name'              => 'دسته‌بندی محصولات WHMCS',
			'singular_name'     => 'دسته‌بندی محصول WHMCS',
			'search_items'      => 'جستجوی دسته‌بندی‌ها',
			'all_items'         => 'همه دسته‌بندی‌ها',
			'parent_item'       => 'دسته‌بندی مادر',
			'parent_item_colon' => 'دسته‌بندی مادر:',
			'edit_item'         => 'ویرایش دسته‌بندی',
			'update_item'       => 'به‌روزرسانی دسته‌بندی',
			'add_new_item'      => 'افزودن دسته‌بندی جدید',
			'new_item_name'     => 'نام دسته‌بندی جدید',
			'menu_name'         => 'دسته‌بندی‌ها',
		],
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_admin_column'  => true,
		'show_in_rest'       => false,
		'meta_box_cb'        => false,
		'hierarchical'       => true,
		'rewrite'            => false,
		'query_var'          => false,
	] );
}

function payam_register_whmcs_product_post_type(): void {
	register_post_type( 'whmcs_product', [
		'labels' => [
			'name'               => 'محصولات WHMCS',
			'singular_name'      => 'محصول WHMCS',
			'menu_name'          => 'محصولات WHMCS',
			'add_new'            => 'افزودن محصول',
			'add_new_item'       => 'افزودن محصول WHMCS',
			'edit_item'          => 'ویرایش محصول WHMCS',
			'new_item'           => 'محصول جدید WHMCS',
			'view_item'          => 'مشاهده محصول WHMCS',
			'search_items'       => 'جستجوی محصولات WHMCS',
			'not_found'          => 'محصولی پیدا نشد.',
			'not_found_in_trash' => 'محصولی در زباله‌دان پیدا نشد.',
		],
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'query_var'           => false,
		'menu_icon'           => 'dashicons-products',
		'supports'            => [ 'title' ],
	] );
}

/**
 * Registers the local TLD catalogue synchronized from WHMCS GetTLDPricing.
 */
function payam_register_whmcs_domain_tld_post_type(): void {
	register_post_type( 'whmcs_domain_tld', [
		'labels' => [
			'name'               => 'دامنه‌های WHMCS',
			'singular_name'      => 'دامنه WHMCS',
			'menu_name'          => 'دامنه‌های WHMCS',
			'add_new'            => 'افزودن دامنه',
			'add_new_item'       => 'افزودن دامنه WHMCS',
			'edit_item'          => 'ویرایش دامنه WHMCS',
			'new_item'           => 'دامنه جدید WHMCS',
			'view_item'          => 'مشاهده دامنه WHMCS',
			'search_items'       => 'جستجوی دامنه‌ها',
			'not_found'          => 'دامنه‌ای پیدا نشد.',
			'not_found_in_trash' => 'دامنه‌ای در زباله‌دان پیدا نشد.',
		],
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'query_var'           => false,
		'menu_icon'           => 'dashicons-admin-site-alt3',
		'supports'            => [ 'title' ],
	] );
}

/**
 * Registers canonical domain categories: term slug is the WHMCS key and the
 * term name is its editable display label.
 */
function payam_register_whmcs_domain_taxonomy(): void {
	register_taxonomy( 'whmcs_domain_category', [ 'whmcs_domain_tld' ], [
		'labels' => [
			'name'              => 'دسته‌بندی دامنه‌ها',
			'singular_name'     => 'دسته‌بندی دامنه',
			'menu_name'         => 'دسته‌بندی‌ها',
			'all_items'         => 'همه دسته‌بندی‌ها',
			'edit_item'         => 'ویرایش دسته‌بندی',
			'update_item'       => 'به‌روزرسانی دسته‌بندی',
			'add_new_item'      => 'افزودن دسته‌بندی جدید',
			'new_item_name'     => 'نام دسته‌بندی جدید',
			'search_items'      => 'جستجوی دسته‌بندی‌ها',
			'parent_item'       => 'دسته‌بندی مادر',
			'parent_item_colon' => 'دسته‌بندی مادر:',
		],
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_admin_column'  => true,
		'show_in_rest'       => false,
		'meta_box_cb'        => false,
		'hierarchical'       => true,
		'rewrite'            => false,
		'query_var'          => false,
	] );
}

/**
 * Assigns the single canonical WHMCS category to a domain record.
 * Future API sync code should use this helper instead of writing term IDs.
 *
 * @return array<int>|WP_Error
 */
function payam_set_whmcs_domain_category( int $post_id, string $key, string $label = '' ) {
	if ( 'whmcs_domain_tld' !== get_post_type( $post_id ) ) {
		return new WP_Error( 'invalid_domain', 'رکورد دامنه معتبر نیست.' );
	}

	$key = sanitize_title( $key );
	if ( '' === $key ) {
		return wp_set_object_terms( $post_id, [], 'whmcs_domain_category', false );
	}

	$label = sanitize_text_field( $label );
	$term  = get_term_by( 'slug', $key, 'whmcs_domain_category' );
	if ( ! $term ) {
		$created = wp_insert_term( $label ?: $key, 'whmcs_domain_category', [ 'slug' => $key ] );
		if ( is_wp_error( $created ) ) {
			return $created;
		}
		$term_id = (int) $created['term_id'];
	} else {
		$term_id = (int) $term->term_id;
		if ( '' !== $label && $label !== $term->name ) {
			$updated = wp_update_term( $term_id, 'whmcs_domain_category', [ 'name' => $label ] );
			if ( is_wp_error( $updated ) ) {
				return $updated;
			}
		}
	}

	return wp_set_object_terms( $post_id, [ $term_id ], 'whmcs_domain_category', false );
}

/**
 * Normalizes one local TLD record for frontend cards.
 */
function payam_get_whmcs_domain_tld_data( int $post_id ): array {
	if ( 'whmcs_domain_tld' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
		return [];
	}

	$get_value = static function ( string $field ) use ( $post_id ) {
		if ( function_exists( 'get_field' ) ) {
			return get_field( $field, $post_id );
		}

		return get_post_meta( $post_id, $field, true );
	};

	$extension = trim( (string) $get_value( 'whmcs_extension' ) );
	$extension = ltrim( $extension ?: get_the_title( $post_id ), '.' );

	if ( '' === $extension ) {
		return [];
	}

	$register_price = $get_value( 'whmcs_register_price' );
	$regular_price  = $get_value( 'domain_regular_price' );
	$discount       = absint( $get_value( 'domain_discount_percent' ) );
	$currency       = strtoupper( trim( (string) $get_value( 'whmcs_currency' ) ) );
	$price_suffix   = trim( (string) $get_value( 'domain_price_suffix' ) );
	$order_link     = $get_value( 'domain_order_link' );
	$renew_price    = $get_value( 'whmcs_renew_price' );
	// Legacy migration: preserve old transfer-only prices without rewriting stored data.
	if ( ! is_numeric( $renew_price ) ) {
		$renew_price = get_post_meta( $post_id, 'whmcs_transfer_price', true );
	}
	$redemption_fee = $get_value( 'whmcs_redemption_fee' );
	$category_terms = get_the_terms( $post_id, 'whmcs_domain_category' );
	$category_term  = is_array( $category_terms ) ? reset( $category_terms ) : false;
	$category_key   = $category_term instanceof WP_Term ? $category_term->slug : sanitize_key( (string) $get_value( 'whmcs_category' ) );
	$category_label = $category_term instanceof WP_Term ? $category_term->name : sanitize_text_field( (string) $get_value( 'whmcs_category_label' ) );

	if ( '' === $category_label && '' !== $category_key ) {
		$default_category_labels = [
			'popular'     => 'محبوب‌ترین‌ها',
			'new'         => 'جدید',
			'sale'        => 'تخفیف‌دار',
			'geographic'  => 'جغرافیایی',
			'country-code'=> 'دامنه‌های کشوری',
			'business'    => 'کسب‌وکار',
			'technology'  => 'فناوری',
			'generic'     => 'عمومی',
		];
		$category_labels = apply_filters( 'payam_whmcs_domain_category_labels', $default_category_labels );
		$category_label  = sanitize_text_field( (string) ( $category_labels[ $category_key ] ?? '' ) );
	}

	if ( ! $discount && is_numeric( $regular_price ) && is_numeric( $register_price ) && (float) $regular_price > (float) $register_price ) {
		$discount = (int) round( ( ( (float) $regular_price - (float) $register_price ) / (float) $regular_price ) * 100 );
	}

	if ( '' === $price_suffix ) {
		$price_suffix = match ( $currency ) {
			'IRT', 'TMN' => 'تومان/سالانه',
			'IRR'        => 'ریال/سالانه',
			'USD'        => 'دلار/سالانه',
			default      => $currency ? $currency . '/سالانه' : 'سالانه',
		};
	}

	if ( is_string( $order_link ) && '' !== trim( $order_link ) ) {
		$order_link = [
			'url'    => $order_link,
			'title'  => 'مشاهده و خرید',
			'target' => '',
		];
	}

	return [
		'id'               => $post_id,
		'extension'        => sanitize_text_field( $extension ),
		'register_price'   => is_numeric( $register_price ) ? (float) $register_price : '',
		'renew_price'      => is_numeric( $renew_price ) && (float) $renew_price >= 0 ? (float) $renew_price : '',
		'redemption_fee'   => is_numeric( $redemption_fee ) && (float) $redemption_fee >= 0 ? (float) $redemption_fee : '',
		'category'         => $category_key,
		'category_label'   => $category_label,
		'currency'         => $currency,
		'regular_price'    => is_numeric( $regular_price ) ? (float) $regular_price : '',
		'discount_percent' => min( 100, $discount ),
		'price_suffix'     => $price_suffix,
		'group'            => sanitize_key( (string) $get_value( 'whmcs_group' ) ),
		'order_link'       => is_array( $order_link ) ? $order_link : [],
	];
}

/**
 * Normalizes a local WHMCS product for frontend product cards.
 *
 * @param int $post_id       Product post ID.
 * @param int $feature_limit Maximum visible features; zero keeps all.
 */
function payam_get_whmcs_product_card_data( int $post_id, int $feature_limit = 0 ): array {
	if ( 'whmcs_product' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
		return [];
	}

	$get_value = static function ( string $field ) use ( $post_id ) {
		return function_exists( 'get_field' )
			? get_field( $field, $post_id )
			: get_post_meta( $post_id, $field, true );
	};

	$prices = $get_value( 'product_prices' );
	$prices = is_array( $prices ) ? array_values( array_filter( $prices, static function ( $price ): bool {
		return is_array( $price ) && ( isset( $price['price_amount'] ) || ! empty( $price['price_suffix'] ) );
	} ) ) : [];
	$price  = [];

	foreach ( $prices as $candidate ) {
		if ( ! empty( $candidate['is_primary_price'] ) ) {
			$price = $candidate;
			break;
		}
	}

	if ( empty( $price ) && ! empty( $prices ) ) {
		$price = $prices[0];
	}

	$features = $get_value( 'product_features' );
	$features = is_array( $features ) ? array_values( array_filter( $features, static function ( $feature ): bool {
		return is_array( $feature )
			&& ( ! array_key_exists( 'feature_show_on_card', $feature ) || ! empty( $feature['feature_show_on_card'] ) )
			&& ( '' !== trim( (string) ( $feature['feature_label'] ?? '' ) ) || '' !== trim( (string) ( $feature['feature_value'] ?? '' ) ) );
	} ) ) : [];

	if ( $feature_limit > 0 ) {
		$features = array_slice( $features, 0, $feature_limit );
	}

	$icon_field = $get_value( 'product_card_icon' );
	$icon_id    = absint( is_array( $icon_field ) ? ( $icon_field['ID'] ?? 0 ) : $icon_field );
	$link_field = $get_value( 'product_order_link' );
	$link       = is_array( $link_field ) ? $link_field : [];

	return [
		'id'       => $post_id,
		'title'    => get_the_title( $post_id ),
		'desc'     => trim( (string) $get_value( 'product_short_description' ) ),
		'icon_id'  => $icon_id,
		'features' => $features,
		'amount'   => isset( $price['price_amount'] ) && is_numeric( $price['price_amount'] ) ? max( 0, (float) $price['price_amount'] ) : 0,
		'period'   => trim( (string) ( $price['price_suffix'] ?? '' ) ),
		'currency' => strtoupper( trim( (string) ( $price['currency_code'] ?? '' ) ) ),
		'url'      => trim( (string) ( $link['url'] ?? '' ) ),
		'target'   => trim( (string) ( $link['target'] ?? '' ) ),
	];
}

function payam_register_whmcs_catalogue(): void {
	payam_register_whmcs_product_post_type();
	payam_register_whmcs_product_taxonomy();
	payam_register_whmcs_domain_tld_post_type();
	payam_register_whmcs_domain_taxonomy();
}

add_action( 'init', 'payam_register_whmcs_catalogue' );
