<?php

function get_header(){
	?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>Robin Messenger - Chirp</title>

	<meta name="viewport" content="width=device-width,initial-scale=1">

	<!-- CSS concatenated and minified via ant build script-->
	<link rel="stylesheet" href="style.css">
	<!-- end CSS-->
	<script type="text/javascript">
	function search() {
		var search = jQuery('#search input').val();
		
		if( (search != '') && (search != 'Search...') ) {
			jQuery('#search').submit();
		}
		
		if (search == '') {
			jQuery('#search input').val('Search...');
		}
	}
	function clearSearch() {
		jQuery('#search input').val('');
	}
	</script>
</head>

<body>

	<div id="container">
		<header>
			<form id="search" action="index.php" method="get"><input type="text" name="q" value="Search..." onfocus="clearSearch()" onblur="search()" /></form>
			<nav>
				<ul>
					<?php if(LOGGEDIN == true) : ?>
						<li><a href="nodes.php">Nodes</a></li>
						<li><a href="follow.php">Follow</a></li>
						<li><a href="create.php">Create Message</a></li>
						<li><a href="signup.php?signout=1">Logout</a></li>
					<?php else : ?>
						<li><a href="nodes.php">Nodes</a></li>
						<li><a href="signup.php">Login</a></li>
						<li><a href="signup.php">Register</a></li>
					<?php endif; ?>
				</ul>
			</nav>
			<figure><a href="index.php">Robin</a></figure>
		</header>
	<?php
}

function get_footer() {
	?>
	<footer>
			Open Source - Robin Messenger.  It's free!  v<?php echo VERSION; ?> | <?php echo date('Y'); ?>
		</footer>
	</div> <!--! end of #container -->

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>

	<!--[if lt IE 7 ]>
	<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
	<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
	<![endif]-->
  
</body>
</html>
	<?php
}