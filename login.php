<?php
session_start();

require_once("config.php");

$errorMessage = "";

// ログインボタンが押された場合
if (isset($_POST['login'])) {	
	// １．ユーザIDの入力チェック
	if (empty($_POST['password'])) {
		$errorMessage = "パスワードが未入力です。";
	}

	// ２．ユーザIDとパスワードが入力されていたら認証する
	if (!empty($_POST['password'])) {

		// クエリの実行
		$query = "SELECT * FROM account WHERE role=".$_POST['role'];
		$result = $mysqli->query($query);
		if (!$result) {
			print('クエリーが失敗しました。' . $mysqli->error);
			$mysqli->close();
			exit();
		}

		while ($row = $result->fetch_assoc()) {
			// パスワード(暗号化済み）の取り出し
			$password = $row['password'];
		}

		// データベースの切断
		$mysqli->close();

		// ３．画面から入力されたパスワードとデータベースから取得したパスワードのハッシュを比較します。
		if ($_POST['password'] == $password) {
			// ４．認証成功なら、セッションIDを新規に発行する
			session_regenerate_id(true);
			$_SESSION['role'] = $_POST['role'];
			header("Location: index.php");
		} else {
			// 認証失敗
			$errorMessage = "パスワードが違います。";
		} 
	} else {
		// 未入力なら何もしない
	} 
} 
 
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<title><?php echo COMPANY_NAME.' '.SITE_TITLE ?></title>
<meta charset="<?php echo DEFAULT_CHAR ?>" />
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<meta name="format-detection" content="telephone=no">
<link rel="stylesheet" href="<?php echo CSS_ROOT ?>/reset.css" />
<link media="only screen and (max-device-width:480px)" href="<?php echo CSS_ROOT ?>/sp.css" rel="stylesheet" />
<link media="screen and (min-device-width:481px)" href="<?php echo CSS_ROOT ?>/style.css" rel="stylesheet" />
<link rel="apple-touch-icon" href="<?php echo IMG_ROOT ?>/logo.png" />
<link rel="shortcut icon" href="<?php echo IMG_ROOT ?>/logo.png">

</head>

<body>
<div id="header">
<h1><?php echo COMPANY_NAME.' '.SITE_TITLE ?></h1>
</div>

<div class="main" id="login">
<div id="form_area">
<form id="loginForm" name="loginForm" action="" method="POST">
	<div class="red"><?php echo $errorMessage ?></div>
    <div>
    	<select id="role" name="role">
        	<option value="0">全権管理者</option>
            <option value="1">記録票管理者</option>
		</select>
    </div>
	<div><label for="password">パスワード<br></label><input type="password" id="password" name="password" value=""></div>
	<div><input type="submit" id="login" name="login" value="ログイン"></div>
</form>
</div>

<?php
require_once("footer.php");
?>