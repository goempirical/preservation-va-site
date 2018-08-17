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
			<div class="col-lg-6 col-xl-5 footer--contact-details">
				<?php 

				$opt_subscribe = get_field('opt_subscribe', 'option');
				if ($opt_subscribe["title"]) {
					echo '<h4>'.$opt_subscribe["title"].'</h4>';
				}
				if ($opt_subscribe["opt_subscribe_snippet"]) {
					echo $opt_subscribe["opt_subscribe_snippet"];
				}

				$opt_footer = get_field('opt_footer', 'option'); 

				if ($opt_footer["opt_footer_address"]) {
					echo "<p class='address'>" . $opt_footer["opt_footer_address"] . "</p>";
				}

				echo "<p class='more_info'>";
				if ($opt_footer["opt_footer_phone"]) {
					echo "<span>phone " . $opt_footer["opt_footer_phone"] . "</span> <span class='gold'>|</span> ";
				}

				if ($opt_footer["opt_footer_fax"]) {
					echo "<span>fax " . $opt_footer["opt_footer_fax"] . "</span> <span class='gold'>|</span> ";
				}

				if ($opt_footer["opt_footer_email"]) {
					echo "<span>" . $opt_footer["opt_footer_email"] . "</span>";
				}
				echo "</p>";
				?>
				<p>&copy; <?php echo date('Y') ?> Preservation Virginia. All Rights Reserved.</p>
			</div>
			<div class="col-lg-6 col-xl-7 row">

				<div class="col-lg-6 social_media">
					<?php get_template_part( 'global-templates/social-links' ); ?>

					<?php 

					$link = get_field('primary_button', 'option');

					if( $link ): ?>

					<div>
						<a href="<?php echo $link['url']; ?>" class="btn marigold small" target="<?php echo $link['target']; ?>"><?php echo $link['title']; ?></a>
					</div>

					<?php endif; ?>
				</div>

				<div class="col-lg-6">

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

				</div>

			</div><!--col end -->

		</div><!-- row end -->

	</div><!-- container end -->

</div><!-- wrapper end -->

</div><!-- #page we need this extra closing tag here -->

<?php wp_footer(); ?>

</body>

</html>

