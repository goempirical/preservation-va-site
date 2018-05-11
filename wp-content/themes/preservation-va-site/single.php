<?php
/**
 * The template for displaying all single news.
 *
 * @package understrap
 */

get_header();
$container   = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="single-wrapper">
	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">
		<div class="row justify-content-center">
			<div class="col-md-10">
				<div class="aux_content box-stroke adding__space adding__padding__top">
					<div class="row">
						<!-- Do the left sidebar check -->
						<?php get_template_part( 'global-templates/left-sidebar-check' ); ?>

						<main class="site-main" id="main">
							<?php while ( have_posts() ) : the_post(); ?>
								<?php get_template_part( 'loop-templates/content', 'single' ); ?>
							<?php endwhile; // end of the loop. ?>
						</main><!-- #main -->
					</div><!-- #primary -->

					<!-- Do the right sidebar check -->
					<?php get_template_part( 'global-templates/right-sidebar-check' ); ?>
											
					</div>
				</div>
			</div>
		</div><!-- .row -->
	</div><!-- Container end -->
</div><!-- Wrapper end -->
<?php get_footer(); ?>
