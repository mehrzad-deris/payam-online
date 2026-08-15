<?php
/**
 * About section block.
 */

defined( 'ABSPATH' ) || exit;

$sectionColor        = get_sub_field( 'section_color' ) ?: '#07041d';
$sectionStyle        = get_sub_field( 'section_style' ) ?: 'dark';
$sectionIcon         = absint( get_sub_field( 'section_icon' ) );
$sectionTitle        = get_sub_field( 'section_title' ) ?: '';
$sectionTitleTag     = get_sub_field( 'title_tag' ) ?: 'h2';
$sectionSubtitle     = get_sub_field( 'section_subtitle' ) ?: '';
$sectionAboutUsImage = absint( get_sub_field( 'about_image' ) );
$sectionAboutUsDesc  = get_sub_field( 'about_us_description' );
$sectionAboutLink1   = get_sub_field( 'link_1' );
$sectionAboutLink2   = get_sub_field( 'link_2' );
$mediaType           = (string) ( get_sub_field( 'about_media_type' ) ?: 'image' );
$videoPosterField    = get_sub_field( 'about_video_poster' );
$videoField          = get_sub_field( 'about_video' );
$marginTopField      = get_sub_field( 'section_margin_top' );
$marginBottomField   = get_sub_field( 'section_margin_bottom' );

$sectionStyles = [ 'background-color: ' . sanitize_hex_color( $sectionColor ) ];

if ( is_numeric( $marginTopField ) ) {
	$marginTop       = max( -1000, min( 1000, (int) $marginTopField ) );
	$sectionStyles[] = 'margin-top: ' . $marginTop . 'px';
}

if ( is_numeric( $marginBottomField ) ) {
	$marginBottom    = max( -1000, min( 1000, (int) $marginBottomField ) );
	$sectionStyles[] = 'margin-bottom: ' . $marginBottom . 'px';
}

$videoPosterId = absint( is_array( $videoPosterField ) ? ( $videoPosterField['ID'] ?? 0 ) : $videoPosterField );
$videoId       = absint( is_array( $videoField ) ? ( $videoField['ID'] ?? 0 ) : $videoField );
$videoUrl      = $videoId ? wp_get_attachment_url( $videoId ) : '';
$videoMime     = $videoId ? get_post_mime_type( $videoId ) : '';
$isVideo       = 'video' === $mediaType && $videoPosterId && $videoUrl;
$mediaImageId  = $isVideo ? $videoPosterId : $sectionAboutUsImage;
$modalId       = $isVideo ? wp_unique_id( 'about-video-modal-' ) : '';

$aboutDesktop1x = false;
$aboutDesktop2x = '';
$aboutMobile1x  = '';
$aboutMobile2x  = '';

if ( $mediaImageId ) {
	$aboutDesktop1x = wp_get_attachment_image_src( $mediaImageId, 'about_image_section' );
	$aboutDesktop2x = wp_get_attachment_image_url( $mediaImageId, 'about_image_section_2x' );
	$aboutMobile1x  = wp_get_attachment_image_url( $mediaImageId, 'about_image_section_mobile' );
	$aboutMobile2x  = wp_get_attachment_image_url( $mediaImageId, 'about_image_section_mobile_2x' );
}

$hasMedia = $aboutDesktop1x && ! empty( $aboutDesktop1x[0] );
?>

<section class="about-section about-section-<?= esc_attr( $sectionStyle ); ?>" data-header-theme="<?= esc_attr( $sectionStyle ); ?>" data-feature-module style="<?= esc_attr( implode( '; ', $sectionStyles ) ); ?>">
	<div class="container">
		<?php
		section_heading( [
			'icon'        => $sectionIcon,
			'title'       => $sectionTitle,
			'title_tag'   => $sectionTitleTag,
			'subtitle'    => $sectionSubtitle,
			'title_class' => 'dark' === $sectionStyle ? 'text-white' : '',
			'class'       => 'relative z-1',
		] );
		?>

		<?php if ( $hasMedia ) : ?>
			<div class="about-section-media">
				<?php if ( $isVideo ) : ?>
					<button type="button" class="about-video-trigger" data-about-video-open aria-controls="<?= esc_attr( $modalId ); ?>" aria-label="<?= esc_attr( sprintf( 'پخش ویدیو: %s', $sectionTitle ) ); ?>">
				<?php endif; ?>

				<picture class="about-section-image">
					<?php if ( $aboutMobile1x ) : ?>
						<source media="(max-width: 767px)" srcset="<?= esc_url( $aboutMobile1x ); ?> 1x<?= $aboutMobile2x ? ', ' . esc_url( $aboutMobile2x ) . ' 2x' : ''; ?>">
					<?php endif; ?>
					<img
						src="<?= esc_url( $aboutDesktop1x[0] ); ?>"
						<?= $aboutDesktop2x ? 'srcset="' . esc_url( $aboutDesktop1x[0] ) . ' 1x, ' . esc_url( $aboutDesktop2x ) . ' 2x"' : ''; ?>
						alt="<?= esc_attr( $sectionTitle ); ?>"
						width="1280"
						height="400"
						loading="lazy"
						decoding="async"
						class="rounded-[26px]"
					>
				</picture>

				<?php if ( $isVideo ) : ?>
						<span class="about-play" aria-hidden="true"><?= icon( 'watch-video', 'watch-video-svg' );?><span class="play-inner"></span></span>
                        <?= icon( 'play-2', 'about-play-icon fill-white' );?>
					</button>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $hasMedia && $sectionAboutUsDesc ) : ?>
			<div class="<?= 'dark' === $sectionStyle ? 'text-white' : ''; ?> leading-[35px] lg:px-37 md:px-10 md:text-center mt-10">
				<?= wp_kses_post( $sectionAboutUsDesc ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $sectionAboutLink1 || $sectionAboutLink2 ) : ?>
			<div class="about-section-links">
				<?php foreach ( [ [ $sectionAboutLink1, 'primary-cta' ], [ $sectionAboutLink2, 'secondary-cta' ] ] as $linkItem ) :
					$link       = is_array( $linkItem[0] ) ? $linkItem[0] : [];
					$linkUrl    = (string) ( $link['url'] ?? '' );
					$linkTitle  = (string) ( $link['title'] ?? '' );
					$linkTarget = (string) ( $link['target'] ?? '' );

					if ( '' === $linkUrl ) {
						continue;
					}
					?>
					<a href="<?= esc_url( $linkUrl ); ?>" class="cta-link <?= esc_attr( $linkItem[1] ); ?>"<?= $linkTarget ? ' target="' . esc_attr( $linkTarget ) . '"' : ''; ?><?= '_blank' === $linkTarget ? ' rel="noopener noreferrer"' : ''; ?>>
						<?= esc_html( $linkTitle ); ?>
						<span class="icon"><?= icon( 'arrow-linear', 'w-3.5 h-2.5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

    <?= icon('about-shape-left', 'bg-shape shape-left') ?>
    <?= icon('about-shape-left', 'bg-shape shape-right') ?>
    <span class="fade-bottom-shape" style="<?= $sectionColor ? 'background-color: ' . sanitize_hex_color( $sectionColor ) : '' ?>"></span>
</section>

<?php if ( $isVideo ) : ?>
	<dialog class="about-video-modal" id="<?= esc_attr( $modalId ); ?>" data-about-video-modal aria-label="<?= esc_attr( $sectionTitle ); ?>">
		<div class="about-video-dialog">
			<button type="button" class="about-video-close" data-about-video-close aria-label="بستن ویدیو">
				<?= icon( 'close', 'about-close-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
			<video class="about-video-player" controls playsinline preload="none" poster="<?= esc_url( $aboutDesktop1x[0] ); ?>" data-about-video-player>
				<source data-video-src="<?= esc_url( $videoUrl ); ?>"<?= $videoMime ? ' type="' . esc_attr( $videoMime ) . '"' : ''; ?>>
			</video>
		</div>
	</dialog>
<?php endif; ?>
