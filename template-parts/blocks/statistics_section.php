<?php
/**
 * Statistics section block.
 */

defined( 'ABSPATH' ) || exit;

$sectionStyle            = (string) ( get_sub_field( 'section_style' ) ?: 'dark' );
$paddingTopValue          = get_sub_field( 'padding_top' );
$paddingTopMobileValue    = get_sub_field( 'padding_top_mobile' );
$paddingBottomValue       = get_sub_field( 'padding_bottom' );
$paddingBottomMobileValue = get_sub_field( 'padding_bottom_mobile' );
$statisticRows            = get_sub_field( 'statistics_items' );
$sectionStyles            = [];
$statistics               = [];

if ( is_numeric( $paddingTopValue ) ) {
	$sectionStyles[] = '--statistics-padding-top: ' . absint( $paddingTopValue ) . 'px';
}

if ( is_numeric( $paddingTopMobileValue ) ) {
	$sectionStyles[] = '--statistics-padding-top-mobile: ' . absint( $paddingTopMobileValue ) . 'px';
}

if ( is_numeric( $paddingBottomValue ) ) {
	$sectionStyles[] = '--statistics-padding-bottom: ' . absint( $paddingBottomValue ) . 'px';
}

if ( is_numeric( $paddingBottomMobileValue ) ) {
	$sectionStyles[] = '--statistics-padding-bottom-mobile: ' . absint( $paddingBottomMobileValue ) . 'px';
}

if ( is_array( $statisticRows ) ) {
	$statistics = array_values(
		array_slice(
			array_filter(
				$statisticRows,
				static function ( $item ): bool {
					if ( ! is_array( $item ) ) {
						return false;
					}

					return is_numeric( $item['counter_value'] ?? null )
						|| '' !== trim( (string) ( $item['static_value'] ?? '' ) )
						|| '' !== trim( (string) ( $item['counter_label'] ?? '' ) );
				}
			),
			0,
			6
		)
	);
}

if ( empty( $statistics ) ) {
	return;
}
?>

<section
	class="statistics-section statistics-section-<?= esc_attr( $sectionStyle ); ?>"
	data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
	data-feature-module
	<?= $sectionStyles ? 'style="' . esc_attr( implode( '; ', $sectionStyles ) ) . '"' : ''; ?>
>
	<div class="container">
		<div class="statistics-list">
			<?php foreach ( $statistics as $statistic ) :
				$rawValue   = $statistic['counter_value'] ?? '';
				$hasCounter = is_numeric( $rawValue );
				$prefix     = (string) ( $statistic['counter_prefix'] ?? '' );
				$suffix     = (string) ( $statistic['counter_suffix'] ?? '' );
				$staticValue = (string) ( $statistic['static_value'] ?? '' );
				$label       = (string) ( $statistic['counter_label'] ?? '' );
				$decimals    = 0;

				if ( $hasCounter ) {
					$valueParts = explode( '.', (string) $rawValue, 2 );
					$decimals   = isset( $valueParts[1] ) ? strlen( rtrim( $valueParts[1], '0' ) ) : 0;
				}
				?>
				<div class="statistics-item">
					<div class="statistics-value" dir="ltr">
						<?php if ( '' !== $prefix ) : ?><span><?= esc_html( $prefix ); ?></span><?php endif; ?>
						<?php if ( $hasCounter ) : ?>
							<span data-counter-target="<?= esc_attr( $rawValue ); ?>" data-counter-decimals="<?= esc_attr( $decimals ); ?>"><?= esc_html( number_format_i18n( (float) $rawValue, $decimals ) ); ?></span>
						<?php elseif ( '' !== $staticValue ) : ?>
							<span><?= esc_html( $staticValue ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== $suffix ) : ?><span><?= esc_html( $suffix ); ?></span><?php endif; ?>
					</div>

					<?php if ( '' !== $label ) : ?>
						<p class="statistics-label"><?= esc_html( $label ); ?></p>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
