<?php
/**
 * FAQ accordion section.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor      = get_sub_field( 'section_color' ) ?: '';
$sectionStyle      = (string) ( get_sub_field( 'section_style' ) ?: 'light' );
$faqRows           = get_sub_field( 'faq_items' );
$ctaText           = (string) ( get_sub_field( 'faq_cta_text' ) ?: '' );
$ctaLink           = get_sub_field( 'faq_cta_link' );
$sectionStyles     = [];
$faqItems          = [];
$faqFromPosts      = 'cpt' === get_sub_field( 'faq_source' );
$faqCategories     = [];
$faqShowCategories = $faqFromPosts && (bool) get_sub_field( 'faq_show_categories' );
$faqPerPage         = min( 30, max( 1, absint( get_sub_field( 'faq_items_per_page' ) ?: 8 ) ) );
$faqHasMore         = false;

if ( '' !== $sectionColor ) {
    $sectionStyles[] = 'background-color: ' . $sectionColor;
}

foreach ( [ 'padding_top', 'padding_top_mobile', 'padding_bottom', 'padding_bottom_mobile' ] as $name ) {
    $value = get_sub_field( $name );
    if ( is_numeric( $value ) ) {
        $sectionStyles[] = '--faq-' . str_replace( '_', '-', $name ) . ': ' . absint( $value ) . 'px';
    }
}

if ( $faqFromPosts ) {
    $catalogue = payam_faq_catalogue( $faqPerPage );
    $faqItems = $catalogue['items'];
    $faqHasMore = $catalogue['total_pages'] > 1;
    $faqCategories = $faqShowCategories ? payam_faq_categories() : [];
} elseif ( is_array( $faqRows ) ) {
    $faqItems = array_values( array_filter( $faqRows, static fn( $item ): bool => is_array( $item ) && '' !== trim( (string) ( $item['question'] ?? '' ) ) ) );
}

$ctaLink   = is_array( $ctaLink ) ? $ctaLink : [];
$ctaUrl    = (string) ( $ctaLink['url'] ?? '' );
$ctaTitle  = (string) ( $ctaLink['title'] ?? '' );
$ctaTarget = (string) ( $ctaLink['target'] ?? '' );
?>

<section
        class="faq-section faq-section-<?= esc_attr( $sectionStyle ); ?>"
        data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
        data-faq
        <?= $faqFromPosts ? 'data-faq-endpoint="' . esc_url( rest_url( 'payam/v1/faqs' ) ) . '" data-faq-per-page="' . esc_attr( $faqPerPage ) . '"' : ''; ?>
        <?= $sectionStyles ? 'style="' . esc_attr( implode( '; ', $sectionStyles ) ) . '"' : ''; ?>
>
    <div class="container faq-container ">
        <?php if ( ! empty( $faqItems ) ) : ?>
            <div class="faq-list-block<?= $faqCategories ? ' faq-with-categories' : ''; ?>">
                <?php if ( $faqCategories ) : ?>
                    <aside class="faq-categories" aria-label="دسته‌بندی سوالات">
                        <h3>دسته‌بندی سوالات</h3>
                        <nav aria-label="فیلتر سوالات متداول" class="text-body-3">
                            <button type="button" data-faq-category="all" aria-pressed="true"><span class="flex items-center gap-2"><?= icon('grid', 'w-6 h-6') ?> همه سوالات</span><span><?= icon('arrow-linear-2', 'w-4 h-4') ?></span></button>
                            <?php foreach ( $faqCategories as $categoryId => $categoryName ) : ?>
                                <button type="button" data-faq-category="<?= esc_attr( $categoryId ); ?>" aria-pressed="false"><span class="flex items-center gap-2"><?= icon('grid', 'w-6 h-6') ?><?= esc_html( $categoryName ); ?></span><span><?= icon('arrow-linear-2', 'w-4 h-4') ?></span></button>
                            <?php endforeach; ?>
                        </nav>
                    </aside>
                <?php endif; ?>
                <div class="faq-list">
                    <div data-faq-items><?= payam_render_faq_items( $faqItems ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by renderer. ?></div>

                    <?php if ( $faqFromPosts ) : ?>
                        <button type="button" class="faq-load-more" data-faq-load-more data-next-page="2"<?= $faqHasMore ? '' : ' hidden'; ?>>مشاهده سوالات بیشتر</button>
                    <?php endif; ?>

                    <?php if ( '' !== $ctaText || '' !== $ctaUrl ) : ?>
                        <div class="faq-cta shadow-mellow">
                            <?php if ( '' !== $ctaText ) : ?>
                                <p class="faq-cta-text text-body-mobile-2 md:text-body-2"><?= esc_html( $ctaText ); ?></p>
                            <?php endif; ?>

                            <?php if ( '' !== $ctaUrl ) : ?>
                                <a
                                        class="cta-link cta-btn-primary px-4! lg:px-8! text-body-mobile-3! md:text-body-3!"
                                        href="<?= esc_url( $ctaUrl ); ?>"
                                        <?= $ctaTarget ? 'target="' . esc_attr( $ctaTarget ) . '"' : ''; ?>
                                        <?= '_blank' === $ctaTarget ? 'rel="noopener noreferrer"' : ''; ?>
                                ><?= esc_html( $ctaTitle ); ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
