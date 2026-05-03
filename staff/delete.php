<?php
require_once("../connect.php");
?>

<?php if(!$a_role): ?>
<?php
if(isset($_POST['staff_id'])) {
	$staff_id = $_POST['staff_id'];
	$mysqli->query("DELETE FROM staff WHERE staff_id=$staff_id");
}
?>
<script src="<?php echo JS_ROOT ?>/staff.js" /></script>

<?php
$delete_id = (!empty($_GET['staff_id']) && is_numeric($_GET['staff_id'])) ? $_GET['staff_id'] : 0;
if($delete_id) {
	$res_staff = $mysqli->query("SELECT * FROM staff WHERE staff_id = ".$delete_id);
    while($staff = $res_staff->fetch_assoc()) {
?>
        <p class="validateTips">スタッフ"<?php echo $staff['name'] ?>"を削除しますか</p>
        <form>
                <input type="hidden" id="staff_id" value="<?php echo $staff['staff_id'] ?>" />
        </form>
<?php
	}
}
?>
<?php endif; ?>