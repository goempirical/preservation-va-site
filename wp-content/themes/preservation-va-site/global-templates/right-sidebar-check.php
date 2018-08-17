<?php
/**
 * Right sidebar check.
 *
 * @package understrap
 */
?>
<!-- sidebar check --><?php $sidebar_pos = get_theme_mod( 'understrap_sidebar_position' ); ?>
<div class="col-lg-4 sidebar-right">
  <?php if ( 'right' === $sidebar_pos || 'both' === $sidebar_pos ) : ?>
    
    <h3>Categories</h3>
  
    <h4>Historic Sites</h4>
    <ul class="historic_sites-list">
      <?php 
        $terms = get_terms('historic_sites'); 

        foreach ($terms as $term) : 
      ?>
          <li><a href="<?php echo get_term_link($term) ?>"><?php echo $term->name; ?></a></li>
      <?php  
        endforeach;
      ?>
    </ul>

    <h4>Our Work</h4>
    <ul class="our_work-list">
      <?php 
        $terms = get_terms('our_work'); 

        foreach ($terms as $term) : 
      ?>
          <li><a href="<?php echo get_term_link($term) ?>"><?php echo $term->name; ?></a></li>
      <?php  
        endforeach;
      ?>
    </ul>
    <?php get_sidebar( 'right' ); ?>

  <?php endif; ?>
</div>
