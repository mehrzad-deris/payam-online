<?php
/**
 * Standalone section heading block.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor             = (string) ( get_sub_field( 'section_color' ) ?: '' );
$sectionStyle             = (string) ( get_sub_field( 'section_style' ) ?: 'light' );
$sectionIcon              = absint( get_sub_field( 'section_icon' ) );
$sectionTitle             = (string) ( get_sub_field( 'section_title' ) ?: '' );
$sectionTitleTag          = (string) ( get_sub_field( 'title_tag' ) ?: 'h2' );
$sectionSubtitle          = (string) ( get_sub_field( 'section_subtitle' ) ?: '' );
$showShapes               = (bool) get_sub_field( 'section_heading_shapes' );
$showCenterGradient       = (bool) get_sub_field( 'section_heading_center_gradient' );
$paddingTopValue          = get_sub_field( 'padding_top' );
$paddingTopMobileValue    = get_sub_field( 'padding_top_mobile' );
$paddingBottomValue       = get_sub_field( 'padding_bottom' );
$paddingBottomMobileValue = get_sub_field( 'padding_bottom_mobile' );
$sectionStyles           = [];

if ( '' === $sectionTitle && '' === $sectionSubtitle && ! $sectionIcon ) {
	return;
}

if ( '' !== $sectionColor ) {
	$sectionStyles[] = 'background-color: ' . $sectionColor;
}

if ( is_numeric( $paddingTopValue ) ) {
	$sectionStyles[] = '--section-heading-padding-top: ' . absint( $paddingTopValue ) . 'px';
}

if ( is_numeric( $paddingTopMobileValue ) ) {
	$sectionStyles[] = '--section-heading-padding-top-mobile: ' . absint( $paddingTopMobileValue ) . 'px';
}

if ( is_numeric( $paddingBottomValue ) ) {
	$sectionStyles[] = '--section-heading-padding-bottom: ' . absint( $paddingBottomValue ) . 'px';
}

if ( is_numeric( $paddingBottomMobileValue ) ) {
	$sectionStyles[] = '--section-heading-padding-bottom-mobile: ' . absint( $paddingBottomMobileValue ) . 'px';
}
?>

<section
	class="section-heading-section section-heading-section-<?= esc_attr( $sectionStyle ); ?><?= $showCenterGradient ? ' section-heading-section-center-gradient' : ''; ?> overflow-hidden"
	data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
	<?= $sectionStyles ? 'style="' . esc_attr( implode( '; ', $sectionStyles ) ) . '"' : ''; ?>
>
	<div class="container">
		<?php
		section_heading( [
			'icon'        => $sectionIcon,
			'title'       => $sectionTitle,
			'title_tag'   => $sectionTitleTag,
			'subtitle'    => $sectionSubtitle,
			'class'       => 'mb-0!',
			'title_class' => 'dark' === $sectionStyle ? 'text-white' : '',
			'show_shapes' => $showShapes,
		] );
		?>
	</div>
</section>
