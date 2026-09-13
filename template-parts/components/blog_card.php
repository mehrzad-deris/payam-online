<?php
/** Reusable article card. */

defined( 'ABSPATH' ) || exit;

$post_id       = absint( $args['post_id'] ?? 0 );
$extra_class   = trim( (string) ( $args['class'] ?? '' ) );
$thumbnail_id  = $post_id ? get_post_thumbnail_id( $post_id ) : 0;
$thumbnail     = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'blog_card' ) : false;
$thumbnail_2x  = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'blog_card_x2' ) : false;
$thumbnail_alt = $thumbnail_id ? get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) : '';
$post_datetime = $post_id ? get_post_datetime( $post_id ) : false;

if ( ! $post_id || 'post' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
	return;
}
?>
<article class="blog-card<?= $extra_class ? ' ' . esc_attr( $extra_class ) : ''; ?>">
	<a class="blog-link group" href="<?= esc_url( get_permalink( $post_id ) ); ?>">
		<?php if ( $thumbnail ) : ?>
			<span class="blog-image"><img src="<?= esc_url( $thumbnail ); ?>"<?= $thumbnail_2x ? ' srcset="' . esc_url( $thumbnail ) . ' 1x, ' . esc_url( $thumbnail_2x ) . ' 2x"' : ''; ?> alt="<?= esc_attr( $thumbnail_alt ?: get_the_title( $post_id ) ); ?>" width="389" height="218" loading="lazy" decoding="async"></span>
		<?php endif; ?>
		<span class="card-caption">
			<span class="blog-title text-neutral-900 md:text-body-2 text-body-mobile-2"><?= esc_html( get_the_title( $post_id ) ); ?></span>
			<span class="blog-meta">
				<?php if ( $post_datetime ) : ?><time class="blog-date text-neutral-500" datetime="<?= esc_attr( $post_datetime->format( DATE_W3C ) ); ?>"><?= esc_html( payamava_jalali_date( 'j F Y', $post_datetime ) ); ?></time><?php endif; ?>
				<span class="read-more text-yellow-primary text-body-3"><span>مطالعه بیشتر</span><span class="icon" aria-hidden="true"><?= icon( 'arrow-linear-2', 'service-cta-icon' ); ?></span></span>
			</span>
		</span>
	</a>
</article>
