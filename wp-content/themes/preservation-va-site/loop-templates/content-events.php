<?php
/**
 * Single post partial template.
 *
 * @package understrap
 */

$eLocation = get_field('location');
$link = get_field('e_link');
?>

			<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<div class="row blog-flex-row">
					<div>
						<div class="date_release">
							<span><?php echo date( 'M<\b\\r>j', strtotime( get_field('event_dates_times')[0]['e_start_date'] ) );?></span>
						</div>
					</div>
					<div>
						<header class="entry-header">
							<?php if(is_tax('our_work') || is_tax('historic_sites') || is_search()) : ?>
								<h4>Event</h4>
							<?php endif; ?>
							<?php 
								the_title( sprintf( 
									'<h2 class="entry-title"><a href="%s" rel="bookmark">', 
									esc_url( get_permalink() ) ), 
									'</a></h2>'
								); 
							?>
						</header><!-- .entry-header -->

						<div class="entry-content">

							<div class="flex-row image-text">
								<?php  if ( has_post_thumbnail() ) : ?>
									<div class="image">
										<?php
											echo get_the_post_thumbnail( $post->ID, 'medium');

											$attach_id = get_post_thumbnail_id( $post->ID );
											$image_data = wp_get_attachment( $attach_id );
										?>
										<?php if($image_data["caption"]) { ?><p class="caption"><?php echo $image_data["caption"]; ?></p> <?php } ?>
									</div>
								<?php endif; ?>
								<div class="details">
									<?php if( have_rows('event_dates_times') ): ?>
										<div class="event-dates">
										<?php while ( have_rows('event_dates_times') ) : the_row(); ?>
									    	<div class="event-date"><p><?php the_sub_field('e_start_date'); ?></p>
										    	<?php if( have_rows('event_times') ): ?>
												 		<div class="calendar-event-time">
													    <?php while ( have_rows('event_times') ) : the_row(); ?>
												    		<p> <?php the_sub_field('e_start_time'); ?> – <?php the_sub_field('e_end_time'); ?></p>
												    	<?php endwhile; ?>
												    </div>
													<?php else : ?>
													    // no rows found
													<?php endif; ?>
													</div>
										<?php endwhile; ?>
										</div>
										<?php else :
										    // no rows found
										endif; ?>
									<?php if ( $eLocation ) echo '<div class="event-location" ><p>'.$eLocation.'</p></div>'; ?>

								</div><!-- .details -->
							</div>
							<div class="content">
								<?php the_content(); ?>
							</div>
						</div><!-- .entry-content -->
						<?php if ( $link ) echo '<a class="event-link more-link" href="'.$link['url'].'" target="'.$link['target'].'">more info</a>'; ?>
					</div>
				</div>
			</article><!-- #post-## -->