<?php
require_once('inc/config.php');
if(LOGGEDIN != true) {
	header('location: signup.php');
}

if(isset($_GET['follow'])) {
	$r->followUser($_SESSION['uid'],$_GET['uid']);
	$r->redirect_profile();
}
if(isset($_GET['unfollow'])) {
	$r->unfollowUser($_SESSION['uid'],$_GET['uid']);
	$r->redirect_profile();
}

get_header();
?>
	<div id="main" role="main">
		<section id="tab">
			<h1>Search Users</h1>
			<form action="follow.php" method="get">
				<label for="name">Search by user name:</label>
				<input type="text" name="q" id="name" />
				<select name="type">
					<option value="0">On this node</option>
					<option value="1">On other nodes</option>
				</select>
				<input type="submit" name="search" value="Search" />
			</form>
			<hr />
			<div id="results">
				<?php
				if(isset($_GET['search'])) {
					if($_GET['type'] == 0) {
						$r->searchThisUsers($_GET['q']);
					} else {
						$r->searchOtherUsers($_GET['q']);
					}
				}
				?>
			</div>
		</section>
		<?php include('inc/sidebar.php'); ?>
		<div class="clear"></div>
	</div>
<?php get_footer(); ?>