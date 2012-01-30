<?php
class Robin {  

	public $db;
	
	public function __construct() {
		$this->db = Database::obtain();
	}
	
	function checkUserName($name) {
		$this->db->query('SELECT * FROM users WHERE name = "'.$name.NID.'"'); 
		
		if($this->db->affected_rows > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	function signin($data) {
		$error = false;
		$errors = array();
		$results = array();
		
		$this->db->query('SELECT * FROM users WHERE name = "'.$data['name'].NID.'" OR name = "'.$data['name'].'"'); 
		
		if($this->db->affected_rows == 0) {
			$error = true;
			$errors[] = 'User name not found.';
		}
		
		$row = $this->db->query_first('SELECT uid, pass FROM users WHERE name = "'.$data['name'].NID.'" OR name = "'.$data['name'].'"'); 
		if(md5($data['pass']) != $row['pass']) {
			$error = true;
			$errors[] = 'Password was not correct.';
		}
		
		if($error == false) {
			$results['success'] = true;
			$_SESSION['uid'] = $row['uid'];
		} else {
			$results['success'] = false;
			$results['errors'] = $errors;
		}
		
		return $results;
	}
	
	function signup($data) {
		$error = false;
		$errors = array();
		$results = array();
		
		// see if it's available
		if($this->checkUserName($data['name'])) {
			$error = true;
			$errors[] = 'User Name already exists on this node.';
		}
		
		// cannot be longer than 15
		if(strlen($data['name']) > 15) {
			$error = true;
			$errors[] = 'Please do not make a user name longer than 15 characters.';
		}
		
		// check name exists
		if($data['name'] == '') {
			$error = true;
			$errors[] = 'You must have a user name entered.';
		} else {
			// no numbers in user names (we need the node)
			$chars = str_split($data['name']);
			foreach($chars as $char) {
				if(is_numeric($char)) {
					$error = true;
				}
			}
			if($error == true) {
				$errors[] = 'Please do not include numbers in your user name.';
			}
		}
		
		if($data['pass'] == '') {
			$error = true;
			$errors[] = 'You must have a password.';
		}
		
		// check pass
		if($data['pass'] != $data['pass2']) {
			$error = true;
			$errors[] = 'Your passwords do not match.';
		}
		
		if($error == false) {
			// create user
			$uid = time().'-'.NID;
			
			$a = array();			
			$a['uid'] = $uid;
			$a['name'] = $data['name'].NID;
			$a['pass'] = md5($data['pass']);
			
			$this->db->insert("users", $a);
			
			// follow drewry-1 1327802268-1 by default	
			$fid = '1327802268-1';
			$this->followUser($uid,$fid);
			
			$results['success'] = true;
			
			$_SESSION['uid'] = $uid;
		} else {
			$results['success'] = false;
			$results['errors'] = $errors;
		}
		
		return $results;
	}
	
	function create($data) {
		$error = false;
		$errors = array();
		$results = array();
		
		if( strlen($data['msg']) > 140) {
			$error = true;
			$errors[] = 'Message is too long!';
		}
		
		if( ($data['uid'] == 0) or ($data['uid'] == null) ) {
			$error = true;
			$errors[] = 'Something went wrong!';
		}
		
		if($error == false) {
			// create user
			$a = array();
			$a['gid'] = time().rand(100,999).'-'.NID;
			$a['uid'] = $data['uid'];
			$a['msg'] = $data['msg'];
			
			$gid = $this->db->insert("msgs", $a);
			$results['success'] = true;
		} else {
			$results['success'] = false;
			$results['errors'] = $errors;
		}
		
		return $results;
	}
	
	function follow($uid,$fid) {
		$this->db->query('SELECT * FROM follows WHERE uid = "'.$uid.'" AND fid = "'.$fid.'"'); 
		
		if($this->db->affected_rows == 0) {
			$a = array();
			$a['uid'] = $uid;
			$a['fid'] = $fid;
			
			$gid = $this->db->insert("follows", $a);
		}
	}
	
	function followUser($uid,$fid) {
		// do other node stuff
		$nid = $this->getNid($fid);
		if($nid != NID) {
			// send notification that user is following..
			$url = $this->getNodeUrl($nid);
			$this->curlJson($url,'followUser','uid='.$uid.'&fid='.$fid);
		}
		
		$this->follow($uid,$fid);
	}
	
	function unfollow($uid,$fid) {
		$sql = "DELETE FROM follows WHERE uid = '$uid' AND fid = '$fid'";
		$this->db->query($sql);
	}
	
	function unfollowUser($uid,$fid) {
		// do other node stuff
		$nid = $this->getNid($fid);
		if($nid != NID) {
			// send notification that user is following..
			$url = $this->getNodeUrl($nid);
			$this->curlJson($url,'unfollowUser','uid='.$uid.'&fid='.$fid);
		}
		
		$this->unfollow($uid,$fid);
	}
	
	function redirect_profile() {
		header('location: profile.php?uid='.$_SESSION['uid']);
	}
	
	function getUserMsgs($uid, $start = 0, $limit = 10) {
		$rows = $this->db->fetch_array('SELECT * FROM msgs WHERE uid = "'.$uid.'" ORDER BY gid DESC LIMIT '.$start.', '.$limit);
		return $rows;
	}
	
	function getTimeline($uid) {
		$following = $this->getFollowing($uid);
		
		$or = '';
		foreach($following as $user) {
			$fid = $user['fid'];
			$nid = $this->getNid($fid);
			// if they are not on this node then get their data
			if($nid != NID) {
				// check last sync and get msgs
				if($this->syncNode($nid) == true) {
					$url = $this->getNodeUrl($nid);
					$msgs = $this->curlJson($url,'importMsgs','uid='.$fid);
					
					if(count($msgs) > 0 ) {
						$this->importMsgs($msgs);
					}
				}
			} // end syncing
			
			// build or statement
			$or .= ' OR uid = "'.$user['fid'].'"';
		}
		
		$rows = $this->db->fetch_array('SELECT * FROM msgs WHERE uid = "'.$uid.'" '.$or.' ORDER BY gid DESC');
		return $rows;
	}
	
	function importMsgs($msgs) {
		foreach($msgs as $msg) {	
			$this->db->query('SELECT gid FROM msgs WHERE gid = "'.$msg->gid.'"'); 
		
			if($this->db->affected_rows == 0) {
				$this->db->insert("msgs", $msg);
			}
		}
	}
	
	function searchOtherUsers($search) {
		$nodes = $this->getNodes();
		$users = array();
		foreach($nodes as $node) {
			$url = $node['url'];
			$new_users = $this->curlJson($url,'searchUsers','q='.$search);
			$users = array_merge($users,$new_users);
		}
		
		foreach($users as $user) {
			echo '<div class="user"><a href="http://'.$url.'/profile.php?uid='.$user->uid.'" target="_blank">@'.$user->name.'</a><span class="right">'.$this->displayBtnFollow($_SESSION['uid'],$user->uid,'follow.php').'</span></div>';
		}
	}
	
	function searchThisUsers($search) {	
		$users = $this->searchUsers($search);
		foreach($users as $user) {
			$nid = $this->getNid($user['uid']);
			if($nid == NID) {
				echo '<div class="user"><a href="profile.php?uid='.$user['uid'].'" target="_blank">@'.$user['name'].'</a><span class="right">'.$this->displayBtnFollow($_SESSION['uid'],$user['uid'],'follow.php',$user['uid'],false).'</span></div>';
			}
		}
	}
	
	function searchUsers($search) {
		$sql = 'SELECT uid, name FROM users WHERE name LIKE "'.$search.'%" AND uid LIKE "%-'.NID.'"';
		$rows = $this->db->fetch_array($sql);
		return $rows;
	}
	
	function searchUser($user) {
		$sql="SELECT uid FROM users WHERE name = '$user'";
		$row = $this->db->query_first($sql);
		
		header('location: profile.php?uid='.$row['uid']);
	}
	
	function searchTag($tag) {
		$rows = $this->db->fetch_array('SELECT * FROM msgs WHERE msg LIKE "%'.$tag.'%" ORDER BY gid DESC');
		return $rows;
	}
	
	function displayBtnFollow($uid,$fid,$base = 'profile.php', $personal = '', $others = true) {
		$display = true;
		
		if($personal != '') {
			if($others == false) {
				if($personal == $_SESSION['uid']) {
					$display = false;
				}
			} else {
				if($personal != $_SESSION['uid']) {
					$display = false;
				}
			}
		}
		
		if( ($_SESSION['uid'] == null) or ($_SESSION['uid'] == 0) ) {
			$display = false;
		}
		
		if($display == true) {
			$this->db->query('SELECT * FROM follows WHERE uid = "'.$uid.'" AND fid = "'.$fid.'"'); 
			
			if($this->db->affected_rows > 0) {
				return "<a href='$base?uid=$fid&unfollow' class='btn'>Unfollow</a>";
			} else {
				return "<a href='$base?uid=$fid&follow' class='btn'>Follow</a>";
			}
		}
	}
	
	function displayMsg($msg) {
		$name = $this->getUserName($msg['uid']);
		$time = explode('-',$msg['gid']);
		$time = substr($time[0],0,-3);
		?>
		<div class="msg">
			<?php $nid = $this->getNid($msg['uid']); if($nid == NID) : ?>
				<span class="name"><a href="profile.php?uid=<?php echo $msg['uid']; ?>">@<?php echo $name; ?></a></span>
			<?php else : $url = $this->getNodeUrl($nid); ?>
				<span class="name"><a href="http://<?php echo $url; ?>/profile.php?uid=<?php echo $msg['uid']; ?>" target="_blank">@<?php echo $name; ?></a></span>
			<?php endif; ?>
			<span class="txt"><?php echo $this->displayTxt($msg['msg']); ?></span>
			<a href="create.php?reply=<?php echo $name; ?>" class="right">Reply</a>
			<span class="time"><?php echo $this->displayTime($time); ?></span>
			<div class="clear"></div>			
		</div>
		<?php
	}
	
	function displayTxt($txt) {
		$txt = ' '.$txt; // added because of a weird bug with the regex
		
		$txt = preg_replace("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie","'<a href=\"$1\" target=\"_blank\">$3</a>$4'",$txt);
		
		preg_match('{(\d+)}', $txt, $m); 
		if(count($m) > 0){
			$nid = $m[0];
		} else {
			$nid = NID;
		}

		if($nid != NID) {
			$url = $this->getNodeUrl($nid);
			$txt = preg_replace('/@(\w+)/','<a href="http://'.$url.'/index.php?u=$1" target="_blank">@$1</a>',$txt);
		} else {
			$txt = preg_replace('/@(\w+)/','<a href="index.php?u=$1">@$1</a>',$txt);
		}
		$txt = preg_replace('/\s+#(\w+)/',' <a href="index.php?q=%23$1">#$1</a>',$txt);
		
		
		
		return $txt;
	}
	
	function displayTime($time) {
		return $this->humanTimeDiff($time, time()) . " " . 'ago';
	}
	
	function listNodes() {
		echo "<ul>";
		$nodes = $this->db->fetch_array('SELECT * FROM nodes WHERE nid != "'.NID.'" ORDER BY nid ASC');
		foreach($nodes as $node) {
			$last = $node['last'];
			if($last > 0) {
				$date = date("m/d/Y H:i:s", $last).' GMT';
			} else {
				$date = 'Never';
			}
			
			echo '<li><a href="http://'.$node['url'].'" target="_blank">Node '.$node['nid'].'</a> - Last Sync: '.$date.'</li>';
		}
		echo "</ul>";
	}
	
	function getNodes() {
		$nodes = $this->db->fetch_array('SELECT * FROM nodes WHERE nid != "'.NID.'" ORDER BY nid ASC');
		return $nodes;
	}
	
	function getAllNodes() {
		$nodes = $this->db->fetch_array('SELECT * FROM nodes ORDER BY nid ASC');
		return $nodes;
	}
	
	function addNode($nid,$url) {
		$a = array();
		$a['nid'] = $nid;
		$a['url'] = $url;
		
		$this->db->insert("nodes", $a);
	}
	
	function humanTimeDiff( $from, $to = '' ) {
		if ( empty($to) )
			$to = time();
		$diff = (int) abs($to - $from);
		if ($diff <= 3600) {
			$mins = round($diff / 60);
			if ($mins <= 1) {
				$mins = 1;
			}
			$since = sprintf('%s mins', $mins);
		} else if (($diff <= 86400) && ($diff > 3600)) {
			$hours = round($diff / 3600);
			if ($hours <= 1) {
				$hours = 1;
			}
			$since = sprintf('%s hours', $hours);
		} elseif ($diff >= 86400) {
			$days = round($diff / 86400);
			if ($days <= 1) {
				$days = 1;
			}
			$since = sprintf('%s days', $days);
		}
		return $since;
	}
	
	function getFollowing($uid) {
		$rows = $this->db->fetch_array('SELECT fid FROM follows WHERE uid = "'.$uid.'"');
		return $rows;
	}
	
	function getFollowers($uid) {
		$rows = $this->db->fetch_array('SELECT uid FROM follows WHERE fid = "'.$uid.'"');
		return $rows;
	}
	
	function getMsgCount($uid) {
		$rows = $this->db->fetch_array('SELECT gid FROM msgs WHERE uid = "'.$uid.'"');
		return $this->db->affected_rows;
	}
	
	function getProfileData($uid) {
		$data = array();
		$row = $this->db->query_first('SELECT uid, name FROM users WHERE uid = "'.$uid.'"');
		$data['uid'] = $row['uid'];
		$data['name'] = $row['name'];
		$data['following'] = $this->getFollowing($uid);
		$data['followers'] = $this->getFollowers($uid);		
		$data['msgs'] = $this->getMsgCount($uid);	
		
		return $data;
	}
	
	function getNodeUrl($nid) {
		$data = $this->db->query_first('SELECT url FROM nodes WHERE nid = "'.$nid.'"');
		return $data['url'];
	}
	
	function getNid($id) {
		$parts = explode('-',$id);
		return $parts[1];
	}
	
	function syncNode($nid) {
		$data = $this->db->query_first('SELECT last FROM nodes WHERE nid = "'.$nid.'"');
		$last = $data['last'];
		$current = time();
		$diff = $current - $last;
		
		if($diff > 300) { // sync every 5 minutes (or when prompted)
			$this->updateNodeLast($nid); // set current time for node
			return true;
		} else {
			return false;
		}
	}
	
	function updateNodeLast($nid) {
		$this->db->update('nodes', array('last' => time()), "nid='$nid'");
	}
	
	function getUserName($uid) {		
		$row = $this->db->query_first('SELECT name FROM users WHERE uid = "'.$uid.'"');
		if($this->db->affected_rows == 0) {
			$nid = $this->getNid($uid);
			if($nid != NID) {
				$url = $this->getNodeUrl($nid);
				$external_user = $this->curlJson($url,'getUserName','uid='.$uid);
				
				// add this to the db
				$a = array();
				$a['uid'] = $uid;
				$a['name'] = $external_user;
				
				$this->db->insert("users", $a);
				
				return $external_user;
			}
		} else {
			return $row['name'];
		}
	}
	
	function curlJson($url,$action,$data) {
		$ch = curl_init();
		$aurl = 'http://'.$url.'/api.php?'.$action.'&'.$data;
		curl_setopt($ch, CURLOPT_URL, $aurl);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$html = curl_exec($ch);
		curl_close($ch);
		
		return json_decode($html);
	}
	
} 