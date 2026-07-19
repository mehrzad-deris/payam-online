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

<header class="site-header group fixed z-20 w-full py-5">
    <div class="container">
        <div class="flex lg:justify-between justify-end-safe items-center gap-3">
            <div class="lg:min-w-70 order-2 lg:order-0 flex-none">
                <a href="<?= esc_url( home_url() ) ?>" class="relative flex h-10 shrink-0 overflow-hidden lg:w-auto w-[37px]">
                    <img class="logo-dark absolute inset-y-0 right-0 h-10 w-[119px] max-w-none object-cover object-right" src="<?= get_template_directory_uri() ?>/assets/images/payamonline.svg" width="116" height="40" alt="راهکار میزبانی دیجیتال پیام آنلاین"/>
                    <img class="logo-light absolute inset-y-0 right-0 h-10 w-[119px] max-w-none object-cover object-right" src="<?= get_template_directory_uri() ?>/assets/images/payamonline-colored.svg" width="116" height="40" alt="راهکار میزبانی دیجیتال پیام آنلاین"/>
                </a>
            </div>
            <div class="w-full lg:w-auto">
                <span aria-hidden="true" class="lg:hidden cursor-pointer"><?= icon( 'hamburger-menu', 'w-8 h-8 duration-200' ) ?></span>
                <div class="hidden lg:block">
                    <?= wp_nav_menu( [
                            'theme_location' => 'main_menu',
                            'container'      => 'nav',
                            'menu_class'     => 'main-menu flex gap-7 [&_a]:duration-200 [&_a]:hover:text-yellow-primary',
                    ] ) ?>
                </div>
            </div>
            <div class="lg:min-w-70 flex gap-2.5 justify-end order-1 lg:order-2 flex-none">
                <a href="#" class="flex items-center gap-1.5 fill-yellow-primary duration-200 text-yellow-primary px-2.5 lg:pe-2 py-1.75 border rounded-[30px] border-b-yellow-primary hover:text-white hover:bg-yellow-primary hover:fill-white hover:border-yellow-primary">
                    <span class="hidden lg:inline">مشاوره رایگان</span>
                    <span aria-hidden="true"><?= icon( 'call', 'w-5 h-5 duration-200' ) ?></span>
                </a>
                <a href="#" class="flex items-center gap-1.5 fill-white hover:fill-blue-primary duration-200 hover:text-blue-primary px-2.5 lg:pe-2 py-1.75 border rounded-[30px] hover:bg-white hover:border-white  group-[.site-header--light]:fill-blue-primary group-[.site-header--light]:hover:bg-blue-primary group-[.site-header--light]:hover:text-white group-[.site-header--light]:hover:fill-white group-[.site-header--light]:hover:border-blue-primary">
                    <span>پنل کاربری</span>
                    <span aria-hidden="true"><?= icon( 'user', 'w-5 h-5 duration-200 hidden lg:inline' ) ?></span>
                </a>
            </div>
        </div>
    </div>

    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[calc(100%+64px)] bg-gradient-to-b from-black/35 via-black/15 to-transparent backdrop-blur-[8px] backdrop-saturate-150 [mask-image:linear-gradient(to_bottom,black_0%,black_60%,transparent_100%)] [-webkit-mask-image:linear-gradient(to_bottom,black_0%,black_60%,transparent_100%)]"></div>
</header>