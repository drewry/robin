<?php
require_once('inc/config.php');
if(LOGGEDIN != true) {
	header('location: signup.php');
}
get_header();

if(isset($_POST['create'])) {
	$data = array();
	$data['msg'] = $_POST['msg'];
	$data['uid'] = $_SESSION['uid'];
	$create = $r->create($data);
	
	if($create['success'] == true) {
		header('location: index.php');
	}
}
?>
	<div id="main" role="main">
		<section id="tab">
			<h1>Create your message!</h1>
			<form action="create.php" method="post">
				<p>
					<?php 
					if(isset($create)) {
						if($create['success'] == false) {
							echo "<ul>";
							foreach($create['errors'] as $error): ?>
								<li><?php echo $error; ?></li>
							<?php endforeach;
							echo "</ul>";
						}
					}?>
				</p>
				<fieldset>
					<p>
						<label for="msg">Message:</label>
						<textarea onkeyup="charCount()" id="msg" name="msg" rows="5" cols="80"><?php if(isset($_GET['reply'])) { echo '@'.$_GET['reply'].' '; } ?></textarea>
						<span id="chars"></span>
					</p>
					<span>Must be 140 characters or under.</span>
				</fieldset>
				<input type="submit" class="submit" name="create" value="Chirp" />
			</form>
		</section>
		<?php include('inc/sidebar.php'); ?>
		<div class="clear"></div>
	</div>
	<script type="text/javascript">
	function charCount() {
		var msg = jQuery('#msg').val();
		jQuery('#chars').html(msg.length + ' chars');
	}
	</script>
<?php get_footer(); ?>