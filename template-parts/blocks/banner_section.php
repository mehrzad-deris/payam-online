<?php
/**
 * Responsive linked banner section.
 */

defined( 'ABSPATH' ) || exit;

$bannerImage        = absint( get_sub_field( 'banner_image' ) );
$bannerMobileImage  = absint( get_sub_field( 'banner_mobile_image' ) );
$bannerLink         = get_sub_field( 'cta_link' ) ?: get_sub_field( 'banner_link' );
$bannerSize         = (string) ( get_sub_field( 'banner_size' ) ?: 'medium' );
$bannerText         = get_sub_field( 'banner_text' );
$bannerPrimaryCTA   = get_sub_field( 'primary_cta' );
$bannerSecondaryCTA = get_sub_field( 'secondary_cta' );
$paddingTopValue          = get_sub_field( 'padding_top' );
$paddingTopMobileValue    = get_sub_field( 'padding_top_mobile' );
$paddingBottomValue       = get_sub_field( 'padding_bottom' );
$paddingBottomMobileValue = get_sub_field( 'padding_bottom_mobile' );
$bannerPaddingTop          = is_numeric( $paddingTopValue ) ? '--banner-padding-top:' . absint( $paddingTopValue ) . 'px;' : '';
$bannerPaddingTopMobile    = is_numeric( $paddingTopMobileValue ) ? '--banner-padding-top-mobile:' . absint( $paddingTopMobileValue ) . 'px;' : '';
$bannerPaddingBottom       = is_numeric( $paddingBottomValue ) ? '--banner-padding-bottom:' . absint( $paddingBottomValue ) . 'px;' : '';
$bannerPaddingBottomMobile = is_numeric( $paddingBottomMobileValue ) ? '--banner-padding-bottom-mobile:' . absint( $paddingBottomMobileValue ) . 'px;' : '';

$transparentPixel = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

if ( ! in_array( $bannerSize, [ 'medium', 'full' ], true ) ) {
    $bannerSize = 'medium';
}

if ( ! $bannerImage ) {
    return;
}

$isFull            = 'full' === $bannerSize;
$desktopImageSize  = $isFull ? 'banner_full' : 'banner_desktop';
$desktopImage2x    = $isFull ? 'banner_full_x2' : 'banner_desktop_x2';
$mobileImageSize   = $isFull ? 'banner_full_mobile' : 'banner_mobile';
$mobileImage2x     = $isFull ? 'banner_full_mobile_x2' : 'banner_mobile_x2';
$desktopImage      = wp_get_attachment_image_src( $bannerImage, $desktopImageSize );
$desktopImage2xSrc = wp_get_attachment_image_src( $bannerImage, $desktopImage2x );
$mobileImage       = $bannerMobileImage ? wp_get_attachment_image_src( $bannerMobileImage, $mobileImageSize ) : false;
$mobileImage2xSrc  = $bannerMobileImage ? wp_get_attachment_image_src( $bannerMobileImage, $mobileImage2x ) : false;
$imageAlt          = (string) get_post_meta( $bannerImage, '_wp_attachment_image_alt', true );
$linkUrl           = is_array( $bannerLink ) ? ( $bannerLink['url'] ?? '' ) : $bannerLink;
$linkTarget        = is_array( $bannerLink ) ? ( $bannerLink['target'] ?? '_self' ) : '_self';
$linkTitle         = is_array( $bannerLink ) ? ( $bannerLink['title'] ?? '' ) : '';

if ( ! $desktopImage ) {
    return;
}

[ $desktopUrl, $desktopWidth, $desktopHeight ] = $desktopImage;
$desktop2x = $desktopImage2xSrc ? $desktopImage2xSrc[0] : false;
$mobileUrl = $mobileImage ? $mobileImage[0] : false;
$mobile2x  = $mobileImage2xSrc ? $mobileImage2xSrc[0] : false;
?>

<section class="banner-section banner-section-<?= esc_attr( $bannerSize )?>" style="<?= esc_attr( $bannerPaddingTop ) . ' ' . esc_attr( $bannerPaddingTopMobile ) . ' ' . esc_attr( $bannerPaddingBottom ) . ' ' . esc_attr( $bannerPaddingBottomMobile ); ?>" data-lazy-root>
    <div class="container banner-container flex justify-center">
        <<?= esc_url( $linkUrl ) ? 'a' : 'div' ?> class="banner-link relative block w-full <?= $isFull ? 'max-w-[1280px]' : 'max-w-[1144px]'; ?>" href="<?= esc_url( $linkUrl ); ?>" target="<?= esc_attr( $linkTarget ); ?>"<?= '_blank' === $linkTarget ? ' rel="noopener noreferrer"' : ''; ?><?= $linkTitle ? ' aria-label="' . esc_attr( $linkTitle ) . '"' : ''; ?>>

        <picture class="banner-picture block w-full <?= $isFull ? 'max-w-[1280px]' : 'max-w-[1144px]'; ?>">
            <?php if ( $mobileUrl ) : ?>
                <source
                        media="(max-width: 1024px)"
                        data-lazy-srcset="<?= esc_url( $mobileUrl ); ?><?= $mobile2x ? ', ' . esc_url( $mobile2x ) . ' 2x' : ''; ?>"
                        width="<?= esc_attr( $mobileImage[1] ); ?>"
                        height="<?= esc_attr( $mobileImage[2] ); ?>"
                >
            <?php endif; ?>
            <img
                    class="block h-auto w-full rounded-[32px] lg:rounded-[24px]"
                    src="<?= esc_attr( $transparentPixel ); ?>"
                    data-lazy-src="<?= esc_url( $desktopUrl ); ?>"
                    <?= $desktop2x ? 'data-lazy-srcset="' . esc_url( $desktopUrl ) . ' 1x, ' . esc_url( $desktop2x ) . ' 2x"' : ''; ?>
                    alt="<?= esc_attr( $imageAlt ); ?>"
                    width="<?= esc_attr( $desktopWidth ); ?>"
                    height="<?= esc_attr( $desktopHeight ); ?>"
                    decoding="async"
            >
        </picture>

        <span class="absolute right-0 left-0 top-0 bottom-0 flex flex-row items-center justify-center">
            <span class="flex lg:justify-between flex-col lg:flex-row items-center ps-10 z-1 text-white p-10 lg:p-5 gap-10 w-full">
                <?php if ( $bannerText ) : ?>
                    <span class="text-desktop-h5 text-center lg:text-start"><?= esc_html( $bannerText ) ?></span>
                <?php endif; ?>
                <span class="flex items-center gap-2.5">
                    <?php if ( $bannerSecondaryCTA ) : ?>
                        <a href="<?= esc_url( $bannerSecondaryCTA['url'] ) ?>" class="cta-link cta-btn-secondary cta-has-icon py-2.75!">
                            <span><?= esc_html( $bannerSecondaryCTA['title'] ) ?></span>
                            <?= icon( 'arrow-linear-2', 'w-5 h-5 fill-white hover:rotate-45 duration-200' ) ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( $bannerPrimaryCTA ) : ?>
                        <a href="<?= esc_url( $bannerPrimaryCTA['url'] ) ?>" class="cta-link cta-btn-primary cta-has-icon py-2.75!">
                            <span><?= esc_html( $bannerPrimaryCTA['title'] ) ?></span>
                            <?= icon( 'arrow-linear-2', 'w-5 h-5 fill-white hover:rotate-45 duration-200' ) ?>
                        </a>
                    <?php endif; ?>
                </span>
            </span>
        </span>

    </<?= esc_url( $linkUrl ) ? 'a' : 'span' ?>>
    </div>
</section>
