<?php
/**
 * Inner-page hero with server tabs.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor    = get_sub_field( 'section_color' ) ?: '';
$sectionStyle    = get_sub_field( 'section_style' ) ?: 'light';
$sectionTitle    = (string) ( get_sub_field( 'section_title' ) ?: '' );
$sectionDesc     = (string) ( get_sub_field( 'section_desc' ) ?: '' );
$sectionTitleTag = strtolower( (string) ( get_sub_field( 'title_tag' ) ?: 'h2' ) );
$sectionServices = get_sub_field( 'service_tab' );

if ( ! in_array( $sectionTitleTag, [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ], true ) ) {
    $sectionTitleTag = 'h2';
}

if ( ! is_array( $sectionServices ) ) {
    $sectionServices = [];
}

$sectionServices = array_values( array_filter( $sectionServices, static fn( $service ): bool => is_array( $service ) && ! empty( $service['tab_title'] ) ) );

/*
 * Temporary data source.
 * Replace only this source with the normalized WHMCS API response later.
 */
$server_items = [
        [
                'id'      => 101,
                'type'    => 'dedicated_server_iran',
                'icon'    => 'iran',
                'title'   => 'خرید سرور ایران',
                'desc'    => 'امکان دریافت سرور در موقعیت جغرافیایی ایران با سیستم‌عامل دلخواه',
                'pricing' => [
                        'amount'       => 5625000,
                        'currency'     => 'IRT',
                        'period'       => 'monthly',
                        'period_label' => 'تومان/ماهانه',
                ],
                'config'  => [
                        'disk' => [
                                'label' => 'Up to',
                                'value' => '3000GB SSD',
                        ],
                        'cpu'  => [
                                'label' => 'Up to',
                                'value' => '16 vCPU',
                        ],
                        'ram'  => [
                                'label' => 'Up to',
                                'value' => '32GB RAM',
                        ],
                ],
                'url'     => home_url( '#server-101' ),
        ],
        [
                'id'      => 102,
                'type'    => 'dedicated_server_iran',
                'icon'    => 'national',
                'title'   => 'خرید سرور خارج',
                'desc'    => 'امکان دریافت سرور در موقعیت‌های جغرافیایی خارج با سیستم‌عامل دلخواه',
                'pricing' => [
                        'amount'       => 5625000,
                        'currency'     => 'IRT',
                        'period'       => 'monthly',
                        'period_label' => 'تومان/ماهانه',
                ],
                'config'  => [
                        'disk' => [
                                'label' => 'Up to',
                                'value' => '3000GB SSD',
                        ],
                        'cpu'  => [
                                'label' => 'Up to',
                                'value' => '16 vCPU',
                        ],
                        'ram'  => [
                                'label' => 'Up to',
                                'value' => '32GB RAM',
                        ],
                ],
                'url'     => home_url( '#server-101' ),
        ],
        [
                'id'      => 103,
                'type'    => 'dedicated_server_iran',
                'icon'    => 'windows',
                'title'   => 'خرید سرور ویندوز',
                'desc'    => 'امکان دریافت سرور در موقعیت‌های جغرافیایی خارج با سیستم‌عامل دلخواه',
                'pricing' => [
                        'amount'       => 5625000,
                        'currency'     => 'IRT',
                        'period'       => 'monthly',
                        'period_label' => 'تومان/ماهانه',
                ],
                'config'  => [
                        'disk' => [
                                'label' => 'Up to',
                                'value' => '3000GB SSD',
                        ],
                        'cpu'  => [
                                'label' => 'Up to',
                                'value' => '16 vCPU',
                        ],
                        'ram'  => [
                                'label' => 'Up to',
                                'value' => '32GB RAM',
                        ],
                ],
                'url'     => home_url( '#server-101' ),
        ],
        [
                'id'      => 104,
                'type'    => 'dedicated_server_iran',
                'icon'    => 'linux',
                'title'   => 'خرید سرور لینوکس',
                'desc'    => 'امکان دریافت سرور در موقعیت‌های جغرافیایی خارج با سیستم‌عامل دلخواه',
                'pricing' => [
                        'amount'       => 5625000,
                        'currency'     => 'IRT',
                        'period'       => 'monthly',
                        'period_label' => 'تومان/ماهانه',
                ],
                'config'  => [
                        'disk' => [
                                'label' => 'Up to',
                                'value' => '3000GB SSD',
                        ],
                        'cpu'  => [
                                'label' => 'Up to',
                                'value' => '16 vCPU',
                        ],
                        'ram'  => [
                                'label' => 'Up to',
                                'value' => '32GB RAM',
                        ],
                ],
                'url'     => home_url( '#server-101' ),
        ],
        [
                'id'      => 201,
                'type'    => 'virtual_server_iran',
                'icon'    => 'iran',
                'title'   => 'خرید سرور مجازی ایران',
                'desc'    => 'امکان دریافت سرور در موقعیت جغرافیایی ایران با سیستم‌عامل دلخواه',
                'pricing' => [
                        'amount'       => 5625000,
                        'currency'     => 'IRT',
                        'period'       => 'monthly',
                        'period_label' => 'تومان/ماهانه',
                ],
                'config'  => [
                        'disk' => [
                                'label' => 'Up to',
                                'value' => '3000GB SSD',
                        ],
                        'cpu'  => [
                                'label' => 'Up to',
                                'value' => '16 vCPU',
                        ],
                        'ram'  => [
                                'label' => 'Up to',
                                'value' => '32GB RAM',
                        ],
                ],
                'url'     => home_url( '#server-201' ),
        ],
];

$allowedServerIcons = [
        'iran',
        'national',
        'windows',
        'linux',
];

$allowedServerConfigIcons = [
        'disk',
        'cpu',
        'ram',
];

/* Normalize the temporary array to the same contract expected from the WHMCS addon. */
$serverItems = array_values( array_filter( array_map( static function ( $server ) use ( $allowedServerIcons ): ?array {
    if ( ! is_array( $server ) ) {
        return null;
    }

    $type    = sanitize_key( $server['type'] ?? '' );
    $icon    = sanitize_key( $server['icon'] ?? '' );
    $title   = trim( (string) ( $server['title'] ?? '' ) );
    $pricing = is_array( $server['pricing'] ?? null ) ? $server['pricing'] : [];

    if ( '' === $type || '' === $title ) {
        return null;
    }

    return [
            'id'      => absint( $server['id'] ?? 0 ),
            'type'    => $type,
            'icon'    => in_array( $icon, $allowedServerIcons, true ) ? $icon : 'server',
            'title'   => $title,
            'desc'    => (string) ( $server['desc'] ?? '' ),
            'pricing' => [
                    'amount'       => max( 0, (float) ( $pricing['amount'] ?? 0 ) ),
                    'currency'     => sanitize_key( $pricing['currency'] ?? '' ),
                    'period'       => sanitize_key( $pricing['period'] ?? '' ),
                    'period_label' => (string) ( $pricing['period_label'] ?? '' ),
            ],
            'config'  => is_array( $server['config'] ?? null ) ? $server['config'] : [],
            'url'     => (string) ( $server['url'] ?? '' ),
    ];
}, $server_items ) ) );

$availableServerTypes = array_values( array_unique( array_column( $serverItems, 'type' ) ) );
$tabsId               = wp_unique_id( 'server-tabs-' );
?>

<section class="hero-section-on-page mb-32 <?= 'dark' === $sectionStyle ? 'text-white' : ''; ?> min-h-screen bottom-fade relative overflow-hidden" data-header-theme="<?= esc_attr( $sectionStyle ); ?>" <?php if ( '' !== $sectionColor ) : ?>style="background-color: <?= esc_attr( $sectionColor ); ?>"<?php endif; ?>>
    <div class="container relative z-2 pb-16 pt-39 lg:pt-43">
        <?php if ( '' !== $sectionTitle ) : ?>
        <div class="relative">
            <<?= esc_attr( $sectionTitleTag ); ?> class="mb-4 text-center text-[24px] font-bold leading-12 lg:text-[36px] lg:leading-14 section-heading">
            <span class="relative">
                <?= esc_html( $sectionTitle ); ?>

                <span class="gradient-shape shape-right top-2"><?= icon( 'rounded-shape', 'rounded-shape' ) ?></span>
               <span class="gradient-shape shape-left top-2"><?= icon( 'rounded-shape', 'rounded-shape' ) ?></span>
            </span>
        </<?= esc_attr( $sectionTitleTag ); ?>>
        </div>
    <?php endif; ?>

    <?php if ( '' !== $sectionDesc ) : ?>
        <p class="text-body-3 text-center text-description"><?= esc_html( $sectionDesc ); ?></p>
    <?php endif; ?>

    <?php if ( ! empty( $sectionServices ) ) : ?>
        <div class="server-tabs mt-8" data-tabs data-tabs-mobile="tabs">
            <div class="server-tabs-list" role="tablist" aria-label="<?= esc_attr( $sectionTitle ?: 'انتخاب موقعیت سرور' ); ?>">
                <div class="server-tab-list-inner">
                    <?php foreach ( $sectionServices as $index => $serviceItem ) :
                        $tabTitle = (string) $serviceItem['tab_title'];
                        $tabType = sanitize_key( $serviceItem['service_type'] ?? $serviceItem['server_type'] ?? $serviceItem['type'] ?? $availableServerTypes[ $index ] ?? '' );
                        $tabId = $tabsId . '-tab-' . $index;
                        $panelId = $tabsId . '-panel-' . $index;
                        ?>
                        <button class="server-tab-button" type="button" id="<?= esc_attr( $tabId ); ?>" role="tab" aria-controls="<?= esc_attr( $panelId ); ?>" aria-selected="<?= 0 === $index ? 'true' : 'false'; ?>" tabindex="<?= 0 === $index ? '0' : '-1'; ?>" data-server-type="<?= esc_attr( $tabType ); ?>">
                            <?= esc_html( $tabTitle ); ?>
                            <?= icon( 'top-shape', 'top-shape' ) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="server-tabs-panels">
                <?php foreach ( $sectionServices as $index => $serviceItem ) :
                    $tabType = sanitize_key( $serviceItem['service_type'] ?? $serviceItem['server_type'] ?? $serviceItem['type'] ?? $availableServerTypes[ $index ] ?? '' );
                    $tabId = $tabsId . '-tab-' . $index;
                    $panelId = $tabsId . '-panel-' . $index;
                    $panelServers = array_values( array_filter( $serverItems, static fn( array $server ): bool => $server['type'] === $tabType ) );
                    ?>
                    <div class="server-tab-panel" id="<?= esc_attr( $panelId ); ?>" role="tabpanel" aria-labelledby="<?= esc_attr( $tabId ); ?>" tabindex="0" <?= 0 !== $index ? 'hidden' : ''; ?>>
                        <div class="server-list pt-10">
                            <?php foreach ( $panelServers as $serverItem ) :
                                $serverId = absint( $serverItem['id'] ?? 0 );
                                $serverTitle = trim( (string) ( $serverItem['title'] ?? '' ) );
                                $serverUrl = (string) ( $serverItem['url'] ?? '' );
                                $serverIcon = sanitize_key( $serverItem['icon'] ?? '' );
                                $titleId = wp_unique_id( 'server-card-title-' );
                                $pricing = is_array( $serverItem['pricing'] ?? null ) ? $serverItem['pricing'] : [];

                                if ( ! in_array( $serverIcon, $allowedServerIcons, true ) ) {
                                    $serverIcon = 'server';
                                }
                                ?>
                                <article class="server-card-item" aria-labelledby="<?= esc_attr( $titleId ); ?>" <?php if ( $serverId ) : ?>data-server-id="<?= esc_attr( $serverId ); ?>"<?php endif; ?>>
                                    <div class="server-card">
                                        <?php if ( '' !== $serverUrl ) : ?>
                                        <a href="<?= esc_url( $serverUrl ); ?>" class="server-card-inner">
                                            <?php else : ?>
                                            <?php endif; ?>

                                            <header class="server-card-header">
                                                <span class="server-card-icon" aria-hidden="true">
                                                    <?= icon( $serverIcon, 'server-card-icon-svg' ); ?>
                                                </span>
                                                <h3 id="<?= esc_attr( $titleId ); ?>" class="server-card-title text-desktop-h5"><?= esc_html( $serverTitle ); ?></h3>
                                                <?php if ( '' !== $serverItem['desc'] ) : ?><p class="server-card-description text-caption"><?= esc_html( $serverItem['desc'] ); ?></p><?php endif; ?>
                                            </header>

                                            <?php if ( ! empty( $serverItem['config'] ) ) : ?>
                                                <dl class="server-card-config flex">
                                                    <?php foreach ( $serverItem['config'] as $configName => $config ) :
                                                        if ( ! is_array( $config ) ) {
                                                            continue;
                                                        }
                                                        $configIcon  = sanitize_key( $configName );
                                                        $configLabel = trim( (string) ( $config['label'] ?? '' ) );
                                                        $configValue = trim( (string) ( $config['value'] ?? '' ) );

                                                        if ( '' === $configLabel && '' === $configValue ) {
                                                            continue;
                                                        }
                                                        $configIcon = in_array( $configIcon, $allowedServerConfigIcons, true ) ? $configIcon : '';
                                                        ?>
                                                        <div class="server-card-config-item">
                                                            <?php if ( '' !== $configIcon ) : ?>
                                                                <span class="server-card-config-icon" aria-hidden="true">
                                                            <?= icon( $configIcon, 'server-card-config-icon-svg' ); ?>
                                                        </span>
                                                            <?php endif; ?>
                                                            <span>
                                                                <?php if ( '' !== $configLabel ) : ?>
                                                                    <dt><?= esc_html( $configLabel ); ?></dt>
                                                                <?php endif; ?>

                                                                <?php if ( '' !== $configValue ) : ?>
                                                                    <dd><?= esc_html( $configValue ); ?></dd>
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </dl>
                                            <?php endif; ?>

                                            <?php $amount = (float) ( $pricing['amount'] ?? 0 );
                                            $periodLabel  = trim( (string) ( $pricing['period_label'] ?? '' ) ); ?>

                                            <?php if ( $amount > 0 || '' !== $periodLabel ) : ?>
                                                <p class="server-card-price">
                                                    <span class="price-caption text-caption text-description-light">شروع قیمت از</span>
                                                    <span class="price-amount">
                                                        <?php if ( $amount > 0 ) : ?>
                                                            <data value="<?= esc_attr( $amount ); ?>">
                                                                <strong class="text-desktop-h3">
                                                                    <?= esc_html( number_format_i18n( $amount ) ); ?>
                                                                </strong>
                                                            </data>
                                                        <?php endif; ?>

                                                        <?php if ( '' !== $periodLabel ) : ?><span class="period-label text-body-3"><?= esc_html( $periodLabel ); ?></span><?php endif; ?>
                                                    </span>
                                                </p>
                                            <?php endif; ?>

                                            <?php if ( '' !== $serverUrl ) : ?>
                                        </a>
                                    <?php else : ?>
                                    <?php endif; ?>

                                        <?= icon( 'card-shape', 'card-shape-left' ); ?>
                                        <?= icon( 'card-shape', 'card-shape-right' ); ?>
                                        <?= icon( 'top-shape', 'top-shape' ) ?>
                                    </div>

                                    <?php if ( '' !== $serverUrl ) : ?>
                                        <a href="<?= esc_url( $serverUrl ); ?>" class="server-card-link text-caption" aria-hidden="true">مشاهده و خرید <?= icon( 'arrow-linear-2', 'fill-white w-5 h-5' ); ?> </a>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    </div>
</section>
