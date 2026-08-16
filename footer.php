<?php
/**
 * Site footer.
 */

defined( 'ABSPATH' ) || exit;

$option = static function ( string $field, $default = '' ) {
    if ( ! function_exists( 'get_field' ) ) {
        return $default;
    }

    $value = get_field( $field, 'option' );

    return null !== $value && false !== $value && '' !== $value ? $value : $default;
};

$footerText      = $option( 'footer_text' );
$footerSocials   = $option( 'footer_socials', [] );
$footerCopyright = $option( 'footer_copyright' );
$contactItems    = $option( 'footer_contact_items', [] );

if ( ! is_array( $footerSocials ) ) {
    $footerSocials = [];
}

if ( ! is_array( $contactItems ) || empty( $contactItems ) ) {
    $contactItems = [
            [ 'type' => 'landline', 'value' => $option( 'footer_landline' ) ],
            [ 'type' => 'mobile', 'value' => $option( 'footer_mobile' ) ],
            [ 'type' => 'email', 'value' => $option( 'footer_email' ) ],
            [ 'type' => 'address', 'value' => $option( 'footer_address' ) ],
    ];
}

$contactItems = array_values( array_filter( $contactItems, static fn( $item ): bool => is_array( $item ) && ! empty( $item['value'] ) ) );

$menuSections = [
        'footer_company',
        'footer_services',
        'footer_solutions',
        'footer_quick_links',
];

$getMenuName = static function ( string $location ): string {
    $locations = get_nav_menu_locations();
    $menuId    = absint( $locations[ $location ] ?? 0 );
    $menu      = $menuId ? wp_get_nav_menu_object( $menuId ) : false;

    return $menu instanceof WP_Term ? $menu->name : '';
};

$renderMenu = static function ( string $location ): string {
    if ( ! has_nav_menu( $location ) ) {
        return '';
    }

    return (string) wp_nav_menu( [
            'theme_location' => $location,
            'container'      => false,
            'echo'           => false,
            'fallback_cb'    => false,
            'depth'          => 1,
            'menu_class'     => 'flex flex-col gap-2.5 text-base leading-[30px] text-white [&_a]:transition-colors [&_a]:duration-200 [&_a:hover]:text-yellow-primary',
    ] );
};

$footerAsset = static fn( string $file ): string => get_theme_file_uri( '/assets/images/' . $file );
?>

<footer class="site-footer relative overflow-hidden bg-dark pt-16 text-white xl:pt-[107px]" data-footer data-header-theme="dark">
    <div class="container relative z-10 flex flex-col items-center mb-16">
        <a class="flex flex-col items-center" href="<?= esc_url( home_url( '/' ) ); ?>" aria-label="<?= esc_attr( get_bloginfo( 'name' ) ); ?>">
            <img class="h-[108px] w-[89px] object-contain" src="<?= esc_url( $footerAsset( 'payamava-logo-vertical-light.svg' ) ); ?>" alt="<?= esc_attr( get_bloginfo( 'name' ) ); ?>" width="89" height="108" loading="lazy" decoding="async">
        </a>

        <?php if ( $footerText ) : ?>
            <div class="mt-5 max-w-[1064px] text-center text-sm leading-[30px] text-white xl:mt-8 xl:text-base xl:leading-9">
                <?= wp_kses_post( $footerText ); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ( ! empty( $footerSocials ) ) : ?>
        <div class="flex justify-center">

            <div class="relative flex">
                <ul class="mt-6 flex items-center justify-center gap-6 xl:mt-8 xl:gap-9" aria-label="شبکه‌های اجتماعی">
                    <?php foreach ( $footerSocials as $social ) :
                        if ( ! is_array( $social ) ) {
                            continue;
                        }

                        $socialTypeField = $social['type'] ?? '';
                        $socialType      = sanitize_key( is_array( $socialTypeField ) ? ( $socialTypeField['value'] ?? '' ) : $socialTypeField );
                        $socialName      = (string) ( is_array( $socialTypeField ) ? ( $socialTypeField['label'] ?? $socialType ) : $socialType );
                        $socialSlug      = $socialType;
                        $socialLink      = $social['link'] ?? $social['url'] ?? '';
                        $socialUrl       = is_array( $socialLink ) ? ( $socialLink['url'] ?? '' ) : $socialLink;
                        $socialTarget    = is_array( $socialLink ) ? ( $socialLink['target'] ?? '_blank' ) : '_blank';

                        if ( ! $socialSlug || ! $socialUrl ) {
                            continue;
                        }
                        ?>
                        <li>
                            <a class="block rounded-lg transition-transform duration-200 hover:-translate-y-1 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-yellow-primary" href="<?= esc_url( $socialUrl ); ?>" target="<?= esc_attr( $socialTarget ); ?>"<?= '_blank' === $socialTarget ? ' rel="noopener noreferrer"' : ''; ?> aria-label="<?= esc_attr( $socialName ); ?>">
                                <?= icon( 'social-' . $socialSlug, 'size-8 xl:size-6' ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <span class="gradient-shape shape-right top-2"><?= icon( 'rounded-shape', 'rounded-shape' ) ?></span>
                <span class="gradient-shape shape-left top-2"><?= icon( 'rounded-shape', 'rounded-shape' ) ?></span>
            </div>
        </div>
    <?php endif; ?>

    <div class="container relative z-10 flex flex-col items-center mb-5 ">
        <div class="mt-[136px] w-full  xl:mt-24 flex xl:flex-row flex-col gap-4 xl:gap-12" data-footer-accordion>
            <section class="overflow-hidden rounded-xl flex-none bg-white/[0.03] backdrop-blur-[22px] xl:overflow-visible xl:rounded-none xl:bg-transparent xl:backdrop-blur-none" data-footer-item>
                <button class="flex h-16 w-full items-center justify-between px-5 text-right text-lg font-medium xl:pointer-events-none xl:h-auto xl:px-0 xl:text-base xl:text-[#575775]" type="button" aria-expanded="false" aria-controls="footer-contact" data-footer-toggle>
                    <span>اطلاعات تماس</span>
                    <span class="size-5 transition-transform duration-200 xl:hidden" aria-hidden="true" data-footer-arrow><?= icon( 'arrow-down', 'size-5 fill-white' ); ?></span>
                </button>
                <div class="px-5 pb-5 xl:mt-2.5 xl:px-0 xl:pb-0" id="footer-contact" hidden data-footer-panel>
                    <span class="mb-2.5 hidden h-px w-[27px] bg-description xl:block"></span>
                    <ul class="flex flex-col gap-2.5">
                        <?php foreach ( $contactItems as $contact ) :
                            $type = sanitize_key( $contact['type'] ?? 'contact' );
                            $value = (string) $contact['value'];
                            $url = (string) ( $contact['url'] ?? '' );
                            $iconType = [
                                                'mobile'  => 'mobile',
                                                'phone'   => 'phone',
                                                'address' => 'address',
                                        ][ $type ] ?? $type;

                            if ( ! $url && 'email' === $type ) {
                                $url = 'mailto:' . sanitize_email( $value );
                            } elseif ( ! $url && in_array( $type, [ 'phone', 'mobile', 'landline' ], true ) ) {
                                $url = 'tel:' . preg_replace( '/[^0-9+]/', '', $value );
                            }
                            ?>
                            <li class="flex items-start gap-2 text-base leading-[30px]">
                                <span class="mt-[3px] size-6 shrink-0" aria-hidden="true"><?= icon( 'contact-' . $iconType, 'size-6' ); ?></span>
                                <?php if ( $url ) : ?>
                                    <a class="transition-colors duration-200 hover:text-yellow-primary" href="<?= esc_url( $url ); ?>"><?= esc_html( $value ); ?></a>
                                <?php else : ?>
                                    <span><?= esc_html( $value ); ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>

            <div class="grid grid-cols-1 xl:grid-cols-4 xl:items-start gap-4 xl:gap-12 w-full">
                <?php foreach ( $menuSections as $index => $menuLocation ) :
                    $panelId = 'footer-menu-' . $index;
                    $menuHtml = $renderMenu( $menuLocation );
                    $menuName = $getMenuName( $menuLocation );

                    if ( '' === $menuName ) {
                        continue;
                    }
                    ?>
                    <section class="overflow-hidden rounded-xl bg-white/[0.03] backdrop-blur-[22px] xl:overflow-visible xl:rounded-none xl:bg-transparent xl:backdrop-blur-none" data-footer-item>
                        <button class="flex h-16 w-full items-center justify-between px-5 text-right text-lg font-medium xl:pointer-events-none xl:h-auto xl:px-0 xl:text-base xl:text-[#575775]" type="button" aria-expanded="false" aria-controls="<?= esc_attr( $panelId ); ?>" data-footer-toggle>
                            <span><?= esc_html( $menuName ); ?></span>
                            <span class="size-5 transition-transform duration-200 xl:hidden" aria-hidden="true" data-footer-arrow><?= icon( 'arrow-down', 'size-5 fill-white' ); ?></span>
                        </button>
                        <div class="px-5 pb-5 xl:mt-2.5 xl:px-0 xl:pb-0" id="<?= esc_attr( $panelId ); ?>" hidden data-footer-panel>
                            <span class="mb-2.5 hidden h-px w-[27px] bg-description xl:block"></span>
                            <?= $menuHtml;?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ( $footerCopyright ) : ?>
            <div class="copyright mt-8 w-full relative pt-5 text-right text-xs leading-[30px] text-white xl:mt-10 xl:text-center xl:text-sm [&_a]:text-yellow-primary text-[14px] font-normal">
                <?= wp_kses_post( $footerCopyright ); ?>
            </div>
        <?php endif; ?>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
