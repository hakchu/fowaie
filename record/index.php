<?php
require_once("../connect.php");

if(isset($_GET['date'])) {
	$date = $_GET['date'];
} else {
	$date = date('Y-m-d');
}

if(isset($_POST['shift_user_id'])) {
	$mysqli->query("UPDATE shift_user SET issue=".$_POST['issue']." WHERE shift_user_id=".$_POST['shift_user_id']);
}

if(isset($_POST['all']) && $_POST['all']) {
	$mysqli->query("UPDATE shift_user SET issue=1 WHERE date='$date'");
}

if(isset($_POST['all']) && !$_POST['all']) {
	$mysqli->query("UPDATE shift_user SET issue=0 WHERE date='$date'");
}

require_once("../header.php");
?>

<script src="<?php echo JS_ROOT ?>/record.js"></script>

<div id="record1" class="main record">
    <h1>記録票</h1>

	<div id="sub_menu">
        <ul class="cf">
            <li><a href="index.php" class="current">日別</a></li>
            <li><a href="user.php">利用者別</a></li>
        </ul>
    </div>

    <div>
		<div class="btn_area">
			<button class="print_all" name="print_all" date="<?php echo $date; ?>" style="margin-top: 10px; left: 187px;">一括印刷</button>
		</div>
        <form method="get" action="">
			<div style="margin: 10px 0;">
				<input type="text" name="date" value="<?php echo $date; ?>" class="datepicker" />
				<input type="submit" name="search" value="検索" />
			</div>
        </form>

		<table>
            <tr><th>利用者</th><th>利用時間</th><th>担当</th><th>データ</th><th>発行<input type="checkbox" id="all" /></th><th>編集</th><th>印刷</th></tr>
            <?php
                if(!$a_role || disp_record($date)) {
                    for($sex=1; $sex>=0; $sex--) {
                        $res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.date='$date' AND shift_user.cancel=0 AND user.sex=$sex ORDER BY user.kana, shift_user.user_start");
                        while($shift_user = $res_shift_user->fetch_assoc()) {
                            $shift_user_id = $shift_user['shift_user_id'];
                            echo '<tr>';
                            echo '<td class="left ';
                            echo ($shift_user['sex'] ? 'pink' : 'blue'); 
                            echo '">'.$shift_user['name'].'</td>';
                            echo '<td';
                            if($shift_user['cancel']) echo ' class="cancel"';
                            echo '>'.$shift_user['user_start'].'～'.$shift_user['user_end'].'</td>';
                            echo '<td class="left">';
                            $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE shift_user_id=".$shift_user['shift_user_id']);
                            while($shift_staff = $res_shift_staff->fetch_assoc()) {
                                $res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE staff_id=".$shift_staff['staff_id_1']." OR staff_id=".$shift_staff['staff_id_2']);
                                while($staff = $res_staff->fetch_assoc()) {
                                    echo '<span class="shift_row">'.$staff['name'].'</span>';
                                }
                            }
                            echo '</td>';
                            echo '<td>';
                            $res_record = $mysqli->query("SELECT * FROM record WHERE shift_user_id=".$shift_user['shift_user_id']);
                            if($res_record->num_rows) {
                                echo $res_record->num_rows;
                            }
                            echo '<td>';
                            echo '<input type="checkbox" class="issue" name="issue" value="'.$shift_user['shift_user_id'].'"';
                            if($shift_user['issue']) echo ' checked';
                            echo ' />';
                            echo '</td>';
                            echo '<td><button class="edit" name="edit" value="'.$shift_user['shift_user_id'].'">編集</button></td>';
                            echo '<td>';
                            echo '<button class="print" name="print" value="'.$shift_user['shift_user_id'].'" page="2">2部</button> ';
                            echo '<button class="print" name="print" value="'.$shift_user['shift_user_id'].'" page="1">利用者控のみ</button>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    }
                }
            ?>
        </table>
    </div>
</div>

<?php
require_once("../footer.php");
?>