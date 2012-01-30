<aside>
	<h1>Sign in on Node <?php echo NID; ?></h1>
	<form action="signup.php" method="post">
		<p>
			<?php 
			if(isset($signin)) {
				if($signin['success'] == false) {
					echo "<ul>";
					foreach($signin['errors'] as $error): ?>
						<li><?php echo $error; ?></li>
					<?php endforeach;
					echo "</ul>";
				}
			}?>
		</p>
		<fieldset>
			<p>
				<label for="login">User Name:</label>
				<input type="text" name="user[name]" id="login" />
			</p>
			<p>
				<label for="lpass">Password:</label>
				<input type="password" name="user[pass]" id="lpass" />
			</p>
		</fieldset>
		<input type="submit" class="submit" name="signin" value="Sign In" />
	</form>
</aside>