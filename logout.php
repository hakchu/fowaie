<?php
require_once("config.php");
	
session_start();

header("Location: ".DOMAIN_ROOT."/login.php");

// セッション変数のクリア
$_SESSION = array();

// セッションクリア
@session_destroy();
?>

