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

/** Incremented whenever FAQ content changes, invalidating all related transients. */
function payam_faq_cache_version(): int {
	return max( 1, absint( get_option( 'payam_faq_cache_version', 1 ) ) );
}

function payam_flush_faq_cache(): void {
	update_option( 'payam_faq_cache_version', payam_faq_cache_version() + 1, false );
}
add_action( 'save_post_payam_faq', 'payam_flush_faq_cache' );
add_action( 'before_delete_post', static function ( int $post_id ): void {
	if ( 'payam_faq' === get_post_type( $post_id ) ) { payam_flush_faq_cache(); }
} );
add_action( 'created_payam_faq_category', 'payam_flush_faq_cache' );
add_action( 'edited_payam_faq_category', 'payam_flush_faq_cache' );
add_action( 'delete_payam_faq_category', 'payam_flush_faq_cache' );

/** Cached list of non-empty FAQ categories. */
function payam_faq_categories(): array {
	$key        = 'payam_faq_categories_' . payam_faq_cache_version();
	$categories = get_transient( $key );

	if ( false !== $categories && is_array( $categories ) ) { return $categories; }

	$terms      = get_terms( [ 'taxonomy' => 'payam_faq_category', 'hide_empty' => true ] );
	$categories = [];
	foreach ( is_array( $terms ) ? $terms : [] as $term ) {
		$categories[ $term->term_id ] = $term->name;
	}
	set_transient( $key, $categories, DAY_IN_SECONDS );

	return $categories;
}

/** A bounded and cacheable page of FAQs. */
function payam_faq_catalogue( int $limit = 8, int $page = 1, int $category_id = 0 ): array {
	$limit       = min( 30, max( 1, $limit ) );
	$page        = min( 200, max( 1, $page ) );
	$category_id = absint( $category_id );
	$key         = sprintf( 'payam_faq_%d_%d_%d_%d', payam_faq_cache_version(), $limit, $page, $category_id );
	$catalogue   = get_transient( $key );

	if ( false !== $catalogue && is_array( $catalogue ) ) { return $catalogue; }

	$args = [
		'post_type'              => 'payam_faq',
		'post_status'            => 'publish',
		'posts_per_page'         => $limit,
		'paged'                  => $page,
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'update_post_meta_cache' => false,
		'update_post_term_cache' => true,
	];
	if ( $category_id ) {
		$args['tax_query'] = [ [ 'taxonomy' => 'payam_faq_category', 'field' => 'term_id', 'terms' => $category_id ] ];
	}

	$query = new WP_Query( $args );
	$items = [];
	foreach ( $query->posts as $post ) {
		if ( '' === trim( $post->post_title ) || '' === trim( $post->post_content ) ) { continue; }
		$terms = get_the_terms( $post, 'payam_faq_category' );
		$items[] = [
			'question'   => $post->post_title,
			'answer'     => wpautop( $post->post_content ),
			'categories' => array_map( static fn( WP_Term $term ): int => $term->term_id, is_array( $terms ) ? $terms : [] ),
		];
	}

	$catalogue = [
		'items'       => $items,
		'page'        => $page,
		'total_pages' => max( 1, (int) $query->max_num_pages ),
	];
	set_transient( $key, $catalogue, HOUR_IN_SECONDS );

	return $catalogue;
}

function payam_render_faq_items( array $items, bool $open_first = true ): string {
	ob_start();
	foreach ( $items as $index => $item ) {
		$is_open  = $open_first && 0 === $index;
		$button_id = wp_unique_id( 'faq-button-' );
		$panel_id  = wp_unique_id( 'faq-panel-' ); ?>
		<article class="faq-item<?= $is_open ? ' is-open' : ''; ?> shadow-mellow" data-faq-item data-faq-categories="<?= esc_attr( implode( ' ', $item['categories'] ?? [] ) ); ?>">
			<h3 class="faq-question"><button type="button" class="faq-button" id="<?= esc_attr( $button_id ); ?>" data-faq-toggle aria-expanded="<?= $is_open ? 'true' : 'false'; ?>" aria-controls="<?= esc_attr( $panel_id ); ?>"><span class="faq-question-text text-body-mobile-2 md:text-body-2 text-neutral-100"><?= esc_html( $item['question'] ?? '' ); ?></span><span class="faq-symbol" data-faq-symbol aria-hidden="true"><?= $is_open ? icon( 'minus', 'fill-white w-6 h-6' ) : icon( 'plus', 'stroke-blue-primary w-6 h-6' ); ?></span></button></h3>
			<div class="faq-answer" id="<?= esc_attr( $panel_id ); ?>" data-faq-panel role="region" aria-labelledby="<?= esc_attr( $button_id ); ?>"<?= $is_open ? '' : ' hidden'; ?>><div class="faq-answer-content text-body-3 md:text-caption"><?= wp_kses_post( $item['answer'] ?? '' ); ?></div></div>
		</article><?php
	}
	return (string) ob_get_clean();
}

function payam_rest_faqs( WP_REST_Request $request ) {
	if ( ! payam_rate_limit_consume( 'faq-rest', 120, 10 * MINUTE_IN_SECONDS ) ) {
		return new WP_Error( 'payam_faq_rate_limit', 'تعداد درخواست‌ها زیاد است. چند دقیقه دیگر تلاش کنید.', [ 'status' => 429 ] );
	}

	$category_id = absint( $request['category'] );
	if ( $category_id && ! term_exists( $category_id, 'payam_faq_category' ) ) {
		return new WP_Error( 'payam_faq_category', 'دسته‌بندی معتبر نیست.', [ 'status' => 400 ] );
	}

	$catalogue = payam_faq_catalogue( absint( $request['per_page'] ), absint( $request['page'] ), $category_id );
	return rest_ensure_response( [
		'html'     => payam_render_faq_items( $catalogue['items'], 1 === $catalogue['page'] ),
		'hasMore'  => $catalogue['page'] < $catalogue['total_pages'],
		'nextPage' => $catalogue['page'] + 1,
	] );
}

add_action( 'rest_api_init', static function (): void {
	register_rest_route( 'payam/v1', '/faqs', [
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => 'payam_rest_faqs',
		'permission_callback' => '__return_true',
		'args'                => [
			'per_page' => [ 'default' => 8, 'sanitize_callback' => 'absint', 'validate_callback' => static fn( $value ): bool => is_numeric( $value ) && (int) $value >= 1 && (int) $value <= 30 ],
			'page'     => [ 'default' => 1, 'sanitize_callback' => 'absint', 'validate_callback' => static fn( $value ): bool => is_numeric( $value ) && (int) $value >= 1 && (int) $value <= 200 ],
			'category' => [ 'default' => 0, 'sanitize_callback' => 'absint' ],
		],
	] );
} );

add_filter( 'enter_title_here', static function ( string $title, WP_Post $post ): string {
	return 'payam_faq' === $post->post_type ? 'متن سؤال را وارد کنید' : $title;
}, 10, 2 );

add_action( 'edit_form_after_title', static function ( WP_Post $post ): void {
	if ( 'payam_faq' === $post->post_type ) {
		echo '<p><strong>پاسخ سؤال</strong> را در ویرایشگر زیر وارد کنید.</p>';
	}
} );
