<?php
ini_set( 'display_errors', 'Off' );
//error_reporting(E_ALL);
if (!isset($_SESSION['role'])) {
	header("Location: ".DOMAIN_ROOT."/login.php");
	exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<title><?php echo COMPANY_NAME.' '.SITE_TITLE ?></title>
<meta charset="<?php echo DEFAULT_CHAR ?>" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,user-scalable=no">
<meta name="format-detection" content="telephone=no">
<link rel="stylesheet" href="<?php echo CSS_ROOT ?>/reset.css" />
<?php if($detect->isMobile()): ?>
<link href="<?php echo CSS_ROOT ?>/sp.css" rel="stylesheet" />
<?php else: ?>
<link href="<?php echo CSS_ROOT ?>/style.css" rel="stylesheet" />
<?php endif; ?>

<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/ui-lightness/jquery-ui.css" />
<?php if($detect->isMobile()): ?>
<script src="<?php echo JS_ROOT ?>/jquery.sidr.min.js"></script>
<link rel="stylesheet" href="<?php echo CSS_ROOT ?>/jquery.sidr.dark.css" />
<?php endif; ?>
<script src="<?php echo JS_ROOT ?>/jquery.maskedinput.min.js"></script>
<script>
jQuery(function($){
   $("input.time").mask("99:99");
});
</script>
</head>

<body>
<div id="header">
	<h2><?php echo COMPANY_NAME.' '.SITE_TITLE ?>　<?php if(!$detect->isMobile()): ?>　<a href="<?php echo DOMAIN_ROOT ?>/logout.php">ログアウト</a><?php else: ?><a href="#main_menu" class="menu"><img src="<?php echo IMG_ROOT ?>/menu.png" width="30" height="20"></a><?php endif; ?></h2>
</div>
<div id="main_menu">
	<ul class="cf">
        <?php if($detect->isMobile()): ?><li><a href="#main_menu" class="menu">×閉じる</a></li><?php endif; ?>
        <?php if(!$a_role): ?>
            <li><a href="<?php echo DOMAIN_ROOT ?>/shift"<?php if (strpos($_SERVER['REQUEST_URI'], "/shift/") !== false) echo ' class="current"'; ?>>シフト管理</a></li>
            <li><a href="<?php echo DOMAIN_ROOT ?>/user"<?php if (strpos($_SERVER['REQUEST_URI'], "/user/") !== false) echo ' class="current"'; ?>>利用者管理</a></li>
            <li><a href="<?php echo DOMAIN_ROOT ?>/staff"<?php if (strpos($_SERVER['REQUEST_URI'], "/staff/") !== false) echo ' class="current"'; ?>>スタッフ管理</a></li>
            <li><a href="<?php echo DOMAIN_ROOT ?>/attendance"<?php if (strpos($_SERVER['REQUEST_URI'], "/attendance/") !== false) echo ' class="current"'; ?>>出勤簿</a></li>
        <?php endif; ?>
        <li><a href="<?php echo DOMAIN_ROOT ?>/record"<?php if (strpos($_SERVER['REQUEST_URI'], "/record/") !== false) echo ' class="current"'; ?>>記録票</a></li>
        <?php if(!$a_role): ?>
            <li><a href="<?php echo DOMAIN_ROOT ?>/company"<?php if (strpos($_SERVER['REQUEST_URI'], "/system/") !== false) echo ' class="current"'; ?>>システム管理</a></li>
        <?php endif; ?>
    	<?php if($detect->isMobile()): ?><li><a href="<?php echo DOMAIN_ROOT ?>/logout.php">ログアウト</a></li><?php endif; ?>
    </ul>
</div>
<?php if($detect->isMobile()): ?>
<script>
	jQuery(document).ready(function() {
		jQuery('.menu').sidr({
			name: 'main_menu',
			side: 'right'
		});
	});
</script>
<?php endif; ?>