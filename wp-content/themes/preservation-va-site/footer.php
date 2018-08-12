<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package understrap
 */

$the_theme = wp_get_theme();
$container = get_theme_mod( 'understrap_container_type' );
?>

<?php get_sidebar( 'footerfull' ); ?>

<div class="wrapper" id="wrapper-footer">

	<div class="footer">

		<div class="row">
			<div class="col-lg-7 col-xl-6 footer--contact-details">
				<?php get_template_part( 'global-templates/social-links' ); ?>
				<p>&copy; <?php echo date('Y') ?> Preservation Virginia. All Rights Reserved.</p>
			</div>
			<div class="col-xl-4 col-lg-3 social_media">
				<?php $opt_social = get_field('opt_social', 'option'); ?>
				<?php if ($opt_social["opt_social_facebook"]) : ?>
					<a href="<?php echo $opt_social['opt_social_facebook'] ?>" target="_blank">
						<img src="<?php echo THEME_IMG_PATH ?>group-28@2x.png" width="35" />
					</a>
				<?php endif; ?>

				<?php if ($opt_social["opt_social_twitter"]) : ?>
					<a href="<?php echo $opt_social['opt_social_twitter'] ?>" target="_blank">
						<img src="<?php echo THEME_IMG_PATH ?>group-27@2x.png" width="35" />
					</a>
				<?php endif; ?>

				<?php if ($opt_social["opt_social_instagram"]) : ?>
					<a href="<?php echo $opt_social['opt_social_instagram'] ?>" target="_blank">
						<img src="<?php echo THEME_IMG_PATH ?>group-29@2x.png" width="35" />
					</a>
				<?php endif; ?>

				<?php if ($opt_social["opt_social_youtube"]) : ?>
					<a href="<?php echo $opt_social['opt_social_youtube'] ?>" target="_blank">
						<img src="<?php echo THEME_IMG_PATH ?>group-31@2x.png" width="35" />
					</a>
				<?php endif; ?>
				<div>
					<a href="/contact-us" class="btn marigold small">CONTACT US</a>
				</div>
			</div>

			<div class="col-lg-2">

				<footer class="site-footer" id="colophon">

					<div class="site-info">

						<!-- The WordPress Menu goes here -->
						<?php wp_nav_menu(
							array(
								'theme_location'  => 'footermenu',
								'menu_id'         => 'footer-menu'
							)
						); ?>
					</div><!-- .site-info -->

				</footer><!-- #colophon -->

			</div><!--col end -->

		</div><!-- row end -->

	</div><!-- container end -->

</div><!-- wrapper end -->

</div><!-- #page we need this extra closing tag here -->

<?php wp_footer(); ?>

</body>

</html>

