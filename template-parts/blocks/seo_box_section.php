<?php
/**
 * Expandable SEO content section.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor      = get_sub_field( 'section_color' ) ?: '';
$sectionStyle      = (string) ( get_sub_field( 'section_style' ) ?: 'light' );
$sectionContent    = get_sub_field( 'seo_content' );
$collapsedLines    = max( 1, min( 20, absint( get_sub_field( 'seo_collapsed_lines' ) ?: 6 ) ) );
$sectionStyles     = [];
$contentId         = wp_unique_id( 'seo-box-content-' );

if ( '' !== $sectionColor ) {
    $sectionStyles[] = 'background-color: ' . $sectionColor;
}

foreach ( [ 'padding_top', 'padding_top_mobile', 'padding_bottom', 'padding_bottom_mobile' ] as $fieldName ) {
    $fieldValue = get_sub_field( $fieldName );
    if ( is_numeric( $fieldValue ) ) {
        $sectionStyles[] = '--seo-box-' . str_replace( '_', '-', $fieldName ) . ': ' . absint( $fieldValue ) . 'px';
    }
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
