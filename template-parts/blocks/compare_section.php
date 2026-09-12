<?php
/**
 * Responsive plan comparison.
 */
defined( 'ABSPATH' ) || exit;

$plans = get_sub_field( 'compare_plans' );
$rows  = get_sub_field( 'compare_features' );
$plans = is_array( $plans ) ? array_slice( array_values( array_filter( $plans, static fn( $plan ) => is_array( $plan ) && '' !== trim( (string) ( $plan['plan_title'] ?? '' ) ) ) ), 0, 4 ) : [];
$rows  = is_array( $rows ) ? array_values( array_filter( $rows, static fn( $row ) => is_array( $row ) && '' !== trim( (string) ( $row['feature_label'] ?? '' ) ) ) ) : [];
if ( ! $plans ) {
	return;
}
$styles = [ '--compare-count: ' . count( $plans ) ];
$color  = sanitize_hex_color( (string) get_sub_field( 'section_color' ) );
if ( $color ) {
	$styles[] = 'background-color: ' . $color;
}
foreach ( [ 'padding_top', 'padding_top_mobile', 'padding_bottom', 'padding_bottom_mobile' ] as $field ) {
	$value = get_sub_field( $field );
	if ( is_numeric( $value ) ) {
		$styles[] = '--compare-' . str_replace( '_', '-', $field ) . ': ' . absint( $value ) . 'px';
	}
}
$theme = 'dark' === get_sub_field( 'section_style' ) ? 'dark' : 'light';
$modalId = wp_unique_id( 'compare-modal-' );

// Shared renderer keeps table, mobile summary and modal values consistent.
$renderValue = static function ( $value ): void {
	$value = is_array( $value ) ? $value : [];
	$type = $value['value_type'] ?? 'text';
	if ( in_array( $type, [ 'yes', 'no' ], true ) ) {
		$yes = 'yes' === $type;
		?>
		<span class="compare-status <?= $yes ? 'compare-yes' : 'compare-no'; ?>" role="img" aria-label="<?= $yes ? 'دارد' : 'ندارد'; ?>"><?= icon( $yes ? 'check' : 'uncheck', 'compare-status-icon' ); ?></span>
		<?php
	} else {
		echo esc_html( (string) ( $value['feature_value'] ?? '—' ) );
	}
};
?>
<section class="compare-section" data-compare data-header-theme="<?= esc_attr( $theme ); ?>" style="<?= esc_attr( implode( '; ', $styles ) ); ?>">
	<div class="container">
		<div class="compare-layout">
			<div class="compare-corner" aria-hidden="true"></div>
			<?php foreach ( $plans as $index => $plan ) :
				$title = (string) $plan['plan_title'];
				$style = in_array( $plan['plan_style'] ?? '', [ 'plain', 'soft', 'featured', 'dark' ], true ) ? $plan['plan_style'] : 'plain';
				$link = is_array( $plan['plan_link'] ?? null ) ? $plan['plan_link'] : [];
				$price = trim( (string) ( $plan['plan_price'] ?? '' ) );
				$price = is_numeric( $price ) ? number_format_i18n( (float) $price ) : $price;
				?>
				<article class="compare-plan compare-plan-<?= esc_attr( $style ); ?>" data-compare-plan>
					<header class="compare-plan-header">
						<h3 data-compare-title><?= esc_html( $title ); ?></h3>
						<?php if ( $rows ) : ?>
							<button type="button" class="compare-more" data-compare-open aria-haspopup="dialog" aria-controls="<?= esc_attr( $modalId ); ?>">
								<span>مشاهده همه ویژگی‌ها</span>
								<?= icon( 'arrow-linear-2', 'compare-more-icon' ); ?>
							</button>
						<?php endif; ?>
					</header>
					<p class="compare-description"><?= esc_html( (string) ( $plan['plan_description'] ?? '' ) ); ?></p>
					<dl class="compare-summary">
						<?php foreach ( array_slice( $rows, 0, 3 ) as $row ) : ?>
							<div class="compare-feature"><dt><?= esc_html( $row['feature_label'] ); ?></dt><dd><?php $renderValue( $row['feature_values'][ $index ] ?? [] ); ?></dd></div>
						<?php endforeach; ?>
					</dl>
					<div class="compare-price" data-compare-price>
						<span class="compare-price-label">قیمت:</span>
						<span><strong><?= esc_html( $price ); ?></strong> <small><?= esc_html( (string) ( $plan['plan_period'] ?? '' ) ); ?></small></span>
					</div>
					<?php if ( ! empty( $link['url'] ) ) : ?>
						<a class="compare-order" data-compare-order href="<?= esc_url( $link['url'] ); ?>"<?php if ( '_blank' === ( $link['target'] ?? '' ) ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>><?= esc_html( ( $link['title'] ?? '' ) ?: 'ثبت سفارش' ); ?></a>
					<?php endif; ?>
					<template data-compare-details>
						<?php foreach ( $rows as $row ) : ?>
							<div class="compare-feature"><dt><?= esc_html( $row['feature_label'] ); ?></dt><dd><?php $renderValue( $row['feature_values'][ $index ] ?? [] ); ?></dd></div>
						<?php endforeach; ?>
					</template>
				</article>
			<?php endforeach; ?>
			<div class="compare-table-wrap">
				<table class="compare-table" aria-label="مقایسه ویژگی‌های پلن‌ها">
					<tbody>
						<?php foreach ( $rows as $row ) : ?>
							<tr><th scope="row"><?= esc_html( $row['feature_label'] ); ?></th>
								<?php foreach ( $plans as $index => $plan ) : ?>
									<td>
										<span class="compare-cell-label"><?= esc_html( (string) $plan['plan_title'] ); ?>: </span>
										<?php $renderValue( $row['feature_values'][ $index ] ?? [] ); ?>
									</td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<dialog class="compare-modal" id="<?= esc_attr( $modalId ); ?>" aria-labelledby="<?= esc_attr( $modalId ); ?>-title" data-compare-modal>
		<div class="compare-modal-content">
			<header class="compare-modal-header">
				<h3 id="<?= esc_attr( $modalId ); ?>-title" data-compare-modal-title></h3>
				<button type="button" class="compare-close" data-compare-close aria-label="بستن" autofocus><?= icon( 'close', 'compare-close-icon' ); ?></button>
			</header>
			<dl class="compare-modal-features" data-compare-modal-features tabindex="0"></dl>
			<div data-compare-modal-footer></div>
		</div>
	</dialog>
</section>
