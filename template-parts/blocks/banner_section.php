<?php
/** Responsive content banner with either a full-card link or inner CTAs. */

defined( 'ABSPATH' ) || exit;

$bannerImage        = absint( get_sub_field( 'banner_image' ) );
$bannerMobileImage  = absint( get_sub_field( 'banner_mobile_image' ) );
$bannerLink         = get_sub_field( 'cta_link' ) ?: get_sub_field( 'banner_link' );
$bannerSize         = (string) ( get_sub_field( 'banner_size' ) ?: 'full' );
$bannerStyle        = (string) ( get_sub_field( 'banner_style' ) ?: 'style_1' );
$bannerTitle        = trim( (string) get_sub_field( 'banner_text' ) );
$bannerContent      = trim( (string) get_sub_field( 'banner_content' ) );
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

if ( ! in_array( $bannerStyle, [ 'style_1', 'style_2' ], true ) ) {
    $bannerStyle = 'style_1';
}

if ( ! $bannerImage || '' === $bannerTitle ) {
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
$linkTarget        = is_array( $bannerLink ) && ! empty( $bannerLink['target'] ) ? (string) $bannerLink['target'] : '_self';
$linkTitle         = is_array( $bannerLink ) ? ( $bannerLink['title'] ?? '' ) : '';
$hasPrimaryCTA     = is_array( $bannerPrimaryCTA ) && ! empty( $bannerPrimaryCTA['url'] );
$hasSecondaryCTA   = is_array( $bannerSecondaryCTA ) && ! empty( $bannerSecondaryCTA['url'] );
$hasInnerCtas      = $hasPrimaryCTA || $hasSecondaryCTA;
$wrapperTag        = $linkUrl && ! $hasInnerCtas ? 'a' : 'div';

if ( ! $desktopImage ) {
    return;
}

[ $desktopUrl, $desktopWidth, $desktopHeight ] = $desktopImage;
$desktop2x = $desktopImage2xSrc ? $desktopImage2xSrc[0] : false;
$mobileUrl = $mobileImage ? $mobileImage[0] : false;
$mobile2x  = $mobileImage2xSrc ? $mobileImage2xSrc[0] : false;
?>

<section class="banner-section banner-section-<?= esc_attr( $bannerSize ); ?> banner-section-<?= esc_attr( $bannerStyle ); ?>" style="<?= esc_attr( $bannerPaddingTop ) . ' ' . esc_attr( $bannerPaddingTopMobile ) . ' ' . esc_attr( $bannerPaddingBottom ) . ' ' . esc_attr( $bannerPaddingBottomMobile ); ?>" data-lazy-root>
    <div class="container banner-container flex justify-center">
        <<?= $wrapperTag; ?> class="banner-link relative block w-full <?= $isFull ? 'max-w-[1280px]' : 'max-w-[1144px]'; ?>"<?php if ( 'a' === $wrapperTag ) : ?> href="<?= esc_url( $linkUrl ); ?>" target="<?= esc_attr( $linkTarget ); ?>"<?= '_blank' === $linkTarget ? ' rel="noopener noreferrer"' : ''; ?><?= $linkTitle || $bannerTitle ? ' aria-label="' . esc_attr( $linkTitle ?: $bannerTitle ) . '"' : ''; ?><?php endif; ?>>

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

        <div class="banner-body">
            <div class="banner-content-wrap">
                <div class="banner-copy">
                    <?php if ( '' !== $bannerTitle ) : ?>
                        <h2 class="banner-title"><?= esc_html( $bannerTitle ); ?></h2>
                    <?php endif; ?>
                    <?php if ( '' !== $bannerContent ) : ?>
                        <p class="banner-content"><?= nl2br( esc_html( $bannerContent ) ); ?></p>
                    <?php endif; ?>
                </div>
                <?php if ( $hasInnerCtas ) : ?>
                <div class="banner-ctas">
                    <?php if ( $hasSecondaryCTA ) : $secondaryTarget = ! empty( $bannerSecondaryCTA['target'] ) ? (string) $bannerSecondaryCTA['target'] : '_self'; ?>
                        <a href="<?= esc_url( $bannerSecondaryCTA['url'] ) ?>" target="<?= esc_attr( $secondaryTarget ); ?>"<?= '_blank' === $secondaryTarget ? ' rel="noopener noreferrer"' : ''; ?> class="cta-link cta-btn-secondary cta-has-icon py-2.75!">
                            <span><?= esc_html( $bannerSecondaryCTA['title'] ?? '' ) ?></span>
                            <?= icon( 'arrow-linear-2', 'w-5 h-5 fill-white ' ) ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( $hasPrimaryCTA ) : $primaryTarget = ! empty( $bannerPrimaryCTA['target'] ) ? (string) $bannerPrimaryCTA['target'] : '_self'; ?>
                        <a href="<?= esc_url( $bannerPrimaryCTA['url'] ) ?>" target="<?= esc_attr( $primaryTarget ); ?>"<?= '_blank' === $primaryTarget ? ' rel="noopener noreferrer"' : ''; ?> class="cta-link cta-btn-primary cta-has-icon py-2.75!">
                            <span><?= esc_html( $bannerPrimaryCTA['title'] ?? '' ) ?></span>
                            <?= icon( 'arrow-linear-2', 'w-5 h-5 fill-white' ) ?>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </<?= $wrapperTag; ?>>
    </div>
</section>
