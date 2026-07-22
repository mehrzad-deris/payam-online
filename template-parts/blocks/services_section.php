<?php
$sectionColor    = get_sub_field( 'section_color' ) ?: "";
$sectionStyle    = get_sub_field( 'section_style' ) ?: 'light';
$sectionTitle    = get_sub_field( 'section_title' );
$sectionTitleTag = get_sub_field( 'title_tag' ) ?: 'h2';
?>

<section data-header-theme="<?= esc_attr( $sectionStyle ); ?>" class="min-h-screen relative overflow-hidden" style="background-color: <?= esc_attr( $sectionColor ) ?>"></section>