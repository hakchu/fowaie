<?php
	// database setting
	define('DB_HOST', 'localhost');
	define('DB_USER', 'root');
	define('DB_PASS', 'hachoo1986');
	define('DB_NAME', 'fowaie');
	
	// site setting
	define('DOMAIN_ROOT', 'http://localhost/fowaie');
	define('CSS_DIR', '/css');
	define('CSS_ROOT', DOMAIN_ROOT.CSS_DIR);
	define('JS_DIR', '/js');
	define('JS_ROOT', DOMAIN_ROOT.JS_DIR);
	define('IMG_ROOT', DOMAIN_ROOT.'/img');
	
	define('COMPANY_NAME', 'フォワイエ');
	define('SITE_TITLE', 'Management System');
	define('SITE_VER', 'ver1.00');
	define('DEFAULT_CHAR', 'UTF-8');
	define('DB_CHAR', 'UTF-8');
	
	// mysqlへの接続
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);
	if ($mysqli->connect_errno) {
		print('<p>データベースへの接続に失敗しました。</p>' . $mysqli->connect_error);
		exit();
	}
	// データベースの選択
	$mysqli->select_db(DB_NAME);

?>
