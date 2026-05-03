<?php 
require_once("../connect.php");
?>

<?php if(!$a_role): ?>
<?php
if(isset($_POST['user_id'])) {
	$user_id = $_POST['user_id'];
	$mysqli->query("DELETE FROM user WHERE user_id=$user_id");
}
?>
<script src="<?php echo JS_ROOT ?>/user.js" /></script>

<?php
$delete_id = (!empty($_GET['user_id']) && is_numeric($_GET['user_id'])) ? $_GET['user_id'] : 0;
if($delete_id) {
	$res_user = $mysqli->query("SELECT * FROM user WHERE user_id = ".$delete_id);
    while($user = $res_user->fetch_assoc()) {
?>
        <p class="validateTips">利用者"<?php echo $user['name'] ?>"を削除しますか</p>
        <form>
                <input type="hidden" id="user_id" value="<?php echo $user['user_id'] ?>" />
        </form>
<?php
	}
}
?>
<?php endif; ?>