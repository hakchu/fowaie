<?php
if(isset($_SESSION['role'])) {
	$role = $mysqli->real_escape_string($_SESSION['role']);
	$result = $mysqli->query("SELECT * FROM account WHERE role = '$role'");
	while ($row = $result->fetch_assoc()) {
		$a_role = $row['role'];
	}
}

$detect = new Mobile_Detect;

$weekjp = array('日', '月', '火', '水', '木', '金', '土');
$weekeveryjp = array('毎週', '第1', '第2', '第3', '第4', '第5');
$weeken = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
$weekeveryen = array('every', 'first', 'second', 'third', 'fourth', 'fifth');
$sexjp = array('男性', '女性');
$salarytypejp = array('月給', '時給');
$registerjp = array('退職', '在籍');
$register2jp = array('退所', '在所');
$tooljp = array('車椅子', '装具', '呼吸器', 'ヘッドギア');
$servicejp = array('居宅サービス', '身体介護', '重度訪問介護', '行動援護', '移動支援');
$servjp = array('[居]', '[身]', '[重]', '[行]', '[移]');

$tax = 8;
?>