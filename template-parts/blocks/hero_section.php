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

<section data-header-theme="<?= esc_attr( $sectionStyle ); ?>" class="hero-section <?= esc_attr( $sectionStyle ) == 'dark' ? 'text-white' : '' ?> min-h-screen bottom-fade  relative overflow-hidden" style="background-color: <?= esc_attr( $sectionColor ) ?>">
    <div class="container relative z-2 lg:pt-56 pt-39 pb-27">
        <?php if ( $sectionTitle ) : ?>
        <<?= esc_attr( $sectionTitleTag ) ?> class="whitespace-pre-line mb-15 text-center lg:text-[36px] font-bold lg:leading-14 text-[24px] leading-12"><?= esc_html( $sectionTitle ) ?></<?= esc_attr( $sectionTitleTag ) ?>>
    <?php endif; ?>

    <div class="flex justify-center">
        <div class="w-197.5 max-w-full relative">
            <div class="domain-whois mb-4 lg:mb-5 relative">
                <input type="text" class="h-16 text-[14px] lg:text-[16px] rounded-[30px] w-full bg-[#0D052A]/60 backdrop-blur-[5px] border border-white/10 py-5 placeholder:text-description pe-14 ps-30 lg:ps-35 field-rtl" dir="ltr" placeholder="دامنه موردنظر خود را وارد کنید ..."/>
                <?= icon( 'search', 'w-6 h-6 absolute right-6 top-5 stroke-description' ) ?>

                <button type="submit" class="absolute bg-yellow-primary hover:bg-yellow-primary/90 duration-200 text-white font-semibold top-2 bottom-2 left-2 rounded-[30px] px-6 lg:px-8 cursor-pointer text-[14px] lg:text-[16px]">جستجو</button>
            </div>

            <div id="domain-prices" class="domain-prices gap-2 lg:gap-5 scrollbar-none" data-domain-prices="<?= esc_attr( wp_json_encode( $domain_prices ) ) ?>">
                <?php for ( $i = 0; $i < $visible_items_count; $i ++ ): ?>
                    <div class="domain-price-item">
                        <div class="px-5 lg:px-10 py-2.5 stroke-yellow-primary">
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
        <div class="services-items grid md:grid-cols-2 lg:grid-cols-4 gap-5 mt-15">
            <?php foreach ( $section_services as $service ) : ?>
                <?php
                $title    = $service['title'] ?? '';
                $subtitle = $service['subtitle'] ?? '';
                $icon     = $service['icon'] ?? '';
                $link     = $service['link'] ?? '';
                ?>

                <div class="service-item group lg:text-center text-start">
                    <a href="<?= esc_url( $link ?: '#' ) ?>" class="inner">
                        <span class="item-inner flex-row md:flex-col gap-2.5 md:gap-0 items-center p-5 pb-7 after:hidden md:after:block  before:hidden md:before:block">
                            <?php if ( $icon ) : ?>
                                <span class="md:mb-4.5 grayscale-[1] brightness-[3.5] group-hover:grayscale-[0] group-hover:brightness-[1] duration-300"><img src="<?= esc_url( $icon ) ?>" alt="" loading="lazy" decoding="async"></span>
                            <?php endif; ?>

                            <span class="flex flex-col">
                                <?php if ( $title ) : ?>
                                    <span class="font-bold text-[18px] leading-8.5"><?= esc_html( $title ) ?></span>
                                <?php endif; ?>

                                <?php if ( $subtitle ) : ?>
                                    <span class="mt-0.5 leading-7 text-[14px] font-light"><?= esc_html( $subtitle ) ?></span>
                                <?php endif; ?>
                            </span>
                        </span>

                        <span class="link-block md:right-0 left-3 md:left-0 bottom-1/2 md:translate-y-0 translate-y-1/2 md:-bottom-5 md:opacity-0 group-hover:opacity-100 md:invisible group-hover:visible">
                            <span class="flex gap-0 duration-300 group-hover:gap-2 items-center rounded-[30px] md:bg-blue-primary px-4 py-1">
                                <span class="md:inline hidden">مشاهده و خرید</span>
                                <?= icon( 'arrow-linear', 'w-4.25 h-2 stroke-white' ) ?>
                            </span>
                        </span>
                    </a>
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 z-2 fill-white group-hover:fill-blue-primary duration-300 md:block hidden">
                        <?= icon( 'top-shape', 'w-40 h-1' ) ?>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    </div>

    <div class="hero-visual absolute inset-0 will-change-transform ease-out duration-300">
        <picture class="pointer-events-none absolute inset-0" aria-hidden="true">
            <source media="(max-width: 767px)" srcset="<?= esc_url( get_template_directory_uri() ) ?>/assets/images/hero-bg-mobile.webp">
            <source media="(min-width: 768px)" srcset="<?= esc_url( get_template_directory_uri() ) ?>/assets/images/hero-bg.webp">
            <img class="absolute inset-0 w-full object-cover top-0" src="<?= esc_url( get_template_directory_uri() ) ?>/assets/images/hero-bg.webp" alt="" fetchpriority="high" decoding="async">
        </picture>
        <canvas id="hero-stars" class="pointer-events-none absolute inset-0 z-[1] h-full w-full" aria-hidden="true"></canvas>
    </div>
</section>