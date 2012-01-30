<?php
require_once('inc/config.php');
get_header();

if(isset($_POST['register'])) {
	$signup = $r->signup($_POST['user']);
	
	if($signup['success'] == true) {
		header('location: index.php');
	}
}

if(isset($_POST['signin'])) {
	$signin = $r->signin($_POST['user']);
	
	if($signin['success'] == true) {
		header('location: index.php');
	}
}

if(isset($_REQUEST['signout'])) {
	session_destroy();
	header('location: signup.php');
}
?>
	<div id="main" role="main">
		<section id="tab">
			<h1>Register on Node <?php echo NID; ?></h1>
			<form action="signup.php" method="post">
				<p>
					<?php 
					if(isset($signup)) {
						if($signup['success'] == false) {
							echo "<ul>";
							foreach($signup['errors'] as $error): ?>
								<li><?php echo $error; ?></li>
							<?php endforeach;
							echo "</ul>";
						}
					}?>
				</p>
				<fieldset>
					<p>
						<label for="name">User Name:</label>
						<span>Your user name will have the number <?php echo NID; ?> after it but you can sign in without it. <br />Your username needs to not have any numbers and be less than 15 chars.</span><br />
						<input type="text" name="user[name]" id="name" onkeyup="charCount()" />  <span id="chars"></span>
					</p>
					<p>
						<label for="pass">Password:</label>
						<input type="password" name="user[pass]" id="pass" />
					</p>
					<p>
						<label for="pass2">Confirm Password:</label>
						<input type="password" name="user[pass2]" id="pass2" />
					</p>
				</fieldset>
				<input type="submit" class="submit" name="register" value="Register" />
			</form>
			<hr />
			<div id="about">
				<h1>What is Robin!? Why not just use Twitter?</h1>
				<p>Recently Twitter has <a href="http://abcnews.go.com/blogs/technology/2012/01/twitter-to-censor-content-on-country-by-country-basis/" target="_blank">made the decision to start censoring tweets</a> that "break the law" in local governments.  We believe that this action is a direct contradiction to the freedom of the internet and the <strong>free unaltered flow of information</strong> from one computer to the next.  Much of the political change we saw in 2011 on Twitter on other social networks was possible only because of these free uncensored forums.</p>
				
				<h2>How Robin Works</h2>
				<p>On Twitter, all of your information is stored and managed by Twitter.  You have little to no control over your data as all information is <strong>centralized</strong>.  By creating an open source software that anyone can download and install on any server in the world -- and have all of these different "nodes" communicating with each other -- we have the ability to <strong>decentralize</strong> information in a brand new way.  Basically, your information is free and you can register on a node that is managed by somebody you trust -- like one of your friends.</p>
				
				<h2>What we are not</h2>
				<p>We are not trying to kill Twitter, quite the contrary (the founder <a href="http://twitter.com/drewry" target="_blank">@Drewry</a> has been an avid Twitter user since its inception).  We understand that Twitter as a large organization is obligated to operate under the laws specific to each country.  We are simply providing a free and open alternative to users around the world.  This project would allow for a simple messaging system on Local Area Networks and Open Mesh Networks in countries where the internet has been shut down as well as an open network on the internet.</p>
				
				<h2>Start your own Robin Node</h2>
				<p>The first version of the source code will be released at 10:00 PM EST on 01/29/2012 and will be posted in this section here -- as well as on <a href="http://github.com/drewry" target="_blank">github</a>.  From that day forward all versions will be listed on the signup page so that anyone can download and install a version.  The installation will register your node on Node 1 and subsequently register your node on all other nodes in the network.</p>
				
				<h2>Release Versions</h2>
				<ul>
					<li>1.0.2 - <a href="http://dev.morriscgroup.com/robin/versions/robin_1-0-2.zip" target="_blank">Download here</a></li>
				</ul>
			</div>
		</section>
		<?php include('inc/login.php'); ?>
		<div class="clear"></div>
	</div>
	<script type="text/javascript">
	function charCount() {
		var msg = jQuery('#name').val();
		jQuery('#chars').html(msg.length + ' chars');
	}
	</script>
<?php get_footer(); ?>