<?php
/**
 * Post rendering content according to caller of get_template_part.
 *
 * @package understrap
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<div class="row">
		<div>

			<?php if ( get_page_template_slug( $post->ID ) === 'custom-page-templates/historic-site.php' ) : ?>

				<header class="entry-header">
					<h4>Historic Site</h4>
					<?php 
						the_title( sprintf( 
							'<h2 class="entry-title"><a href="%s" rel="bookmark">', 
							esc_url( get_permalink() ) ), 
							'</a></h2>' 
						); 
					?>
				</header><!-- .entry-header -->
				<?php 
					$hs_main_section = get_field('hs_main_section');

					if ($hs_main_section) {
						$image = $hs_main_section['hs_banner_image'];

				?>
				<?php echo wp_get_attachment_image( $image['ID'], 'large', false, array('alt' => $image['alt']) ); ?>

      <?php } ?>
      <div class="entry-content">
				<?php the_excerpt(); ?>

				<a href="<?php the_permalink() ?>" class="more-link">Learn more about this historic site</a>
			</div><!-- .entry-content -->

			<?php else: ?>

				<header class="entry-header">
					<?php 
						the_title( sprintf( 
							'<h2 class="entry-title"><a href="%s" rel="bookmark">', 
							esc_url( get_permalink() ) ), 
							'</a></h2>' 
						); 
					?>
				</header><!-- .entry-header -->

				<?php echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); ?>
				<div class="entry-content">
					<?php

						echo $hs_main_section['main_content']['hs_site_text'];

					?>

					<a href="<?php the_permalink() ?>" class="more-link">Learn more</a>
				</div><!-- .entry-content -->

			<?php endif; ?>
		</div>
	</div>
</article><!-- #post-## -->
