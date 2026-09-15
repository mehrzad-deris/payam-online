<?php
defined( 'ABSPATH' ) || exit;
$formId = absint( $args['id'] ?? 0 );
$fields = $args['fields'] ?? [];
$instance = wp_unique_id( 'contact-form-' );
$description = (string) get_field( 'form_description', $formId );
$button = (string) get_field( 'form_submit_label', $formId ) ?: 'ثبت و ارسال';
?>
<div class="contact-form-box" data-contact-form-box>
	<header class="contact-form-heading">
		<h2><?= esc_html( get_the_title( $formId ) ); ?></h2>
		<?php if ( $description ) : ?><p><?= esc_html( $description ); ?></p><?php endif; ?>
	</header>
	<form data-contact-form data-form-id="<?= esc_attr( $formId ); ?>" data-endpoint="<?= esc_url( admin_url( 'admin-ajax.php' ) ); ?>" aria-label="<?= esc_attr( get_the_title( $formId ) ); ?>">
		<div class="contact-form-fields">
			<?php foreach ( $fields as $key => $field ) : $inputId = $instance . '-' . $key; ?>
				<div class="contact-field<?= $field['wide'] ? ' contact-field-wide' : ''; ?>">
					<?php if ($field['show_label']) : ?><label class="<?= ! empty( $field['show_label'] ) ? 'contact-field-label' : 'screen-reader-text'; ?>" for="<?= esc_attr( $inputId ); ?>"><?= esc_html( $field['label'] ); ?><?= $field['required'] ? ' (الزامی)' : ''; ?></label><?php endif; ?>
					<?php $placeholder = $field['placeholder'] ?: $field['label']; ?>
					<?php if ( 'textarea' === $field['type'] ) : ?>
						<textarea id="<?= esc_attr( $inputId ); ?>" name="<?= esc_attr( $key ); ?>" data-form-field maxlength="5000" rows="6" placeholder="<?= esc_attr( $placeholder ); ?>" aria-describedby="<?= esc_attr( $inputId ); ?>-error" <?= $field['required'] ? 'required' : ''; ?>></textarea>
					<?php elseif ( 'select' === $field['type'] ) : ?>
						<select id="<?= esc_attr( $inputId ); ?>" name="<?= esc_attr( $key ); ?>" data-form-field aria-describedby="<?= esc_attr( $inputId ); ?>-error" <?= $field['required'] ? 'required' : ''; ?>>
							<option value=""><?= esc_html( $placeholder ); ?></option>
							<?php foreach ( $field['options'] as $value => $label ) : ?><option value="<?= esc_attr( $value ); ?>"><?= esc_html( $label ); ?></option><?php endforeach; ?>
						</select>
					<?php else : ?>
						<input type="<?= esc_attr( $field['type'] ); ?>" id="<?= esc_attr( $inputId ); ?>" name="<?= esc_attr( $key ); ?>" data-form-field maxlength="300" placeholder="<?= esc_attr( $placeholder ); ?>" aria-describedby="<?= esc_attr( $inputId ); ?>-error" <?= $field['required'] ? 'required' : ''; ?>>
					<?php endif; ?>
					<span class="contact-field-error" id="<?= esc_attr( $inputId ); ?>-error" data-field-error></span>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="contact-trap" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off" data-form-trap></label></div>
		<div class="contact-captcha">
			<label for="<?= esc_attr( $instance ); ?>-captcha">پاسخ عبارت <bdi data-captcha-question>…</bdi></label>
			<input id="<?= esc_attr( $instance ); ?>-captcha" data-captcha-answer type="text" inputmode="numeric" autocomplete="off" maxlength="3" required aria-label="پاسخ کپچا">
			<button type="button" data-captcha-refresh>عبارت جدید</button>
		</div>
		<button type="submit" class="contact-submit cta-link cta-btn-primary" disabled><span class="contact-spinner" aria-hidden="true"></span><span><?= esc_html( $button ); ?></span></button>
		<noscript>برای ارسال فرم، جاوااسکریپت مرورگر را فعال کنید.</noscript>
	</form>
	<div class="contact-form-status" data-form-status role="status" aria-live="polite" tabindex="-1" hidden></div>
</div>
