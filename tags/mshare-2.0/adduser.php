<?php
	require_once("HttpAuthPlus_class.php");

	$user = "";
	$pass = "";

	if ($_SERVER['argc'] != 0) parse_str($_SERVER['QUERY_STRING']);

	$login = new HttpAuthPlus; 
	$login->setAuthType('file'); 
	$login->setAuthEncrypt('crypt');
	$login->setAuthFile('data/.mshare.db'); 
	$login->AddUser($user, $pass, ''); 
?>
