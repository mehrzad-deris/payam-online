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

<header class="site-header fixed z-20 w-full">
    <div class="container">
        <div class="header-inner py-5 relative flex lg:justify-between justify-end-safe items-center gap-3">
            <div class="lg:min-w-70 order-2 lg:order-0 flex-none">
                <a href="<?= esc_url( home_url() ) ?>" class="relative flex h-10 shrink-0 overflow-hidden lg:w-auto w-9.25">
                    <img class="logo-dark absolute inset-y-0 right-0 h-10 w-29.75 max-w-none object-cover object-right" src="<?= get_template_directory_uri() ?>/assets/images/payamonline.svg" width="116" height="40" alt="راهکار میزبانی دیجیتال پیام آنلاین"/>
                    <img class="logo-light absolute inset-y-0 right-0 h-10 w-29.75 max-w-none object-cover object-right" src="<?= get_template_directory_uri() ?>/assets/images/payamonline-colored.svg" width="116" height="40" alt="راهکار میزبانی دیجیتال پیام آنلاین"/>
                </a>
            </div>
            <div class="w-full lg:w-auto">
                <span aria-hidden="true" class="lg:hidden cursor-pointer"><?= icon( 'hamburger-menu', 'w-8 h-8 duration-200 fill-white' ) ?></span>
                <div class="hidden lg:block">
                    <?= wp_nav_menu( [
                            'theme_location' => 'main_menu',
                            'container'      => 'nav',
                            'menu_class'     => 'main-menu flex gap-7 [&_a]:duration-200 [&_a]:hover:text-yellow-primary',
                    ] ) ?>
                </div>
            </div>
            <div class="lg:min-w-70 flex gap-2.5 justify-end order-1 lg:order-2 flex-none">
                <a href="#" class="cta-link cta-btn-secondary cta-opacity-style cta-has-icon group">
                    <span class="hidden lg:inline">مشاوره رایگان</span>
                    <span aria-hidden="true"><?= icon( 'call', 'w-5 h-5 duration-200 fill-yellow-primary group-hover:fill-white' ) ?></span>
                </a>
                <a href="#" class="cta-link cta-btn-primary cta-has-icon">
                    <span>پنل کاربری</span>
                    <span aria-hidden="true"><?= icon( 'user', 'w-5 h-5 duration-200 hidden lg:inline fill-white' ) ?></span>
                </a>
            </div>
        </div>
    </div>
</header>