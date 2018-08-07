<?php
	date_default_timezone_set('America/New_York');

	$yesterday = date( "Ymd", strtotime('yesterday') );

	$args = array(
		'numberposts'	=> -1,
		'post_type' => 'events',
		'meta_key'     => 'event_dates_times_$_e_end_date', // this matches the acf key we want to target for sorting
		'meta_value'   => $yesterday, // this filters out items that ended in the past
		'meta_compare' => '>='
	);

	$query = new WP_Query($args);

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) : $query->the_post(); ?>

        <?php get_template_part('loop-templates/content-events') ?>
		<?php

		endwhile;

	} else {
		echo 'no upcoming events';
	}
	wp_reset_postdata();
?>
<a class="btn" href="<?php echo home_url(); ?>/events">See all events</a>
