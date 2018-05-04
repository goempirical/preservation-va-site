<?php
/**
 * Template Name: Historic Site
 */
get_header();

$container   = get_theme_mod( 'understrap_container_type' );

?>

<div class="wrapper" id="page-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<div class="col-md-12 content-area" id="primary">
			
			<!-- Do the left sidebar check -->
			<?php //get_template_part( 'global-templates/left-sidebar-check' ); ?>

			<main class="site-main" id="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php //get_template_part( 'loop-templates/content', 'page' ); ?>

						<?php include 'historic-site-main-section.php' ?>

						<?php //include 'historic-site-tours-and-rentals.php' ?>

				<?php endwhile; // end of the loop. ?>

			</main><!-- #main -->

			</div> <!-- .col-md-12#primary -->

		</div><!-- #primary -->

		<!-- Do the right sidebar check -->
		<?php //get_template_part( 'global-templates/right-sidebar-check' ); ?>

	</div><!-- .row -->

</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
