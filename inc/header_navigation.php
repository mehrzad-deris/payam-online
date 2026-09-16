<?php
/** Shared WordPress menu renderer for the header and mobile drawer. */
defined( 'ABSPATH' ) || exit;

class Payam_Header_Menu_Walker extends Walker_Nav_Menu {
	private string $submenu_id = '';

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<ul class="sub-menu" id="' . esc_attr( $this->submenu_id ) . '">';
	}

	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item = $data_object;
		$this->submenu_id = wp_unique_id( 'header-submenu-' );
		parent::start_el( $output, $item, $depth, $args, $current_object_id );
		if ( empty( $args->payam_mobile_menu ) && in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
			$output .= '<button type="button" class="header-submenu-toggle" data-submenu-toggle aria-expanded="false" aria-controls="' . esc_attr( $this->submenu_id ) . '" aria-label="' . esc_attr( 'زیرمنوی ' . wp_strip_all_tags( $item->title ) ) . '">' . icon( 'arrow-down', 'header-menu-chevron' ) . '</button>';
		}
	}
}

add_filter( 'nav_menu_item_id', static function ( $id, $item, $args ) {
	return ! empty( $args->payam_header_menu ) ? wp_unique_id( 'header-menu-item-' ) : $id;
}, 10, 3 );

add_filter( 'nav_menu_item_title', static function ( $title, $item, $args, $depth ) {
	if ( empty( $args->payam_header_menu ) ) {
		return $title;
	}
	$icon_id = absint( get_post_meta( $item->ID, 'menu_item_icon', true ) );
	$subtitle = (string) get_post_meta( $item->ID, 'menu_item_subtitle', true );
	$image = $icon_id ? wp_get_attachment_image( $icon_id, 'thumbnail', false, [ 'class' => 'header-menu-icon', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ] ) : '';
	return $image . '<span class="header-menu-copy"><span class="header-menu-title">' . esc_html( wp_strip_all_tags( $title ) ) . '</span>'
		. ( '' !== $subtitle ? '<span class="header-menu-subtitle">' . esc_html( $subtitle ) . '</span>' : '' )
		. '</span>' . ( in_array( 'menu-item-has-children', (array) $item->classes, true ) ? '<span class="header-menu-arrow" aria-hidden="true">' . icon( 'arrow-linear-2', 'header-menu-chevron' ) . '</span>' : '' );
}, 10, 4 );

function payam_header_menu( string $class ): void {
	wp_nav_menu( [
		'theme_location' => in_array( 'header-menu-mobile', explode( ' ', $class ), true ) ? 'mobile_menu' : 'main_menu',
		'container' => 'nav',
		'container_aria_label' => 'فهرست اصلی',
		'menu_class' => $class,
		'menu_id' => wp_unique_id( 'header-menu-' ),
		'fallback_cb' => false,
		'payam_header_menu' => true,
		'payam_mobile_menu' => in_array( 'header-menu-mobile', explode( ' ', $class ), true ),
		'walker' => new Payam_Header_Menu_Walker(),
	] );
}

/** Render a shared header action; an unset link intentionally has no output. */
function payam_header_action( string $name, string $class, string $icon_name, string $icon_class, string $label_class = '' ): void {
	$link = function_exists( 'get_field' ) ? get_field( $name, 'option' ) : null;
	if ( ! is_array( $link ) || empty( $link['url'] ) || ! esc_url( $link['url'] ) ) {
		return;
	}
	$title = ! empty( $link['title'] ) ? $link['title'] : ( 'header_account_link' === $name ? 'پنل کاربری' : 'مشاوره رایگان' );
	$target = ( $link['target'] ?? '' ) === '_blank' ? '_blank' : '_self';
	echo '<a href="' . esc_url( $link['url'] ) . '" class="' . esc_attr( $class ) . '" target="' . esc_attr( $target ) . '"' . ( '_blank' === $target ? ' rel="noopener noreferrer"' : '' ) . ' aria-label="' . esc_attr( $title ) . '"><span class="' . esc_attr( $label_class ) . '">' . esc_html( $title ) . '</span><span aria-hidden="true">' . icon( $icon_name, $icon_class ) . '</span></a>';
}
