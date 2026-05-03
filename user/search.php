<?php
session_start();
if(!isset($_SESSION['status'])) $_SESSION['where'] = " WHERE register=1";
if(!isset($_SESSION['order'])) $_SESSION['order'] = " ORDER BY user_id DESC";
if(isset($_POST['search'])) {
	$_SESSION['status'] = $_POST['status'];
	if($_SESSION['status'] == "enroll") {
		$where = " WHERE register=1";
	} elseif($_SESSION['status'] == "retire") {
		$where = " WHERE register=0";
	} elseif($_SESSION['status'] == "all") {
		$where = " WHERE register IS NOT NULL";
	}
	$_SESSION['name'] = $_POST['name'];
	if($_SESSION['name']) {
		$where .= " AND (name LIKE '%".$_SESSION['name']."%' OR kana LIKE '%".$_SESSION['name']."%')";
	}
	$_SESSION['where'] = $where;
	
	$_SESSION['sort'] = $_POST['sort'];
	if($_SESSION['sort'] == "id") {
		$order = " ORDER BY user_id DESC";
	} elseif($_SESSION['sort'] == "namae") {
		$order = " ORDER BY kana ASC";
	}
	$_SESSION['order'] = $order;
}

$query = $query.$_SESSION['where'].$_SESSION['order'];
?>

<div id="search">
    <form method="post" action="">
        <div>
            <label>名前:</label><input type="text" name="name" value="<?php echo $_SESSION['name']; ?>" />　
           	<label>在所:</label>
            <select name="status">
                <option value="enroll"<?php if($_SESSION['status'] == "enroll") echo " selected"; ?>>在所</option>
                <option value="retire"<?php if($_SESSION['status'] == "retire") echo " selected"; ?>>退所</option>
                <option value="all"<?php if($_SESSION['status'] == "all") echo " selected"; ?>>全て</option>
            </select>　

           	<label>並べ替え:</label>
            <select name="sort">
                <option value="id"<?php if($_SESSION['sort'] == "id") echo " selected"; ?>>登録順</option>
                <option value="namae"<?php if($_SESSION['sort'] == "namae") echo " selected"; ?>>五十音順</option>
            </select>　

            <input type="submit" name="search" value="検索" />
            <input type="submit" name="reset" value="リセット" />
        </div>
   </form>
</div>