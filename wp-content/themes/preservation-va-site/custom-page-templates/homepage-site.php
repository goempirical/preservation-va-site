<?php
/**
 * Template Name: Homepage Site
 */
get_header();

$container   = get_theme_mod( 'understrap_container_type' );

?>

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<div class="col-md-12 content-area" id="primary">

			<main class="site-main" id="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<!-- Intro Banner -->
					<?php if( have_rows('hp_intro_banner') ):  
						while ( have_rows('hp_intro_banner') ) : // loop through the rows of data
							the_row(); ?>

							
							<?php 

								$banner_image = get_sub_field('banner_image');
								$image = $banner_image['hp_intro_bg_image'];
								$overlay_tint = $banner_image['overlay_tint'] > 0 ? $banner_image['overlay_tint'] / 100 : 0;

							?>

							<div class="row">
								<section class="banner_image" style="background-image: url(<?php echo $image; ?>);">
								<!-- Image -->
								<?php // echo wp_get_attachment_image( $image['ID'], 'Home Banner', false, array('alt' => $image['alt']) ); ?>
								
								<?php $homeIntro = get_sub_field('hp_intro_center_column')  ?>

								<div class="tint" style="opacity: <?php echo $overlay_tint; ?>"></div>
								
								<!-- Heading and Button -->
								<div class="heading">
									<div class="align-middle">
										<h1 class="title"><?php echo $homeIntro['hp_intro_heading'] ?></h1>
										<?php echo $homeIntro['hp_intro_subheading'] ?>
										<a href="<?php echo $homeIntro['hp_intro_button']['url'] ?>" 
											target="<?php echo $homeIntro['hp_intro_button']['target'] ?>" 
											class="btn marigold">
											<?php echo $homeIntro['hp_intro_button']['title'] ?>
										</a>
									</div>
								</div>
							</section>
						</div>

					<?php endwhile;
					endif; ?>

					<!-- Our Work -->
					<?php if( have_rows('hp_our_work') ):  
						while ( have_rows('hp_our_work') ) : // loop through the rows of data
							the_row(); ?>
							<div class="row our_work">
								<?php 
								$our_work_sections = array('1','2','3');
								foreach ($our_work_sections as $section => $val) {
								?>
								<div class="col-md-4 focus focus<?php echo $val; ?>">
									<?php 
										$ow_key = 'hp_work_focus_'.$val;
										$hp_work_focus = get_sub_field($ow_key); 

									?>
									
									<a href="<?php echo $hp_work_focus[$ow_key.'_link']['url'] ?>" target="<?php echo $hp_work_focus[$ow_key.'_link']['target'] ?>">
										<?php echo wp_get_attachment_image( $hp_work_focus[$ow_key.'_icon']['ID'], 'full', false, array('alt' => $hp_work_focus[$ow_key.'_icon']['alt']) ); ?>
									</a>
									<a href="<?php echo $hp_work_focus[$ow_key.'_link']['url'] ?>" target="<?php echo $hp_work_focus[$ow_key.'_link']['target'] ?>">
										<h2><?php echo $hp_work_focus[$ow_key.'_title'] ?></h2>
									</a>	
										<?php echo $hp_work_focus[$ow_key.'_description'] ?>
								</div>
								<?php
								}
								?>
							</div>
					<?php endwhile;
					endif; ?>

					<!-- Optional Feature -->
					<?php 
					if ( have_rows('hp_intro_banner_temporarily') ):  
						while ( have_rows('hp_intro_banner_temporarily') ) : the_row(); // loop through the rows of data
							if (get_sub_field('hp_intro_temp_enable')) :
					?>

							<section class="layout-block two_columns">

								<div class="row align-items-center p_medium">

										<div class="col-md-6 left-column">

											<?php $hp_intro_temp_content = get_sub_field('hp_intro_temp_content'); ?>
											<?php echo wp_get_attachment_image( $hp_intro_temp_content['hp_intro_temp_featured_content_image']['ID'], 'full', false, array('alt' => $hp_intro_temp_content['hp_intro_temp_featured_content_image']['alt']) ); ?>

										</div>

										<div class="col-md-6 right-column">

											<h3><?php echo $hp_intro_temp_content['hp_intro_temp_featured_content_heading'] ?></h3>
											<?php echo $hp_intro_temp_content['hp_intro_temp_featured_content_description'] ?>
											
											<a href="<?php echo $hp_intro_temp_content['hp_intro_temp_featured_content_button']['url'] ?>" 
												target="<?php echo $hp_intro_temp_content['hp_intro_temp_featured_content_button']['target'] ?>" 
												class="btn marigold small">
												<?php echo $hp_intro_temp_content['hp_intro_temp_featured_content_button']['title'] ?>
											</a>

										</div>

								</div> <!-- .ROW -->

							</section>



							<?php endif; ?>
						<?php endwhile; ?>
					<?php endif; ?>

					<!-- Gallery -->
					<?php 

					if ( have_rows('hp_gallery') ):  
					
						while ( have_rows('hp_gallery') ) : the_row(); // loop through the rows of data
						
						$container_images = get_sub_field('hp_images_container');

							if($container_images): 

								$count_images = sizeof( $container_images );

								$grid_total = 12;

							?>

							<section class="image_row">
								<div class="row">
								<?php if ( $count_images > 3 ) : ?>

									<div class="col-md-12 no_padding_both_sides">
										<div  class="owl-one owl-carousel owl-theme next owl-height_for_three">
											<?php foreach ( $container_images as $image ) :?>
												<div class="item">
													<?php echo  wp_get_attachment_image( $image['ID'], 'full' );?>
												</div>
											<?php endforeach; ?>
										</div>
									</div>

								<?php else: ?>

									<?php foreach ( $container_images as $image ) :?>

										<div class="image_row--static col-md-<?php echo $grid_total / $count_images ?> <?php echo "images_{$count_images}"?> no_padding_both_sides" >

											<?php echo  wp_get_attachment_image( $image['ID'], 'full' );?>
										
										</div>

									<?php endforeach; ?>

								<?php endif; ?>
								
								</div>
							</section>

							<?php endif; ?>

						<?php endwhile; ?>

					<?php endif; ?>

					<!-- Historic Sites Navigator -->
					<?php 
					if ( have_rows('hp_historic_sites_navigator') ):  
						while ( have_rows('hp_historic_sites_navigator') ) : the_row(); // loop through the rows of data
					?>

						<section class="hp_historic_sites_navigator layout-block single_column mid_width dark_blue content_action_block">

							<div class="row">
								<h2><?php the_sub_field('hp_historic_nav_intro_text') ?></h2>
								<div class="content_button">
									<?php 
										// Get Taxonimies Historic Sites
										$taxonomies_hs = get_terms('historic_sites');
										$array_taxonomies_hs = array();
							
										if ($taxonomies_hs) {
											foreach ( $taxonomies_hs as $term ) {
												$array_taxonomies_hs[] = $term->slug;
											}
										}
										// Arguments for get Pages with taxonomies choosed of Historic Sites
										$args = array(
											'post_type' => 'page',
											'tax_query' => array(
												array(
													'taxonomy' => 'historic_sites',
													'field'    => 'slug',
													'terms'    => $array_taxonomies_hs,
												)
											),
										);

										$query = new WP_Query( $args );
									?>

									<div class="select-container">

											<select id='historysite' class="select-custom">
		
												<?php if ($query->have_posts() ) :?>
													
													<?php while ( $query->have_posts() ) : $query->the_post(); ?>
														
														<option value="<?php the_permalink() ?>"><?php the_title(); ?></option>
													
													<?php endwhile; wp_reset_postdata(); ?>
												
												<?php endif; ?>
											
											</select>
									
									</div>
									
									<button type="button" onclick="javascript: window.location.href = document.getElementById('historysite').value;" class="btn marigold small">LEARN MORE</button>
							
							</div>

						</section>

						<?php endwhile; ?>

					<?php endif; ?>

					<!-- Call to Donate -->
					<?php 
					if ( have_rows('hp_call_to_donate') ):  
						while ( have_rows('hp_call_to_donate') ) : the_row(); // loop through the rows of data
					?>

					<section class="hp_call_to_donate layout-block single_column mid_width content_action_block">

							<div class="row">
								<?php the_sub_field('hp_call_to_donate_text') ?>
								<div class="content_button">

									<?php 
										$button_1 = get_sub_field('hp_call_to_donate_button');
									?>
									<a href="<?php echo $button_1['url']?>" class="btn">
										<?php echo $button_1['title']?>
									</a>

								</div>

						</section>

						<?php endwhile; ?>
					<?php endif; ?>

					<!-- Featured Post -->
					<?php 
					if ( have_rows('hp_featured_blog') ):  
						while ( have_rows('hp_featured_blog') ) : the_row(); // loop through the rows of data
							$hp_featured_blog_post = get_sub_field('hp_featured_blog_post');

							if ($hp_featured_blog_post && get_sub_field('enable')) :
								$post = $hp_featured_blog_post;
								setup_postdata( $post ); 
					?>

							<section class="featured_post layout-block two_columns red">

								<div class="row align-items-center p_medium">

										<div class="col-md-5 left-column">

											<a href="<?php the_permalink() ?>"><?php the_post_thumbnail(); ?></a>
										
										</div>

										<div class="col-md-6 right-column">
										<?php 
												$post_categories = wp_get_post_categories( $post->ID );
								
										    $cat = get_category( $post_categories[0] );
											?>
											<?php if($cat->name !== "Uncategorized") : ?>
												<h4><?php echo $cat->name; ?></h4>
											<?php endif; ?>

											<h2><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h2>
											<?php the_excerpt(); ?>
											<?php $opt_subscribe = get_field('opt_subscribe', 'option'); ?>
											<p class="links">
												<a href="<?php the_permalink() ?>">Read More</a> &nbsp; 
												<a href="<?php echo ($opt_subscribe['opt_subscribe_link']) ? $opt_subscribe['opt_subscribe_link'] : '' ?>" target="_blank">Subscribe</a>
											</p>

										</div>

								</div> <!-- .ROW -->

							</section>

							<?php 

								wp_reset_postdata();

								endif; 

							endwhile;

					endif; 

				endwhile; // end of the loop. 

				?>

			</main><!-- #main -->

			</div> <!-- .col-md-12#primary -->

		</div><!-- #primary -->

	</div><!-- .row -->

</div><!-- Container end -->

<?php get_footer(); ?>
