<?php
/**
 * Single post partial template.
 *
 * @package understrap
 */

?>
<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		<div class="entry-meta">
			<?php echo get_the_date( 'F j, Y' ); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>

	<?php 
	$my_content = get_field('news_content');
	while ( have_rows('news_content') ) : the_row();

		$sub_field = get_sub_field('news_post_embed');
		
			if ( have_rows('news_post_embed') ) {
				
				while ( have_rows('news_post_embed') ) : the_row();

					$layout = get_row_layout();

						if ( $layout === 'select_video' ) { 
?>
							<div class="videoWrapper">
								<?php echo get_sub_field('video'); ?>
							</div>
<?php
						} else {
							echo  wp_get_attachment_image( get_sub_field('image')['ID'], 'post' );
						}
				endwhile;
			}
	endwhile;
?>

	<div class="entry-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->
</article><!-- #post-## -->
