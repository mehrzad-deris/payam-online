<?php
/**
 * Domain check section with legacy and promotional layouts.
 */
defined( 'ABSPATH' ) || exit;

$sectionColor       = sanitize_hex_color( (string) get_sub_field( 'section_color' ) );
$sectionStyle       = 'dark' === get_sub_field( 'section_style' ) ? 'dark' : 'light';
$domainCheckStyle   = 'style_2' === get_sub_field( 'domain_check_style' ) ? 'style_2' : 'style_1';
$sectionStyles      = [];
$searchPlaceholder = trim( (string) get_sub_field( 'domain_search_placeholder' ) ) ?: 'دامنه موردنظر خود را وارد کنید ...';
$searchButtonText  = trim( (string) get_sub_field( 'domain_search_button_text' ) ) ?: 'جستجو';
$searchAction      = get_sub_field( 'domain_search_action' );
$searchAction      = is_string( $searchAction ) && '' !== trim( $searchAction ) ? $searchAction : home_url( '/' );
$searchInputId     = wp_unique_id( 'domain-search-' );

if ( $sectionColor ) {
	$sectionStyles[] = 'background-color: ' . $sectionColor;
}

foreach ( [ 'padding_top', 'padding_top_mobile', 'padding_bottom', 'padding_bottom_mobile' ] as $paddingField ) {
	$paddingValue = get_sub_field( $paddingField );

	if ( is_numeric( $paddingValue ) ) {
		$sectionStyles[] = '--domain-check-' . str_replace( '_', '-', $paddingField ) . ': ' . absint( $paddingValue ) . 'px';
	}
}

$formatPrice = static function ( $price ): string {
	$price = trim( (string) $price );

	return is_numeric( $price ) ? number_format_i18n( (float) $price ) : $price;
};

$selectedDomains = get_sub_field( 'featured_domains' );
$selectedDomains = is_array( $selectedDomains ) ? $selectedDomains : [];
$domainTlds      = [];
$seenDomainIds   = [];

foreach ( $selectedDomains as $selectedDomain ) {
	$domainId = $selectedDomain instanceof WP_Post ? $selectedDomain->ID : absint( $selectedDomain );

	if ( ! $domainId || isset( $seenDomainIds[ $domainId ] ) ) {
		continue;
	}

	$domainData = payam_get_whmcs_domain_tld_data( $domainId );

	if ( ! $domainData ) {
		continue;
	}

	$seenDomainIds[ $domainId ] = true;
	$domainTlds[]                = $domainData;
}

$domainPrices = array_map(
	static fn( array $domain ): array => [
		'extension' => $domain['extension'],
		'price'     => $formatPrice( $domain['register_price'] ),
	],
	$domainTlds
);
?>
<section
	class="domain-check-section domain-check-<?= esc_attr( $domainCheckStyle ); ?><?= 'dark' === $sectionStyle ? ' text-white' : ''; ?>"
	data-domain-check
	data-header-theme="<?= esc_attr( $sectionStyle ); ?>"
	<?= $sectionStyles ? 'style="' . esc_attr( implode( '; ', $sectionStyles ) ) . '"' : ''; ?>
>
	<?php if ( 'style_2' === $domainCheckStyle ) :
		$actionBoxes       = get_sub_field( 'domain_action_boxes' );
		$actionBoxes       = is_array( $actionBoxes ) ? array_slice( array_values( array_filter( $actionBoxes, 'is_array' ) ), 0, 2 ) : [];
		$domainOffers      = array_slice( $domainTlds, 0, 4 );
		$moreDomainsLink   = get_sub_field( 'more_domains_link' );
		$moreDomainsLink   = is_array( $moreDomainsLink ) ? $moreDomainsLink : [];
		$domainPostCounts  = wp_count_posts( 'whmcs_domain_tld' );
		$totalDomainCount  = isset( $domainPostCounts->publish ) ? absint( $domainPostCounts->publish ) : count( $domainTlds );
		$remainingDomains = max( 0, $totalDomainCount - count( $domainOffers ) );
		$moreDomainsUrl   = ! empty( $moreDomainsLink['url'] ) ? (string) $moreDomainsLink['url'] : $searchAction;
		$moreDomainsTitle = trim( (string) ( $moreDomainsLink['title'] ?? '' ) );

		if ( '' === $moreDomainsTitle ) {
			$moreDomainsTitle = $remainingDomains > 0
				? sprintf( 'مشاهده %s دامنه دیگر', number_format_i18n( $remainingDomains ) )
				: 'مشاهده دامنه‌های بیشتر';
		}
		?>
		<div class="container domain-check-container">
			<div class="domain-search-wrap">
				<form class="domain-whois" action="<?= esc_url( $searchAction ); ?>" method="get" role="search">
					<label class="sr-only" for="<?= esc_attr( $searchInputId ); ?>">جستجوی دامنه</label>
					<input id="<?= esc_attr( $searchInputId ); ?>" type="text" name="domain" class="whois-input text-[14px] lg:text-[16px] py-5 placeholder:text-neutral-500 pe-14 ps-30 lg:ps-35 field-rtl" dir="ltr" placeholder="<?= esc_attr( $searchPlaceholder ); ?>" autocomplete="off">
					<?= icon( 'search', 'w-6 h-6 absolute right-6 top-5 stroke-neutral-500' ); ?>
					<button type="submit" class="whois-submit cta-link cta-btn-primary text-[14px] lg:text-[16px]"><?= esc_html( $searchButtonText ); ?></button>
				</form>

				<?php if ( $actionBoxes ) : ?>
					<div class="domain-actions">
						<?php foreach ( $actionBoxes as $box ) :
							$boxText = trim( (string) ( $box['action_text'] ?? '' ) );
							$boxLink = is_array( $box['action_link'] ?? null ) ? $box['action_link'] : [];
							if ( '' === $boxText && empty( $boxLink['url'] ) ) {
								continue;
							}
							?>
							<div class="domain-action">
								<?php if ( '' !== $boxText ) : ?><span><?= esc_html( $boxText ); ?></span><?php endif; ?>
								<?php if ( ! empty( $boxLink['url'] ) ) : ?>
									<a href="<?= esc_url( $boxLink['url'] ); ?>"<?= '_blank' === ( $boxLink['target'] ?? '' ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
										<span><?= esc_html( ( $boxLink['title'] ?? '' ) ?: 'مشاهده' ); ?></span>
										<?= icon( 'arrow-linear-2', 'domain-action-icon' ); ?>
									</a>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $domainOffers ) : ?>
				<div class="domain-offers">
					<?php foreach ( $domainOffers as $offer ) :
						$extension = (string) $offer['extension'];
						$regular   = $formatPrice( $offer['regular_price'] ?? '' );
						$sale      = $formatPrice( $offer['register_price'] ?? '' );
						$period    = trim( (string) ( $offer['price_suffix'] ?? '' ) );
						$discount  = absint( $offer['discount_percent'] ?? 0 );
						$orderLink = is_array( $offer['order_link'] ?? null ) ? $offer['order_link'] : [];
						$orderTitle = trim( (string) ( $orderLink['title'] ?? '' ) ) ?: 'مشاهده و خرید';
						?>
						<article class="domain-offer-card">
							<h3 dir="ltr">.<?= esc_html( $extension ); ?></h3>
							<?php if ( '' !== $regular ) : ?>
								<div class="domain-regular-price">
									<del><?= esc_html( $regular ); ?></del>
									<?php if ( '' !== $period ) : ?><small><?= esc_html( $period ); ?></small><?php endif; ?>
									<?php if ( $discount ) : ?><span class="domain-discount"><?= esc_html( $discount ); ?>%</span><?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ( '' !== $sale ) : ?>
								<div class="domain-sale-price"><strong><?= esc_html( $sale ); ?></strong><?php if ( '' !== $period ) : ?><small><?= esc_html( $period ); ?></small><?php endif; ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $orderLink['url'] ) ) : ?>
								<span class="domain-order-link" aria-hidden="true">
									<span><?= esc_html( $orderTitle ); ?></span>
									<?= icon( 'arrow-linear-2', 'domain-order-icon' ); ?>
								</span>
								<a class="domain-offer-overlay" href="<?= esc_url( $orderLink['url'] ); ?>" aria-label="<?= esc_attr( $orderTitle . ' دامنه .' . $extension ); ?>"<?= '_blank' === ( $orderLink['target'] ?? '' ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>></a>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $domainOffers && '' !== $moreDomainsUrl ) : ?>
            <div class="flex justify-center mt-10">
				<a class="more-domains-link" href="<?= esc_url( $moreDomainsUrl ); ?>"<?= '_blank' === ( $moreDomainsLink['target'] ?? '' ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
					<span><?= esc_html( $moreDomainsTitle ); ?></span>
					<?= icon( 'arrow-linear-2', 'more-domains-icon' ); ?>
				</a>
            </div>
			<?php endif; ?>
		</div>
	<?php else :
		$visibleItemsCount = min( 3, count( $domainPrices ) );
		?>
		<div class="container relative z-2">
			<div class="flex justify-center">
				<div class="w-197.5 max-w-full relative">
					<form class="domain-whois mb-4 lg:mb-5" action="<?= esc_url( $searchAction ); ?>" method="get" role="search">
						<label class="sr-only" for="<?= esc_attr( $searchInputId ); ?>">جستجوی دامنه</label>
						<input id="<?= esc_attr( $searchInputId ); ?>" type="text" name="domain" class="whois-input text-[14px] lg:text-[16px] py-5 placeholder:text-neutral-500 pe-14 ps-30 lg:ps-35 field-rtl" dir="ltr" placeholder="<?= esc_attr( $searchPlaceholder ); ?>" autocomplete="off">
						<?= icon( 'search', 'w-6 h-6 absolute right-6 top-5 stroke-neutral-500' ); ?>
						<button type="submit" class="whois-submit cta-link cta-btn-primary text-[14px] lg:text-[16px]"><?= esc_html( $searchButtonText ); ?></button>
					</form>

					<div class="domain-prices gap-2 lg:gap-5 scrollbar-none" data-domain-prices="<?= esc_attr( wp_json_encode( $domainPrices ) ); ?>">
						<?php for ( $i = 0; $i < $visibleItemsCount; $i++ ) : ?>
							<div class="domain-price-item">
								<div class="px-5 lg:px-10 py-2.5 stroke-yellow-primary flex items-center">
									<span dir="ltr" class="flex gap-1.5"><span>تومان</span><span class="domain-price"></span></span>
									<span><?= icon( 'arrow-linear', 'w-4.25 h-2' ); ?></span>
									<span dir="ltr" class="domain-extension"></span>
								</div>
							</div>
						<?php endfor; ?>
					</div>
				</div>
			</div>

		</div>
	<?php endif; ?>
</section>
