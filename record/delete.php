<?php
require_once("../connect.php");
if(isset($_POST['record_id'])) {
        $record_id = $_POST['record_id'];
        $mysqli->query("DELETE FROM record WHERE record_id=$record_id");
}
?>
<script src="<?php echo JS_ROOT ?>/record.js" /></script>

<?php
$delete_id = (!empty($_GET['record_id']) && is_numeric($_GET['record_id'])) ? $_GET['record_id'] : 0;
if($delete_id) {
	$res_record = $mysqli->query("SELECT * FROM record WHERE record_id = ".$delete_id);
    while($record = $res_record->fetch_assoc()) {
?>
        <p class="validateTips">記録票を削除しますか</p>
        <form>
                <input type="hidden" id="record_id" value="<?php echo $record['record_id'] ?>" />
                <input type="hidden" id="shift_user_id" value="<?php echo $record['shift_user_id'] ?>" />
        </form>
<?php
	}
}
?>