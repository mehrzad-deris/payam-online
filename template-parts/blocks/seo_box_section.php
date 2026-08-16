<?php
/**
 * Expandable SEO content section.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor      = get_sub_field( 'section_color' ) ?: '';
$sectionStyle      = (string) ( get_sub_field( 'section_style' ) ?: 'light' );
$sectionTitle      = (string) ( get_sub_field( 'section_title' ) ?: '' );
$sectionTitleTag   = strtolower( (string) ( get_sub_field( 'title_tag' ) ?: 'h2' ) );
$sectionContent    = get_sub_field( 'seo_content' );
$collapsedLines    = max( 1, min( 20, absint( get_sub_field( 'seo_collapsed_lines' ) ?: 6 ) ) );
$marginTopField    = get_sub_field( 'section_margin_top' );
$marginBottomField = get_sub_field( 'section_margin_bottom' );
$sectionStyles     = [];
$contentId         = wp_unique_id( 'seo-box-content-' );

if ( ! in_array( $sectionTitleTag, [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ], true ) ) {
    $sectionTitleTag = 'h2';
}

if ( '' !== $sectionColor ) {
    $sectionStyles[] = 'background-color: ' . $sectionColor;
}

if ( is_numeric( $marginTopField ) ) {
    $sectionStyles[] = 'margin-top: ' . max( - 1000, min( 1000, (int) $marginTopField ) ) . 'px';
}

if ( is_numeric( $marginBottomField ) ) {
    $sectionStyles[] = 'margin-bottom: ' . max( - 1000, min( 1000, (int) $marginBottomField ) ) . 'px';
}
?>

<section
        class="seo-box-section seo-box-section-<?= esc_attr( $sectionStyle ); ?>"
        data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
        data-seo-box
        data-collapsed-lines="<?= esc_attr( $collapsedLines ); ?>"
        <?= $sectionStyles ? 'style="' . esc_attr( implode( '; ', $sectionStyles ) ) . '"' : ''; ?>
>
    <div class="container seo-box-container flex justify-center">
        <div class="seco-box-block">
            <?php if ( '' !== $sectionTitle ) : ?>
            <<?= esc_attr( $sectionTitleTag ); ?> class="seo-box-title md:text-desktop-h2 text-mobile-h2"><?= esc_html( $sectionTitle ); ?></<?= esc_attr( $sectionTitleTag ); ?>>
        <?php endif; ?>

        <?php if ( $sectionContent ) : ?>
            <div class="seo-box-content md:text-body-3 text-body-mobile-3" id="<?= esc_attr( $contentId ); ?>" data-seo-box-content>
                <?= wp_kses_post( $sectionContent ); ?>
            </div>

            <div class="flex justify-center">
                <button
                        type="button"
                        class="seo-box-toggle"
                        data-seo-box-toggle
                        data-collapsed-label="مطالعه بیشتر"
                        data-expanded-label="نمایش کمتر"
                        aria-expanded="false"
                        aria-controls="<?= esc_attr( $contentId ); ?>"
                        hidden
                >
                    <span data-seo-box-label>مطالعه بیشتر</span>
                    <span class="seo-box-toggle-icon" data-seo-box-icon aria-hidden="true">
					<?= icon( 'arrow-down', 'w-5 h-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped    ?>
				</span>
                </button>
            </div>

        <?php endif; ?>
    </div>
    </div>
</section>
