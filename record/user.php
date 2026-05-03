<?php
require_once("../connect.php");

$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$month = isset($_GET['month']) ? $_GET['month'] : date('n');
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
$ym = $year.'-'.sprintf('%02d', $month);
$date = $ym.'-01';

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

<div id="record2" class="main record">
    <h1>記録票</h1>

	<div id="sub_menu">
        <ul class="cf">
            <li><a href="index.php">日別</a></li>
            <li><a href="user.php" class="current">利用者別</a></li>
        </ul>
    </div>

    <div>
		<div class="btn_area">
			<button class="print_all" name="print_all" year="<?php echo $year; ?>" month="<?php echo $month; ?>" user_id="<?php echo $user_id; ?>" style="margin-top: 10px; left: 427px;">一括印刷</button>
		</div>
        <form method="get" action="">
			<div style="margin: 10px 0;">
				<select name="year">
					<?php
						for($y = date("Y")-5; $y <= date("Y")+1; $y++) {
							echo '<option value="'.$y.'"';
							if($year == $y) echo ' selected';
							echo '>'.$y.'</option>';
						}
					?>
				</select>年
				<select name="month">
					<?php
						for($m = 1; $m <= 12; $m++) {
							echo '<option value="'.$m.'"';
							if($month == $m) echo ' selected';
							echo '>'.$m.'</option>';
						}
					?>
				</select>月　
				利用者：<select name="user_id">
				<option value="">--</option>
				<?php
					$res_user = $mysqli->query("SELECT user_id, name FROM user WHERE (register=1 OR retire_day>='$date')");
					while($user = $res_user->fetch_assoc()) {
						echo '<option value="'.$user['user_id'].'"';
						if($user_id == $user['user_id']) echo ' selected';
						echo '>'.$user['name'].'</option>';
					}
				?>
				</select>
				<input type="submit" name="search" value="検索" />
			</div>
        </form>

		<table>
            <tr><th>日</th><th>曜</th><th>利用時間</th><th>担当</th><th>データ</th><th>発行<input type="checkbox" id="all" /></th><th>編集</th><th>印刷</th></tr>
            <?php
                if(!$a_role || disp_record($date)) {
                    $res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.date LIKE '%$ym%' AND shift_user.user_id=$user_id AND shift_user.cancel=0 ORDER BY shift_user.date, shift_user.user_start");
                    while($shift_user = $res_shift_user->fetch_assoc()) {
                        $shift_user_id = $shift_user['shift_user_id'];
                        $weekno = date('w', strtotime($shift_user['date']));
                        echo '<tr>';
                        echo '<td>'.date('m月d日', strtotime($shift_user['date'])).'</td>';
                        echo '<td';
                        if($weekno == 0) echo ' class="red"';
                        elseif($weekno == 6) echo ' class="blue"';
                        echo '>'.$weekjp[$weekno].'</td>';
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
            ?>
        </table>
    </div>
</div>

<?php
require_once("../footer.php");
?>