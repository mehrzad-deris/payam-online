<?php
defined( 'ABSPATH' ) || exit;

$sectionColor               = get_sub_field( 'section_color' ) ? 'background-color: ' . get_sub_field( 'section_color' ) : "";
$sectionStyle               = get_sub_field( 'section_style' ) ?: 'light';
$sectionIcon                = get_sub_field( 'section_icon' );
$sectionTitle               = get_sub_field( 'section_title' );
$sectionTitleTag            = get_sub_field( 'title_tag' ) ?: 'h2';
$sectionSubTitle            = get_sub_field( 'section_subtitle' );
$serviceTabStyle            = get_sub_field( 'service_tab_style' );
$serviceTabs                = get_sub_field( 'service_tabs' );
$tabsId                     = wp_unique_id( 'services-tabs-' );
$paddingTopValue            = get_sub_field( 'padding_top' );
$paddingTopMobileValue      = get_sub_field( 'padding_top_mobile' );
$paddingBottomValue         = get_sub_field( 'padding_bottom' );
$paddingBottomMobileValue   = get_sub_field( 'padding_bottom_mobile' );
$servicePaddingTop          = is_numeric( $paddingTopValue ) ? '--services-padding-top:' . absint( $paddingTopValue ) . 'px;' : '';
$servicePaddingTopMobile    = is_numeric( $paddingTopMobileValue ) ? '--services-padding-top-mobile:' . absint( $paddingTopMobileValue ) . 'px;' : '';
$servicePaddingBottom       = is_numeric( $paddingBottomValue ) ? '--services-padding-bottom:' . absint( $paddingBottomValue ) . 'px;' : '';
$servicePaddingBottomMobile = is_numeric( $paddingBottomMobileValue ) ? '--services-padding-bottom-mobile:' . absint( $paddingBottomMobileValue ) . 'px;' : '';

if ( is_array( $serviceTabs ) ) {
    $serviceTabs = array_values( array_filter( $serviceTabs, static fn( $tab ) => ! empty( $tab['tab_title'] ) ) );
}

$serviceTabStyle = in_array( $serviceTabStyle, [ 'style_1', 'style_2' ], true ) ? $serviceTabStyle : 'style_1';
$isStyleTwo      = 'style_2' === $serviceTabStyle;
$serviceTabClass = str_replace( '_', '-', $serviceTabStyle );
?>

<section data-header-theme="<?= esc_attr( $sectionStyle ); ?>" class="services-section services-tab-<?= esc_attr( $serviceTabClass ); ?> relative xl:px-[135px]" style="<?= esc_attr( $sectionColor ) . ' ' . esc_attr( $servicePaddingTop ) . ' ' . esc_attr( $servicePaddingTopMobile ) . ' ' . esc_attr( $servicePaddingBottom ) . ' ' . esc_attr( $servicePaddingBottomMobile ); ?>">
    <div class="container">
        <?php section_heading( [
                'title'     => $sectionTitle,
                'title_tag' => $sectionTitleTag,
                'icon'      => $sectionIcon,
                'subtitle'  => $sectionSubTitle,
        ] ) ?>

        <?php if ( is_array( $serviceTabs ) && ! empty( $serviceTabs ) ) : ?>
            <div class="services-tabs<?= $isStyleTwo ? ' services-tabs-style-2 lg:px-27' : ' services-tabs-style-1'; ?>" data-tabs data-tabs-style="<?= esc_attr( $serviceTabStyle ); ?>"<?= $isStyleTwo && count( $serviceTabs ) > 1 ? ' data-tabs-autoplay="5000"' : ''; ?>>
                <div class="services-tabs__list" role="tablist" aria-orientation="<?= $isStyleTwo ? 'horizontal' : 'vertical'; ?>" aria-label="<?= esc_attr( $sectionTitle ?: 'خدمات' ); ?>">
                    <?php foreach ( $serviceTabs as $index => $serviceTab ) :
                        $tabTitle = $serviceTab['tab_title'] ?? '';
                        $tabIcon = absint( $serviceTab['tab_icon'] ?? 0 );
                        $tabId = $tabsId . '-tab-' . $index;
                        $panelId = $tabsId . '-panel-' . $index;

                        ?>
                        <button
                                type="button"
                                class="services-tabs__button appearance-none outline-none text-desktop-h6 "
                                id="<?= esc_attr( $tabId ); ?>"
                                role="tab"
                                aria-controls="<?= esc_attr( $panelId ); ?>"
                                aria-selected="<?= 0 === $index ? 'true' : 'false'; ?>"
                                tabindex="<?= 0 === $index ? '0' : '-1'; ?>"
                        >
                            <?php if ( $tabIcon && ! $isStyleTwo ) : ?>
                                <span class="services-tabs__button__icon" aria-hidden="true">
									<?= wp_get_attachment_image( $tabIcon, 'thumbnail', false, [
                                            'alt'      => '',
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                    ] ); ?>
								</span>
                            <?php endif; ?>

                            <span class="services-tabs__button-title">
							    <?= esc_html( $tabTitle ); ?>
							</span>

                            <?php if ( !$isStyleTwo ) : ?>
                                <span class="absolute right-0 shadow-shape fill-blue-primary opacity-20">
                                    <?= icon( 'right-shape', 'w-[1px] h-12' ) ?>
                                </span>
                            <?php endif; ?>

                            <?php if ( $isStyleTwo ) : ?>
                                <span class="services-tabs__progress" aria-hidden="true"></span>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="services-tabs__panels">
                    <?php foreach ( $serviceTabs as $index => $serviceTab ) :
                        $tabTitle = $serviceTab['tab_title'] ?? '';
                        $tabIcon = absint( $serviceTab['tab_icon'] ?? 0 );
                        $tabContent = $serviceTab['tab_content'] ?? '';
                        $tabImage = absint( $serviceTab['tab_image'] ?? 0 );
                        $tabCta = $serviceTab['tab_cta'] ?? [];
                        $features = $serviceTab['tab_features'] ?? [];
                        $tabId = $tabsId . '-tab-' . $index;
                        $panelId = $tabsId . '-panel-' . $index;

                        $features = is_array( $features ) ? array_slice( $features, 0, 4 ) : [];

                        $ctaUrl            = is_array( $tabCta ) ? ( $tabCta['url'] ?? '' ) : $tabCta;
                        $ctaTitle          = is_array( $tabCta ) ? ( $tabCta['title'] ?? '' ) : '';
                        $ctaTarget         = is_array( $tabCta ) ? ( $tabCta['target'] ?? '_self' ) : '_self';
                        $imageSize         = $isStyleTwo ? 'tab_image_style_2' : 'tab_image';
                        $imageDisplayWidth = $isStyleTwo ? 561 : 290;
                        $imageSrc          = $tabImage ? wp_get_attachment_image_src( $tabImage, $imageSize ) : false;
                        $imageSrcset       = $tabImage ? wp_get_attachment_image_srcset( $tabImage, $imageSize ) : false;
                        $imageAlt          = $tabImage ? get_post_meta( $tabImage, '_wp_attachment_image_alt', true ) : '';

                        ?>
                        <div class="services-tabs__panel" id="<?= esc_attr( $panelId ); ?>" role="tabpanel" aria-labelledby="<?= esc_attr( $tabId ); ?>" tabindex="0"<?= 0 !== $index ? 'hidden' : ''; ?>>
                            <div class="services-tabs__panel-layout">
                                <div class="services-tabs__panel-content">
                                    <div class="flex gap-5 items-center">
                                        <div class="p-8 xl:p-10">
                                            <?php if ( $tabIcon ) : ?>
                                                <?= wp_get_attachment_image( $tabIcon, 'full', false, [
                                                        'class'    => 'services-tabs__content-icon',
                                                        'alt'      => '',
                                                        'width'    => 48,
                                                        'height'   => 48,
                                                        'loading'  => 'lazy',
                                                        'decoding' => 'async',
                                                ] ); ?>
                                            <?php endif; ?>
                                            <div class="services-tabs__title mb-2.5">
                                                <span class="text-mobile-h2 lg:text-desktop-h4 text-neutral-900">
                                                    <?= esc_html( $tabTitle ); ?>
                                                </span>
                                            </div>
                                            <?php if ( '' !== $tabContent ) : ?>
                                                <div class="services-tabs__description text-caption text-justify">
                                                    <?= apply_filters( 'the_content', $tabContent ); ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ( '' !== $ctaUrl ) : ?>
                                                <div class="mt-5">
                                                    <a class="cta-link cta-btn-primary cta-has-icon rounded-1_5!" href="<?= esc_url( $ctaUrl ); ?>" target="<?= esc_attr( $ctaTarget ); ?>" <?= '_blank' === $ctaTarget ? 'rel="noopener noreferrer"' : ''; ?>>
                                                        <?= esc_html( $ctaTitle ?: 'اطلاعات بیشتر' ); ?>
                                                        <span class="icon"><?= icon( 'arrow-linear-2', 'w-5 h-5' ) ?></span>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ( $imageSrc ) : ?>
                                            <div class="services-tabs__image flex-none">
                                                <picture>
                                                    <source media="(min-width: 1280px)" data-lazy-desktop-srcset="<?= esc_attr( $imageSrcset ?: $imageSrc[0] ); ?>" sizes="<?= esc_attr( $imageDisplayWidth ); ?>px">
                                                    <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" data-lazy-desktop-src="<?= esc_url( $imageSrc[0] ); ?>" width="<?= esc_attr( $imageSrc[1] ); ?>" height="<?= esc_attr( $imageSrc[2] ); ?>" alt="<?= esc_attr( $imageAlt ); ?>" loading="lazy" decoding="async">
                                                </picture>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ( ! empty( $features ) && ! $isStyleTwo ) : ?>
                                        <ul class="services-tabs__features p-4 xl:p-10 xl:pt-10 pt-4 mb-4 xl:mb-0">
                                            <?php foreach ( $features as $feature ) :
                                                $featureTitle = $feature['feature_title'] ?? '';
                                                $featureDescription = $feature['feature_description'] ?? '';

                                                if ( '' === $featureTitle && '' === $featureDescription ) {
                                                    continue;
                                                }
                                                ?>
                                                <li class="services-tabs__feature text-center xl:text-start xlpy-6 xlpx-5 p-3 xl:leading-[30px]">
                                                    <?php if ( '' !== $featureTitle ) : ?>
                                                        <span><?= esc_html( $featureTitle ); ?></span>
                                                    <?php endif; ?>

                                                    <?php if ( '' !== $featureDescription ) : ?>
                                                        <span><?= esc_html( $featureDescription ); ?></span>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
