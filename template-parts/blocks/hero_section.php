<?php
$sectionColor     = get_sub_field( 'section_color' ) ?: "";
$sectionStyle     = get_sub_field( 'section_style' ) ?: 'light';
$sectionTitle     = get_sub_field( 'section_title' );
$sectionTitleTag  = get_sub_field( 'title_tag' ) ?: 'h2';
$section_services = get_sub_field( 'services' );

/* Delete this line */
$domain_prices       = [
        [
                'extension' => 'com',
                'price'     => '۱٬۶۵۰٬۰۰۰',
                'url'       => home_url( '/domains/com/' ),
        ],
        [
                'extension' => 'net',
                'price'     => '۱٬۶۵۰٬۰۰۰',
                'url'       => home_url( '/domains/net/' ),
        ],
        [
                'extension' => 'org',
                'price'     => '۱٬۶۵۰٬۰۰۰',
                'url'       => home_url( '/domains/org/' ),
        ],
        [
                'extension' => 'ir',
                'price'     => '۹۵٬۰۰۰',
                'url'       => home_url( '/domains/ir/' ),
        ],
        [
                'extension' => 'shop',
                'price'     => '۸۵۰٬۰۰۰',
                'url'       => home_url( '/domains/shop/' ),
        ],
        [
                'extension' => 'info',
                'price'     => '۷۲۰٬۰۰۰',
                'url'       => home_url( '/domains/info/' ),
        ],
];
$visible_items_count = min( 3, count( $domain_prices ) );
?>

<section data-header-theme="<?= esc_attr( $sectionStyle ); ?>" class="hero-section <?= esc_attr( $sectionStyle ) == 'dark' ? 'text-white' : '' ?>" style="background-color: <?= esc_attr( $sectionColor ) ?>">
    <div class="container relative z-2 lg:pt-46 pt-39 pb-16">
        <?php if ( $sectionTitle ) : ?>
        <<?= esc_attr( $sectionTitleTag ) ?> class="title-section lg:text-[36px] text-[24px] lg:leading-14 leading-12"><?= esc_html( $sectionTitle ) ?></<?= esc_attr( $sectionTitleTag ) ?>>
    <?php endif; ?>

    <div class="flex justify-center">
        <div class="w-197.5 max-w-full relative">
            <div class="domain-whois mb-4 lg:mb-5">
                <input type="text" class="whois-input text-[14px] lg:text-[16px] py-5 placeholder:text-neutral-500 pe-14 ps-30 lg:ps-35 field-rtl" dir="ltr" placeholder="دامنه موردنظر خود را وارد کنید ..."/>
                <?= icon( 'search', 'w-6 h-6 absolute right-6 top-5 stroke-neutral-500' ) ?>

                <button type="submit" class="whois-submit cta-link cta-btn-primary text-[14px] lg:text-[16px] ">جستجو</button>
            </div>

            <div id="domain-prices" class="domain-prices gap-2 lg:gap-5 scrollbar-none" data-domain-prices="<?= esc_attr( wp_json_encode( $domain_prices ) ) ?>">
                <?php for ( $i = 0; $i < $visible_items_count; $i ++ ): ?>
                    <div class="domain-price-item">
                        <div class="px-5 lg:px-10 py-2.5 stroke-yellow-primary flex items-center">
                            <span dir="ltr" class="flex gap-1.5">
                                <span>تومان</span>
                                <span class="domain-price"></span>
                            </span>

                            <span>
                              <?= icon( 'arrow-linear', 'w-4.25 h-2' ) ?>
                           </span>

                            <span dir="ltr" class="domain-extension"></span>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <?php if ( is_array( $section_services ) && ! empty( $section_services ) ) : ?>
        <div class="services-items grid md:grid-cols-2 lg:grid-cols-4 gap-5 mt-18">
            <?php foreach ( $section_services as $service ) : ?>
                <?php
                $title    = $service['title'] ?? '';
                $subtitle = $service['subtitle'] ?? '';
                $price    = $service['price'] ?? '';
                $icon     = $service['icon'] ?? '';
                $link     = $service['link'] ?? '';
                ?>

                <div class="service-item group">
                    <a href="<?= esc_url( $link ?: '#' ) ?>" class="inner">
                        <span class="item-inner flex-row md:flex-col p-5 pb-7 text-neutral-500">
                            <span class="flex items-center gap-2">
                                <?php if ( $icon ) : ?>
                                    <span><img src="<?= esc_url( $icon ) ?>" alt="" loading="lazy" decoding="async"></span>
                                <?php endif; ?>
                                <?php if ( $title ) : ?>
                                    <span class="text-desktop-h5 text-neutral-900"><?= esc_html( $title ) ?></span>
                                <?php endif; ?>
                            </span>

                            <?php if ( $price ) : ?>
                                <span class="flex items-center gap-1">
                                    <span class="text-caption">شروع قیمت از:</span><span class="text-desktop-h5 text-neutral-900"><?= number_format( esc_html( $price ) ) ?></span>
                                </span>
                            <?php endif; ?>
                            <?php if ( $subtitle ) : ?>
                                <span>
                                    <span class="text-caption-mobile"><?= esc_html( $subtitle ) ?></span>
                                </span>
                            <?php endif; ?>

                            <span class="cta-link cta-btn-primary cta-opacity-style border-transparent! fill-transparent group-hover:fill-white group-hover:bg-primary-500! group-hover:text-white! text-body-3 gap-0!">
                                <span class="md:inline hidden">مشاهده و خرید</span>
                                <?= icon( 'arrow-linear-2', 'w-0 h-5 group-hover:w-7 duration-300' ) ?>
                            </span>
                        </span>
                    </a>
                </div>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    </div>
</section>