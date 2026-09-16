<?php
/**
 * Section heading component.
 *
 * Available arguments:
 * - icon: WordPress attachment ID.
 * - icon_alt: Optional fallback alternative text.
 * - title: Heading text.
 * - subtitle: Supporting text.
 * - title_tag: h1 through h6. Defaults to h2.
 * - class: Additional wrapper classes.
 * - icon_class: Additional icon classes.
 * - title_class: Additional title classes.
 * - subtitle_class: Additional subtitle classes.
 * - show_shapes: Whether to render the decorative side shapes.
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? [], [
        'icon'           => '',
        'icon_alt'       => '',
        'title'          => '',
        'subtitle'       => '',
        'title_tag'      => 'h2',
        'class'          => '',
        'icon_class'     => '',
        'title_class'    => '',
        'subtitle_class' => '',
        'show_shapes'    => false,
] );

$icon_id     = absint( $args['icon'] );
$icon_alt    = (string) $args['icon_alt'];
$title       = (string) $args['title'];
$subtitle    = (string) $args['subtitle'];
$show_shapes = (bool) $args['show_shapes'];
$title_tag   = strtolower( (string) $args['title_tag'] );

if ( ! in_array( $title_tag, [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ], true ) ) {
    $title_tag = 'h2';
}

if ( ! $icon_id && '' === $title && '' === $subtitle ) {
    return;
}

$wrapper_class = trim( 'section-heading relative flex flex-col items-center text-center lg:gap-4 gap-2 mb-10 ' . (string) $args['class'] );

$icon_class = trim( 'w-8 h-8 object-contain ' . (string) $args['icon_class'] );

$icon_attributes = [
        'class'    => $icon_class,
        'decoding' => 'async',
        'loading'  => 'lazy',
];

if ( '' !== $icon_alt ) {
    $icon_attributes['alt'] = $icon_alt;
}

$title_class = trim( 'whitespace-pre-line text-mobile-h1 md:text-desktop-h2 text-neutral-900 ' . (string) $args['title_class'] );

$subtitle_class = trim( 'max-w-180 text-body-mobile-3 md:text-desktop-h6 text-neutral-500 ' . (string) $args['subtitle_class'] );
?>

<div class="<?= esc_attr( $wrapper_class ); ?>">
    <?php if ( $show_shapes ) : ?>
        <span class="gradient-shape shape-right top-2" aria-hidden="true">
            <svg class="rounded-shape" width="102" height="94" viewBox="0 0 102 94" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 94C45.0207 20.3231 80.8966 0 102 0V94H0Z" fill="url(#paint0_linear_6009_3594)"/>
                <defs>
                    <linearGradient id="paint0_linear_6009_3594" x1="255.754" y1="0" x2="255.754" y2="94" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#3B56FF" stop-opacity="0.14"/>
                        <stop offset="1" stop-color="#3B56FF" stop-opacity="0"/>
                    </linearGradient>
                </defs>
            </svg>
        </span>
        <span class="gradient-shape shape-left top-2" aria-hidden="true">
            <svg class="rounded-shape" width="102" height="94" viewBox="0 0 102 94" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 94C45.0207 20.3231 80.8966 0 102 0V94H0Z" fill="url(#paint0_linear_6009_3594)"/>
                <defs>
                    <linearGradient id="paint0_linear_6009_3594" x1="255.754" y1="0" x2="255.754" y2="94" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#3B56FF" stop-opacity="0.14"/>
                        <stop offset="1" stop-color="#3B56FF" stop-opacity="0"/>
                    </linearGradient>
                </defs>
            </svg>
        </span>
    <?php endif; ?>

    <?php if ( $icon_id ) : ?>
        <div class="section-heading__icon">
            <?= wp_get_attachment_image( $icon_id, 'full', false, $icon_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped   ?>
        </div>
    <?php endif; ?>

    <?php if ( '' !== $title ) : ?>
    <<?= esc_attr( $title_tag ); ?> class="section-heading__title <?= esc_attr( $title_class ); ?>"><?= esc_html( $title ); ?></<?= esc_attr( $title_tag ); ?>>
<?php endif; ?>

<?php if ( '' !== $subtitle ) : ?>
    <p class="section-heading__subtitle whitespace-pre-line <?= esc_attr( $subtitle_class ); ?>"><?= esc_html( $subtitle ); ?></p>
<?php endif; ?>
</div>
