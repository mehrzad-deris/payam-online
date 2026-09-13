<?php
/**
 * SSL products section.
 */

defined( 'ABSPATH' ) || exit;

$section_color               = (string) ( get_sub_field( 'section_color' ) ?: '' );
$section_style               = (string) ( get_sub_field( 'section_style' ) ?: 'light' );
$padding_top                 = absint( get_sub_field( 'padding_top' ) );
$padding_top_mobile          = absint( get_sub_field( 'padding_top_mobile' ) );
$padding_bottom              = absint( get_sub_field( 'padding_bottom' ) );
$padding_bottom_mobile       = absint( get_sub_field( 'padding_bottom_mobile' ) );
$tabs                        = get_sub_field( 'ssl_product_tabs' );
$tabs                        = is_array( $tabs ) ? $tabs : [];
$normalized_tabs             = [];

foreach ( $tabs as $tab ) {
	if ( ! is_array( $tab ) ) {
		continue;
	}

	$title              = trim( (string) ( $tab['tab_title'] ?? '' ) );
	$selected_products  = is_array( $tab['selected_products'] ?? null ) ? $tab['selected_products'] : [];
	$featured_reference = $tab['featured_product'] ?? 0;
	$featured_id        = absint( $featured_reference instanceof WP_Post ? $featured_reference->ID : $featured_reference );
	$products           = [];

	foreach ( $selected_products as $product_reference ) {
		$product_id = absint( $product_reference instanceof WP_Post ? $product_reference->ID : $product_reference );
		$product    = payam_get_whmcs_product_card_data( $product_id, 5 );

		if ( ! empty( $product ) ) {
			$product['featured'] = $product_id === $featured_id;
			$products[]          = $product;
		}
	}

	if ( '' !== $title && ! empty( $products ) ) {
		$normalized_tabs[] = [
			'title'    => $title,
			'products' => $products,
		];
	}
}

if ( empty( $normalized_tabs ) ) {
	return;
}

$tabs_id          = wp_unique_id( 'ssl-products-tabs-' );
$has_multiple_tabs = count( $normalized_tabs ) > 1;
$section_styles   = sprintf(
	'--ssl-padding-top:%dpx;--ssl-padding-top-mobile:%dpx;--ssl-padding-bottom:%dpx;--ssl-padding-bottom-mobile:%dpx;%s',
	$padding_top,
	$padding_top_mobile,
	$padding_bottom,
	$padding_bottom_mobile,
	$section_color ? 'background-color:' . sanitize_hex_color( $section_color ) . ';' : ''
);
?>

<section class="ssl-products-section" data-header-theme="<?= esc_attr( $section_style ); ?>" style="<?= esc_attr( $section_styles ); ?>">
	<div class="container">
		<div class="ssl-products-tabs"<?= $has_multiple_tabs ? ' data-tabs data-tabs-mobile="tabs"' : ''; ?>>
			<?php if ( $has_multiple_tabs ) : ?>
				<div class="ssl-tabs-list" role="tablist" aria-label="نوع گواهی SSL">
					<?php foreach ( $normalized_tabs as $index => $tab ) :
						$tab_id   = $tabs_id . '-tab-' . $index;
						$panel_id = $tabs_id . '-panel-' . $index;
						?>
						<button class="ssl-tab-button" type="button" id="<?= esc_attr( $tab_id ); ?>" role="tab" aria-controls="<?= esc_attr( $panel_id ); ?>" aria-selected="<?= 0 === $index ? 'true' : 'false'; ?>" tabindex="<?= 0 === $index ? '0' : '-1'; ?>"><?= esc_html( $tab['title'] ); ?></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="ssl-tabs-panels">
				<?php foreach ( $normalized_tabs as $index => $tab ) :
					$tab_id   = $tabs_id . '-tab-' . $index;
					$panel_id = $tabs_id . '-panel-' . $index;
					?>
					<div class="ssl-tab-panel" id="<?= esc_attr( $panel_id ); ?>"<?= $has_multiple_tabs ? ' role="tabpanel" aria-labelledby="' . esc_attr( $tab_id ) . '" tabindex="0"' : ''; ?><?= 0 !== $index ? ' hidden' : ''; ?>>
						<div class="ssl-products-list">
							<?php foreach ( $tab['products'] as $product ) :
								$title_id = wp_unique_id( 'ssl-product-title-' );
								?>
								<article class="ssl-product-card<?= $product['featured'] ? ' is-featured' : ''; ?>" aria-labelledby="<?= esc_attr( $title_id ); ?>">
									<?php if ( '' !== $product['url'] ) : ?>
										<a class="ssl-product-overlay" href="<?= esc_url( $product['url'] ); ?>" aria-label="<?= esc_attr( 'مشاهده و خرید ' . $product['title'] ); ?>"<?= $product['target'] ? ' target="' . esc_attr( $product['target'] ) . '"' : ''; ?><?= '_blank' === $product['target'] ? ' rel="noopener noreferrer"' : ''; ?>></a>
									<?php endif; ?>

									<header class="ssl-product-header">
										<?php if ( $product['icon_id'] ) : ?>
											<span class="ssl-product-icon" aria-hidden="true"><?= wp_get_attachment_image( $product['icon_id'], 'full', false, [ 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ] ); ?></span>
										<?php endif; ?>
										<div>
											<h3 class="ssl-product-title" id="<?= esc_attr( $title_id ); ?>"><?= esc_html( $product['title'] ); ?></h3>
											<?php if ( '' !== $product['desc'] ) : ?><p class="ssl-product-description"><?= esc_html( $product['desc'] ); ?></p><?php endif; ?>
										</div>
									</header>

									<?php if ( ! empty( $product['features'] ) ) : ?>
										<ul class="ssl-product-features">
											<?php foreach ( $product['features'] as $feature ) :
												$feature_text = trim( implode( ' ', array_filter( [ $feature['feature_label'] ?? '', $feature['feature_value'] ?? '', $feature['feature_unit'] ?? '' ] ) ) );
												if ( '' === $feature_text ) { continue; }
												?>
												<li><?= icon( 'check', 'ssl-feature-icon' ); ?><span><?= esc_html( $feature_text ); ?></span></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>

									<?php if ( $product['amount'] > 0 || '' !== $product['period'] ) : ?>
										<p class="ssl-product-price">
											<?php if ( $product['amount'] > 0 ) : ?><data value="<?= esc_attr( $product['amount'] ); ?>"><?= esc_html( number_format_i18n( $product['amount'] ) ); ?></data><?php endif; ?>
											<?php if ( '' !== $product['period'] ) : ?><span><?= esc_html( $product['period'] ); ?></span><?php endif; ?>
										</p>
									<?php endif; ?>

									<?php if ( '' !== $product['url'] ) : ?><span class="ssl-product-cta" aria-hidden="true">مشاهده و خرید</span><?php endif; ?>
								</article>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
