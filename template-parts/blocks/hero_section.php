<?php
$sectionTitle   = get_sub_field( 'section_title' );
$sectionTag     = get_sub_field( 'section_tag' ) ?: 'h2';
$sectionTitleEn = get_sub_field( 'section_title_en' );
$sectionDesc    = get_sub_field( 'section_desc' );
$sectionCTALink = get_sub_field( 'cta_link_group' );
$sectionImage   = get_sub_field( 'section_image' );
$sectionColor   = get_sub_field( 'section_color' ) ?: '#0C101D';
$sectionVideo   = get_sub_field( 'video_section' );

if ( $sectionImage ) {
    $sectionImage_385 = wp_get_attachment_image_url( $sectionImage, 'hero_section' );
    $sectionImage_770 = wp_get_attachment_image_url( $sectionImage, 'hero_section_2x' );
}

/* Theme options */
$callLink       = get_field( 'call_link', 'option' );
$consultantLink = get_field( 'consultant_link', 'option' );
?>

<section id="hero-section" class="text-white pt-[74px] lg:pt-[110px] overflow-hidden relative lg:pb-11 pb-20 z-2"
         style="background-color: <?= $sectionColor ?>">
    <div class="container relative z-1">
        <div class="flex flex-col lg:flex-row items-center justify-between lg:gap-10">
            <div class="text-right  pt-8">
                <?php if ( $sectionTitle ) { ?>
                <<?= $sectionTag ?> class="font-Artin text-[22px] lg:text-[32px] text-center lg:text-right"
                dir="auto"><?= esc_html( $sectionTitle ) ?></<?= $sectionTag ?>>
            <?php } ?>
            <?php if ( $sectionTitleEn ) { ?>
                <div class="font-Artin text-[16px] lg:text-[24px] text-gray mt-2.5 text-center lg:text-right"
                     dir="auto"><?= esc_html( $sectionTitleEn ) ?></div>
            <?php } ?>
            <?php if ( $sectionDesc ) { ?>
                <div class="text-[16px] mt-4.5 leading-[28px] font-[300] hidden lg:block text-justify"><?= esc_html( $sectionDesc ) ?></div>
            <?php } ?>
            <div class=" items-center gap-2.5 font-medium mt-8 hidden lg:flex">
                <?php if ( $sectionCTALink && count( $sectionCTALink ) ) {
                    foreach ( $sectionCTALink as $cta ) { ?>
                        <a href="<?= esc_url( $cta['link_url'] ) ?>" class="<?= esc_attr( $cta['cta_type'] ) ?>-cta-bg">
                            <span><?= esc_html( $cta['link_title'] ) ?></span>
                            <span><img width="20" height="21" src="<?= esc_url( $cta['link_img'] ) ?>"
                                       aria-hidden="true" alt=""></span>
                        </a>
                    <?php }
                } ?>
            </div>
        </div>

        <div class="flex flex-none gap-5 lg:mt-9 scale-60 lg:scale-100 -mt-10">
            <div class="flex flex-col gap-[64px]">
                <div class="parallax-card  -mt-10.5 p-6.75 gap-4 rounded-[32px] font-medium flex flex-col items-end bg-gradient-to-b from-white/[0.06] to-white/[0.01]">
                    <div class="parallax-layer"
                         data-depth="23"
                         aria-hidden="true"><?= icon( 'percentage', 'w-[93px] h-[44px] fill-white' ); ?></div>
                    <div class="parallax-layer pointer-events-none text-[20px] w-full text-center" data-depth="15">رضایت
                        مشتریان
                    </div>
                    <div class="parallax-layer pointer-events-none"
                         data-depth="20" aria-hidden="true"><?= icon( 'bar', 'w-[152px] h-[81px] fill-white' ); ?></div>
                </div>

                <div class="relative z-1  -left-[85px]">
                    <div class="parallax-card backdrop-blur-[10px] p-8 rounded-[32px] font-medium flex flex-col items-start gap-5 bg-gradient-to-b from-white/[0.06] to-white/[0.01]">
                        <div data-depth="16"
                             class="parallax-layer pointer-events-none bg-[#18203A] flex justify-center items-center h-20 w-20 rounded-full shadow-[inset_0px_2px_0px_0px_rgba(255,255,255,0.20),inset_0px_-1px_0px_1px_rgba(0,0,0,0.05),0px_4px_2.7px_0px_rgba(255,255,255,0.10)]"
                             aria-hidden="true"><?= icon( 'cup', 'w-10 h-10 fill-white' ); ?></div>
                        <div data-depth="12"
                             class="parallax-layer pointer-events-none text-[24px] w-full text-right font-medium leading-[36px]">
                            بیش از
                            <span class="counter w-[45px] text-center inline-block" data-start="427"
                                  data-target="500"
                                  data-speed="1">0</span> پروژه<br> فعال و موفق
                        </div>
                    </div>
                    <div class="absolute top-full -mt-8 mb-5 left-full -ml-14 pointer-events-none"
                         aria-hidden="true"><?= icon( 'line-beam', 'w-[83px] h-[75px] fill-white' ); ?></div>
                </div>
            </div>

            <?php if ( $sectionImage ) { ?>
                <div class="w-[385px] relative" data-video-modal-open>
                    <div class="absolute bottom-full mb-5 right-8.5"
                         aria-hidden="true"><?= icon( 'arrow-draw', 'w-[117px] h-[51px] fill-white' ); ?></div>
                    <div class="relative overflow-hidden rounded-[42px] cursor-pointer">
                        <img class="w-full" width="385" height="441" fetchpriority="high" decoding="async"
                             src="<?= esc_url( $sectionImage_385 ) ?>"
                             srcset="<?= esc_url( $sectionImage_385 ) ?> 1x, <?= esc_url( $sectionImage_770 ) ?> 2x"
                             alt="<?= $sectionTitle ?: '' ?>">
                        <div class="absolute flex items-center gap-2 bg-black rounded-tr-[32px] py-4.25 px-6.5 bottom-0 left-0">
                            <div class="text-[16px]">ما چیکار میکنیم؟</div>
                            <div aria-hidden="true">
                                <?= icon( 'play-colored', 'w-11.5 h-11.5' ); ?>
                            </div>

                            <svg class="absolute left-full bottom-0" width="32" height="32" viewBox="0 0 32 32"
                                 fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M32 32C6.4 32 0 10.6667 0 0V32H32Z" fill="#0C101D"/>
                            </svg>

                            <svg class="absolute left-0 bottom-full" width="32" height="32" viewBox="0 0 32 32"
                                 fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M32 32C6.4 32 0 10.6667 0 0V32H32Z" fill="#0C101D"/>
                            </svg>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php if ( $sectionDesc ) { ?>
            <div class="lg:hidden text-[14px] -mt-10 leading-[28px] font-[300]  text-justify"><?= esc_html( $sectionDesc ) ?></div>
        <?php } ?>

        <div class=" items-center gap-2.5 font-medium mt-8 lg:hidden flex">
            <?php if ( $sectionCTALink && count( $sectionCTALink ) ) {
                foreach ( $sectionCTALink as $cta ) { ?>
                    <a href="<?= esc_url( $cta['link_url'] ) ?>" class="<?= esc_attr( $cta['cta_type'] ) ?>-cta-bg">
                        <span><?= esc_html( $cta['link_title'] ) ?></span>
                        <span><img width="20" height="21" src="<?= esc_url( $cta['link_img'] ) ?>" aria-hidden="true"
                                   alt=""></span>
                    </a>
                <?php }
            } ?>
        </div>
    </div>

    <div class="flex justify-center relative z-1 lg:mt-21.5 ">
        <div class="w-full lg:w-auto bg-[linear-gradient(90deg,#1E7777_0%,#036666_60%,#2F8080_100%)] lg:rounded-[20px_0_20px_0] rounded-t-[20px] fixed bottom-0 lg:relative lg:after:absolute after:right-full after:top-full after:w-[16px] after:h-[16px] after:bg-[#297D7D] after:rounded-[4px_0_4px_0] lg:before:absolute before:left-full before:bottom-full before:w-[16px] before:h-[16px] before:bg-[#066868] before:rounded-[4px_0_4px_0]">
            <div class="overflow-hidden font-medium px-2.5 lg:py-2.5 py-4 lg:rounded-[20px_0_20px_0] rounded-t-[20px] relative">
                <span class="absolute w-full h-full right-0 top-0 mix-blend-overlay"
                      style="background: url('<?= esc_url( get_template_directory_uri() ) ?>/assets/images/box-pattern.svg') center"></span>
                <div class="relative z-1 gap-5 flex items-center">
                    <div class="text-[16px] ps-8 hidden lg:block">برای مشاوره و استعلام قیمت با مشاوران ما تماس
                        بگیرید.
                    </div>
                    <div class="flex items-center justify-center w-full lg:w-auto gap-2.5">
                        <div>
                            <a href="<?= esc_url( $callLink ) ?>"
                               class="flex items-center gap-2 bg-white text-primary px-8 py-3.5 rounded-[16px] duration-200 hover:text-white hover:bg-secondary group">
                                <span>تماس تلفنی</span>
                                <span aria-hidden="true"><?= icon( 'call', 'w-5 h-5 fill-primary group-hover:fill-white duration-200' ); ?></span>
                            </a>
                        </div>
                        <div>
                            <a href="<?= esc_url( $consultantLink ) ?>"
                               class="flex items-center gap-2 bg-secondary px-8 py-3.5 rounded-[16px] duration-200 hover:bg-white hover:text-primary group">
                                <span>درخواست مشاوره</span>
                                <span aria-hidden="true"><?= icon( 'edit', 'w-5 h-5 fill-white group-hover:fill-primary duration-200' ); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Beam -->
    <div class="flex justify-center absolute right-0 top-0 w-full">
        <div class="ocean-god-rays pointer-events-none"></div>
    </div>
</section>

<div
        data-video-modal
        class="fixed inset-0 z-10 hidden items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="video-modal-title"
        aria-hidden="true"
>
    <button
            type="button"
            data-video-modal-close
            class="absolute inset-0 bg-black/80 backdrop-blur-[16px]"
            aria-label="بستن مودال"
    ></button>

    <div class="relative z-10 w-full max-w-5xl rounded-2xl p-px shadow-2xl">
        <div class="relative">
            <button
                    type="button"
                    data-video-modal-close
                    class="absolute right-3 bottom-full z-20 mb-3 flex size-10 cursor-pointer items-center justify-center"
                    aria-label="بستن"
            >
                <?= icon('close-icon', 'w-[40px] h-[40px]') ?>
            </button>

            <div class="relative overflow-hidden rounded-[15px] bg-black">
                <h2 id="video-modal-title" class="sr-only">
                    نمایش ویدیو
                </h2>

                <div class="aspect-video">
                    <video
                            data-modal-video
                            data-src="<?= esc_url($sectionVideo) ?>"
                            class="h-full w-full object-cover"
                            controls
                            preload="none"
                            playsinline
                    >
                        مرورگر شما از پخش ویدیو پشتیبانی نمی‌کند.
                    </video>
                </div>
            </div>
        </div>
    </div>
</div>
