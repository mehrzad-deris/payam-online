<?php
$sectionColor    = get_sub_field( 'section_color' ) ?: "";
$sectionStyle    = get_sub_field( 'section_style' ) ?: 'light';
$sectionTitle    = get_sub_field( 'section_title' );
$sectionTitleTag = get_sub_field( 'title_tag' ) ?: 'h2';
?>

<section data-header-theme="<?php echo esc_attr( $sectionStyle ); ?>" class="hero min-h-screen bottom-fade text-white relative overflow-hidden" style="background-color: <?= esc_attr( $sectionColor ) ?>">
    <div class="container relative z-2 lg:pt-50 pt-31">
        <?php if ( $sectionTitle ) : ?>
        <<?= esc_attr( $sectionTitleTag ) ?> class="whitespace-pre-line mb-10 text-center lg:text-[36px] font-bold lg:leading-14 text-[24px] leading-12"><?= esc_html( $sectionTitle ) ?></<?= esc_attr( $sectionTitleTag ) ?>>
    <?php endif; ?>

    <div class="flex flex-col gap-5 max-w-197.5 items-center">
        <div class="w-full">
            <input type="text" class="h-16 rounded-[30px] w-full" placeholder="دامنه موردنظر خود را وارد کنید ..." />
        </div>
    </div>
    </div>

    <picture class="pointer-events-none" aria-hidden="true">
        <source media="(max-width: 767px)" srcset="<?= esc_url( get_template_directory_uri() ) ?>/assets/images/hero-bg-mobile.webp">
        <source media="(min-width: 768px)" srcset="<?= esc_url( get_template_directory_uri() ) ?>/assets/images/hero-bg.webp">
        <img class="absolute inset-0 w-full object-cover top-0" src="<?= esc_url( get_template_directory_uri() ) ?>/assets/images/hero-bg.webp" alt="" fetchpriority="high" decoding="async">
    </picture>
    <canvas id="hero-stars" class="pointer-events-none absolute inset-0 z-1 h-full w-full" aria-hidden="true"></canvas>
</section>