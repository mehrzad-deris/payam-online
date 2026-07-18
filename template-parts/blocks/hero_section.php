<?php
$sectionColor = get_sub_field( 'section_color' ) ?: "";
$sectionStyle = get_sub_field( 'section_style' ) ?: 'light';
?>

<section data-header-theme="<?php echo esc_attr( $sectionStyle ); ?>" class="min-h-screen text-white relative"
         style="background-color: <?= $sectionColor ?>">

</section>