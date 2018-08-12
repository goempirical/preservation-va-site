<?php
/**
 * Social Links.
 *
 * @package understrap
 */

?>

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

