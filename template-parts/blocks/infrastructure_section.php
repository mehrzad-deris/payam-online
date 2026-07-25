<?php
/**
 * Global infrastructure section block.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor     = get_sub_field( 'section_color' ) ?: '#07041d';
$sectionStyle     = get_sub_field( 'section_style' ) ?: 'dark';
$sectionIcon      = absint( get_sub_field( 'section_icon' ) );
$sectionTitle     = get_sub_field( 'section_title' );
$sectionTitleTag  = get_sub_field( 'title_tag' ) ?: 'h2';
$sectionSubtitle  = get_sub_field( 'section_subtitle' );
$backgroundId     = absint( get_sub_field( 'background_image' ) ?: get_sub_field( 'background_desktop' ) );
$mapImageId       = absint( get_sub_field( 'map_image' ) );
$statistics       = get_sub_field( 'statistics' );
$transparentPixel = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
$backgroundSrc    = $backgroundId ? wp_get_attachment_url( $backgroundId ) : false;
$mapImageSrc      = $mapImageId ? wp_get_attachment_image_src( $mapImageId, 'full' ) : false;
$mapImageSrcset   = $mapImageId ? wp_get_attachment_image_srcset( $mapImageId, 'full' ) : false;

if ( is_array( $statistics ) ) {
    $statistics = array_slice( $statistics, 0, 4 );
}

$serverPins = [
        [
                'class'   => 'canada',
                'label'   => 'سرور کانادا',
                'latency' => '123ms',
                'asset'   => 'server-pin-canada.svg',
        ],
        [
                'class'   => 'uk',
                'label'   => 'سرور انگلستان',
                'latency' => '72ms',
                'asset'   => 'server-pin-uk.svg',
        ],
        [
                'class'   => 'germany',
                'label'   => 'سرور آلمان',
                'latency' => '94ms',
                'asset'   => 'server-pin-germany.svg',
        ],
        [
                'class'   => 'france',
                'label'   => 'سرور فرانسه',
                'latency' => '87ms',
                'asset'   => 'server-pin-france.svg',
        ],
        [
                'class'   => 'turkey',
                'label'   => 'سرور ترکیه',
                'latency' => '32ms',
                'asset'   => 'server-pin-turkey.svg',
        ],
];
?>

<section
        class="infrastructure-section infrastructure-section--<?= esc_attr( $sectionStyle ); ?> pt-20"
        data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
        data-feature-module
        style="background-color: <?= esc_attr( $sectionColor ); ?>"
>
    <?php if ( $backgroundSrc ) : ?>
        <div class="infrastructure-section__background" aria-hidden="true">
            <img src="<?= esc_url( $transparentPixel ); ?>" data-feature-src="<?= esc_url( $backgroundSrc ); ?>" alt="" decoding="async">
        </div>
    <?php endif; ?>

    <div class="container infrastructure-section__container">
        <?php
        section_heading( [
                'icon'           => $sectionIcon,
                'title'          => $sectionTitle,
                'title_tag'      => $sectionTitleTag,
                'subtitle'       => $sectionSubtitle,
                'icon_class'     => 'infrastructure-section__heading-icon',
                'title_class'    => $sectionStyle === 'dark' ? 'text-white' : '',
                'subtitle_class' => 'infrastructure-section__subtitle',
                'class'          => 'relative z-1',
        ] );
        ?>

        <?php if ( is_array( $statistics ) && ! empty( $statistics ) ) : ?>
            <div class="infrastructure-section__stats pb-22 lg:pb-0">
                <?php foreach ( $statistics as $statistic ) :
                    $value = (string) ( $statistic['counter_value'] ?? '0' );
                    $prefix = $statistic['counter_prefix'] ?? '';
                    $suffix = $statistic['counter_suffix'] ?? '';
                    $label = $statistic['counter_label'] ?? '';
                    $decimalPart = explode( '.', $value, 2 );
                    $decimals = isset( $decimalPart[1] ) ? strlen( rtrim( $decimalPart[1], '0' ) ) : 0;
                    $displayValue = number_format_i18n( (float) $value, $decimals );
                    ?>
                    <div class="infrastructure-section__stat">
                        <?= icon('top-shape', 'w-10 h-1 infrastructure-section__stat-line fill-white') ?>

                        <div class="infrastructure-section__stat-value" dir="ltr">
                            <?php if ( '' !== $prefix ) : ?><span><?= esc_html( $prefix ); ?></span><?php endif; ?>
                            <span data-counter-target="<?= esc_attr( $value ); ?>" data-counter-decimals="<?= esc_attr( $decimals ); ?>"><?= esc_html( $displayValue ); ?></span>
                            <?php if ( '' !== $suffix ) : ?><span><?= esc_html( $suffix ); ?></span><?php endif; ?>
                        </div>

                        <?php if ( '' !== $label ) : ?>
                            <p class="infrastructure-section__stat-label"><?= esc_html( $label ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ( $mapImageSrc ) : ?>
            <div class="infrastructure-section__map" aria-hidden="true">
                <picture>
                    <source media="(min-width: 1280px)"
                            data-feature-srcset="<?= esc_attr( $mapImageSrcset ?: $mapImageSrc[0] ); ?>"
                            sizes="1440px"
                    >
                    <img src="<?= esc_url( $transparentPixel ); ?>" alt="" width="1440" height="908" decoding="async">
                </picture>

                <?php foreach ( $serverPins as $index => $serverPin ) : ?>
                    <div class="infrastructure-section__pin infrastructure-section__pin--<?= esc_attr( $serverPin['class'] ); ?>" style="--pin-delay: <?= esc_attr( $index * 110 ); ?>ms">
                        <?= icon(esc_attr( $serverPin['class'] ) . '-flag', 'flag w-8 h-8 ') ?>
<!--                        <img src="--><?php //= esc_url( $transparentPixel ); ?><!--"-->
<!--                             data-feature-desktop-src="--><?php //= esc_url( get_theme_file_uri( '/assets/images/' . $serverPin['asset'] ) ); ?><!--"-->
<!--                             alt=""-->
<!--                             width="32"-->
<!--                             height="32"-->
<!--                             decoding="async"-->
<!--                        >-->
                        <span class="infrastructure-section__pin-copy">
							<strong><?= esc_html( $serverPin['label'] ); ?></strong>
							<small><?= esc_html( $serverPin['latency'] ); ?></small>
						</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
