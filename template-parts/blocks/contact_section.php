<?php
defined( 'ABSPATH' ) || exit;
$contactItems = get_sub_field( 'contact_items' );
$contactItems = is_array( $contactItems ) ? array_filter( $contactItems, 'is_array' ) : [];
$formId = get_sub_field( 'contact_form' );
$formId = $formId instanceof WP_Post ? $formId->ID : absint( $formId );
$styles = [];
$color = sanitize_hex_color( (string) get_sub_field( 'section_color' ) );
if ( $color ) { $styles[] = 'background-color:' . $color; }
foreach ( [ 'padding_top', 'padding_top_mobile', 'padding_bottom', 'padding_bottom_mobile' ] as $name ) {
	$value = get_sub_field( $name );
	if ( is_numeric( $value ) ) { $styles[] = '--contact-' . str_replace( '_', '-', $name ) . ':' . absint( $value ) . 'px'; }
}
if ( ! $contactItems && ! $formId ) { return; }
?>
<section class="contact-section" data-header-theme="light" style="<?= esc_attr( implode( ';', $styles ) ); ?>">
	<div class="container">
		<?php if ( $contactItems ) : ?>
			<div class="contact-info">
				<?php foreach ( $contactItems as $item ) :
					$link = $item['contact_link'] ?? [];
					$url = is_array( $link ) ? ( $link['url'] ?? '' ) : '';
					$iconId = absint( $item['contact_icon'] ?? 0 ); ?>
					<article class="contact-info-card">
						<?php if ( $iconId ) : ?>
							<?= wp_get_attachment_image( $iconId, 'thumbnail', false, [ 'class' => 'contact-info-icon', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '48px' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Image markup escaped by WordPress. ?>
						<?php endif; ?>
						<h3><?= esc_html( $item['contact_title'] ?? '' ); ?></h3>
						<?php if ( $url ) : ?>
							<a href="<?= esc_url( $url ); ?>"<?= '_blank' === ( $link['target'] ?? '' ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?= esc_html( $item['contact_value'] ?? '' ); ?></a>
						<?php else : ?><p><?= esc_html( $item['contact_value'] ?? '' ); ?></p><?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ( $formId ) { echo do_shortcode( '[payam_form id="' . $formId . '"]' ); /* Trusted shortcode renderer escapes its output. */ } ?>
	</div>
</section>
