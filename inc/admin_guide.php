<?php
/** In-dashboard editorial guide for managing Payam Online content. */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', static function (): void {
	add_theme_page(
		'آموزش مدیریت سایت',
		'آموزش مدیریت سایت',
		'edit_pages',
		'payam-management-guide',
		'payam_render_management_guide'
	);
} );

add_action( 'admin_enqueue_scripts', static function ( string $hook ): void {
	if ( 'appearance_page_payam-management-guide' !== $hook ) {
		return;
	}

	$style = '/assets/styles/admin/admin-guide.css';
	wp_enqueue_style( 'payam-management-guide', get_theme_file_uri( $style ), [], payam_asset_version( $style ) );
} );

/** Render a compact field/value table. */
function payam_admin_guide_table( array $rows ): void {
	?>
	<div class="payam-guide-table-wrap">
		<table class="widefat striped payam-guide-table">
			<thead><tr><th>فیلد یا بخش</th><th>روش استفاده</th></tr></thead>
			<tbody>
			<?php foreach ( $rows as $field => $description ) : ?>
				<tr><th scope="row"><code><?= esc_html( $field ); ?></code></th><td><?= wp_kses_post( $description ); ?></td></tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/** Render the central management guide. */
function payam_render_management_guide(): void {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'شما اجازه مشاهده این صفحه را ندارید.' ) );
	}
	?>
	<div class="wrap payam-management-guide">
		<header class="payam-guide-hero">
			<div>
				<h1>آموزش مدیریت سایت پیام آنلاین</h1>
				<p>راهنمای ورود و نگهداری محتوای سایت، محصولات، دامنه‌ها و صفحات.</p>
			</div>
			<a class="button button-primary" href="<?= esc_url( admin_url( 'themes.php?page=payam-section-guide' ) ); ?>">مشاهده راهنمای سکشن‌ها</a>
		</header>

		<nav class="payam-guide-nav" aria-label="فهرست آموزش‌ها">
			<a href="#guide-page-builder">صفحه‌چین</a>
			<a href="#guide-products">محصولات WHMCS</a>
			<a href="#guide-filters">فیلتر محصولات</a>
			<a href="#guide-domains">دامنه‌ها</a>
			<a href="#guide-articles">مقالات</a>
			<a href="#guide-faq">سوالات متداول</a>
			<a href="#guide-forms">فرم‌ها</a>
			<a href="#guide-options">تنظیمات قالب</a>
		</nav>

		<main class="payam-guide-content">
			<section id="guide-page-builder" class="payam-guide-section">
				<h2>ساخت صفحه با صفحه‌چین</h2>
				<ol>
					<li>در ویرایش برگه، قالب «Page Builder» را انتخاب کنید.</li>
					<li>از فیلد «صفحه‌چین» سکشن موردنظر را اضافه و اطلاعات آن را تکمیل کنید.</li>
					<li>برای عنوان صفحه، سکشن مستقل «سربرگ سکشن» را قبل از سکشن محتوا قرار دهید.</li>
					<li>برای شناخت ظاهر سکشن‌ها ابتدا به «نمایش ← راهنمای سکشن‌ها» مراجعه کنید.</li>
				</ol>
				<div class="payam-guide-note">فاصله سکشن‌ها فقط با چهار فیلد فاصله بالا و پایین دسکتاپ و موبایل تنظیم شود؛ برای فاصله‌گذاری از سکشن خالی استفاده نکنید.</div>
				<?php payam_admin_guide_table( [
					'padding_top'           => 'فاصله بالای سکشن در دسکتاپ، برحسب پیکسل.',
					'padding_top_mobile'    => 'فاصله بالای سکشن در موبایل.',
					'padding_bottom'        => 'فاصله پایین سکشن در دسکتاپ.',
					'padding_bottom_mobile' => 'فاصله پایین سکشن در موبایل.',
				] ); ?>
			</section>

			<section id="guide-products" class="payam-guide-section">
				<h2>محصولات WHMCS</h2>
				<p>از منوی «محصولات WHMCS» یک محصول بسازید. عنوان پست، نام محصول روی کارت است. محصول باید منتشر شده باشد تا در سکشن‌ها قابل نمایش باشد.</p>
				<?php payam_admin_guide_table( [
					'whmcs_external_id'         => 'شناسه واقعی محصول در WHMCS. برای محصول دستی نیز مقدار یکتا وارد کنید.',
					'whmcs_group_id'            => 'شناسه گروه محصول در WHMCS.',
					'whmcs_product_type'        => 'نوع خام محصول؛ مانند <code>server</code> یا <code>hostingaccount</code>.',
					'whmcs_module'              => 'نام سیستمی ماژول ارائه سرویس.',
					'whmcs_pay_type'            => 'رایگان، یک‌باره یا دوره‌ای.',
					'whmcs_is_hidden'           => 'برای محصولی که نباید انتخاب یا عرضه شود فعال کنید.',
					'product_category'          => 'دسته تجاری محصول مانند سرور اختصاصی، VPS یا هاست.',
					'product_short_description' => 'توضیح کوتاه قابل نمایش روی کارت.',
					'product_card_icon'         => 'آیکون اصلی کارت؛ ترجیحاً تصویر بهینه و با Return Format برابر ID.',
					'product_order_link'        => 'لینک سفارش. لینک سفارشی وردپرس بر لینک همگام‌شده اولویت دارد.',
				] ); ?>

				<h3>قیمت محصول</h3>
				<p>در <code>product_prices</code> برای هر ارز یا دوره یک ردیف بسازید. مبلغ را بدون ویرگول وارد کنید و فقط یک ردیف را به‌عنوان قیمت اصلی مشخص کنید.</p>
				<?php payam_admin_guide_table( [
					'currency_code'    => 'کد پایدار ارز مانند <code>IRT</code> یا <code>USD</code>.',
					'billing_cycle'    => 'دوره پرداخت مانند ماهانه، سالانه یا یک‌باره.',
					'price_amount'     => 'فقط عدد خام؛ مثال: <code>5625000</code>.',
					'setup_fee'        => 'هزینه راه‌اندازی، در صورت وجود.',
					'price_suffix'     => 'متن کنار قیمت؛ مانند «تومان/ماهانه».',
					'is_primary_price' => 'قیمتی که روی کارت نمایش داده می‌شود.',
				] ); ?>

				<h3>ویژگی‌های کارت</h3>
				<p>در <code>product_features</code> مشخصات محصول را به ترتیب نمایش وارد کنید. کارت سرور حداکثر سه ویژگی اولِ فعال را نمایش می‌دهد.</p>
				<?php payam_admin_guide_table( [
					'feature_key'          => 'کلید ثابت انگلیسی مانند <code>ram</code>، <code>cpu</code> یا <code>disk</code>.',
					'feature_label'        => 'عنوان نمایشی ویژگی.',
					'feature_value'        => 'مقدار ویژگی بدون واحد.',
					'feature_unit'         => 'واحد مانند GB یا Core.',
					'feature_icon_name'    => 'نام آیکون موجود در icon pack.',
					'feature_show_on_card' => 'برای نمایش این ویژگی روی کارت فعال باشد.',
				] ); ?>
				<div class="payam-guide-warning">فیلدهای دارای پیشوند <code>whmcs_</code> در آینده توسط API به‌روزرسانی می‌شوند. توضیح، آیکون و لینک نمایشی را فقط در فیلدهای <code>product_</code> مدیریت کنید.</div>
			</section>

			<section id="guide-filters" class="payam-guide-section">
				<h2>فیلتر محصولات سرور</h2>
				<p>ابتدا مقدارهای قابل فیلتر را در خود محصول و سپس گزینه‌های قابل انتخاب را در سکشن کارت سرور تعریف کنید.</p>
				<ol>
					<li>در محصول، داخل <code>product_filter_values</code> یک <code>filter_key</code> و یک <code>filter_value</code> پایدار وارد کنید.</li>
					<li>در <code>server_card_section</code>، داخل <code>server_filters</code> فیلتری با همان کلید بسازید.</li>
					<li>مقدار <code>option_value</code> باید دقیقاً با مقدار ثبت‌شده روی محصول یکسان باشد.</li>
					<li>عنوان فارسی فقط در <code>option_label</code> نوشته شود.</li>
				</ol>
				<p class="payam-guide-example"><strong>مثال:</strong> روی محصول <code>filter_key = operating_system</code> و <code>filter_value = linux</code>؛ در سکشن نیز گزینه‌ای با value برابر <code>linux</code> و label برابر «لینوکس» بسازید.</p>
				<div class="payam-guide-note">فیلترها با منطق AND کار می‌کنند و حداکثر چهار فیلتر در هر سکشن نمایش داده می‌شود.</div>
			</section>

			<section id="guide-domains" class="payam-guide-section">
				<h2>مدیریت دامنه‌ها</h2>
				<p>هر پسوند در «دامنه‌های WHMCS» یک رکورد مستقل است. پسوند را بدون نقطه ذخیره کنید؛ مثلاً <code>ir</code> یا <code>com</code>.</p>
				<p>دسته اصلی را از فیلد «دسته‌بندی دامنه» انتخاب کنید. نامک ترم کلید پایدار WHMCS و نام ترم عنوان فارسی قابل نمایش در فیلتر است.</p>
				<?php payam_admin_guide_table( [
					'whmcs_extension'         => 'پسوند پایدار بدون نقطه.',
					'whmcs_register_price'    => 'قیمت ثبت دامنه، فقط عدد خام.',
					'whmcs_renew_price'       => 'قیمت مشترک تمدید و انتقال دامنه.',
					'whmcs_currency'          => 'کد ارز مانند IRT یا USD.',
					'whmcs_category'          => 'کلید پایدار دسته مانند popular.',
					'whmcs_category_label'    => 'عنوان نمایشی دسته مانند محبوب‌ترین‌ها.',
					'whmcs_group'             => 'برچسب گروه مانند HOT، NEW یا SALE.',
					'domain_regular_price'    => 'قیمت قبلی اختیاری برای نمایش تخفیف.',
					'domain_discount_percent' => 'درصد تخفیف اختیاری؛ در نبود آن می‌تواند از قیمت‌ها محاسبه شود.',
					'domain_price_suffix'     => 'متن نمایشی مانند «تومان/سالانه».',
					'domain_order_link'       => 'لینک ثبت یا خرید این پسوند.',
				] ); ?>
				<p>در سکشن بررسی دامنه، فیلد <code>featured_domains</code> را باز کنید و دامنه‌ها را به ترتیب دلخواه انتخاب کنید.</p>
			</section>

			<section id="guide-articles" class="payam-guide-section">
				<h2>مقالات و صفحه مقاله</h2>
				<ol>
					<li>مقاله را از «نوشته‌ها» بسازید و عنوان، متن، تصویر شاخص، دسته و برچسب‌ها را تکمیل کنید.</li>
					<li>برای ساخت فهرست «آنچه در این مقاله می‌خوانید»، تیترهای داخل متن را با Headingهای H2 تا H6 ثبت کنید.</li>
					<li>تعداد مقالات آرشیو از «تنظیمات ← خواندن ← بیشترین تعداد نوشته‌ها در هر برگه» خوانده می‌شود.</li>
					<li>چینش عمومی پایین همه مقالات در تنظیمات قالب و بخش <code>article_page_builder</code> قرار دارد.</li>
					<li>برای یک مقاله متفاوت، گزینه «چینش اختصاصی پایین مقاله» را فعال و Page Builder همان مقاله را تکمیل کنید.</li>
				</ol>
				<div class="payam-guide-note">در حالت عادی گزینه چینش اختصاصی خاموش بماند تا تغییرات چینش عمومی روی همه مقالات اعمال شود.</div>
			</section>

			<section id="guide-faq" class="payam-guide-section">
				<h2>سوالات متداول</h2>
				<p>در «سوالات متداول» عنوان پست را به‌عنوان سؤال و ویرایشگر را به‌عنوان پاسخ تکمیل کنید. در صورت نیاز دسته مناسب را نیز انتخاب کنید.</p>
				<p>در سکشن FAQ، منبع را روی <code>cpt</code> قرار دهید تا سوال‌ها از کاتالوگ خوانده شوند. نمایش دسته‌بندی و تعداد آیتم‌های هر صفحه نیز از تنظیمات همان سکشن کنترل می‌شود. برای تعداد کم می‌توانید منبع دستی را انتخاب کنید.</p>
			</section>

			<section id="guide-forms" class="payam-guide-section">
				<h2>فرم‌های تماس</h2>
				<ol>
					<li>از منوی «فرم‌ها» یک فرم منتشرشده بسازید.</li>
					<li>فیلدهای متن، ایمیل، تلفن، متن بلند یا انتخابی را تعریف کنید و برای هر فیلد یک نام انگلیسی یکتا قرار دهید.</li>
					<li>شورت‌کد نمایش‌داده‌شده کنار فرم را کپی و در محتوای موردنظر قرار دهید.</li>
					<li>پیام‌های ارسال‌شده فقط در «پیام‌های دریافتی» پنل مدیریت قابل مشاهده‌اند.</li>
				</ol>
				<div class="payam-guide-warning">نام فیلد را با <code>field_</code> شروع نکنید و فرم منتشرنشده در سایت نمایش داده نمی‌شود.</div>
			</section>

			<section id="guide-options" class="payam-guide-section">
				<h2>تنظیمات قالب و صفحات عمومی</h2>
				<ul>
					<li><strong>هدر و فوتر:</strong> اطلاعات عمومی، تماس و لینک‌های سراسری را فقط در تنظیمات قالب و فهرست‌های وردپرس مدیریت کنید.</li>
					<li><strong>فهرست‌ها:</strong> منوی سربرگ، ستون‌های فوتر و لینک‌های مفید مقاله از «نمایش ← فهرست‌ها» تنظیم می‌شوند.</li>
					<li><strong>پایین مقالات:</strong> از چینش عمومی <code>article_page_builder</code> استفاده کنید.</li>
					<li><strong>صفحه 404:</strong> چینش مستقل <code>error_404_builder</code> دارد و نباید با چینش مقالات مشترک باشد.</li>
					<li><strong>تصاویر:</strong> از Media Library استفاده، متن جایگزین مناسب ثبت و تصویر را متناسب با جایگاه انتخاب کنید.</li>
				</ul>
				<div class="payam-guide-warning">شناسه‌ها، کلیدهای انگلیسی و مقادیر سیستمی را بعد از استفاده در صفحات تغییر ندهید؛ تغییر label فارسی مشکلی ندارد.</div>
			</section>
		</main>
	</div>
	<?php
}
