
<?php if( have_rows('hs_main_section') ): ?>
    <?php // loop through the rows of data
        while ( have_rows('hs_main_section') ) : the_row(); ?>

        <h1>Main Section</h1>

        <!-- Main Banner -->
        <h4>Image: </h4>
        <?php $image = get_sub_field('hs_banner_image') ?>
        <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />

        <!-- Gallery -->
        <h4>Image Gallery: </h4>
        <?php $image_gallery = get_sub_field('hs_main_gallery') ?>
        <?php if($image_gallery): ?>
          <?php foreach( $image_gallery as $image ): ?>
              <li>
                  <a href="<?php echo $image['url']; ?>">
                       <img src="<?php echo $image['sizes']['thumbnail']; ?>" alt="<?php echo $image['alt']; ?>" />
                  </a>
                  <p><?php echo $image['caption']; ?></p>
              </li>
          <?php endforeach; ?>
        <?php endif ?>

        <h4>Title: <?php the_sub_field('hs_title'); ?></h4>
        <h4>Year of Origintation: <?php the_sub_field('hs_year_of_origination'); ?></h4>
        <h4>Address: <?php the_sub_field('hs_address'); ?></h4>
        <h4>Site Text: <?php the_sub_field('hs_site_text'); ?></h4>

        <!-- To Do:Map Feature Implementation: https://www.advancedcustomfields.com/resources/google%20map/ -->
        <h4>Map: </h4>
        <?php $map = get_sub_field('hs_visit_map'); ?>
        Map Object: <?php echo var_dump($map) ?>

    <?php endwhile; ?>
<?php endif ?>

<h4>Contact Info: <?php the_field('hs_contact_info'); ?></h4>
<h4>Hours Info: <?php the_field('hs_hours_info'); ?></h4>
<h4>Admission Info: <?php the_field('hs_admission_info'); ?></h4>
<h4>Additional Info: <?php the_field('hs_additional_info'); ?></h4>

<h4>Link</h4>
<?php
$link = get_field('hs_donate_button_link');
if( $link ): ?>
	<a class="button" href="<?php echo $link[url]; ?>"><?php var_dump($link); ?></a>
<?php endif; ?>

<h4>Upcoming Events: <?php the_field('hs_upcoming_events'); ?></h4>
<h4>Related Stories: <?php the_field('hs_related_stories'); ?></h4>
