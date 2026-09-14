<?php
/** 404 content uses the shared ACF page builder through an Options clone. */
defined( 'ABSPATH' ) || exit;

status_header( 404 );
get_header();
$builderSource = payam_options_builder_source( 'error_404_builder' );
?>
<main class="min-h-screen pt-[140px]" data-header-theme="light">
    <div class="">
        <div class="section-heading relative flex flex-col items-center text-center lg:gap-4 gap-2 mb-10">
            <span class="gradient-shape shape-right top-2" aria-hidden="true"><?= icon( 'rounded-shape', 'rounded-shape' ); ?></span>
            <span class="gradient-shape shape-left top-2" aria-hidden="true"><?= icon( 'rounded-shape', 'rounded-shape' ); ?></span>

            <div class="mb-10">
                <svg xmlns="http://www.w3.org/2000/svg" width="473" height="184" fill="none" viewBox="0 0 473 184"><g fill="#07041d" clip-path="url(#a)"><path d="M81.92 137.984H0v-24.32L44.8 0h36.608L38.144 107.776H81.92V56.32h35.584v51.456h18.944v30.208h-18.944v43.008H81.92zM216.488 183.552q-66.56 0-66.56-91.648 0-47.616 15.872-69.632Q181.672 0 215.976 0t50.176 22.528Q282.28 44.8 282.28 91.904q0 91.648-65.792 91.648m0-30.464q16.64 0 23.296-14.336 6.912-14.592 6.912-46.848 0-23.552-2.816-36.864-2.816-13.313-9.472-18.944-6.4-5.632-17.92-5.632t-18.176 5.632q-6.656 5.633-9.472 18.944-2.816 13.313-2.816 36.864 0 32.256 6.656 46.848 6.912 14.336 23.808 14.336M381.92 137.984H300v-24.32L344.8 0h36.608l-43.264 107.776h43.776V56.32h35.584v51.456h18.944v30.208h-18.944v43.008H381.92z"/></g><path fill="#bacaff" d="M265.571 132c21.04 0 38.095-16.855 38.095-37.647 0-16.479-10.713-30.485-25.632-35.587-2.12-18.808-18.262-33.433-37.86-33.433-21.039 0-38.095 16.855-38.095 37.647 0 4.6.834 9.005 2.362 13.079a29 29 0 0 0-5.537-.53c-15.779 0-28.571 12.642-28.571 28.236S183.125 132 198.904 132z"/><path fill="#1844da" d="M278.905 158.667c13.675 0 24.762-10.535 24.762-23.53 0-10.299-6.964-19.053-16.662-22.241C285.627 101.14 275.136 92 262.397 92c-13.676 0-24.762 10.534-24.762 23.529 0 2.875.542 5.628 1.535 8.175a19.5 19.5 0 0 0-3.599-.331c-10.256 0-18.571 7.9-18.571 17.647s8.315 17.647 18.571 17.647z"/><g fill="#07041d" clip-path="url(#b)"><path d="M118.92 137.984H37v-24.32L81.8 0h36.608L75.144 107.776h43.776V56.32h35.584v51.456h18.944v30.208h-18.944v43.008H118.92zM253.488 183.552q-66.56 0-66.56-91.648 0-47.616 15.872-69.632Q218.672 0 252.976 0t50.176 22.528Q319.28 44.8 319.28 91.904q0 91.648-65.792 91.648m0-30.464q16.64 0 23.296-14.336 6.912-14.592 6.912-46.848 0-23.552-2.816-36.864-2.816-13.313-9.472-18.944-6.4-5.632-17.92-5.632t-18.176 5.632q-6.656 5.633-9.472 18.944-2.816 13.313-2.816 36.864 0 32.256 6.656 46.848 6.912 14.336 23.808 14.336M418.92 137.984H337v-24.32L381.8 0h36.608l-43.264 107.776h43.776V56.32h35.584v51.456h18.944v30.208h-18.944v43.008H418.92z"/></g><defs><clipPath id="a"><path fill="#fff" d="M0 0h137v184H0z"/></clipPath><clipPath id="b"><path fill="#fff" d="M337 0h136v184H337z"/></clipPath></defs></svg>
            </div>

            <div class="section-heading__title whitespace-pre-line text-mobile-h3 md:text-desktop-h2 text-neutral-900 ">صفحه ای که دنبال آن هستیدیافت نشد!</div>

            <p class="section-heading__subtitle whitespace-pre-line max-w-180 text-body-mobile-3 md:text-desktop-h6 text-neutral-500 mb-5">صفحه ای که دنبالش هستید یا حذف شده و یا کلا وجود نداشته است!</p>

            <a href="<?php echo home_url('/') ?>" class="cta-link cta-btn-primary cta-has-icon py-2.75!">
                <span>بازگشت به صفحه اصلی</span>
                <?= icon('arrow-linear-2', 'w-5 h-5 fill-white hover:rotate-45 duration-200') ?>
            </a>
    </div>
    </div>
	<?php if ( payam_builder_sections( $builderSource ) ) : ?>
		<?php payam_render_builder( $builderSource ); ?>
	<?php else : ?>
		<section class="container py-32 text-center">
			<?php section_heading( [
				'title' => '۴۰۴ — صفحه پیدا نشد',
				'title_tag' => 'h1',
				'subtitle' => 'صفحه‌ای که به دنبال آن هستید وجود ندارد یا آدرس آن تغییر کرده است.',
			] ); ?>
			<a class="cta-link cta-btn-primary mt-8" href="<?= esc_url( home_url( '/' ) ); ?>"><span>بازگشت به صفحه اصلی</span></a>
		</section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
