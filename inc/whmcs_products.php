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
		'regular_price'    => is_numeric( $regular_price ) ? (float) $regular_price : '',
		'discount_percent' => min( 100, $discount ),
		'price_suffix'     => $price_suffix,
		'group'            => sanitize_key( (string) $get_value( 'whmcs_group' ) ),
		'order_link'       => is_array( $order_link ) ? $order_link : [],
	];
}

function payam_register_whmcs_catalogue(): void {
	payam_register_whmcs_product_post_type();
	payam_register_whmcs_product_taxonomy();
	payam_register_whmcs_domain_tld_post_type();
}

add_action( 'init', 'payam_register_whmcs_catalogue' );
