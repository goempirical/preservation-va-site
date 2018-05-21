<?php
/**
 * Right sidebar check.
 *
 * @package understrap
 */
?>

<?php $sidebar_pos = get_theme_mod( 'understrap_sidebar_position' ); ?>
<div class="col-4 sidebar-right">
  <?php if ( 'right' === $sidebar_pos || 'both' === $sidebar_pos ) : ?>
    
    <h3>Blog Categories</h3>
  
    <h4>Historic Sites</h4>
    <?php 
      $terms = get_terms('historic_sites'); 

      foreach ($terms as $term) : 
    ?>
        <a href="<?php echo get_term_link($term) ?>"><?php echo $term->name; ?></a>
    <?php  
      endforeach;
    ?>

    <h4>Our Work</h4>
    <?php 
      $terms = get_terms('our_work'); 

      foreach ($terms as $term) : 
    ?>
        <a href="<?php echo get_term_link($term) ?>"><?php echo $term->name; ?></a>
    <?php  
      endforeach;
    ?>

    <?php get_sidebar( 'right' ); ?>

  <?php endif; ?>
</div>
