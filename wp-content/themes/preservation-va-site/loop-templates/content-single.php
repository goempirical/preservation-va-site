<?php
/**
 * Single post partial template.
 *
 * @package understrap
 */

?>
<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<div class="row">
		<div class="col-1">
			<div class="date_release">
				<span><?php echo date( 'F j', strtotime( get_the_date() ) );?></span>
			</div>
		</div>
		<div class="col-11">
			<header class="entry-header">
				<?php 
					the_title( sprintf( 
						'<h2 class="entry-title"><a href="%s" rel="bookmark">', 
						esc_url( get_permalink() ) ), 
						'</a></h2>' 
					); 
				?>
			</header><!-- .entry-header -->

			<div class="entry-content">
				<?php 
					echo get_the_post_thumbnail( $post->ID, 'full');

					$attach_id = get_post_thumbnail_id( $post->ID );
					$image_data = wp_get_attachment( $attach_id );
				?>
				<p class="caption"><?php echo $image_data["caption"]; ?></p>

				<?php the_content(); ?>

				<div class="category">category: 
					<span>
						<?php 
							$array_categories = [];
							$array_hs = get_the_terms($post, 'historic_sites');
							if (!is_array($array_hs)) {
								$array_hs = [];
							}

							$array_ow = get_the_terms($post, 'our_work');
							if (!is_array($array_ow)) {
								$array_ow = [];
							}

							$array_categories = array_merge(
								$array_hs, 
								$array_ow
							);

							$array_term = [];
							foreach ( $array_categories as $term ) {
								$array_term[] = $term->slug;
							}

							$string = join(', ', $array_term);

							echo $string;
						?>
					</span>
				</div>
			</div><!-- .entry-content -->
		</div>
	</div>
</article><!-- #post-## -->
