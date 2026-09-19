<?php
/** Filterable local domain catalogue; navigation state never enters the URL. */
defined( 'ABSPATH' ) || exit;

$selected = get_sub_field( 'domain_products' );
$selected = is_array( $selected ) ? array_values( array_unique( array_filter( array_map(
    static fn( $item ): int => $item instanceof WP_Post ? $item->ID : absint( $item ), $selected
) ) ) ) : [];
$query_args = [
    'post_type' => 'whmcs_domain_tld', 'post_status' => 'publish',
    'posts_per_page' => -1, 'no_found_rows' => true, 'ignore_sticky_posts' => true,
    'update_post_term_cache' => false, 'orderby' => 'title', 'order' => 'ASC',
];
if ( $selected ) {
    $query_args['post__in'] = $selected;
    $query_args['orderby'] = 'post__in';
}
$domains = [];
$categories = [];
foreach ( get_posts( $query_args ) as $domain_post ) {
    $domain = payam_get_whmcs_domain_tld_data( $domain_post->ID );
    if ( ! $domain ) { continue; }
    $domains[] = $domain;
    if ( '' !== trim( $domain['category'] ) && '' !== trim( $domain['category_label'] ) ) {
        $categories[ $domain['category'] ] = $domain['category_label'];
    }
}
if ( ! $domains ) { return; }
natcasesort( $categories );
$batch = get_sub_field( 'domain_items_per_page' );
$batch = is_numeric( $batch ) ? max( 1, min( 100, absint( $batch ) ) ) : 10;
$style = 'dark' === get_sub_field( 'section_style' ) ? 'dark' : 'light';
$color = sanitize_hex_color( (string) get_sub_field( 'section_color' ) );
$styles = $color ? [ 'background-color:' . $color ] : [];
foreach ( [ 'padding_top', 'padding_top_mobile', 'padding_bottom', 'padding_bottom_mobile' ] as $field ) {
    $value = get_sub_field( $field );
    if ( is_numeric( $value ) ) { $styles[] = '--domain-products-' . str_replace( '_', '-', $field ) . ':' . absint( $value ) . 'px'; }
}
$id = wp_unique_id( 'domain-products-' );
$price = static fn( $value ): string => is_numeric( $value ) && (float) $value >= 0 ? number_format_i18n( (float) $value ) : '—';
?>
<section class="domain-products-section" data-domain-products data-batch="<?= esc_attr( $batch ); ?>" data-header-theme="<?= esc_attr( $style ); ?>" style="<?= esc_attr( implode( ';', $styles ) ); ?>">
    <div class="container">
        <div class="domain-products-inner">
            <div class="domain-products-filters" data-domain-filters hidden role="search" aria-label="فیلتر دامنه‌ها">
                <label class="domain-products-search">
                    <span class="sr-only">جستجوی پسوند دامنه</span>
                    <?= icon( 'search', 'domain-products-search-icon' ); ?>
                    <input type="search" data-domain-search placeholder="دامنه موردنظر خود را وارد کنید ..." autocomplete="off" aria-controls="<?= esc_attr( $id ); ?>">
                </label>
                <label class="domain-products-category">
                    <span class="sr-only">دسته‌بندی دامنه</span>
                    <select data-domain-category aria-controls="<?= esc_attr( $id ); ?>">
                        <option value="">دسته‌بندی</option>
                        <?php foreach ( $categories as $category_key => $category_label ) : ?>
                            <option value="<?= esc_attr( $category_key ); ?>"><?= esc_html( $category_label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= icon( 'arrow-down', 'domain-products-chevron' ); ?>
                </label>
<!--                <button type="button" class="cta-link cta-btn-primary domain-products-search-button" data-domain-search-button>جستجو</button>-->
            </div>
            <div class="domain-products-head" aria-hidden="true">
                <span>پسوند</span><span>خرید <small>(سالانه)</small></span><span>تمدید/انتقال <small>(سالانه)</small></span><span>جریمه</span><span></span>
            </div>
            <ul class="domain-products-list" id="<?= esc_attr( $id ); ?>" data-domain-list aria-label="قیمت دامنه‌ها">
                <?php foreach ( $domains as $domain ) :
                    $link = $domain['order_link'];
                    $currency_label = match ( $domain['currency'] ) { 'IRT', 'TMN' => 'تومان', 'IRR' => 'ریال', 'USD' => 'دلار', default => $domain['currency'] };
                    $annual_label = $currency_label ? $currency_label . '/سالانه' : 'سالانه';
                    ?>
                    <li class="domain-product-row" data-domain-row data-extension="<?= esc_attr( $domain['extension'] ); ?>" data-category="<?= esc_attr( $domain['category'] ); ?>">
                        <strong class="domain-product-extension" dir="ltr">.<?= esc_html( $domain['extension'] ); ?></strong>
                        <div class="domain-product-price">
                            <span class="domain-product-label">خرید</span><small>(<?= esc_html( $annual_label ); ?>)</small>
                            <strong><?= esc_html( $price( $domain['register_price'] ) ); ?></strong>
                        </div>
                        <div class="domain-product-price">
                            <span class="domain-product-label">تمدید/انتقال</span><small>(<?= esc_html( $annual_label ); ?>)</small>
                            <strong><?= esc_html( $price( $domain['renew_price'] ) ); ?></strong>
                        </div>
                        <div class="domain-product-price domain-product-fee">
                            <span class="domain-product-label">جریمه</span><small><?= $currency_label ? '(' . esc_html( $currency_label ) . ')' : ''; ?></small>
                            <strong><?= esc_html( $price( $domain['redemption_fee'] ) ); ?></strong>
                        </div>
                        <?php if ( ! empty( $link['url'] ) ) : ?>
                            <a class="domain-product-buy" href="<?= esc_url( $link['url'] ); ?>" aria-label="<?= esc_attr( 'خرید دامنه .' . $domain['extension'] ); ?>"<?= '_blank' === ( $link['target'] ?? '' ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>خرید <?= icon( 'arrow-linear-2', 'domain-product-buy-icon' ); ?></a>
                        <?php else : ?><span class="domain-product-unavailable">ناموجود</span><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="domain-products-empty" data-domain-empty hidden>دامنه‌ای با این مشخصات پیدا نشد.</p>
            <p class="sr-only" data-domain-status role="status" aria-live="polite" aria-atomic="true"></p>
            <button type="button" class="domain-products-more" data-domain-more aria-controls="<?= esc_attr( $id ); ?>" hidden><span data-domain-more-label></span><?= icon( 'arrow-down', 'domain-products-chevron' ); ?></button>
        </div>
    </div>
</section>
