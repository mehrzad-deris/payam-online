<?php
/**
 * FAQ accordion section.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor      = get_sub_field( 'section_color' ) ?: '';
$sectionStyle      = (string) ( get_sub_field( 'section_style' ) ?: 'light' );
$sectionIcon       = absint( get_sub_field( 'section_icon' ) );
$sectionTitle      = (string) ( get_sub_field( 'section_title' ) ?: '' );
$sectionTitleTag   = (string) ( get_sub_field( 'title_tag' ) ?: 'h2' );
$sectionSubtitle   = (string) ( get_sub_field( 'section_subtitle' ) ?: '' );
$marginTopField    = get_sub_field( 'section_margin_top' );
$marginBottomField = get_sub_field( 'section_margin_bottom' );
$faqRows           = get_sub_field( 'faq_items' );
$ctaText           = (string) ( get_sub_field( 'faq_cta_text' ) ?: '' );
$ctaLink           = get_sub_field( 'faq_cta_link' );
$sectionStyles     = [];
$faqItems          = [];

if ( '' !== $sectionColor ) {
    $sectionStyles[] = 'background-color: ' . $sectionColor;
}

if ( is_numeric( $marginTopField ) ) {
    $sectionStyles[] = 'margin-top: ' . max( - 1000, min( 1000, (int) $marginTopField ) ) . 'px';
}

if ( is_numeric( $marginBottomField ) ) {
    $sectionStyles[] = 'margin-bottom: ' . max( - 1000, min( 1000, (int) $marginBottomField ) ) . 'px';
}

if ( is_array( $faqRows ) ) {
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
        <?php
        section_heading( [
                'icon'        => $sectionIcon,
                'title'       => $sectionTitle,
                'title_tag'   => $sectionTitleTag,
                'subtitle'    => $sectionSubtitle,
                'title_class' => 'dark' === $sectionStyle ? 'text-white' : '',
        ] );
        ?>

        <?php if ( ! empty( $faqItems ) ) : ?>
            <div class="faq-list-block">
                <div class="faq-list">
                    <?php foreach ( $faqItems as $faqIndex => $faqItem ) :
                        $isOpen = 0 === $faqIndex;
                        $buttonId = wp_unique_id( 'faq-button-' );
                        $panelId = wp_unique_id( 'faq-panel-' );
                        ?>
                        <article class="faq-item<?= $isOpen ? ' is-open' : ''; ?> shadow-mellow" data-faq-item>
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
                                        class="faq-cta-link cta-link px-8! text-body-mobile-3! md:text-body-3!"
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
