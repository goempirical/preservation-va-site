<?php
/**
 * Template Name: Historic Site
 */
get_header();

$container   = get_theme_mod( 'understrap_container_type' );

$hs_key_visitor_info = get_field('hs_key_visitor_info');
$location = $hs_key_visitor_info['hs_key_visitor_info_location_and_arrival'];
$regulations = $hs_key_visitor_info['hs_key_visitor_info_rules_and_regulations'];

$location = $location !== '' ? $location : false;
$regulations = $regulations !== '' ? $regulations : false;

// Get Taxonimies Historic Sites
$taxonomies_hs = get_the_terms($post, 'historic_sites');
$array_taxonomies_hs = array();

if ($taxonomies_hs) {
    foreach ( $taxonomies_hs as $term ) {
        $array_taxonomies_hs[] = $term->slug;
    }
}

// Arguments for get Events with taxonomies choosed of Historic Sites
$args = array(
    'posts_per_page' => 3,
    'order' => 'ASC',
    'post_type' => 'events',
    'tax_query' => array(
        array(
            'taxonomy' => 'historic_sites',
            'field'    => 'slug',
            'terms'    => $array_taxonomies_hs,
        ),
    ),
);

// Query for get Events with taxonomies choosen from Historic Sites

$eventquery = new WP_Query( $args );

$args['post_type'] = 'post';
$blogquery = new WP_Query( $args );

?>

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<div class="col-md-12 content-area" id="primary">

			<main class="site-main" id="main">

				<?php while ( have_posts() ) : the_post(); ?>


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
                        <?php if ( $eventquery->have_posts() || $blogquery->have_posts() ) { ?><a href="#events">Events</a><?php } ?>
                        <a href="#donate">Donate</a>
                        <a href="#plan-your-visit">Plan your Visit</a>
                        <a href="#admissions">Admissions</a>
                        <a href="#tours-rentals">Tours/Rentals</a>
                        <?php if($location || $regulations) { ?><a href="#key-visitor-info">Key Visitor Info</a><?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <?php $optional_notification = get_sub_field('optional_notification'); ?>

        <?php if($optional_notification['show_this_section']) : ?>


        <section class="layout-block single_column light_blue content_action_block">

            <div class="row">
                <?php if($optional_notification['title']) { ?><h2><?php echo $optional_notification['title']; ?></h2><?php } ?>
                
                <?php echo $optional_notification['text']; ?>
            </div>

        </section>

        <?php endif; ?>

        <!-- section_with_sidebar -->

        <section id="basic-info" class="layout-block section_with_sidebar hs-basic-info">

            <div class="row">
                <?php $mainContent = get_sub_field('main_content'); ?>
                <div class="col-md-8 sws_left-col">
                    <?php echo $mainContent['hs_site_text']; ?>
                </div>

                <div class="col-md-3 sws_right-col content_address">

                    <h3>Address</h3>
                    <?php echo $mainContent['right_column']['hs_address']; ?>


                    <?php if(!empty($mainContent['right_column']['social'])) : ?>
                    <div class="social-links">
                    <?php 
                        foreach ($mainContent['right_column']['social'] as $key => $val) {
                            switch ($val['platform']) {
                                case 'facebook':
                                    $img_id = '28';
                                    break;
                                case 'twitter':
                                    $img_id = '27';
                                    break;
                                case 'instagram':
                                    $img_id = '29';
                                    break;
                                case 'youtube':
                                    $img_id = '31';
                                    break;
                                
                                default:
                                    $img_id = '26';
                                    break;
                            }
                    ?>
                            <a href="<?php echo $val['link'] ?>" target="_blank">
                                <img src="<?php echo THEME_IMG_PATH ?>group-<?php echo $img_id; ?>@2x.png" width="35" />
                            </a>
                    <?php
                        }
                    ?>
                    </div>
                    <?php endif; ?>
                   
                    <h3>Hours</h3>
                    <?php echo $mainContent['right_column']['hs_hours']; ?>
                    
                    <a href="#plan-your-visit" class="btn marigold small">PLAN YOUR VISIT</a>
                </div>

            </div>

        </section>
        <!-- End section_with_sidebar -->

        <!-- Gallery -->
        <?php $container_images = get_sub_field('hs_main_gallery'); ?>
        
        <?php 

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

        <!-- Red Banner Message -->
        <section class="red_banner_message red layout-block">
            <div class="row">
                <h2><?php the_sub_field('hs_caption_gallery'); ?></h2>
            </div>
        </section>

    <?php endwhile; ?>
<?php endif ?>

<!-- Plan your visit -->
<?php if( have_rows('hs_plan_your_visit') ): ?>
<?php // loop through the rows of data
        while ( have_rows('hs_plan_your_visit') ) : the_row(); ?>

        <section id="plan-your-visit" class="hs_plan_your_visit layout-block">
            <div class="content_headline secondary_white margin_bottom">                  
                <h2>Plan your visit</h2>                              
            </div>
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <?php the_sub_field('hs_visit_text') ?>
                    <?php if($location || $regulations) { ?><a href="#key-visitor-info" class="btn marigold small">VISITOR INFO</a><?php } ?>
                </div>
                <div class="col-md-6 hs_pyv--right">
                    <div class="content_address greyish map">
                        <?php $rightColum = get_sub_field('right_column'); ?>
                        <iframe
                            frameborder="0" style="border:0" height="350"
                            src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBGoU3uwZTeYotBXgkkUKL0ipu6l6CfiGs&q=<?php echo $rightColum['hs_visit_map']['address']; ?>&zoom=10">
                        </iframe>	

                        <h3>Hours &amp; Directions</h3>
                        <?php echo $rightColum['hs_visit_hours_directions'] ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endwhile; ?>
<?php endif; ?>

<!-- Highlight -->
<?php if( have_rows('hs_highlight_information') ): ?>
<?php // loop through the rows of data
    while ( have_rows('hs_highlight_information') ) : the_row(); ?>
        <section class="layout-block two_columns dark_blue">

            <div class="row align-items-center">

                <div class="col-md-6">
                    <?php $image = get_sub_field('hs_highlight_image'); ?>
                    <?php echo wp_get_attachment_image( $image['ID'], 'large', false, array('alt' => $image['alt']) ); ?>
                </div>
                <div class="col-md-6 p_medium">
                    <?php $highlightRightColumn = get_sub_field('hs_highlight_right_column'); ?>
                    <h2><?php echo $highlightRightColumn['hs_highlight_title']; ?></h2>
                    <p><?php echo $highlightRightColumn['hs_highlight_content'] ?></p>
                    <a href="<?php echo $highlightRightColumn['hs_highlight_button']['url'] ?>" 
                        target="<?php echo $highlightRightColumn['hs_highlight_button']['target'] ?>" 
                        class="btn marigold small">
                        <?php echo $highlightRightColumn['hs_highlight_button']['title'] ?>
                    </a>
                </div>

            </div> <!-- .ROW -->

        </section>

    <?php endwhile; ?>
<?php endif; ?>

<!-- Admission -->
<?php if( have_rows('hs_admission_info') ): ?>
<?php // loop through the rows of data
    while ( have_rows('hs_admission_info') ) : the_row(); ?>
        <section id="admissions" class="hs_admission_info layout-block">
            <div class="row justify-content-center margin_bottom">
                <div class="content_headline intro_text">                  
                    <h2>Admission</h2>  
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
                </div>
                <div class="col-md-6 content_address">
                    <h3>Contact</h3>
                    <?php echo $admissionContent['hs_admission_contact_text'] ?>
                </div>
            </div>

        </section>

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

        <section class="layout-block single_column hs_donate_info content_action_block dark_blue">

            <div id="donate" class="row">
                <h2>Donate to <?php the_title() ?></h2>
                <div class="content_button">
                    <?php $button_1 = get_sub_field('hs_donate_button_link'); ?>

                    <?php if ( $button_1 ) : ?>
                        <a href="<?php echo site_url(); ?>/support/historic-site-donation/?hsd=<?php echo $post->post_name; ?>" class="btn">Donate</a>
                    <?php endif; ?>
                </div>
            </div>

        </section>

    <?php endwhile; ?>
<?php endif; ?>

<?php if ( $eventquery->have_posts() || $blogquery->have_posts() ) :?>
<!-- Related Content -->
<section class="related-content row">
    <?php if ( $eventquery->have_posts() ) :?>
    <div class="<?php if ( $blogquery->have_posts() ) { ?>col-md-6 <?php } ?>no_padding_both_sides">
        <div class="content_related events">
            <h3 class="title_related"><?php the_title() ?> Events</h3>

            <?php $cnt = 0; ?>

            <div class="wrapper_related">

                <?php while ( $eventquery->have_posts() ) : $eventquery->the_post(); ?>

                    <?php if( have_rows('event_dates_times') ): ?>
                        <?php while ( have_rows('event_dates_times') ) : the_row(); ?>
                            <?php if ($cnt < 3) : ?>

                                <div class="card_related">

                                    <div class="date_release">
                                        <span><?php echo date( 'M<\b\\r>j', strtotime( get_sub_field('e_start_date') ) );?></span>
                                    </div>

                                    <div class="side_content_related">

                                        <span class="title_cont"> <?php the_title(); ?> </span>

                                        <?php if( have_rows('event_times') ): ?>
                                            <?php while ( have_rows('event_times') ) : the_row(); ?>
                                                <span class="time_range"><?php the_sub_field('e_start_time'); ?> – <?php the_sub_field('e_end_time'); ?></span>
                                            <?php endwhile; ?>
                                        <?php else : ?>

                                        <?php endif; ?>

                                        <a class="more-link" href="<?php echo get_field('e_link')['url'] ?>">Learn more</a>

                                    </div>

                                </div>                      
                                <?php $cnt++; ?>
                            <?php endif; // cnt ?>
                        <?php endwhile; // have rows event_dates_times ?>
                    <?php endif; // have rows event_dates_times ?>

                <?php endwhile; wp_reset_postdata(); // eventquery ?>
            </div><!-- .wrapper_related -->

            <div class="see_all"><a href="<?php site_url(); ?>/historic_sites/<?php echo $post->post_name; ?>" class="more-link">See all</a></div>
        </div><!-- .content_related.events -->
    </div><!-- .col-md-6 -->
    <?php endif; ?>
    <?php if ( $blogquery->have_posts() ) :?>
    <div class="<?php if ( $eventquery->have_posts() ) { ?>col-md-6 <?php } ?>no_padding_both_sides">
        <div class="content_related stories">
            <h3 class="title_related"><?php the_title() ?> Stories</h3>

            <?php
            // Arguments for get Posts with taxonomies from Historic Sites and Our Work
            $args['post_type'] = 'post';

            // Query for get Posts with taxonomies choosed of Historic Sites and Our Work
            $query = new WP_Query( $args );
            ?>

            <?php if ($query->have_posts() ) :?>
                <div class="wrapper_related">
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="card_related">
                        <div class="side_content_related">
                            <span class="title_cont"> <?php the_title(); ?> </span>
                            <a class="more-link" href="<?php echo esc_url( get_permalink( get_post()->ID ) ); ?>">Read more</a>
                        </div>
                    </div>														
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <div class="see_all"><a href="<?php site_url(); ?>/historic_sites/<?php echo $post->post_name; ?>" class="more-link">See all</a></div>
            <?php endif; ?>
        </div>
    </div><!-- .col-md-6 -->
    <?php endif; ?>
</section><!-- .related-content -->
<?php endif; // event + blog query ?>

<!-- Tours & Site Rental -->
<?php if( have_rows('hs_tours_and_site_rental') ): ?>
<?php // loop through the rows of data
    while ( have_rows('hs_tours_and_site_rental') ) : the_row(); ?>
    <section id="tours-rentals" class="hs_tours_and_site_rental layout-block">
        <div class="row justify-content-center margin_bottom">
            <div class="content_headline intro_text">                  
                <h2>Tours & Site Rental</h2>  
                <?php $tourSiteRental = get_sub_field('h2_tours_site_rental_info'); ?>
                <?php echo $tourSiteRental['hs_tour_site_renta_intro_text']; ?>
            </div>
        </div>
        <?php $admissionContent = get_sub_field('hs_admission_content');?>
        <div class="row">
            <div class="col-md-6">
            <?php if ($tourSiteRental['hs_tour_content']['content_left']['hs_tour_site_rental_special_events']) { ?>
                <h3>Site Rental & Special Events</h3>
                <?php echo $tourSiteRental['hs_tour_content']['content_left']['hs_tour_site_rental_special_events']; ?>
            <?php } ?>
            <?php if ($tourSiteRental['hs_tour_content']['content_left']['hs_tour_group_tour_bookings']) { ?>
                <h3>Group Tour Bookings</h3>
                <?php echo $tourSiteRental['hs_tour_content']['content_left']['hs_tour_group_tour_bookings']; ?>
            <?php } ?>
                <?php
                    $table = $tourSiteRental['hs_tour_content']['content_left']['hr_tour_table_prices'];
                    buildTable($table);
                ?>
            </div>
            <div class="col-md-6 content_address">
                <h3>Contact</h3>
                <?php echo $tourSiteRental['hs_tour_content']['hr_tour_contact']; ?>
            </div>
        </div>

    </section>

    <?php endwhile; ?>
<?php endif; ?>

<!-- Key Visitor Info -->
        <?php
            if($location || $regulations) :
        ?>
    <div id="key-visitor-info" class="row">
        <div class="col-md-12 no_padding_both_sides margin_bottom">
            <div class="content_headline secondary_gray">                  
                <h2>Key Visitor Info</h2>                              
            </div>
        </div>
    </div>

    <section class="layout-block two_columns hs_key_visitor_info">

        <div class="row">

            <?php if($location) : ?>
            <div<?php if($regulations) { ?> class="col-md-6"<?php } ?>>
                <h3>Location & Arrival</h3>
                <?php echo $location; ?>
            </div>
            <?php endif; ?>

            <?php if($regulations) : ?>
            <div<?php if($location) { ?> class="col-md-6"<?php } ?>>
                <h3>Rules & Regulations</h3>
                <?php echo $regulations; ?>
            </div>
            <?php endif; ?>

        </div> <!-- .ROW -->

    </section>
        <?php endif; // location || regulations ?>

        <?php $optional_section = get_field('optional_last_section'); ?>

        <?php if($optional_section['show_this_section']) : ?>

        <?php if($optional_section['title']) : ?>

            <section class="row headline-row">

            <div class="col-md-12 no_padding_both_sides">

                <div class="content_headline light_blue ">
                    
                    <h1><?php echo $optional_section['title']; ?></h1>  

                </div>

            </div>

        </section>

        <?php endif; ?>

        <section class="layout-block two_columns white">

            <div class="row">

                    <div class="col-md-6 left-column">
                        
                        <div class="content_media">
                            <?php echo wp_get_attachment_image( $optional_section['image']['ID'], 'full' ); ?>                              
                        </div>
                    </div>

                    <div class="col-md-6 right-column">
                                                                    
                        <div class="content_media">
                            <?php echo $optional_section['text']; ?>
                        </div>
                    </div>

            </div> <!-- .ROW -->

        </section>

    <?php endif; ?>

				<?php endwhile; // end of the loop. ?>

			</main><!-- #main -->

			</div> <!-- .col-md-12#primary -->

		</div><!-- .row -->

	</div><!-- Container end -->

<?php get_footer(); ?>
