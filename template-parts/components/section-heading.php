<?php
$title    = $args['title'] ?? '';
$titleEn  = $args['titleEn'] ?? '';
$titleTag = $args['titleTag'] ?? 'h2';
$classes  = $args['_classes'] ?? [];
?>

<div class="<?= esc_attr( $classes['wrapper'] ?? '' ); ?>">
    <div class="flex flex-col items-center gap-3">
        <?php if ( ! empty( $title ) ) : ?>
        <span class="flex justify-center mb-0.25" aria-hidden="true"><?= icon( 'section-icon', 'w-[24px] lg:w-[39px] h-[24px] lg:h-[39px]' ) ?></span>
        <<?= esc_attr( $titleTag ); ?> class=" <?= esc_attr( $classes['title'] ?? '' ); ?>">
        <?= esc_html( $title ); ?>
    </<?= esc_attr( $titleTag ); ?>>
    <?php endif; ?>
    <?php if ( ! empty( $titleEn ) ) : ?>
        <div class="<?= esc_attr( $classes['titleEn'] ?? '' ); ?>">
            <?= esc_html( $titleEn ); ?>
        </div>
    <?php endif; ?>
</div>
</div>