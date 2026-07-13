<?php

function theme_render_component( string $name, array $args = [] ): void {
	get_template_part( "template-parts/components/{$name}", null, $args );
}

/* Section Heading */
function theme_section_heading( array $args = [] ): void {
	$defaults = [
		'title'    => '',
		'titleEn'  => '',
		'titleTag' => 'h2',
		'class'    => '',
	];

	$args = wp_parse_args( $args, $defaults );

	$allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p' ];

	if ( ! in_array( $args['titleTag'], $allowed_tags, true ) ) {
		$args['titleTag'] = 'h2';
	}

	$args['_classes'] = [
		'wrapper' => trim( "section-heading flex justify-center mb-11 {$args['class']}" ),
		'title'   => 'text-[24px] lg:text-[32px] font-Artin',
		'titleEn' => 'lg:text-[20px] font-Artin text-gray',
	];

	theme_render_component( 'section-heading', $args );
}