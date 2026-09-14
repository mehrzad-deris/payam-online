<?php
defined( 'ABSPATH' ) || exit;

/** Shared source selection for rendering and conditional asset discovery. */
function payam_article_builder_source( int $post_id ): array {
	$custom = function_exists( 'get_field' ) && get_field( 'article_custom_layout', $post_id );
	if ( ! $custom ) { return payam_options_builder_source( 'article_page_builder' ); }
	return [
		'field' => 'page_builder',
		'post_id' => $post_id,
	];
}

function payam_article_sidebar_image( int $post_id ): string {
	$id = get_post_thumbnail_id( $post_id );
	if ( ! $id ) { return ''; }
	$src = wp_get_attachment_image_url( $id, 'article_sidebar' );
	$src2 = wp_get_attachment_image_url( $id, 'article_sidebar_2' );
	return wp_get_attachment_image( $id, 'article_sidebar', false, [
		'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '80px',
		'srcset' => $src . ' 80w, ' . $src2 . ' 160w',
	] );
}

function payam_prepare_post_toc( string $content ): array {
	$headings = []; $used_ids = [];
	$content = preg_replace_callback( '/<h([2-4])([^>]*)>(.*?)<\/h\1>/isu', static function ( array $match ) use ( &$headings, &$used_ids ): string {
		$level = absint( $match[1] ); $attrs = $match[2]; $title = trim( wp_strip_all_tags( $match[3] ) );
		if ( '' === $title ) { return $match[0]; }
		if ( preg_match( '/\sid=["\']([^"\']+)["\']/i', $attrs, $id_match ) ) { $id = sanitize_html_class( $id_match[1] ); }
		else { $base = sanitize_title( $title ) ?: 'article-section'; $id = $base; $index = 2; while ( isset( $used_ids[ $id ] ) ) { $id = $base . '-' . $index++; } $attrs .= ' id="' . esc_attr( $id ) . '"'; }
		$used_ids[ $id ] = true; $headings[] = [ 'level' => $level, 'id' => $id, 'title' => $title ];
		return '<h' . $level . $attrs . '>' . $match[3] . '</h' . $level . '>';
	}, $content );
	return [ 'content' => (string) $content, 'headings' => $headings ];
}

function payam_get_related_posts( int $post_id, int $limit = 4 ): array {
	return get_posts( [ 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => max( 1, $limit ), 'post__not_in' => [ $post_id ], 'category__in' => wp_get_post_categories( $post_id ), 'orderby' => 'date', 'order' => 'DESC', 'no_found_rows' => true, 'ignore_sticky_posts' => true, 'update_post_term_cache' => false ] );
}

function payam_article_comment( WP_Comment $comment, array $args, int $depth ): void {
	$is_admin = $comment->user_id && user_can( $comment->user_id, 'edit_posts' ); ?>
	<li id="comment-<?= esc_attr( $comment->comment_ID ); ?>" <?php comment_class( $is_admin ? 'article-comment is-admin-comment' : 'article-comment', $comment ); ?>><?php if ( $is_admin ) : ?><span class="reply-icon"><?= icon('reply', 'w-6 h-6') ?></span><?php endif; ?><article><header><?php if ( $is_admin ) : ?><strong><?= icon( 'logo-white', 'w-[73px] h-6' ); ?></strong><?php else : ?><?= icon( 'user-2', 'comment-user-icon' ); ?><strong><?= esc_html( get_comment_author( $comment ) ); ?></strong><?php endif; ?></header><div><?= wp_kses_post( wpautop( get_comment_text( $comment ) ) ); ?></div></article>
<?php }

/**
 * Keeps the public article comment form limited to name, email and comment.
 */
function payam_article_comment_fields( array $fields ): array {
	if ( is_singular( 'post' ) ) {
		unset( $fields['url'], $fields['cookies'] );
	}

	return $fields;
}
add_filter( 'comment_form_default_fields', 'payam_article_comment_fields', 20 );

function payam_enqueue_single_post_assets(): void {
	if ( ! is_singular( 'post' ) ) { return; }
	wp_enqueue_style( 'payam-vendor-swiper' ); wp_enqueue_style( 'payam-bundle-cards' );
	wp_enqueue_script( 'payam-vendor-swiper' ); wp_enqueue_script( 'payam-bundle-cards' );
	wp_enqueue_style( 'payam-bundle-single-post', get_theme_file_uri( '/assets/styles/scss/bundles/single-post.min.css' ), [ 'payam-app', 'payam-bundle-cards' ], payam_asset_version( '/assets/styles/scss/bundles/single-post.min.css' ) );
	wp_enqueue_script( 'payam-bundle-single-post', get_theme_file_uri( '/assets/js/bundles/single-post.min.js' ), [ 'payam-app' ], payam_asset_version( '/assets/js/bundles/single-post.min.js' ), [ 'strategy' => 'defer', 'in_footer' => true ] );
}
add_action( 'wp_enqueue_scripts', 'payam_enqueue_single_post_assets', 30 );
