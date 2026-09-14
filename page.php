<?php
get_header(); ?>

    <!-- Main Layout -->
    <main class="min-h-screen pt-32 pb-32">
        <section class="section-heading-section overflow-hidden">
            <?php section_heading( [
                    'title'       => get_the_title(),
                    'title_tag'   => 'h1',
                    'subtitle'    => get_field( 'subtite' ) ?: '',
                    'show_shapes' => true,
            ] ); ?>
        </section>

        <div class="container">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) : the_post(); ?>
                <div class="content-block bg-white rounded-3 p-8 text-body-3 text-neutral-900">
                    <?php the_content(); ?>
                </div>
                <?php endwhile;
            endif;
            ?>
        </div>
    </main>
<?php get_footer(); ?>