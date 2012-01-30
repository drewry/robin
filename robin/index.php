<?php
require_once('inc/config.php');
if(LOGGEDIN != true) {
	header('location: signup.php');
}

if(isset($_GET['u'])) {
	$r->searchUser($_GET['u']);
}
if(isset($_GET['q'])) {
	$msgs = $r->searchTag($_GET['q']);
} else {
	$msgs = $r->getTimeline($_SESSION['uid']);
}

get_header();
?>
	<div id="main" role="main">
		<section id="tab">
			<?php foreach($msgs as $msg) {
				$r->displayMsg($msg);
			}?>
		</section>
		<?php include('inc/sidebar.php'); ?>
		<div class="clear"></div>
	</div>
<?php get_footer(); ?>