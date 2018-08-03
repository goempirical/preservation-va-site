<?php
/**
 * Post rendering content according to caller of get_template_part.
 *
 * @package understrap
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<div class="row blog-flex-row">
		<div>
			<div class="date_release">
				<span><?php echo date( 'M<\b\\r>j', strtotime( get_the_date() ) );?></span>
			</div>
		</div>
		<div>
			<header class="entry-header">
				<?php 
					the_title( sprintf( 
						'<h2 class="entry-title"><a href="%s" rel="bookmark">', 
						esc_url( get_permalink() ) ), 
						'</a></h2>' 
					); 
				?>
			</header><!-- .entry-header -->

			<?php echo get_the_post_thumbnail( $post->ID, 'thumnail' ); ?>

			<div class="entry-content">
				<?php the_excerpt(); ?>

				<div class="category">
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
								$term_link = get_term_link( $term );
								$array_term[] = '<a href="' . esc_url( $term_link ) . '">' . $term->name . '</a>';
							}

							$string = join(', ', $array_term);
							echo empty($array_term) ? '' : 'category: <span>';
							echo $string;
							echo empty($array_term) ? '' : '</span>';
						?>
				</div>

				<a href="<?php the_permalink() ?>" class="btn marigold small">READ MORE</a>
			</div><!-- .entry-content -->
		</div>
	</div>
</article><!-- #post-## -->
