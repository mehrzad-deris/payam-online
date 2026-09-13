<?php
defined( 'ABSPATH' ) || exit;
if ( post_password_required() ) { return; }
?>
<section class="article-comments" id="comments">
    <div class="text-desktop-h5 text-neutral-900 mb-5">نظرات کاربران</div>
	<?php if ( have_comments() ) : ?><ol class="comment-list"><?php wp_list_comments( [ 'style' => 'ol', 'callback' => 'payam_article_comment', 'avatar_size' => 0, 'max_depth' => 2 ] ); ?></ol><?php endif; ?>
	<?php if ( comments_open() ) : ?>
		<div class="comment-invitation" data-comment-invitation><span>منتظر شنیدن نظر شما هستیم!</span><button type="button" data-comment-form-toggle aria-expanded="false" aria-controls="article-comment-form">ثبت نظر</button></div>
		<div class="article-comment-form" id="article-comment-form" data-comment-form hidden><?php comment_form( [
			'title_reply' => 'دیدگاه خود را ثبت کنید', 'title_reply_before' => '<h3>', 'title_reply_after' => '</h3>',
			'comment_notes_before' => '', 'comment_notes_after' => '', 'logged_in_as' => '', 'cancel_reply_link' => '', 'label_submit' => 'ثبت دیدگاه',
			'fields' => [
				'cookies' => '',
				'author' => '<p class="comment-field"><label for="author">نام و نام خانوادگی</label><input id="author" name="author" type="text" autocomplete="name" required></p>',
				'email' => '<p class="comment-field"><label for="email">ایمیل</label><input id="email" name="email" type="email" autocomplete="email" required></p>',
			],
			'comment_field' => '<p class="comment-field comment-field-message"><label for="comment">متن دیدگاه</label><textarea id="comment" name="comment" rows="6" required></textarea></p>',
		] ); ?></div>
	<?php endif; ?>
</section>
