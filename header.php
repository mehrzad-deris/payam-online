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
        <div class="flex justify-between items-center">
            <div class="lg:min-w-70">
                <a href="<?= esc_url( home_url() ) ?>"><img
                            src="<?= get_template_directory_uri() ?>/assets/images/payamonline.svg" width="116"
                            height="40" alt="پیام آنلاین"/></a>
            </div>
            <div>
                <?= wp_nav_menu( [
                        'theme_location' => 'main_menu',
                        'container'      => 'nav',
                        'menu_class'     => 'main-menu flex gap-2',
                ] ) ?>
            </div>
            <div class="lg:min-w-70 flex gap-2.5 justify-end">
                <a href="#" class="flex items-center">
                    <span>مشاوره رایگان</span>
                    <?= icon('', 'w-5 h-5') ?>
                </a>
            </div>
        </div>
    </div>
</header>
