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
        ] );

$icon_id   = absint( $args['icon'] );
$icon_alt  = (string) $args['icon_alt'];
$title     = (string) $args['title'];
$subtitle  = (string) $args['subtitle'];
$title_tag = strtolower( (string) $args['title_tag'] );

if ( ! in_array( $title_tag, [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ], true ) ) {
    $title_tag = 'h2';
}

if ( ! $icon_id && '' === $title && '' === $subtitle ) {
    return;
}

$wrapper_class = trim( 'section-heading flex flex-col items-center text-center lg:gap-4 gap-2 mb-10' . (string) $args['class'] );

$icon_class = trim( 'w-8 h-8 object-contain ' . (string) $args['icon_class'] );

$icon_attributes = [
        'class'    => $icon_class,
        'decoding' => 'async',
        'loading'  => 'lazy',
];

if ( '' !== $icon_alt ) {
    $icon_attributes['alt'] = $icon_alt;
}

$title_class = trim( 'text-[20px] lg:text-[32px] lg:leading-[40px] leading-tight font-semibold ' . (string) $args['title_class'] );

$subtitle_class = trim( 'max-w-180 text-[14px] lg:text-[16px] leading-7 text-description ' . (string) $args['subtitle_class'] );
?>

<div class="<?= esc_attr( $wrapper_class ); ?>">
    <?php if ( $icon_id ) : ?>
        <div class="section-heading__icon">
            <?= wp_get_attachment_image( $icon_id, 'full', false, $icon_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>
        </div>
    <?php endif; ?>

    <?php if ( '' !== $title ) : ?>
    <<?= esc_attr( $title_tag ); ?> class="section-heading__title <?= esc_attr( $title_class ); ?>">
    <?= esc_html( $title ); ?>
</<?= esc_attr( $title_tag ); ?>>
<?php endif; ?>

<?php if ( '' !== $subtitle ) : ?>
    <p class="section-heading__subtitle whitespace-pre-line <?= esc_attr( $subtitle_class ); ?>"><?= esc_html( $subtitle ); ?></p>
<?php endif; ?>
</div>
