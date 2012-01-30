<?php
require_once('inc/config.php');

if(isset($_GET['follow'])) {
	$r->follow($_SESSION['uid'],$_GET['uid']);
}
if(isset($_GET['unfollow'])) {
	$r->unfollow($_SESSION['uid'],$_GET['uid']);
}

get_header();

$profile = $r->getProfileData($_GET['uid']);
$msgs = $r->getUserMsgs($_GET['uid']);
?>
	<div id="main" role="main">
		<section id="tab">
			<h2><?php echo $profile['name']; ?> Profile</h2>
			<h3>Registered on Node1</h3>
			<table class="profile">
				<tr>					
					<td><p>Following:  <a href="profile.php?uid=<?php echo $_GET['uid']; ?>&following"><?php echo count($profile['following']); ?></a></p></td>
					<td><p>Followers:  <a href="profile.php?uid=<?php echo $_GET['uid']; ?>&followers"><?php echo count($profile['followers']); ?></a></p></td>
					<td><p>Messages:  <a href="profile.php?uid=<?php echo $_GET['uid']; ?>"><?php echo $profile['msgs']; ?></a></p></td>
					<td><?php if(LOGGEDIN == true) { echo $r->displayBtnFollow($_SESSION['uid'],$_GET['uid'],'profile.php',$_GET['uid'],false); } ?></td>
				</tr>
			</table>
			<hr />
			<?php
			if(isset($_GET['following'])) {
				$following = $r->getFollowing($_GET['uid']);
				
				echo "<h3>Following</h3>";
				foreach($following as $user) {
					$nid = $r->getNid($user['fid']);
					if($nid == NID) {
						echo '<div class="user"><a href="profile.php?uid='.$user['fid'].'">@'.$r->getUserName($user['fid']).'</a> <span class="right">'.$r->displayBtnFollow($_SESSION['uid'],$user['fid'],'follow.php',$_GET['uid']).'</span></div>';
					} else {
						$url = $r->getNodeUrl($nid);
						echo '<div class="user"><a href="http://'.$url.'/profile.php?uid='.$user['fid'].'" target="_blank">@'.$r->getUserName($user['fid']).'</a> <span class="right">'.$r->displayBtnFollow($_SESSION['uid'],$user['fid'],'follow.php',$_GET['uid']).'</span></div>';
					}
				}
			} elseif(isset($_GET['followers'])) {
				$followers = $r->getFollowers($_GET['uid']);
				
				echo "<h3>Followers</h3>";
				foreach($followers as $user) {
					$nid = $r->getNid($user['uid']);
					if($nid == NID) {
						echo '<div class="user"><a href="profile.php?uid='.$user['uid'].'">@'.$r->getUserName($user['uid']).'</a> <span class="right">'.$r->displayBtnFollow($_SESSION['uid'],$user['uid'],'follow.php',$_GET['uid']).'</span></div>';
					} else {
						$url = $r->getNodeUrl($nid);
						echo '<div class="user"><a href="http://'.$url.'/profile.php?uid='.$user['uid'].'" target="_blank">@'.$r->getUserName($user['uid']).'</a> <span class="right">'.$r->displayBtnFollow($_SESSION['uid'],$user['uid'],'follow.php',$_GET['uid']).'</span></div>';
					}
				}
			} else { ?>
			<div class="msgs">
				<?php foreach($msgs as $msg) {
					$r->displayMsg($msg);
				}?>
			</div>
			<?php } ?>
		</section>
		<?php include('inc/sidebar.php'); ?>
		<div class="clear"></div>
	</div>
<?php get_footer(); ?>