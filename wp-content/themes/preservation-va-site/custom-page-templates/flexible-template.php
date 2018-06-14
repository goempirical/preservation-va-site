<?php
/**
 * Template Name: Flexible Template
 */
get_header();

$container   = get_theme_mod( 'understrap_container_type' );

?>

	<div class="<?php echo esc_attr( $container ); ?>" id="content">

		<div class="row">

			<div class="col-md-12 content-area" id="primary">

				<main class="site-main" id="main" role="main">

					<!-- BEGIN CONTENT -->
					
					<?php if ( have_rows('main_flexible_content') ) : ?>
						
						<?php while ( have_rows('main_flexible_content') ) : the_row(); ?>
<<<<<<< Updated upstream
=======


>>>>>>> Stashed changes
							<!-- Two Columns -->
							<?php 
								if ( get_row_layout() == 'two_columns' ) : ?>
								<div class="container">
									<div class="content_two_columns <?php echo get_sub_field('layout_style'); ?>">
										<div class="row">
											<div class="col-md-6 left-column">
												<?php $field_column_left = get_sub_field( 'left_column' ); ?>

												<div class="content_media">
												
													<?php switch ($field_column_left['left_media_type']) {
														case 'Text':
															echo $field_column_left['left_text'];
															break;
														case 'Image':
															echo wp_get_attachment_image( $field_column_left['left_image']['ID'], 'full' );
															break;
														case 'Gallery': ?>
																<div  class="owl-two owl-carousel owl-theme">

																	<?php foreach ( $field_column_left['left_gallery'] as $image ) :?>

																		<div class="item">
																			
																			<?php echo  wp_get_attachment_image( $image['ID'], 'full' );?>

																		</div>

																	<?php endforeach; ?>

																</div>
													<?php	break;
														case 'Video':
															echo $field_column_left['left_video'];
															break;
													}
													?>
												</div>
											</div>

											<div class="col-md-6 right-column">
												<?php $field_column_right = get_sub_field( 'right_column' ); ?>
												
												<div class="content_media">
													<?php switch ($field_column_right['right_media_type']) {
														case 'Text':
															echo $field_column_right['right_text'];
															break;
														case 'Image':
															echo wp_get_attachment_image( $field_column_right['right_image']['ID'], 'full' );
															break;
														case 'Gallery': ?>
																<div  class="owl-two owl-carousel owl-theme">

																	<?php foreach ( $field_column_right['right_gallery'] as $image ) :?>

																		<div class="item">
																			
																			<?php echo  wp_get_attachment_image( $image['ID'], 'full' );?>

																		</div>

																	<?php endforeach; ?>

																</div>
													<?php	break;
														case 'Video':
															echo $field_column_right['right_video'];
															break;
													}
													?>
												</div>
											</div>

										</div> <!-- .ROW -->
									</div>
								</div>
								<!-- End Two Columns -->

							<!-- Two Columns with Label -->
							<?php elseif ( get_row_layout() == 'two_columns_label' ) : ?>
								<div class="row content_two_columns with_label <?php echo get_sub_field('layout_style'); ?>">
									<div class="container">
										<div class="row">
											<div class="col-md-3 label-column">
												<label><?php the_sub_field( 'label_two_columns' ) ?></label>
											</div>
											<div class="col-md-4 left-column">
												<?php $field_column_left = get_sub_field( 'left_column' ); ?>

												<div class="content_media">
												
													<?php switch ($field_column_left['left_media_type']) {
														case 'Text':
															echo $field_column_left['left_text'];
															break;
														case 'Image':
															echo wp_get_attachment_image( $field_column_left['left_image']['ID'], 'full' );
															break;
														case 'Gallery': ?>
																<div  class="owl-two owl-carousel owl-theme">

																	<?php foreach ( $field_column_left['left_gallery'] as $image ) :?>

																		<div class="item">
																			
																			<?php echo  wp_get_attachment_image( $image['ID'], 'full' );?>

																		</div>

																	<?php endforeach; ?>

																</div>
													<?php	break;
														case 'Video':
															echo $field_column_left['left_video'];
															break;
													}
													?>
												</div>
											</div>

											<div class="col-md-4 right-column">
												<?php $field_column_right = get_sub_field( 'right_column' ); ?>
												
												<div class="content_media">
													<?php switch ($field_column_right['right_media_type']) {
														case 'Text':
															echo $field_column_right['right_text'];
															break;
														case 'Image':
															echo wp_get_attachment_image( $field_column_right['right_image']['ID'], 'full' );
															break;
														case 'Gallery': ?>
																<div  class="owl-two owl-carousel owl-theme">

																	<?php foreach ( $field_column_right['right_gallery'] as $image ) :?>

																		<div class="item">
																			
																			<?php echo  wp_get_attachment_image( $image['ID'], 'full' );?>

																		</div>

																	<?php endforeach; ?>

																</div>
													<?php	break;
														case 'Video':
															echo $field_column_right['right_video'];
															break;
													}
													?>
												</div>
											</div>

										</div> <!-- .ROW -->
									</div>
								</div>
								<!-- End Two Columns Label -->

								<!-- Two Columns with Left Label -->
							<?php elseif ( get_row_layout() == 'two_column_with_left_label' ) : ?>
								<section class="two_column_with_left_label <?php the_sub_field('background_color'); ?>">

									<div class="row">

										<div class="col-md-2 tcll_label_col">
										
											<?php if ( get_sub_field('section_title') ) :?>

											<h4><?php the_sub_field('section_title') ?></h4>
										
											<?php endif; ?>
										
										</div>

										<div class="col-md-8 grid-two-col">

												<?php

													// check if the repeater field has rows of data
													if( have_rows('grid_items') ):

													 	// loop through the rows of data
													    while ( have_rows('grid_items') ) : the_row();

												?>

												<div class="gi">

													<?php the_sub_field('item_content'); ?>

												</div>

												<?php

													    endwhile;

													else :

													    // no rows found

													endif;

													?>
										</div>

									</div>

								</section>
								<!-- End Two Columns Left Label -->

								<!-- Three Columns -->
							<?php elseif ( get_row_layout() == 'three_columns' ) : ?>
								<div class="row content_three_columns <?php echo get_sub_field('layout_style'); ?>">
									<div class="container">

										<code>three_columns</code>
										<div class="row">
											<div class="col-md-4 left-column">
												<?php $field_column_left = get_sub_field( 'left_column' ); ?>

												<div class="content_media">
												
													<?php 

														switch ($field_column_left['left_media_type']) {
														case 'Text':
															echo $field_column_left['left_text'];
															break;
														case 'Image':
															echo wp_get_attachment_image( $field_column_left['left_image']['ID'], 'full' );
															break;
														case 'Gallery': ?>
																<div  class="owl-two owl-carousel owl-theme">

																	<?php foreach ( $field_column_left['left_gallery'] as $image ) :?>

																		<div class="item">
																			
																			<?php echo  wp_get_attachment_image( $image['ID'], 'full' );?>

																		</div>

																	<?php endforeach; ?>

																</div>
													<?php	break;
														case 'Video':
															echo $field_column_left['left_video'];
															break;
													}
													?>
												</div>
											</div>
											<div class="col-md-4 center-column">
												<?php $field_column_center = get_sub_field( 'center_column' ); ?>

												<div class="content_media">
												
													<?php switch ($field_column_center['center_media_type']) {
														case 'Text':
															echo $field_column_center['center_text'];
															break;
														case 'Image':
															echo wp_get_attachment_image( $field_column_center['center_image']['ID'], 'full' );
															break;
														case 'Gallery': ?>
																<div  class="owl-two owl-carousel owl-theme">

																	<?php foreach ( $field_column_center['center_gallery'] as $image ) :?>

																		<div class="item">
																			
																			<?php echo  wp_get_attachment_image( $image['ID'], 'full' );?>

																		</div>

																	<?php endforeach; ?>

																</div>
													<?php	break;
														case 'Video':
															echo $field_column_center['center_video'];
															break;
													}
													?>
												</div>
											</div>

											<div class="col-md-4 right-column">
												<?php $field_column_right = get_sub_field( 'right_column' ); ?>
												
												<div class="content_media">
													<?php switch ($field_column_right['right_media_type']) {
														case 'Text':
															echo $field_column_right['right_text'];
															break;
														case 'Image':
															echo wp_get_attachment_image( $field_column_right['right_image']['ID'], 'full' );
															break;
														case 'Gallery': ?>
																<div  class="owl-two owl-carousel owl-theme">

																	<?php foreach ( $field_column_right['right_gallery'] as $image ) :?>

																		<div class="item">
																			
																			<?php echo  wp_get_attachment_image( $image['ID'], 'full' );?>

																		</div>

																	<?php endforeach; ?>

																</div>
													<?php	break;
														case 'Video':
															echo $field_column_right['right_video'];
															break;
													}
													?>
												</div>
											</div>

										</div> <!-- .ROW -->
									</div>
								</div>
								<!-- End Three Columns -->

							<!-- Three Column Grid -->
							<?php elseif ( get_row_layout() == 'three_column_grid' ) : ?>
								<section class="three_column_grid <?php the_sub_field('background_color'); ?>">

									<div class="row grid-three-col">

											<?php

												// check if the repeater field has rows of data
												if( have_rows('grid_items') ):

												 	// loop through the rows of data
												    while ( have_rows('grid_items') ) : the_row();

											?>

											<div class="gi">

												<?php the_sub_field('item_content'); ?>

											</div>

											<?php

												    endwhile;

												else :

												    // no rows found

												endif;

												?>

									</div>

								</section>
								<!-- End Three Column Grid -->

								<!-- section_with_sidebar -->
							<?php elseif ( get_row_layout() == 'section_with_sidebar' ) : ?>

							<section class="section_with_sidebar <?php the_sub_field('background_color'); ?>">

								<div class="row">

                    <div class="col-md-6 sws_left-col">
                        <?php the_sub_field('main_section_content'); ?>
                    </div>

                    <div class="col-md-4 sws_right-col">
                        <?php the_sub_field('section_sidebar_content'); ?>
                    </div>

                </div>

			        </section>
								<!-- End section_with_sidebar -->

								<!-- Call to Actions -->
								<?php elseif ( get_row_layout() == 'call_to_action_block' ) : ?>
									<?php $bg_call_to_action_block = get_sub_field('background_color'); ?>
									<?php if ($bg_call_to_action_block == "none") : ?>
									<div class="container">
									<?php endif; ?>
										<div class="row">
											<div class="col-md-12 no_padding_both_sides">
												<div class="single_column <?php echo get_sub_field('call_to_action_mode') ? 'content_action_block ':''; ?><?php echo $bg_call_to_action_block;?>">
													<?php the_sub_field('text') ?>
													<div class="content_button">
														<?php 
															$button_1 = get_sub_field('button_1');
															$button_2 = get_sub_field('button_2');
															
															$class = "";
															if ($button_1 && $button_2) :
																$class = "small";
															endif;
														?>

														<?php if ( $button_1 ) : ?>
															<button type="button" class="btn_cl <?php echo $class; ?>">
																<?php echo $button_1['title']?>
															</button>
														<?php endif; ?>

														<?php if ( $button_2 ) : ?>
															<button type="button" class="btn_cl <?php echo $class; ?>">
																<?php echo $button_2['title']?>
															</button>
														<?php endif; ?>
													</div>
												</div>
											</div>
										</div>
									<?php if ($bg_call_to_action_block == "none") : ?>
									</div>
									<?php endif; ?>
								
								<?php elseif ( get_row_layout() == 'headline' ) : ?>
									
									<div class="row">

										<div class="col-md-12 no_padding_both_sides">

											<div class="content_headline <?php echo get_sub_field('layout_style');?> ">

												<?php if ( get_sub_field('layout_style') == 'primary' ) :?>

															<h1> <?php the_sub_field('headline_text') ?> </h1>
												
														<?php else: ?>
															
															<h2> <?php the_sub_field('headline_text') ?> </h2>

												<?php endif; ?>
												 
											</div>

										</div>

									</div>
								
								<?php elseif ( get_row_layout() == 'images' ) : ?>
									
									<?php $container_images = get_sub_field( 'container_images' ); ?>

									<?php if ( $container_images ) : 
										$count_images = sizeof( $container_images );
										$grid_total = 12;
										?>

										<div class="content_images">
											<div class="row">

													<?php if ( $count_images > 3 ) : ?>

														<div class="col-md-12 no_padding_both_sides">

															<div  class="owl-one owl-carousel owl-theme next">

																<?php foreach ( $container_images as $image ) :?>

																	<div class="item">
																		
																		<?php echo  wp_get_attachment_image( $image['content_image']['ID'], 'full' );?>

																	</div>

																<?php endforeach; ?>

															</div>

														</div>
													
													<?php else: ?>

														<?php foreach ( $container_images as $image ) :?>

															<div class="col-md-<?php echo $grid_total / $count_images ?> no_padding_both_sides" >

																<div class="<?php echo "pair_image_{$count_images}"?>">

																<?php echo  wp_get_attachment_image( $image['content_image']['ID'], 'full' );?>

																</div>
															
															</div>

														<?php endforeach; ?>

													<?php endif; ?>
											
											</div>
										</div>
									
									<?php endif; ?>
								
								<?php elseif ( get_row_layout() == 'related_content_layout' ) : ?>	

									<?php 
										$historic = get_sub_field('related_historic_sites');
										$work = get_sub_field('related_work');

										$tax_query = array(
											'posts_per_page' => 2,
											'taxt_query' => array(
												'relation' => 'OR',
												array(
													'taxonomy' => 'historic_sites',
													'field' => 'term_id',
													'terms' => $historic
												),
												array(
													'taxonomy' => 'our_work',
													'field' => 'term_id',
													'terms' => $work
												)
											),
											'order' => 'ASC',
									);
									?>
									
									<div class="row">
										
										<div class="col-md-12">

										<div class="row">

											<div class="col-md-6  no_padding_both_sides">

												<div class="content_related events">

														<div class="title_related">

															Scotchtown Events 

														</div>

													
													<?php
													$tax_query["post_type"] = "events";
													
													$query = new WP_Query( $tax_query );
													?>

													<?php if ($query->have_posts() ) :?>

														<div class="wrapper_related">

														<?php while ( $query->have_posts() ) : $query->the_post(); ?>

															<div class="card_related">
																
																<div class="date_release"> <span> <?php echo date( 'F j', strtotime( get_field('e_start_date') ) );?> </span>  </div>
																
																<div class="side_content_related">
																	
																	<span class="title_cont"> <?php the_title(); ?> </span>

																	<span class="time_range"> <?php echo get_field('e_start_time'); ?> </span>

																	<a href="<?php echo get_field('e_link')['url'] ?>">Learn more ></a>
												
																</div>

															</div>

														<?php endwhile; wp_reset_postdata(); ?>

														</div>

														<a href="#" class="see_all">See All ></a>

													<?php endif; ?>
												</div>

											</div>

											<div class="col-md-6  no_padding_both_sides">

												<div class="content_related stories">

													<div class="title_related">

														 Scotchtown Stories 
														
													</div>
													

													<?php
													$tax_query["post_type"] = "post";
													
													$query = new WP_Query( $tax_query );
													
													?>

													<?php if ($query->have_posts() ) :?>

														<div class="wrapper_related">

															<?php while ( $query->have_posts() ) : $query->the_post(); ?>
																
																<div class="card_related">
																	
																	<div class="side_content_related">
																		<span class="title_cont"> <?php the_title(); ?> </span>
																		<a href="<?php echo esc_url( get_permalink( get_post()->ID ) ); ?>">Learn more ></a>
																	</div>
																</div>														

															<?php endwhile; wp_reset_postdata(); ?>

														</div>

														<a href="#" class="see_all">See All ></a>

													<?php endif; ?>
													
												</div>

											</div>

										</div>
											
										
									</div>

								</div>


							<?php endif; ?>	<!-- CONDITIONAL LAYOUTS -->						

						<?php endwhile; ?>
					
					<?php endif; ?>
				</main><!-- #main -->
			</div> <!-- .col-md-12#primary -->
		</div><!-- #primary -->

		<!-- Do the right sidebar check -->
		<?php //get_template_part( 'global-templates/right-sidebar-check' ); ?>

	</div><!-- .row -->

</div><!-- Container end -->

<?php get_footer(); ?>
