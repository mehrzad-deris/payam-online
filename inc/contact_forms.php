<?php
/** Reusable ACF forms and private submissions. No field groups are registered here. */
defined( 'ABSPATH' ) || exit;

add_action( 'init', static function (): void {
	$caps = array_fill_keys( [ 'edit_post', 'read_post', 'delete_post', 'edit_posts', 'edit_others_posts', 'publish_posts', 'read_private_posts', 'delete_posts', 'delete_private_posts', 'delete_published_posts', 'delete_others_posts', 'edit_private_posts', 'edit_published_posts' ], 'manage_options' );
	$base = [ 'public' => false, 'publicly_queryable' => false, 'show_ui' => true, 'show_in_rest' => false, 'rewrite' => false, 'query_var' => false, 'supports' => [ 'title' ], 'capabilities' => $caps, 'map_meta_cap' => false ];
	register_post_type( 'payam_form', array_merge( $base, [ 'labels' => [ 'name' => 'فرم‌ها', 'singular_name' => 'فرم', 'add_new_item' => 'ساخت فرم', 'edit_item' => 'ویرایش فرم' ], 'menu_icon' => 'dashicons-feedback' ] ) );
	$base['capabilities']['create_posts'] = 'do_not_allow';
	register_post_type( 'payam_form_entry', array_merge( $base, [ 'labels' => [ 'name' => 'پیام‌های دریافتی', 'singular_name' => 'پیام', 'edit_item' => 'مشاهده پیام' ], 'show_in_menu' => 'edit.php?post_type=payam_form', 'supports' => [] ] ) );
} );

/** Only the published server-side definition is trusted. */
function payam_form_definition( int $id ): array {
	if ( ! function_exists( 'get_field' ) || 'payam_form' !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) ) { return []; }
	$rows = get_field( 'form_fields', $id );
	$fields = [];
	foreach ( is_array( $rows ) ? array_slice( $rows, 0, 30 ) : [] as $row ) {
		if ( ! is_array( $row ) ) { continue; }
		$key = sanitize_key( $row['input_name'] ?? '' );
		$type = $row['input_type'] ?? 'text';
		$label = sanitize_text_field( $row['input_label'] ?? '' );
		if ( ! $key || ! $label || isset( $fields[ $key ] ) || ! in_array( $type, [ 'text', 'email', 'tel', 'textarea', 'select' ], true ) ) { continue; }
		$options = [];
		foreach ( is_array( $row['input_options'] ?? null ) ? $row['input_options'] : [] as $option ) {
			$value = sanitize_text_field( $option['option_value'] ?? '' );
			if ( '' !== $value ) { $options[ $value ] = sanitize_text_field( $option['option_label'] ?? $value ); }
		}
		$fields[ $key ] = [ 'type' => $type, 'label' => $label, 'show_label' => ! empty( $row['input_show_label'] ), 'required' => ! empty( $row['input_required'] ), 'placeholder' => sanitize_text_field( $row['input_placeholder'] ?? '' ), 'wide' => 'full' === ( $row['input_width'] ?? '' ) || 'textarea' === $type, 'options' => $options ];
	}
	return $fields;
}

function payam_form_assets(): void {
	wp_enqueue_style( 'payam-section-contact' );
	wp_enqueue_script( 'payam-section-contact' );
}
add_action( 'wp_enqueue_scripts', static function (): void {
	if ( is_singular() && has_shortcode( (string) get_post_field( 'post_content', get_queried_object_id() ), 'payam_form' ) ) { payam_form_assets(); }
}, 25 );

add_shortcode( 'payam_form', static function ( $atts ): string {
	$atts = shortcode_atts( [ 'id' => 0 ], $atts, 'payam_form' );
	$id = absint( $atts['id'] );
	$fields = payam_form_definition( $id );
	if ( ! $fields ) { return ''; }
	payam_form_assets();
	ob_start();
	// Covers shortcodes rendered from ACF content after wp_head.
	if ( did_action( 'wp_head' ) && ! wp_style_is( 'payam-section-contact', 'done' ) ) { wp_print_styles( 'payam-section-contact' ); }
	get_template_part( 'template-parts/components/contact_form', null, [ 'id' => $id, 'fields' => $fields ] );
	return (string) ob_get_clean();
} );

function payam_form_request( string $key ): string {
	return isset( $_POST[ $key ] ) && is_string( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
}

/** Use the socket peer, never a client-supplied forwarded IP header. */
function payam_form_client(): string {
	return payam_client_fingerprint();
}

function payam_form_rate_limit( string $scope, int $limit ): void {
	if ( ! payam_rate_limit_consume( 'form-' . $scope, $limit, 10 * MINUTE_IN_SECONDS ) ) {
		wp_send_json_error( [ 'message' => 'تعداد درخواست‌ها زیاد است. چند دقیقه دیگر تلاش کنید.' ], 429 );
	}
}

function payam_form_challenge(): void {
	nocache_headers();
	payam_form_rate_limit( 'challenge', 60 );
	$id = absint( payam_form_request( 'form_id' ) );
	if ( ! payam_form_definition( $id ) ) { wp_send_json_error( [ 'message' => 'فرم در دسترس نیست.' ], 404 ); }
	$a = random_int( 1, 20 );
	$b = random_int( 1, 9 );
	$token = bin2hex( random_bytes( 24 ) );
	set_transient( 'payam_fc_' . $token, [ 'form' => $id, 'answer' => $a + $b, 'client' => payam_form_client(), 'created' => time() ], 10 * MINUTE_IN_SECONDS );
	wp_send_json_success( [ 'token' => $token, 'question' => sprintf( '%d + %d = ؟', $a, $b ), 'nonce' => wp_create_nonce( 'payam_form_' . $id ) ] );
}

function payam_form_submit(): void {
	nocache_headers();
	payam_form_rate_limit( 'submit', 15 );
	$id = absint( payam_form_request( 'form_id' ) );
	if ( ! wp_verify_nonce( payam_form_request( 'nonce' ), 'payam_form_' . $id ) ) { wp_send_json_error( [ 'message' => 'اعتبار فرم تمام شده؛ دوباره تلاش کنید.' ], 403 ); }
	$fields = payam_form_definition( $id );
	if ( ! $fields ) { wp_send_json_error( [ 'message' => 'فرم در دسترس نیست.' ], 404 ); }
	if ( '' !== payam_form_request( 'website' ) || strlen( payam_form_request( 'values' ) ) > 100000 ) { wp_send_json_error( [ 'message' => 'درخواست نامعتبر است.' ], 400 ); }
	$values = json_decode( payam_form_request( 'values' ), true );
	$clean = [];
	$errors = [];
	foreach ( $fields as $key => $field ) {
		$value = is_array( $values ) ? ( $values[ $key ] ?? '' ) : '';
		if ( ! is_string( $value ) ) { $errors[ $key ] = 'مقدار نامعتبر است.'; continue; }
		$value = trim( $value );
		$max = 'textarea' === $field['type'] ? 5000 : 300;
		if ( mb_strlen( $value ) > $max || ( $field['required'] && '' === $value ) ) { $errors[ $key ] = 'این فیلد را با مقدار معتبر تکمیل کنید.'; }
		if ( '' !== $value ) {
			if ( 'email' === $field['type'] && ! is_email( $value ) ) { $errors[ $key ] = 'ایمیل معتبر وارد کنید.'; }
			if ( 'select' === $field['type'] && ! isset( $field['options'][ $value ] ) ) { $errors[ $key ] = 'یکی از گزینه‌های موجود را انتخاب کنید.'; }
			if ( 'tel' === $field['type'] && ! preg_match( '/^[+\d\s()\-۰-۹٠-٩]{5,30}$/u', $value ) ) { $errors[ $key ] = 'شماره تلفن معتبر وارد کنید.'; }
		}
		$clean[] = [ 'label' => $field['label'], 'value' => 'select' === $field['type'] ? ( $field['options'][ $value ] ?? '' ) : sanitize_textarea_field( $value ) ];
	}
	if ( $errors ) { wp_send_json_error( [ 'message' => 'فیلدهای مشخص‌شده را اصلاح کنید.', 'errors' => $errors ], 422 ); }
	$token = payam_form_request( 'captcha_token' );
	if ( ! preg_match( '/^[a-f0-9]{48}$/', $token ) ) { wp_send_json_error( [ 'message' => 'کپچا را دوباره دریافت کنید.' ], 400 ); }
	$challenge = get_transient( 'payam_fc_' . $token );
	$answer = strtr( trim( payam_form_request( 'captcha_answer' ) ), array_combine( preg_split( '//u', '۰۱۲۳۴۵۶۷۸۹٠١٢٣٤٥٦٧٨٩', -1, PREG_SPLIT_NO_EMPTY ), str_split( '01234567890123456789' ) ) );
	if ( ! is_array( $challenge ) || $challenge['form'] !== $id || ! hash_equals( $challenge['client'], payam_form_client() ) || time() - $challenge['created'] < 2 || (string) $challenge['answer'] !== $answer ) {
		delete_transient( 'payam_fc_' . $token );
		wp_send_json_error( [ 'message' => 'پاسخ کپچا نادرست یا منقضی است. دوباره تلاش کنید.' ], 422 );
	}
	// Atomic claim prevents two concurrent requests from saving the same challenge twice.
	$lock = 'payam_fl_' . $token;
	if ( ! add_option( $lock, time(), '', false ) ) { wp_send_json_error( [ 'message' => 'این درخواست قبلاً ارسال شده است.' ], 409 ); }
	// Keep the claim until after expiry, including across concurrent PHP workers.
	wp_schedule_single_event( time() + 11 * MINUTE_IN_SECONDS, 'payam_form_release_claim', [ $token ] );
	delete_transient( 'payam_fc_' . $token );
	$result = wp_insert_post( [ 'post_type' => 'payam_form_entry', 'post_status' => 'private', 'post_title' => get_the_title( $id ) . ' — ' . current_time( 'Y-m-d H:i:s' ), 'meta_input' => [ '_payam_form_id' => $id, '_payam_form_values' => $clean ] ], true );
	if ( is_wp_error( $result ) || ! $result ) { wp_send_json_error( [ 'message' => 'ذخیره پیام انجام نشد. دوباره تلاش کنید.' ], 500 ); }
	$message = sanitize_text_field( (string) get_field( 'form_success_message', $id ) );
	wp_send_json_success( [ 'message' => $message ?: 'پیام شما با موفقیت ثبت شد. از ارتباط شما سپاسگزاریم.' ] );
}
add_action( 'payam_form_release_claim', static function ( string $token ): void {
	if ( preg_match( '/^[a-f0-9]{48}$/', $token ) ) { delete_option( 'payam_fl_' . $token ); }
} );
foreach ( [ 'payam_form_challenge', 'payam_form_submit' ] as $action ) {
	add_action( 'wp_ajax_' . $action, $action );
	add_action( 'wp_ajax_nopriv_' . $action, $action );
}

add_action( 'add_meta_boxes', static function (): void {
	add_meta_box( 'payam-form-shortcode', 'شورت‌کد فرم', static function ( WP_Post $post ): void {
		echo '<code>' . esc_html( '[payam_form id="' . $post->ID . '"]' ) . '</code><p>برای نمایش، فرم را منتشر کنید و فیلدهای آن را تکمیل کنید.</p>';
	}, 'payam_form', 'side' );
	add_meta_box( 'payam-form-message', 'اطلاعات پیام', static function ( WP_Post $post ): void {
		if ( ! current_user_can( 'manage_options' ) ) { return; }
		$rows = get_post_meta( $post->ID, '_payam_form_values', true );
		echo '<table class="widefat striped"><tbody>';
		foreach ( is_array( $rows ) ? $rows : [] as $row ) {
			echo '<tr><th>' . esc_html( $row['label'] ) . '</th><td>' . nl2br( esc_html( $row['value'] ) ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}, 'payam_form_entry', 'normal', 'high' );
} );
