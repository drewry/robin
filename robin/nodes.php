<?php
require_once('inc/config.php');
get_header();
?>
	<div id="main" role="main">
		<section id="tab">
			<?php $r->listNodes(); ?>
		</section>
		<?php if(LOGGEDIN == true) : ?>
			<?php include('inc/sidebar.php'); ?>
		<?php else : ?>
			<?php include('inc/login.php'); ?>
		<?php endif; ?>
		<div class="clear"></div>
	</div>
<?php get_footer(); ?>