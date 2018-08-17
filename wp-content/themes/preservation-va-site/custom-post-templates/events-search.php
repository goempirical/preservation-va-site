<?php
	date_default_timezone_set('America/New_York');

	$yesterday = date( "Ymd", strtotime('yesterday') );

	$number   = 4; // number of terms to display per page
	$paged  = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$offset = ( $paged > 0 ) ?  $number * ( $paged - 1 ) : 1;

	$args = array(
		'posts_per_page'	=> $number,
		'offset'       => $offset,
		'post_type' => 'events',
		'meta_key'     => 'event_dates_times_$_e_end_date', // this matches the acf key we want to target for sorting
		'meta_value'   => $yesterday, // this filters out items that ended in the past
		'meta_compare' => '>=',
		'paged' => $paged
	);

	$query = new WP_Query($args);

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) : $query->the_post(); ?>

        <?php get_template_part('loop-templates/content-events') ?>
		<?php

		endwhile;

$args['posts_per_page'] = -1;
$countquery = new WP_Query($args);
$max   	= $countquery->post_count;
$totalpages   = ceil( $max / $number );

echo custom_page_navi( $totalpages, $paged, 3, 0 );

	} else {
		echo 'no upcoming events';
	}
	wp_reset_postdata();
?>

