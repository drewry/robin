<style>
.error { color: red; }
.ok { color: green; }
</style>
<h1>Install Robin</h1>
<ul>
<?php
$host_url = $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
$host_url = str_replace("/install.php","",$host_url);
echo $host_url;

$error = false;

$curl = curl_version();
if(count($curl) > 0 ) {
	echo '<li class="ok">cURL version - '.$curl['version_number'].'</li>';
} else {
	$error = true;
	echo '<li class="error">cURL version - Cannot find cURL</li>';
}

$filename = 'inc/config.php';
$handle = fopen($filename, "w");
if (fwrite($handle, '1') === FALSE) {
	$error = true;
	echo '<li class="error">Cannot write to "inc" directory</li>';
} else {
	echo '<li class="ok">"inc" directory is writeable</li>';
}
fclose($handle);
?>
</ul>

<?php if(!isset($_POST['step']) ) : ?>
<h2>Database Info</h2>
<form action="install.php" method="post">
	<p>
		<label for="host">Host</label>
		<input type="text" name="db[host]" id="host" value="localhost" />
	</p>
	<p>
		<label for="username">Username</label>
		<input type="text" name="db[username]" id="username" value="root" />
	</p>
	<p>
		<label for="pass">Pass</label>
		<input type="text" name="db[pass]" id="pass" value="" />
	</p>
	<p>
		<label for="database">DB Name</label>
		<input type="text" name="db[database]" id="database" value="robin" />
	</p>
	<input type="hidden" name="step" value="1" />
	<input type="submit" name="test" value="Test DB Connection" />
</form>
<?php endif; ?>

<?php 
if(isset($_POST['test'])) {
	$db = $_POST['db'];
	$dbe1 = false;
	$dbe2 = false;
	
	mysql_connect($db['host'], $db['username'], $db['pass']) or $dbe1 = true;
	mysql_select_db($db['database']) or $dbe2 = true;
	
	if($dbe1 == true) {
		$error = true;
		echo "<p class='error'>Could not connect to the database</p>";
	} else {
		echo "<p class='ok'>Connected to DB Successfully</p>";
	}
	
	if($dbe2 == true) {
		$error = true;
		echo "<p class='error'>Could not select the database</p>";
	} else {
		echo "<p class='ok'>Selected DB Successfully</p>";
	}
	
	if($error == false) {
	?>
	<form action="install.php" method="post">
		<input type="hidden" name="db[host]"  value="<?php echo $db['host']; ?>" />
		<input type="hidden" name="db[username]"  value="<?php echo $db['username']; ?>" />
		<input type="hidden" name="db[pass]"  value="<?php echo $db['pass']; ?>" />
		<input type="hidden" name="db[database]"  value="<?php echo $db['database']; ?>" />
		<input type="hidden" name="step" value="2" />
		<input type="submit" name="install" value="Install" />
	</form>
	<?php
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

if(isset($_POST['install'])) {
	$db = $_POST['db'];
		
	$mysqli = new mysqli($db['host'], $db['username'], $db['pass'], $db['database']);
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	
	$filename = "install.sql";
	$handle = fopen($filename, "r");
	$sql = fread($handle, filesize($filename));
	fclose($handle);
	
	// make db	
	if ($mysqli->multi_query($sql)) {
		echo "<p class='ok'>Successfully created DB</p>"; 
	} else {
		echo "<p class='error'>Error creating db</p>";
		exit;
	}
	
	$mysqli->close();
	
	// get nodes and register
	$default_node = 'dev.morriscgroup.com/robin';
	$nodes = curlJson($default_node,'getNodes','');
	$index = count($nodes) - 1;
	$node = $nodes[$index];
	
	// make config file
	
	$nid = $node->nid + 1;
	$version = '1.0.2';
	
	foreach($nodes as $node) {
		curlJson($node->url,'addNode','nid='.$nid.'&url='.$host_url);
	}
	
	$link = mysql_connect($db['host'], $db['username'], $db['pass']);
	mysql_select_db($db['database']);
	
	foreach($nodes as $node) {
		$sql = "INSERT INTO  nodes (`nid` ,`url` ,`last`)	VALUES ('".$node->nid."', '".$node->url."', '');";
		mysql_query($sql);
	}
	
	$sql = "INSERT INTO  nodes (`nid` ,	`url` ,	`last`) VALUES ( '".$nid."', '".$host_url."', '' );";
	mysql_query($sql);
		
	
		
	$filename = 'inc/config.php';
	$handle = fopen($filename, "w");
	$contents = "<?php\nob_start();\nsession_start();\nerror_reporting(0);\n\ndefine('DB_SERVER', '".$db['host']."');\ndefine('DB_USER', '".$db['username']."');\ndefine('DB_PASS', '".$db['pass']."');\ndefine('DB_DATABASE', '".$db['database']."');\n\nrequire_once('class.db.php');\n\$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);\n\$db->connect();\n\nrequire_once('class.robin.php');\n\$r = new Robin();\n\nrequire_once('layout.php');\n\n// constants\ndefine('NID', '".$nid."');\ndefine('VERSION', '".$version."');\n\nif((!isset(\$_SESSION['uid'])) OR (\$_SESSION['uid'] == 0)) {\n\tdefine('LOGGEDIN', false);\n} else {\n\tdefine('LOGGEDIN', true);\n}";
	fwrite($handle, $contents);
	
	echo "<h1 class='ok'>Installation complete!  Make sure you delete the install.php and install.sql file in the root directory!</h1>";
}
?>