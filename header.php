<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#07041D">
    <meta name="msapplication-navbutton-color" content="#07041D">
    <meta name="apple-mobile-web-app-status-bar-style" content="#07041D">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header fixed z-20 w-full site-header--light">
    <div class="container">
        <div class="header-inner py-5 relative flex lg:justify-between justify-end-safe items-center gap-3">
            <div class="lg:min-w-70 order-2 lg:order-0 flex-none">
                <a href="<?= esc_url( home_url() ) ?>" class="relative flex h-10 shrink-0 overflow-hidden lg:w-[119px] w-9.25">
                    <img class="logo-dark absolute inset-y-0 right-0 h-10 w-29.75 max-w-none object-cover object-right" src="<?= esc_url( get_theme_file_uri( '/assets/images/payamonline.svg' ) ); ?>" width="116" height="40" alt="راهکار میزبانی دیجیتال پیام آنلاین"/>
                    <img class="logo-light absolute inset-y-0 right-0 h-10 w-29.75 max-w-none object-cover object-right" src="<?= esc_url( get_theme_file_uri( '/assets/images/payamonline-colored.svg' ) ); ?>" width="116" height="40" alt="راهکار میزبانی دیجیتال پیام آنلاین"/>
                </a>
            </div>
            <div class="w-full lg:w-auto">
                <button type="button" class="lg:hidden cursor-pointer" data-mobile-menu-toggle aria-expanded="false" aria-controls="site-mobile-menu" aria-label="باز کردن فهرست اصلی"><?= icon( 'hamburger-menu', 'w-8 h-8 duration-200 fill-neutral-900' ) ?></button>
                <div class="hidden lg:block header-desktop-navigation" data-header-navigation>
                    <?php payam_header_menu( 'header-menu header-menu-desktop' ); ?>
                </div>
            </div>
            <div class="lg:min-w-70 flex gap-2.5 justify-end order-1 lg:order-2 flex-none">
                <?php payam_header_action( 'header_consultation_link', 'cta-link cta-btn-secondary cta-opacity-style cta-has-icon group gap-0! lg:gap-3! p-[7px_10px]! lg:p-[7px_14px_7px_7px]!', 'call', 'w-5 h-5 duration-200 fill-current', 'hidden lg:inline ' ); ?>
                <?php payam_header_action( 'header_account_link', 'cta-link cta-btn-primary cta-has-icon gap-0! lg:gap-3! p-[7px_10px]! lg:p-[7px_14px_7px_7px]!', 'user', 'w-5 h-5 duration-200 hidden lg:inline fill-white' ); ?>
            </div>
        </div>
    </div>
</header>
<div class="header-menu-backdrop" data-header-backdrop hidden></div>
<dialog id="site-mobile-menu" class="header-drawer" data-mobile-menu aria-label="فهرست اصلی">
    <div class="header-drawer-top header-inner py-5 relative flex justify-end-safe items-center gap-3">
        <div class="order-2 flex-none">
            <a href="<?= esc_url( home_url( '/' ) ); ?>" class="relative flex h-10 w-9.25 shrink-0 overflow-hidden" aria-label="پیام آنلاین"><img class="absolute inset-y-0 right-0 h-10 w-29.75 max-w-none object-cover object-right" src="<?= esc_url( get_theme_file_uri( '/assets/images/payamonline-colored.svg' ) ); ?>" width="116" height="40" alt=""></a>
        </div>
        <div class="w-full">
            <button type="button" class="cursor-pointer" data-mobile-menu-close aria-label="بستن فهرست"><?= icon( 'close', 'w-8 h-8 stroke-current' ); ?></button>
        </div>
        <div class="flex gap-2.5 justify-end order-1 flex-none">
            <?php payam_header_action( 'header_consultation_link', 'cta-link cta-btn-secondary cta-opacity-style cta-has-icon group  gap-0! lg:gap-3! p-[7px_10px]! lg:p-[7px_14px_7px_7px]!', 'call', 'w-5 h-5 duration-200 fill-current', 'hidden' ); ?>
            <?php payam_header_action( 'header_account_link', 'cta-link cta-btn-primary cta-has-icon  gap-0! lg:gap-3! p-[7px_10px]! lg:p-[7px_14px_7px_7px]!', 'user', 'hidden w-5 h-5 fill-white' ); ?>
        </div>
    </div>
    <div class="header-drawer-step" data-mobile-menu-step hidden>
        <button type="button" data-mobile-menu-back aria-label="بازگشت به منوی قبل"><?= icon( 'arrow-linear-2', 'header-menu-chevron' ); ?> بازگشت</button>
        <span data-mobile-menu-title></span>
    </div>
    <div class="header-drawer-body" data-header-navigation><?php payam_header_menu( 'header-menu header-menu-mobile' ); ?></div>
    <div class="header-drawer-actions">
        <?php payam_header_action( 'header_consultation_link', 'cta-link cta-btn-secondary cta-opacity-style', 'call', 'w-5 h-5 duration-200 fill-current' ); ?>
        <?php payam_header_action( 'header_account_link', 'cta-link cta-btn-primary', 'user', 'w-5 h-5 fill-white' ); ?>
    </div>
</dialog>
