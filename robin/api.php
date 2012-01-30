<?php
require_once('inc/config.php');

if(isset($_GET['getUserName'])) {
	$uid = $_GET['uid'];
	$name = $r->getUserName($uid);
	
	echo json_encode($name);
}

if(isset($_GET['importMsgs'])) {
	$uid = $_GET['uid'];
	$msgs = $r->getUserMsgs($uid);
	
	echo json_encode($msgs);
}

if(isset($_GET['searchUsers'])) {
	$search = $_GET['q'];
	$users = $r->searchUsers($search);
	
	echo json_encode($users);
}

if(isset($_GET['followUser'])) {
	$uid = $_GET['uid'];
	$fid = $_GET['fid'];
	$r->follow($uid,$fid);
	
	echo json_encode(true);
}

if(isset($_GET['unfollowUser'])) {
	$uid = $_GET['uid'];
	$fid = $_GET['fid'];
	$r->unfollow($uid,$fid);
	
	echo json_encode(true);
}

if(isset($_GET['getNodes'])) {
	$nodes = $r->getAllNodes();
	
	echo json_encode($nodes);
}

if(isset($_GET['addNode'])) {
	$nid = $_GET['nid'];
	$url = $_GET['url'];
	
	$r->addNode($nid,$url);
	
	echo json_encode(true);
}