
<!-- https://www.advancedcustomfields.com/resources/flexible-content/ -->
<?php

// check if the flexible content field has rows of data
if( have_rows('hs_main_section') ):
?>
    <h4>Title: <?php the_field('hs_title'); ?></h4>

    <?php // loop through the rows of data
    while ( have_rows('hs_main_section') ) : the_row(); ?>

      <h1>Main Section</h1>
      <h4>Title: <?php the_sub_field('hs_title'); ?></h4>
      <h4>Year of Origintation: <?php the_field('hs_year_of_origination'); ?></h4>
      <h4>Address: <?php the_field('hs_address'); ?></h4>
      <h4>Flexible Content</h4>

    <?php
    endwhile;

else :

    // no layouts found

endif;

?>
