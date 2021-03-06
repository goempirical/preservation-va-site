
<?php if( have_rows('hs_main_section') ): ?>
    <?php // loop through the rows of data
        while ( have_rows('hs_main_section') ) : the_row(); ?>

        <!-- <h1>Main Section</h1> -->

        <!-- Main Banner -->
        <!-- <h4>Image: </h4> -->
        <?php $image = get_sub_field('hs_banner_image') ?>
        <div class="row">
            <div class="col-md-12 no_padding_both_sides">
                <div class="banner_image">
                    <?php echo wp_get_attachment_image( $image['ID'], 'banner_size', false, array('alt' => $image['alt']) ); ?>
                </div>
            </div>
        </div>

        <!-- Title -->
        <div class="row">
            <div class="col-md-12 no_padding_both_sides">
                <div class="content_headline primary ">
                    <h1> <?php the_sub_field('hs_title'); ?></h1>
                    <div class="hs_submenu">
                        <a href="#basic-info">Basic Info</a>
                        <a href="#events">Events</a>
                        <a href="#donate">Donate</a>
                        <a href="#plan-your-visit">Plan your Visit</a>
                        <a href="#admissions">Admissions</a>
                        <a href="#tours-rentals">Tours/Rentals</a>
                        <a href="#key-visitor-info">Key Visitor Info</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Text -->
        <div id="basic-info" class="row">
            <div class="col-md-12 no_padding_both_sides">
                <div class="content_side">
                    <div class="row">
                        <div class="col-md-8">
                            <?php $mainContent = get_sub_field('main_content'); ?>
                            <div class="p_medium">
                                <?php echo $mainContent['hs_site_text']; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="content_address p_nomargin">
                                <h3>Address</h3>
                                <?php echo $mainContent['right_column']['hs_address']; ?>
                               
                                <h3>Hours</h3>
                                <?php echo $mainContent['right_column']['hs_hours']; ?>
                                
                                <a href="#plan-your-visit" class="btn marigold small">PLAN YOUR VISIT</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallery -->
        <?php $container_images = get_sub_field('hs_main_gallery') ?>
        
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

            <!-- Caption Gallery -->
            <div class="col-md-12 no_padding_both_sides">
                <div class="content_action_block red">
                    <?php the_sub_field('hs_caption_gallery'); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endwhile; ?>
<?php endif ?>

<!-- Plan your visit -->
<?php if( have_rows('hs_plan_your_visit') ): ?>
<?php // loop through the rows of data
        while ( have_rows('hs_plan_your_visit') ) : the_row(); ?>
        <div id="plan-your-visit" class="row">
            <div class="col-md-12 no_padding_both_sides">
                <div class="content_side">
                    <div class="row justify-content-between">
                        <div class="col-md-12 no_padding_both_sides">
                            <div class="content_headline secondary_white margin_bottom">                  
                                <h2>Plan your visit</h2>                              
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php the_sub_field('hs_visit_text') ?>
                            <a href="#key-visitor-info" class="btn marigold small">VISITOR INFO</a>
                        </div>
                        <div class="col-md-5">
                            <div class="content_address p_nomargin greyish map">
                                <?php $rightColum = get_sub_field('right_column'); ?>
                                <iframe
                                    frameborder="0" style="border:0"
                                    src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBfUJ2B05OTiOmiPpaGLNK8_BaXeFXiATA&q=<?php echo $rightColum['hs_visit_map']['address']; ?>&zoom=20">
                                </iframe>	
                                <h3>Hours &amp; Directions</h3>
                                <?php echo $rightColum['hs_visit_hours_directions'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<!-- Highlight -->
<?php if( have_rows('hs_highlight_information') ): ?>
<?php // loop through the rows of data
    while ( have_rows('hs_highlight_information') ) : the_row(); ?>
        <div class="row">
            <div class="col-md-12 no_padding_both_sides">
                <div class="content_side highlight blue">
                    <div class="row">
                        <div class="col">
                            <?php $image = get_sub_field('hs_highlight_image'); ?>
                            <?php echo wp_get_attachment_image( $image['ID'], 'large', false, array('alt' => $image['alt']) ); ?>
                        </div>
                        <div class="col p_medium dark_blue">
                            <?php $highlightRightColumn = get_sub_field('hs_highlight_right_column'); ?>
                            <h2><?php echo $highlightRightColumn['hs_highlight_title']; ?></h2>
                            <p><?php echo $highlightRightColumn['hs_highlight_content'] ?></p>
                            <a href="<?php echo $highlightRightColumn['hs_highlight_button']['url'] ?>" 
                                target="<?php echo $highlightRightColumn['hs_highlight_button']['target'] ?>" 
                                class="btn marigold small">
                                <?php echo $highlightRightColumn['hs_highlight_button']['title'] ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<!-- Admission -->
<?php if( have_rows('hs_admission_info') ): ?>
<?php // loop through the rows of data
    while ( have_rows('hs_admission_info') ) : the_row(); ?>
        <div id="admissions" class="row">
            <div class="col-md-12 no_padding_both_sides">
                <div class="content_side">
                    <div class="row justify-content-center margin_bottom">
                        <div class="col-md-12 no_padding_both_sides">
                            <div class="content_headline secondary_white ">                  
                                <h2>Admission</h2>                              
                            </div>
                        </div>
                        <div class="col-md-6 intro_text p_nomargin margin_bottom">
                            <?php the_sub_field('hs_admission_intro_text') ?>
                        </div>
                    </div>
                    <?php $admissionContent = get_sub_field('hs_admission_content');?>
                    <div class="row">
                        <div class="col-md-6">
                            <h3>Pricing</h3>
                            <?php
                                $table = $admissionContent['hr_admission_left_content']['hs_admission_pricing_table'];
                                buildTable($table);
                            ?>
                            <?php echo $admissionContent['hr_admission_left_content']['hs_admission_pricing_text'] ?>
                            <a href="#tours-rentals" class="btn marigold small">GROUP TOUR INFO</a>
                        </div>
                        <div class="col-md-6">
                            <div class="content_address p_nomargin greyish">
                                <h3>Contact</h3>
                                <?php echo $admissionContent['hs_admission_contact_text'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<!-- Donate -->
<?php if( have_rows('hs_donate_info') ): ?>
<?php // loop through the rows of data
        while ( have_rows('hs_donate_info') ) : the_row(); ?>
        <?php $image = get_sub_field('hs_donate_image'); ?>
        
        <div class="row">
            <div class="col-md-12 no_padding_both_sides">
                <div class="banner_image">
                    <?php echo wp_get_attachment_image( $image['ID'], 'banner_size', false, array('alt' => $image['alt']) ); ?>
                </div>
            </div>
        </div>
        <div  id="donate" class="row">
            <div class="col-md-12 no_padding_both_sides">
                <div class="content_action_block dark_blue">
                    <h2>Donate to <?php the_title() ?></h2>
                    <div class="content_button">
                        <a href="<?php the_sub_field('hs_donate_button_link') ?>" class="btn marigold small">DONATE</a>							
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<!-- Related Content -->
<div id="events" class="row">
    <div class="col-md-6  no_padding_both_sides">
        <div class="content_related events">
            <div class="title_related">
                <?php the_title() ?> Events 
            </div>
            
            <?php
            // Get Taxonimies Historic Sites
            $taxonomies_hs = get_the_terms($post, 'historic_sites');
            $array_taxonomies_hs = array();

            if ($taxonomies_hs) {
                foreach ( $taxonomies_hs as $term ) {
                    $array_taxonomies_hs[] = $term->slug;
                }
            }

            // Get Taxonimies Our Work
            $taxonomies_ow = get_the_terms($post, 'our_work');
            $array_taxonomies_ow = array();
            
            if ($taxonomies_ow) {
                foreach ( $taxonomies_ow as $term ) {
                    $array_taxonomies_ow[] = $term->slug;
                }
            }

            // Arguments for get Events with taxonomies choosed of Historic Sites and Our Work
            $args = array(
                'post_type' => 'events',
                'tax_query' => array(
                    'relation' => 'OR',
                    array(
                        'taxonomy' => 'historic_sites',
                        'field'    => 'slug',
                        'terms'    => $array_taxonomies_hs,
                    ),
                    array(
                        'taxonomy' => 'our_work',
                        'field'    => 'slug',
                        'terms'    => $array_taxonomies_ow,
                    )
                ),
            );

            // Query for get Events with taxonomies choosed of Historic Sites and Our Work
            $query = new WP_Query( $args );
            ?>

            <?php if ($query->have_posts() ) :?>
                <div class="wrapper_related">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="card_related"> 
                        <div class="date_release">
                            <span><?php echo date( 'F j', strtotime( get_field('e_start_date') ) );?></span>
                        </div>
                        <div class="side_content_related">
                            <span class="title_cont"> <?php the_title(); ?> </span>
                            <span class="time_range"> <?php echo get_field('e_start_time'); ?> </span>
                            <a href="<?php echo get_field('e_link')['url'] ?>">Learn more ></a>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
                </div>

                <div class="see_all">See All ></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-6  no_padding_both_sides">
        <div class="content_related stories">
            <div class="title_related">
                <?php the_title() ?> Stories 
            </div>

            <?php
            // Arguments for get Posts with taxonomies choosed of Historic Sites and Our Work
            $args = array(
                'post_type' => 'post',
                'tax_query' => array(
                    'relation' => 'OR',
                    array(
                        'taxonomy' => 'historic_sites',
                        'field'    => 'slug',
                        'terms'    => $array_taxonomies_hs,
                    ),
                    array(
                        'taxonomy' => 'our_work',
                        'field'    => 'slug',
                        'terms'    => $array_taxonomies_ow,
                    )
                ),
            );

            // Query for get Posts with taxonomies choosed of Historic Sites and Our Work
            $query = new WP_Query( $args );
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
                <div class="see_all">See All ></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tours & Site Rental -->
<?php if( have_rows('hs_tours_and_site_rental') ): ?>
<?php // loop through the rows of data
    while ( have_rows('hs_tours_and_site_rental') ) : the_row(); ?>
    <div id="tours-rentals" class="row">
        <div class="col-md-12 no_padding_both_sides">
            <div class="content_side">
                <div class="row margin_bottom">
                    <div class="row justify-content-center">
                        <div class="col-md-12 no_padding_both_sides">
                            <div class="content_headline secondary_white">                  
                                <h2>Tours & Site Rental</h2>                              
                            </div>
                        </div>
                        <div class="col-md-6 intro_text p_nomargin margin_bottom">
                            <?php $tourSiteRental = get_sub_field('h2_tours_site_rental_info'); ?>
                            <?php echo $tourSiteRental['hs_tour_site_renta_intro_text']; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h3>Site Rental & Special Events</h3>
                        <?php echo $tourSiteRental['hs_tour_content']['content_left']['hs_tour_site_rental_special_events']; ?>
                        <h3>Group Tour Bookings</h3>
                        <?php echo $tourSiteRental['hs_tour_content']['content_left']['hs_tour_group_tour_bookings']; ?>
                        <?php
                            $table = $tourSiteRental['hs_tour_content']['content_left']['hr_tour_table_prices'];
                            buildTable($table);
                        ?>
                        <a href="<?php the_sub_field('hs_donate_button_link') ?>" class="btn marigold small">BOOK NOW</a>
                    </div>
                    <div class="col-md-6">
                        <h3>Contact</h3>
                        <?php echo $tourSiteRental['hs_tour_content']['hr_tour_contact']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
<?php endif; ?>

<!-- Key Visitor Info -->
<?php if( have_rows('hs_key_visitor_info') ): ?>
<?php // loop through the rows of data
    while ( have_rows('hs_key_visitor_info') ) : the_row(); ?>
    <div id="key-visitor-info" class="row">
        <div class="col-md-12 no_padding_both_sides">
            <div class="content_side">
                <div class="row justify-content-center margin_bottom">
                    <div class="col-md-12 no_padding_both_sides margin_bottom">
                        <div class="content_headline secondary_gray">                  
                            <h2>Key Visitor Info</h2>                              
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h3>Location & Arrival</h3>
                        <?php the_sub_field('hs_key_visitor_info_location_and_arrival'); ?>
                    </div>
                    <div class="col-md-6">
                        <h3>Rules & Regulations</h3>
                        <?php the_sub_field('hs_key_visitor_info_rules_and_regulations'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
<?php endif; ?>