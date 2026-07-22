<?php
$sectionColor    = get_sub_field( 'section_color' ) ?: "";
$sectionStyle    = get_sub_field( 'section_style' ) ?: 'light';
$sectionIcon     = get_sub_field( 'section_icon' );
$sectionTitle    = get_sub_field( 'section_title' );
$sectionTitleTag = get_sub_field( 'title_tag' ) ?: 'h2';
$sectionSubTitle = get_sub_field( 'section_subtitle' );
$serviceTabs     = get_sub_field( 'service_tabs' );
$tabsId          = wp_unique_id( 'services-tabs-' );

if ( is_array( $serviceTabs ) ) {
    $serviceTabs = array_values( array_filter( $serviceTabs, static fn( $tab ) => ! empty( $tab['tab_title'] ) ) );
}
?>

<section data-header-theme="<?= esc_attr( $sectionStyle ); ?>" class="py-32 relative overflow-hidden" style="background-color: <?= esc_attr( $sectionColor ) ?>">
    <div class="container">
        <?php section_heading( [
                'title'     => $sectionTitle,
                'title_tag' => $sectionTitleTag,
                'icon'      => $sectionIcon,
                'subtitle'  => $sectionSubTitle,
        ] ) ?>

        <?php if ( is_array( $serviceTabs ) && ! empty( $serviceTabs ) ) : ?>
            <div class="services-tabs" data-tabs>
                <div class="services-tabs__list" role="tablist" aria-label="<?= esc_attr( $sectionTitle ?: 'خدمات' ); ?>">
                    <?php foreach ( $serviceTabs as $index => $serviceTab ) :
                        $tabTitle = $serviceTab['tab_title'] ?? '';
                        $tabIcon = absint( $serviceTab['tab_icon'] ?? 0 );
                        $tabId = $tabsId . '-tab-' . $index;
                        $panelId = $tabsId . '-panel-' . $index;

                        ?>
                        <button
                                type="button"
                                class="services-tabs__button appearance-none outline-none"
                                id="<?= esc_attr( $tabId ); ?>"
                                role="tab"
                                aria-controls="<?= esc_attr( $panelId ); ?>"
                                aria-selected="<?= 0 === $index ? 'true' : 'false'; ?>"
                                tabindex="<?= 0 === $index ? '0' : '-1'; ?>"
                        >
                            <?php if ( $tabIcon ) : ?>
                                <span class="services-tabs__button__icon" aria-hidden="true">
									<?= wp_get_attachment_image( $tabIcon, 'thumbnail', false, [
                                            'alt'      => '',
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                    ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped      ?>
								</span>
                            <?php endif; ?>

                            <span class="services-tabs__button-title">
							    <?= esc_html( $tabTitle ); ?>
							</span>

                            <span class="absolute right-0 shadow-shape fill-blue-primary opacity-20">
                                <?= icon( 'right-shape', 'w-[1px] h-12' ) ?>
                            </span>
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

                        $ctaUrl      = is_array( $tabCta ) ? ( $tabCta['url'] ?? '' ) : $tabCta;
                        $ctaTitle    = is_array( $tabCta ) ? ( $tabCta['title'] ?? '' ) : '';
                        $ctaTarget   = is_array( $tabCta ) ? ( $tabCta['target'] ?? '_self' ) : '_self';
                        $imageSrc    = $tabImage ? wp_get_attachment_image_src( $tabImage, 'tab_image' ) : false;
                        $imageSrcset = $tabImage ? wp_get_attachment_image_srcset( $tabImage, 'tab_image' ) : false;
                        $imageAlt    = $tabImage ? get_post_meta( $tabImage, '_wp_attachment_image_alt', true ) : '';

                        ?>
                        <div
                                class="services-tabs__panel"
                                id="<?= esc_attr( $panelId ); ?>"
                                role="tabpanel"
                                aria-labelledby="<?= esc_attr( $tabId ); ?>"
                                tabindex="0"
                                <?= 0 !== $index ? 'hidden' : ''; ?>
                        >
                            <div class="services-tabs__panel-layout">
                                <div class="services-tabs__panel-content">
                                    <div class="flex gap-5">
                                        <div>
                                            <div class="services-tabs__title flex xl:justify-start justify-center items-center xl:bg-transparent bg-blue-primary/8  xl:text-[24px] text-[18px] xl:text-black text-blue-primary p-[17px_10px] xl:p-0 mb-4 xl:mb-2.5">
                                                <span class="flex items-center">
                                                    <?php if ( $tabIcon ) : ?>
                                                        <span class="services-tabs__title-icon inline-grid xl:hidden" aria-hidden="true">
														<?= wp_get_attachment_image( $tabIcon, 'thumbnail', false, [
                                                                    'alt'      => '',
                                                                    'loading'  => 'lazy',
                                                                    'decoding' => 'async',
                                                            ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped      ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </span>
                                                <?= esc_html( $tabTitle ); ?>
                                            </div>
                                            <?php if ( '' !== $tabContent ) : ?>
                                                <div class="services-tabs__description">
                                                    <?= apply_filters( 'the_content', $tabContent ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped      ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ( '' !== $ctaUrl ) : ?>
                                                <div class="hidden xl:block mt-5">
                                                    <a
                                                            class="services-tabs__cta"
                                                            href="<?= esc_url( $ctaUrl ); ?>"
                                                            target="<?= esc_attr( $ctaTarget ); ?>"
                                                            <?= '_blank' === $ctaTarget ? 'rel="noopener noreferrer"' : ''; ?>
                                                    >
                                                        <?= esc_html( $ctaTitle ?: 'اطلاعات بیشتر' ); ?>
                                                        <span class="services-tabs__cta__icon"><?= icon( 'arrow-linear', 'w-[13px] h-2.5' ) ?></span>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ( $imageSrc ) : ?>
                                            <div class="services-tabs__image flex-none">
                                                <picture>
                                                    <source
                                                            media="(min-width: 768px)"
                                                            srcset="<?= esc_attr( $imageSrcset ?: $imageSrc[0] ); ?>"
                                                            sizes="290px"
                                                    >
                                                    <img
                                                            src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=="
                                                            width="<?= esc_attr( $imageSrc[1] ); ?>"
                                                            height="<?= esc_attr( $imageSrc[2] ); ?>"
                                                            alt="<?= esc_attr( $imageAlt ); ?>"
                                                            loading="lazy"
                                                            decoding="async"
                                                    >
                                                </picture>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ( ! empty( $features ) ) : ?>
                                        <ul class="services-tabs__features">
                                            <?php foreach ( $features as $feature ) :
                                                $featureTitle = $feature['feature_title'] ?? '';
                                                $featureDescription = $feature['feature_description'] ?? '';

                                                if ( '' === $featureTitle && '' === $featureDescription ) {
                                                    continue;
                                                }
                                                ?>
                                                <li class="services-tabs__feature">
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

                                    <?php if ( '' !== $ctaUrl ) : ?>
                                        <div class="xl:hidden block">
                                            <a
                                                    class="services-tabs__cta"
                                                    href="<?= esc_url( $ctaUrl ); ?>"
                                                    target="<?= esc_attr( $ctaTarget ); ?>"
                                                    <?= '_blank' === $ctaTarget ? 'rel="noopener noreferrer"' : ''; ?>
                                            >
                                                <?= esc_html( $ctaTitle ?: 'اطلاعات بیشتر' ); ?>
                                                <span class="services-tabs__cta__icon"><?= icon( 'arrow-linear', 'w-[13px] h-2.5' ) ?></span>
                                            </a>
                                        </div>
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
