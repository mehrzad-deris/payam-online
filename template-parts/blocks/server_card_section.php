<?php
/**
 * Server cards section with product tabs.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor    = get_sub_field( 'section_color' ) ?: '';
$sectionStyle    = get_sub_field( 'section_style' ) ?: 'light';
$sectionServices = get_sub_field( 'service_tab' );

$getProductField = static function ( string $fieldName, int $postId ) {
    return function_exists( 'get_field' ) ? get_field( $fieldName, $postId ) : get_post_meta( $postId, $fieldName, true );
};

$normalizePrice = static function ( $prices ): array {
    if ( ! is_array( $prices ) ) {
        return [];
    }

    $validPrices = array_values( array_filter( $prices, static function ( $price ): bool {
        return is_array( $price ) && ( isset( $price['price_amount'] ) || ! empty( $price['price_suffix'] ) );
    } ) );

    if ( empty( $validPrices ) ) {
        return [];
    }

    foreach ( $validPrices as $price ) {
        if ( ! empty( $price['is_primary_price'] ) ) {
            return $price;
        }
    }

    return $validPrices[0];
};

$normalizeProduct = static function ( $productReference ) use ( $getProductField, $normalizePrice ): ?array {
    $postId = absint( $productReference instanceof WP_Post ? $productReference->ID : $productReference );

    if ( ! $postId || 'whmcs_product' !== get_post_type( $postId ) || 'publish' !== get_post_status( $postId ) ) {
        return null;
    }

    $iconField = $getProductField( 'product_card_icon', $postId );
    $iconId    = absint( is_array( $iconField ) ? ( $iconField['ID'] ?? 0 ) : $iconField );
    $linkField = $getProductField( 'product_order_link', $postId );
    $link      = is_array( $linkField ) ? $linkField : [];
    $features  = $getProductField( 'product_features', $postId );
    $features  = is_array( $features ) ? array_values( array_filter( $features, static function ( $feature ): bool {
        return is_array( $feature ) && (
            ! array_key_exists( 'feature_show_on_card', $feature ) || ! empty( $feature['feature_show_on_card'] )
        );
    } ) ) : [];
    $features  = array_slice( $features, 0, 3 );
    $price     = $normalizePrice( $getProductField( 'product_prices', $postId ) );

    // Temporary fallbacks keep products entered with the first schema usable.
    $amount = array_key_exists( 'price_amount', $price )
        ? max( 0, (float) $price['price_amount'] )
        : max( 0, (float) $getProductField( 'product_price', $postId ) );
    $period = trim( (string) ( $price['price_suffix'] ?? '' ) );

    if ( '' === $period ) {
        $period = (string) $getProductField( 'product_period_label', $postId );
    }

    return [
        'id'       => $postId,
        'title'    => get_the_title( $postId ),
        'desc'     => (string) ( $getProductField( 'product_short_description', $postId ) ?: $getProductField( 'product_description', $postId ) ),
        'icon_id'  => $iconId,
        'icon_key' => sanitize_key( $getProductField( 'product_icon_name', $postId ) ?: $getProductField( 'product_icon_key', $postId ) ?: '' ),
        'amount'   => $amount,
        'period'   => $period,
        'features' => $features,
        'url'      => (string) ( $link['url'] ?? '' ),
        'target'   => (string) ( $link['target'] ?? '' ),
    ];
};

$sectionServices = is_array( $sectionServices ) ? array_values( array_filter( array_map(
    static function ( $service ) use ( $normalizeProduct ): ?array {
        if ( ! is_array( $service ) || '' === trim( (string) ( $service['tab_title'] ?? '' ) ) ) {
            return null;
        }

        $productReferences = is_array( $service['selected_products'] ?? null ) ? $service['selected_products'] : [];
        $products          = array_values( array_filter( array_map( $normalizeProduct, $productReferences ) ) );

        return [
            'tab_title' => (string) $service['tab_title'],
            'products'  => $products,
        ];
    },
    $sectionServices
) ) ) : [];

$tabsId               = wp_unique_id( 'server-tabs-' );
$hasMultipleTabs      = count( $sectionServices ) > 1;
?>

<section class="server-card-section mb-32 relative" data-header-theme="<?= esc_attr( $sectionStyle ); ?>" <?php if ( '' !== $sectionColor ) : ?>style="background-color: <?= esc_attr( $sectionColor ); ?>"<?php endif; ?>>
    <div class="container relative z-2 pb-16">
    <?php if ( ! empty( $sectionServices ) ) : ?>
        <div class="server-tabs mt-8"<?= $hasMultipleTabs ? ' data-tabs data-tabs-mobile="tabs"' : ''; ?>>
            <?php if ( $hasMultipleTabs ) : ?>
            <div class="server-tabs-list" role="tablist" aria-label="انتخاب موقعیت سرور">
                <div class="server-tab-list-inner">
                    <?php foreach ( $sectionServices as $index => $serviceItem ) :
                        $tabTitle = (string) $serviceItem['tab_title'];
                        $tabId = $tabsId . '-tab-' . $index;
                        $panelId = $tabsId . '-panel-' . $index;
                        ?>
                        <button class="server-tab-button" type="button" id="<?= esc_attr( $tabId ); ?>" role="tab" aria-controls="<?= esc_attr( $panelId ); ?>" aria-selected="<?= 0 === $index ? 'true' : 'false'; ?>" tabindex="<?= 0 === $index ? '0' : '-1'; ?>">
                            <?= esc_html( $tabTitle ); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
			<?php endif; ?>

            <div class="server-tabs-panels">
                <?php foreach ( $sectionServices as $index => $serviceItem ) :
                    $tabId = $tabsId . '-tab-' . $index;
                    $panelId = $tabsId . '-panel-' . $index;
                    $panelServers = $serviceItem['products'];
                    ?>
                    <div class="server-tab-panel" id="<?= esc_attr( $panelId ); ?>" <?php if ( $hasMultipleTabs ) : ?>role="tabpanel" aria-labelledby="<?= esc_attr( $tabId ); ?>" tabindex="0"<?php endif; ?> <?= 0 !== $index ? 'hidden' : ''; ?>>
                        <div class="server-list pt-10">
                            <?php foreach ( $panelServers as $serverItem ) :
                                $serverId = absint( $serverItem['id'] ?? 0 );
                                $serverTitle = trim( (string) ( $serverItem['title'] ?? '' ) );
                                $serverUrl = (string) ( $serverItem['url'] ?? '' );
                                $serverTarget = (string) ( $serverItem['target'] ?? '' );
                                $serverIcon = sanitize_key( $serverItem['icon_key'] ?? '' );
                                $serverIconImageId = absint( $serverItem['icon_id'] ?? 0 );
                                $titleId = wp_unique_id( 'server-card-title-' );
                                ?>
                                <article class="server-card-item" aria-labelledby="<?= esc_attr( $titleId ); ?>" <?php if ( $serverId ) : ?>data-server-id="<?= esc_attr( $serverId ); ?>"<?php endif; ?>>
                                    <?php if ( '' !== $serverUrl ) : ?>
                                        <a href="<?= esc_url( $serverUrl ); ?>" class="server-card-overlay" aria-label="<?= esc_attr( 'مشاهده و خرید ' . $serverTitle ); ?>"<?= $serverTarget ? ' target="' . esc_attr( $serverTarget ) . '"' : ''; ?><?= '_blank' === $serverTarget ? ' rel="noopener noreferrer"' : ''; ?>></a>
                                    <?php endif; ?>

                                    <div class="server-card">

                                            <header class="server-card-header">
                                                <?php if ( $serverIconImageId || '' !== $serverIcon ) : ?>
                                                    <span class="server-card-icon" aria-hidden="true">
                                                    <?php if ( $serverIconImageId ) : ?>
                                                        <?= wp_get_attachment_image(
                                                            $serverIconImageId,
                                                            'full',
                                                            false,
                                                            [
                                                                'class'    => 'server-card-icon-image',
                                                                'alt'      => '',
                                                                'decoding' => 'async',
                                                            ]
                                                        ); ?>
                                                    <?php else : ?>
                                                        <?= icon( $serverIcon, 'server-card-icon-svg' ); ?>
                                                    <?php endif; ?>
                                                    </span>
                                                <?php endif; ?>
                                                <div class="caption-block">
                                                    <h3 id="<?= esc_attr( $titleId ); ?>" class="server-card-title text-desktop-h5 text-neutral-900"><?= esc_html( $serverTitle ); ?></h3>
                                                    <?php if ( '' !== $serverItem['desc'] ) : ?><p class="server-card-description text-caption text-neutral-500"><?= esc_html( $serverItem['desc'] ); ?></p><?php endif; ?>
                                                </div>
                                            </header>

                                            <?php if ( ! empty( $serverItem['features'] ) ) : ?>
                                                <dl class="server-card-config">
                                                    <?php foreach ( $serverItem['features'] as $config ) :
                                                        if ( ! is_array( $config ) ) {
                                                            continue;
                                                        }
                                                        $configIcon  = sanitize_key( $config['feature_icon_name'] ?? $config['feature_icon'] ?? '' );
                                                        $configIconField = $config['feature_icon_image'] ?? 0;
                                                        $configIconImageId = absint( is_array( $configIconField ) ? ( $configIconField['ID'] ?? 0 ) : $configIconField );
                                                        $configLabel = trim( (string) ( $config['feature_label'] ?? '' ) );
                                                        $configType  = (string) ( $config['feature_value_type'] ?? 'text' );
                                                        $configValue = 'boolean' === $configType
                                                            ? ( ! empty( $config['feature_boolean'] ) ? 'دارد' : 'ندارد' )
                                                            : trim( (string) ( $config['feature_value'] ?? '' ) );
                                                        $configUnit = trim( (string) ( $config['feature_unit'] ?? '' ) );

                                                        if ( '' !== $configUnit && '' !== $configValue ) {
                                                            $configValue .= ' ' . $configUnit;
                                                        }

                                                        if ( '' === $configLabel && '' === $configValue ) {
                                                            continue;
                                                        }
                                                        ?>
                                                        <div class="server-card-config-item ">
                                                            <?php if ( $configIconImageId || '' !== $configIcon ) : ?>
                                                                <span class="server-card-config-icon" aria-hidden="true">
                                                                    <?php if ( $configIconImageId ) : ?>
                                                                        <?= wp_get_attachment_image( $configIconImageId, 'thumbnail', false, [
                                                                            'class'    => 'server-card-config-icon-image',
                                                                            'alt'      => '',
                                                                            'loading'  => 'lazy',
                                                                            'decoding' => 'async',
                                                                        ] ); ?>
                                                                    <?php else : ?>
                                                                        <?= icon( $configIcon, 'server-card-config-icon-svg' ); ?>
                                                                    <?php endif; ?>
                                                                </span>
                                                            <?php endif; ?>
                                                                <?php if ( '' !== $configLabel ) : ?>
                                                                    <dt><?= esc_html( $configLabel ); ?></dt>
                                                                <?php endif; ?>

                                                                <?php if ( '' !== $configValue ) : ?>
                                                                    <dd><?= esc_html( $configValue ); ?></dd>
                                                                <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </dl>
                                            <?php endif; ?>

                                            <?php $amount = (float) ( $serverItem['amount'] ?? 0 );
                                            $periodLabel  = trim( (string) ( $serverItem['period'] ?? '' ) ); ?>

                                            <?php if ( $amount > 0 || '' !== $periodLabel ) : ?>
                                                <p class="server-card-price">
                                                    <span class="price-caption">شروع قیمت از:</span>
                                                    <span class="price-amount">
                                                        <?php if ( $amount > 0 ) : ?>
                                                            <data value="<?= esc_attr( $amount ); ?>">
                                                                <strong>
                                                                    <?= esc_html( number_format_i18n( $amount ) ); ?>
                                                                </strong>
                                                            </data>
                                                        <?php endif; ?>

                                                        <?php if ( '' !== $periodLabel ) : ?><span class="period-label"><?= esc_html( $periodLabel ); ?></span><?php endif; ?>
                                                    </span>
                                                </p>
                                            <?php endif; ?>

                                    </div>

                                    <?php if ( '' !== $serverUrl ) : ?>
                                        <span class="server-card-link" aria-hidden="true">مشاهده و خرید <span><?= icon( 'arrow-linear-2' ); ?></span></span>
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
