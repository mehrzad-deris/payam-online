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
    $catalogue = payam_faq_catalogue();
    $faqItems = $catalogue['items'];
    $faqCategories = $faqShowCategories ? $catalogue['categories'] : [];
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
        <?= $sectionStyles ? 'style="' . esc_attr( implode( '; ', $sectionStyles ) ) . '"' : ''; ?>
>
    <div class="container faq-container ">
        <?php if ( ! empty( $faqItems ) ) : ?>
            <div class="faq-list-block<?= $faqCategories ? ' faq-with-categories' : ''; ?>">
                <?php if ( $faqCategories ) : ?>
                    <aside class="faq-categories" aria-label="دسته‌بندی سوالات">
                        <h3>دسته‌بندی سوالات</h3>
                        <nav aria-label="فیلتر سوالات متداول">
                            <button type="button" data-faq-category="all" aria-pressed="true">همه سوالات</button>
                            <?php foreach ( $faqCategories as $categoryId => $categoryName ) : ?>
                                <button type="button" data-faq-category="<?= esc_attr( $categoryId ); ?>" aria-pressed="false"><?= esc_html( $categoryName ); ?></button>
                            <?php endforeach; ?>
                        </nav>
                    </aside>
                <?php endif; ?>
                <div class="faq-list">
                    <?php foreach ( $faqItems as $faqIndex => $faqItem ) :
                        $isOpen = 0 === $faqIndex;
                        $buttonId = wp_unique_id( 'faq-button-' );
                        $panelId = wp_unique_id( 'faq-panel-' );
                        ?>
                        <article class="faq-item<?= $isOpen ? ' is-open' : ''; ?> shadow-mellow" data-faq-item<?= $faqFromPosts ? ' data-faq-categories="' . esc_attr( implode( ' ', $faqItem['categories'] ) ) . '"' : ''; ?>>
                            <h3 class="faq-question">
                                <button
                                        type="button"
                                        class="faq-button"
                                        id="<?= esc_attr( $buttonId ); ?>"
                                        data-faq-toggle
                                        aria-expanded="<?= $isOpen ? 'true' : 'false'; ?>"
                                        aria-controls="<?= esc_attr( $panelId ); ?>"
                                >
                                    <span class="faq-question-text text-body-mobile-2 md:text-body-2"><?= esc_html( $faqItem['question'] ); ?></span>
                                    <span class="faq-symbol" data-faq-symbol aria-hidden="true"><?= $isOpen ? icon( 'minus', 'fill-white w-6 h-6' ) : icon( 'plus', 'stroke-blue-primary w-6 h-6' ); ?></span>
                                </button>
                            </h3>

                            <div
                                    class="faq-answer"
                                    id="<?= esc_attr( $panelId ); ?>"
                                    data-faq-panel
                                    role="region"
                                    aria-labelledby="<?= esc_attr( $buttonId ); ?>"
                                    <?= $isOpen ? '' : 'hidden'; ?>
                            >
                                <div class="faq-answer-content text-description md:text-caption text-caption-mobile"><?= wp_kses_post( $faqItem['answer'] ?? '' ); ?></div>
                            </div>
                        </article>
                    <?php endforeach; ?>

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
