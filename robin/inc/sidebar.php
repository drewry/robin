<?php if( ($_SESSION['uid'] == null) or ($_SESSION['uid'] == 0) ) : ?>
	<?php include('login.php'); ?>
<?php else : ?>
<?php $sideprofile = $r->getProfileData($_SESSION['uid']); ?>
	<aside>
		<h2><a href="profile.php?uid=<?php echo $sideprofile['uid']; ?>"><?php echo $sideprofile['name']; ?> Profile</a></h2>
		<h3>Registered on Node1</h3>
		<p>Following:  <a href="profile.php?uid=<?php echo $_SESSION['uid']; ?>&following"><?php echo count($sideprofile['following']); ?></a></p>
		<p>Followers:  <a href="profile.php?uid=<?php echo $_SESSION['uid']; ?>&followers"><?php echo count($sideprofile['followers']); ?></a></p>
		<p>Messages:  <a href="profile.php?uid=<?php echo $_SESSION['uid']; ?>"><?php echo $sideprofile['msgs']; ?></a></p>
	</aside>
<?php endif; ?>