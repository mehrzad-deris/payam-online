<?php
/**
 * Global infrastructure section block.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor        = get_sub_field( 'section_color' ) ?: '#07041d';
$sectionStyle        = get_sub_field( 'section_style' ) ?: 'dark';
$sectionIcon         = absint( get_sub_field( 'section_icon' ) );
$sectionTitle        = get_sub_field( 'section_title' ) ?: '';
$sectionTitleTag     = get_sub_field( 'title_tag' ) ?: 'h2';
$sectionSubtitle     = get_sub_field( 'section_subtitle' ) ?: '';
$sectionAboutUsImage = absint( get_sub_field( 'about_image' ) );
$sectionAboutUsDesc  = get_sub_field( 'about_us_description' );
$sectionAboutLink_1  = get_sub_field( 'link_1' );
$sectionAboutLink_2  = get_sub_field( 'link_2' );

if ( $sectionAboutUsImage ) {
    $about_desktop1x = wp_get_attachment_image_src( $sectionAboutUsImage, 'about_image_section' );
    $about_desktop2x = wp_get_attachment_image_url( $sectionAboutUsImage, 'about_image_section_2x' );
    $about_mobile1x  = wp_get_attachment_image_url( $sectionAboutUsImage, 'about_image_section_mobile' );
    $about_mobile2x  = wp_get_attachment_image_url( $sectionAboutUsImage, 'about_image_section_mobile_2x' );
}

?>

<section
        class="about-section about-section-<?= esc_attr( $sectionStyle ); ?>"
        data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
        data-feature-module
        style="background-color: <?= esc_attr( $sectionColor ); ?>"
>
    <div class="container">
        <?php
        section_heading( [
                'icon'        => $sectionIcon,
                'title'       => $sectionTitle,
                'title_tag'   => $sectionTitleTag,
                'subtitle'    => $sectionSubtitle,
                'title_class' => $sectionStyle === 'dark' ? 'text-white' : '',
                'class'       => 'relative z-1',
        ] );
        ?>

        <div>
            <picture class="about-section-image">
                <?php if ( $about_mobile1x ) : ?>
                    <source media="(max-width: 767px)"
                            srcset="<?= esc_url( $about_mobile1x ); ?> 1x<?= $about_mobile2x ? ', ' . esc_url( $about_mobile2x ) . ' 2x' : ''; ?>"
                    >
                <?php endif; ?>
                <img src="<?= esc_url( $about_desktop1x[0] ); ?>"
                        <?= $about_desktop2x ? 'srcset="' . esc_url( $about_desktop1x[0] ) . ' 1x, ' . esc_url( $about_desktop2x ) . ' 2x"' : ''; ?>
                     alt="<?= esc_attr( $sectionTitle ) ?>"
                     width="1280" height="400"
                     loading="lazy" decoding="async"
                     class="rounded-[26px]"
                >
            </picture>
        </div>

        <?php if ( $sectionAboutUsImage ) : ?>
            <p class="<?= $sectionStyle === 'dark' ? 'text-white' : '' ?> leading-[35px] lg:px-37 md:px-10 md:text-center mt-10"><?= $sectionAboutUsDesc ?></p>
        <?php endif; ?>

        <?php if ( $sectionAboutLink_1 || $sectionAboutLink_2 ) : ?>
            <div class="about-section-links">
                <?php if ( $sectionAboutLink_1 ) : ?>
                    <a href="<?= esc_html($sectionAboutLink_1['url']) ?: '' ?>" class="primary-cta">
                        <?= esc_html($sectionAboutLink_1['title']) ?: '' ?>
                        <span class="icon"><?= icon('arrow-linear') ?></span>
                    </a>
                <?php endif; ?>
                <?php if ( $sectionAboutLink_2 ) : ?>
                    <a href="<?= esc_html($sectionAboutLink_2['url']) ?: '' ?>" class="secondary-cta">
                        <?= esc_html($sectionAboutLink_2['title']) ?: '' ?>
                        <span class="icon"><?= icon('arrow-linear') ?></span>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <img class="about-us-bg" src="<?= get_template_directory_uri() ?>/assets/images/about-us-bg.webp" alt="">
</section>
