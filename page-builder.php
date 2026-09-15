<?php
/*
 * Template Name: Page Builder
 * */
get_header(); ?>

    <!-- Main Layout -->
    <main class="min-h-screen">
        <?php
		$GLOBALS['payam_page_builder_primary_heading_pending'] = true;

        if ( have_posts() ) :
            while ( have_posts() ) : the_post();
         if ( have_rows( 'page_builder' ) ):
                    while ( have_rows( 'page_builder' ) ): the_row();
                        theme_render_block( get_row_layout() );
                    endwhile;
                endif;

            endwhile;
        endif;

		unset( $GLOBALS['payam_page_builder_primary_heading_pending'] );
        ?>
    </main>
<?php get_footer(); ?>
