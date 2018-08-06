<?php
/**
 * Single post partial template.
 *
 * @package understrap
 */

$eTitle = get_field('event_title');
$eDesc = get_field('event_description');

$eLocation = get_field('e_location');
$link = get_field('e_link');
?>
<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<div class="row blog-flex-row">
		<div>
			<header class="entry-header">
				<?php 
					the_title( sprintf( 
						'<h2 class="entry-title"><a href="%s" rel="bookmark">', 
						esc_url( get_permalink() ) ), 
						'</a></h2>' 
					); 
				?>
			</header><!-- .entry-header -->

			<div class="entry-content">
				<?php 
					if ( has_post_thumbnail() ) :
						echo get_the_post_thumbnail( $post->ID, 'full');

						$attach_id = get_post_thumbnail_id( $post->ID );
						$image_data = wp_get_attachment( $attach_id );
				?>
						<p class="caption"><?php echo $image_data["caption"]; ?></p>
			<?php endif; ?>

				<?php 

						// check if the repeater field has rows of data
						if( have_rows('event_dates_times') ):

						 	// loop through the rows of data
						    while ( have_rows('event_dates_times') ) : the_row();
						    	?>

						    	<span class="calendar-event-date"><strong>Date: </strong><?php the_sub_field('e_start_date'); ?></span>
						    	<?php
						    		

						        if( have_rows('event_times') ):

									 	// loop through the rows of data
									    while ( have_rows('event_times') ) : the_row();
									?>
						    	<span class="calendar-event-date"><strong>Date: </strong><?php the_sub_field('e_end_time'); ?></span>
						    	<?php

									    endwhile;

									else :

									    // no rows found

									endif;

						    endwhile;

						else :

						    // no rows found

						endif;
				?>
					<?php if ( $eLocation ) echo '<span class="calendar-event-location"><strong>Location: </strong>'.$eLocation.'</span>'; ?>
				</div>

				<?php if ( $eDesc ) echo '<span class="calendar-event-desc">'.$eDesc.'</span>'; ?>

				<?php if ( $link ) echo '<a class="calendar-event-link" href="'.$link['url'].'" target="'.$link['target'].'">more info</a>'; ?>

				<?php the_content(); ?>

			</div><!-- .entry-content -->
		</div>
	</div>
</article><!-- #post-## -->
