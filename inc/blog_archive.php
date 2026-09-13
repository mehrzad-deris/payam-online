<?php
/** Blog archive assets and AJAX pagination. */

defined( 'ABSPATH' ) || exit;

function payam_render_blog_cards( array $posts ): string {
	ob_start();
	foreach ( $posts as $post ) {
		get_template_part( 'template-parts/components/blog_card', null, [ 'post_id' => $post->ID ] );
	}
	return (string) ob_get_clean();
}

function payam_load_more_blog_posts(): void {
	check_ajax_referer( 'payam_blog_archive', 'nonce' );
	$offset      = absint( $_POST['offset'] ?? 0 );
	$category_id = absint( $_POST['categoryId'] ?? 0 );
	$query_args  = [
		'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 6,
		'offset' => $offset, 'orderby' => 'date', 'order' => 'DESC',
		'ignore_sticky_posts' => true, 'update_post_meta_cache' => true,
		'update_post_term_cache' => false,
	];

	if ( $category_id ) {
		$query_args['cat'] = $category_id;
	}

	$query      = new WP_Query( $query_args );
	$next_offset = $offset + (int) $query->post_count;
	$remaining = max( 0, (int) $query->found_posts - $next_offset );
	wp_send_json_success( [
		'html' => payam_render_blog_cards( $query->posts ), 'nextOffset' => $next_offset,
		'hasMore' => $remaining > 0, 'remaining' => $remaining,
	] );
}
add_action( 'wp_ajax_payam_load_blog_posts', 'payam_load_more_blog_posts' );
add_action( 'wp_ajax_nopriv_payam_load_blog_posts', 'payam_load_more_blog_posts' );

function payam_enqueue_blog_archive_assets(): void {
	if ( ! is_home() && ! is_archive() ) { return; }
	wp_enqueue_style( 'payam-bundle-cards' );
	wp_enqueue_style( 'payam-blog-archive', get_theme_file_uri( '/assets/styles/scss/sections/blog-archive.css' ), [ 'payam-app', 'payam-bundle-cards' ], payam_asset_version( '/assets/styles/scss/sections/blog-archive.css' ) );
	wp_enqueue_script( 'payam-blog-archive', get_theme_file_uri( '/assets/js/sections/blog-archive.min.js' ), [ 'payam-app' ], payam_asset_version( '/assets/js/sections/blog-archive.min.js' ), [ 'strategy' => 'defer', 'in_footer' => true ] );
	wp_localize_script( 'payam-blog-archive', 'payamBlogArchive', [ 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'payam_blog_archive' ) ] );
}
add_action( 'wp_enqueue_scripts', 'payam_enqueue_blog_archive_assets', 30 );
