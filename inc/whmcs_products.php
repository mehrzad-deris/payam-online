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

function payam_register_whmcs_catalogue(): void {
	payam_register_whmcs_product_post_type();
	payam_register_whmcs_product_taxonomy();
}

add_action( 'init', 'payam_register_whmcs_catalogue' );
