<?php if (!is_paged()) : ?>
		<aside class="sidebar calendar-sidebar">
			<?php
				date_default_timezone_set('America/New_York');

				$args = array(
					'post_type' => 'events',
					'meta_key'     => 'e_end_date', // @seba this matches the acf key we want to target for sorting
					'meta_value'   => date( "Ymd", strtotime('yesterday') ), // @seba this filters out items that ended in the past
					'meta_compare' => '>=',
					'orderby'			=> 'meta_value_num', // and this orders by the previously set meta value as a number
					'order'					=> 'ASC',
					'posts_per_page' => 10
				);

				$query = new WP_Query($args);

				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) : $query->the_post();

						$eTitle = get_field('event_title');
            $eDesc = get_field('event_description');
            $eStartDate = get_field('e_start_date');
            $eEndDate = get_field('e_end_date');
            $eTime = get_field('e_time');

						$eLocation = get_field('e_location');
						$link = get_field('e_link');

						if ($eStartDate) : ?>

							<div class="calendar-event">
								<span class="calendar-event-title"><?php the_title(); ?></span>
								<div class="calendar-event-meta">
									<span class="calendar-event-date"><strong>Date: </strong><?php echo $eStartDate; ?></span>
									<?php if ( $eTime ) echo '<span class="calendar-event-time"><strong>Time: </strong>'.$eTime.'</span>'; ?>
									<?php if ( $eLocation ) echo '<span class="calendar-event-location"><strong>Location: </strong>'.$eLocation.'</span>'; ?>
								</div>

								<?php if ( $eDesc ) echo '<span class="calendar-event-desc">'.$eDesc.'</span>'; ?>

								<?php if ( $link ) echo '<a class="calendar-event-link" href="'.$link['url'].'" target="'.$link['target'].'">more info</a>'; ?>

							</div>

						<?php
						endif;

					endwhile;
					wp_reset_postdata();

				} else {
					echo 'no upcoming events';
				}
			?>
			<a class="btn" href="<?php echo home_url(); ?>/events">See all events</a>
		</aside>
<?php endif; ?>
