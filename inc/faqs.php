<?php
/** Reusable question/answer catalogue with its own categories. */
defined( 'ABSPATH' ) || exit;

function payam_register_faqs(): void {
	register_post_type( 'payam_faq', [
		'labels' => [
			'name' => 'سوالات متداول',
			'singular_name' => 'سؤال متداول',
			'menu_name' => 'سوالات متداول',
			'add_new' => 'افزودن سؤال',
			'add_new_item' => 'افزودن سؤال متداول',
			'edit_item' => 'ویرایش سؤال و پاسخ',
			'new_item' => 'سؤال جدید',
			'all_items' => 'همه سوالات',
			'search_items' => 'جستجوی سوالات',
			'not_found' => 'سؤالی پیدا نشد.',
			'not_found_in_trash' => 'سؤالی در زباله‌دان پیدا نشد.',
		],
		'public' => false,
		'publicly_queryable' => false,
		'exclude_from_search' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_rest' => false,
		'has_archive' => false,
		'rewrite' => false,
		'query_var' => false,
		'menu_icon' => 'dashicons-editor-help',
		'supports' => [ 'title', 'editor', 'revisions' ],
		'taxonomies' => [ 'payam_faq_category' ],
	] );

	register_taxonomy( 'payam_faq_category', [ 'payam_faq' ], [
		'labels' => [
			'name' => 'دسته‌بندی سوالات',
			'singular_name' => 'دسته‌بندی سؤال',
			'menu_name' => 'دسته‌بندی‌ها',
			'all_items' => 'همه دسته‌بندی‌ها',
			'edit_item' => 'ویرایش دسته‌بندی',
			'update_item' => 'به‌روزرسانی دسته‌بندی',
			'add_new_item' => 'افزودن دسته‌بندی جدید',
			'new_item_name' => 'نام دسته‌بندی جدید',
			'parent_item' => 'دسته‌بندی مادر',
			'parent_item_colon' => 'دسته‌بندی مادر:',
			'search_items' => 'جستجوی دسته‌بندی‌ها',
		],
		'hierarchical' => true,
		'public' => false,
		'publicly_queryable' => false,
		'show_ui' => true,
		'show_admin_column' => true,
		'show_in_rest' => false,
		'rewrite' => false,
		'query_var' => false,
	] );
}
add_action( 'init', 'payam_register_faqs' );

/** One cached catalogue per request, shared by repeated FAQ sections. */
function payam_faq_catalogue(): array {
	static $catalogue = null;
	if ( null !== $catalogue ) { return $catalogue; }
	$catalogue = [ 'items' => [], 'categories' => [] ];
	$posts = get_posts( [ 'post_type' => 'payam_faq', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC', 'no_found_rows' => true, 'update_post_meta_cache' => false, 'update_post_term_cache' => true ] );
	foreach ( $posts as $post ) {
		if ( '' === trim( $post->post_title ) || '' === trim( $post->post_content ) ) { continue; }
		$ids = [];
		$terms = get_the_terms( $post, 'payam_faq_category' );
		foreach ( is_array( $terms ) ? $terms : [] as $term ) {
			$ids[] = $term->term_id;
			$catalogue['categories'][ $term->term_id ] = $term->name;
		}
		$catalogue['items'][] = [ 'question' => $post->post_title, 'answer' => wpautop( $post->post_content ), 'categories' => $ids ];
	}
	asort( $catalogue['categories'], SORT_NATURAL );
	return $catalogue;
}

add_filter( 'enter_title_here', static function ( string $title, WP_Post $post ): string {
	return 'payam_faq' === $post->post_type ? 'متن سؤال را وارد کنید' : $title;
}, 10, 2 );

add_action( 'edit_form_after_title', static function ( WP_Post $post ): void {
	if ( 'payam_faq' === $post->post_type ) {
		echo '<p><strong>پاسخ سؤال</strong> را در ویرایشگر زیر وارد کنید.</p>';
	}
} );
