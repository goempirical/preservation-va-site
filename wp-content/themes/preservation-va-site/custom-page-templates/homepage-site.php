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

							
							<?php $image = get_sub_field('hp_intro_bg_image') ?>
							<div class="row">
								<div class="col-md-12 no_padding_both_sides">
									<div class="banner_image">
										<!-- Image -->
										<?php echo wp_get_attachment_image( $image['ID'], 'Home Banner', false, array('alt' => $image['alt']) ); ?>
										
										<?php $homeIntro = get_sub_field('hp_intro_center_column')  ?>
										
										<!-- Heading and Button -->
										<div class="heading">
											<div class="align-middle">
												<h1 class="title"><?php echo $homeIntro['hp_intro_heading'] ?></h1>
												<?php echo $homeIntro['hp_intro_subheading'] ?>
												<a href="<?php echo $homeIntro['hp_intro_button']['url'] ?>" 
													target="<?php echo $homeIntro['hp_intro_button']['target'] ?>" 
													class="btn marigold small">
													<?php echo $homeIntro['hp_intro_button']['title'] ?>
												</a>
											</div>
										</div>
									</div>
								</div>
							</div>

					<?php endwhile;
					endif; ?>

					<!-- Our Work -->
					<?php if( have_rows('hp_our_work') ):  
						while ( have_rows('hp_our_work') ) : // loop through the rows of data
							the_row(); ?>
							<div class="row our_work">
								<!-- Focus 1 -->
								<div class="col-md-4 focus focus1">
									<?php $hp_work_focus_1 = get_sub_field('hp_work_focus_1'); ?>
									
									<a href="<?php echo $hp_work_focus_1['hp_work_focus_1_link']['url'] ?>" target="<?php echo $hp_work_focus_1['hp_work_focus_1_link']['target'] ?>">
										<?php echo wp_get_attachment_image( $hp_work_focus_1['hp_work_focus_1_icon']['ID'], 'full', false, array('alt' => $hp_work_focus_1['hp_work_focus_1_icon']['alt']) ); ?>
										<h2><?php echo $hp_work_focus_1['hp_work_focus_1_title'] ?></h2>
										<?php echo $hp_work_focus_1['hp_work_focus_1_description'] ?>
									</a>
									
								</div>
								<!-- Focus 2 -->
								<div class="col-md-4 focus focus2">
									<?php $hp_work_focus_2 = get_sub_field('hp_work_focus_2'); ?>
									
									<a href="<?php echo $hp_work_focus_2['hp_work_focus_2_link']['url'] ?>" target="<?php echo $hp_work_focus_2['hp_work_focus_2_link']['target'] ?>">
										<?php echo wp_get_attachment_image( $hp_work_focus_2['hp_work_focus_2_icon']['ID'], 'full', false, array('alt' => $hp_work_focus_2['hp_work_focus_2_icon']['alt']) ); ?>
										<h2><?php echo $hp_work_focus_2['hp_work_focus_2_title'] ?></h2>
										<?php echo $hp_work_focus_2['hp_work_focus_2_description'] ?>
									</a>
									
								</div>
								<!-- Focus 3 -->
								<div class="col-md-4 focus focus3">
									<?php $hp_work_focus_3 = get_sub_field('hp_work_focus_3'); ?>
									
									<a href="<?php echo $hp_work_focus_3['hp_work_focus_3_link']['url'] ?>" target="<?php echo $hp_work_focus_3['hp_work_focus_3_link']['target'] ?>">
										<?php echo wp_get_attachment_image( $hp_work_focus_3['hp_work_focus_3_icon']['ID'], 'full', false, array('alt' => $hp_work_focus_3['hp_work_focus_3_icon']['alt']) ); ?>
										<h2><?php echo $hp_work_focus_3['hp_work_focus_3_title'] ?></h2>
										<?php echo $hp_work_focus_3['hp_work_focus_3_description'] ?>
									</a>
									
								</div>
							</div>
					<?php endwhile;
					endif; ?>

					<!-- Banner Temporarily -->
					<?php 
					if ( have_rows('hp_intro_banner_temporarily') ):  
						while ( have_rows('hp_intro_banner_temporarily') ) : the_row(); // loop through the rows of data
							if (get_sub_field('hp_intro_temp_enable')) :
					?>
							<div class="section_temporal content_side">
								<div class="row">
									<div class="col-md-6">
										<?php $hp_intro_temp_content = get_sub_field('hp_intro_temp_content'); ?>
										<?php echo wp_get_attachment_image( $hp_intro_temp_content['hp_intro_temp_featured_content_image']['ID'], 'full', false, array('alt' => $hp_work_focus_1['hp_work_focus_1_icon']['alt']) ); ?>
									</div>
									<div class="col-md-6 p_medium">
										<h3><?php echo $hp_intro_temp_content['hp_intro_temp_featured_content_heading'] ?></h3>
										<?php echo $hp_intro_temp_content['hp_intro_temp_featured_content_description'] ?>
										
										<a href="<?php echo $hp_intro_temp_content['hp_intro_temp_featured_content_button']['url'] ?>" 
											target="<?php echo $hp_intro_temp_content['hp_intro_temp_featured_content_button']['target'] ?>" 
											class="btn marigold small">
											<?php echo $hp_intro_temp_content['hp_intro_temp_featured_content_button']['title'] ?>
										</a>
										
									</div>
								</div>
							</div>
							<?php endif; ?>
						<?php endwhile; ?>
					<?php endif; ?>

					<!-- Gallery -->
					<?php if ( have_rows('hp_gallery') ):  
						while ( have_rows('hp_gallery') ) : the_row(); // loop through the rows of data
					?>
							<?php $container_images = get_sub_field('hp_images_container'); ?>
				
							<?php if($container_images): ?>
							<div class="row">
								<div class="col-md-12 no_padding_both_sides">
									<div  class="owl-one owl-carousel owl-theme next">
										<?php foreach ( $container_images as $image ) :?>
											<div class="item">
												<?php echo  wp_get_attachment_image( $image['ID'], 'full' );?>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
							<?php endif; ?>
						<?php endwhile; ?>
					<?php endif; ?>

					<!-- Historic Sites Navigator -->
					<?php 
					if ( have_rows('hp_historic_sites_navigator') ):  
						while ( have_rows('hp_historic_sites_navigator') ) : the_row(); // loop through the rows of data
					?>
						<div class="row">
							<div class="col-md-12 no_padding_both_sides">
								<div class="content_action_block dark_blue">
									<h3><?php the_sub_field('hp_historic_nav_intro_text') ?></h3>
								</div>
							</div>
						</div>
						<?php endwhile; ?>
					<?php endif; ?>

					<!-- Call to Donate -->
					<?php 
					if ( have_rows('hp_call_to_donate') ):  
						while ( have_rows('hp_call_to_donate') ) : the_row(); // loop through the rows of data
					?>
							<div class="row">
								<div class="col-md-12 no_padding_both_sides">
									<div class="content_action_block none">
										<?php the_sub_field('hp_call_to_donate_text') ?>
										<div class="content_button">
											<?php 
												$button_1 = get_sub_field('hp_call_to_donate_button');
											?>
											<button type="button" class="btn_cl">
												<?php echo $button_1['title']?>
											</button>
										</div>
									</div>
								</div>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>

					<!-- Featured Post -->
					<?php 
					if ( have_rows('hp_featured_blog') ):  
						while ( have_rows('hp_featured_blog') ) : the_row(); // loop through the rows of data
							$hp_featured_blog_post = get_sub_field('hp_featured_blog_post');

							if ($hp_featured_blog_post) :
								$post = $hp_featured_blog_post;
								setup_postdata( $post ); 
					?>
							<div class="featured_post content_side">
								<div class="row">
									<div class="col-md-5">
										<?php the_post_thumbnail(); ?>
									</div>
									<div class="col-md-6 p_medium">
										<h3><?php the_title() ?></h3>
										<?php the_excerpt(); ?>
										<a href="<?php the_permalink() ?>">Read More ></a> &nbsp; <a href="">Subscribe ></a>
									</div>
								</div>
							</div>
							<?php wp_reset_postdata();
								endif; ?>
						<?php endwhile; ?>
					<?php endif; ?>

				<?php endwhile; // end of the loop. ?>

			</main><!-- #main -->

			</div> <!-- .col-md-12#primary -->

		</div><!-- #primary -->

	</div><!-- .row -->

</div><!-- Container end -->

<?php get_footer(); ?>
