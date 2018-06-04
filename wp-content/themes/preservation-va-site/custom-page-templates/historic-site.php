<?php
/**
 * Template Name: Historic Site
 */
get_header();

$container   = get_theme_mod( 'understrap_container_type' );

?>
	<!-- Mega Menu -->
	<div class="historic-mega-menu">
		<div class="container ">
			<?php wp_nav_menu(
				array(
					'theme_location'  => 'hictoricmenu',
					'menu_id'         => 'hictoric-mega-menu'
				)
			); ?>
		</div>
	</div>

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<div class="col-md-12 content-area" id="primary">

			<main class="site-main" id="main">

				<?php while ( have_posts() ) : the_post(); ?>

						<?php include 'historic-site-main-section.php' ?>

				<?php endwhile; // end of the loop. ?>

			</main><!-- #main -->

			</div> <!-- .col-md-12#primary -->

		</div><!-- .row -->

	</div><!-- Container end -->

<?php get_footer(); ?>
