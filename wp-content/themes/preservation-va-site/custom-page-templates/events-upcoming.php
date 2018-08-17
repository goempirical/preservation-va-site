<?php
/**
 * Template Name: Events Upcoming Template
 */
get_header();

?>

<div class="wrapper" id="index-wrapper">
	<div class="container" id="content" tabindex="-1">
		
		<?php while ( have_posts() ) : the_post(); ?>

		<div class="header">
			<h2 class="blog-title"><?php the_title(); ?></h2>
			<?php the_content(); ?>
		</div>

		<?php endwhile; // end of the loop. ?>

		<div class="row">

			<!-- Do the left sidebar check and opens the primary div -->
			<?php get_template_part( 'global-templates/left-sidebar-check' ); ?>

			<main class="site-main" id="main">

        <?php get_template_part('custom-post-templates/events-search') ?>

			</main><!-- #main -->

		</div><!-- #primary -->

		<!-- Do the right sidebar check -->
		<?php get_template_part( 'global-templates/right-sidebar-check' ); ?>

	</div><!-- .row -->

</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
