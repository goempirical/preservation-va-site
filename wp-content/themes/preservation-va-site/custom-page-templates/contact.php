<?php
/**
 * Template Name: Contact Us
 */

get_header();

?>

<div class="wrapper page-contact" id="page-wrapper">

	<div class="container" id="content" tabindex="-1">

		<div class="row justify-content-md-center">
			<div class="col-8">
				<main class="site-main" id="main">

					<?php while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'loop-templates/content', 'page' ); ?>

						<?php
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
						?>

					<?php endwhile; // end of the loop. ?>

				</main><!-- #main -->
			</div>

		</div><!-- #primary -->

	</div><!-- .row -->

</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
