<?php
/** Shared posts archive content. */

defined( 'ABSPATH' ) || exit;

$archive_query = $GLOBALS['wp_query'];
$posts_page_id = absint( get_option( 'page_for_posts' ) );
$archive_title = is_home() && $posts_page_id ? get_the_title( $posts_page_id ) : '';

if ( is_category() || is_tag() || is_tax() ) {
	$archive_title = single_term_title( '', false );
} elseif ( '' === $archive_title ) {
	$archive_title = get_the_archive_title();
}

$archive_title = $archive_title ?: 'آنلاین آکادمی';
$initial_count = (int) $archive_query->post_count;
$remaining     = max( 0, (int) $archive_query->found_posts - $initial_count );
$category_id   = is_category() ? absint( get_queried_object_id() ) : 0;
?>
<main class="blog-archive" data-blog-archive data-header-theme="light" data-offset="<?= esc_attr( $initial_count ); ?>" data-category-id="<?= esc_attr( $category_id ); ?>">
	<div class="container">
		<?php section_heading( [
			'title' => $archive_title, 'title_tag' => 'h1',
			'subtitle' => 'تازه‌ترین آموزش‌ها و مطالب تخصصی میزبانی وب و زیرساخت آنلاین',
			'show_shapes' => true,
		] ); ?>
		<?php if ( $archive_query->have_posts() ) : ?>
			<div class="blog-archive-grid" data-blog-grid aria-live="polite">
				<?php while ( $archive_query->have_posts() ) : $archive_query->the_post();
					get_template_part( 'template-parts/components/blog_card', null, [ 'post_id' => get_the_ID() ] );
				endwhile; ?>
			</div>
			<?php if ( $remaining > 0 ) : ?>
				<div class="blog-load-more-wrap"><button class="blog-load-more" type="button" data-blog-load-more data-remaining="<?= esc_attr( $remaining ); ?>"><span data-blog-load-more-label><?= esc_html( sprintf( 'مشاهده %s آموزش دیگر', number_format_i18n( $remaining ) ) ); ?></span><?= icon( 'arrow-down', 'blog-load-more-icon' ); ?></button></div>
			<?php endif; ?>
		<?php else : ?><p class="blog-archive-empty">هنوز مقاله‌ای منتشر نشده است.</p><?php endif; ?>
	</div>
</main>
