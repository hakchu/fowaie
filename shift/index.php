<?php
require_once("../connect.php");
?>

<?php if(!$a_role): ?>
<?php
if(isset($_GET['date'])) {
	$date = $_GET['date'];
} else {
	$date = date('Y-m-d');
}
list($year, $month, $day) = explode("-", $date);
$month = intval($month);

if(isset($_POST['submit'])) {
	for($i = 0; $i < count($_POST['shift_staff_id']); $i++) {
		$shift_staff_id = $_POST['shift_staff_id'][$i];
		$shift_user_id = $_POST['shift_user_id'][$i];
		$staff_id_1 = $_POST['staff_id_1'][$i] ? $_POST['staff_id_1'][$i] : 0;
		$staff_id_2 = $_POST['staff_id_2'][$i] ? $_POST['staff_id_2'][$i] : 0;
		$staff_start = $_POST['staff_start'][$i];
		$staff_end = $_POST['staff_end'][$i];
		if($staff_start && $staff_end) {
			if($shift_staff_id) {
				$mysqli->query("UPDATE shift_staff SET staff_id_1=$staff_id_1, staff_id_2=$staff_id_2, staff_start='$staff_start', staff_end='$staff_end' WHERE shift_staff_id=$shift_staff_id");
			} else {
				$mysqli->query("INSERT INTO shift_staff (shift_user_id, date, staff_id_1, staff_id_2, staff_start, staff_end, work) VALUES ($shift_user_id, '$date', $staff_id_1, $staff_id_2, '$staff_start', '$staff_end', 1)");
			}
		}
	}

	for($i = 0; $i < count($_POST['other_staff_id']); $i++) {
		$shift_staff_id = $_POST['other_shift_id'][$i];
		$staff_id_1 = $_POST['other_staff_id'][$i] ? $_POST['other_staff_id'][$i] : 0;
		$staff_start = $_POST['other_start'][$i];
		$staff_end = $_POST['other_end'][$i];
		$other = $_POST['other'][$i];
		$work = $_POST['work'][$i];
		if($other && $staff_start && $staff_end) {
			$mysqli->query("UPDATE shift_staff SET staff_id_1=$staff_id_1, staff_start='$staff_start', staff_end='$staff_end', other='$other', work=$work WHERE shift_staff_id=$shift_staff_id");
		}
	}
	
	if($_POST['content']) {
		$content = $_POST['content'];
		$res_remark = $mysqli->query("SELECT * FROM remark WHERE date='$date'");
		if($res_remark->num_rows) {
			$mysqli->query("UPDATE remark SET content='$content' WHERE date='$date'");
		} else {
			$mysqli->query("INSERT INTO remark (date, content) VALUES ('$date', '$content')");
		}
	} else {
		$mysqli->query("DELETE FROM remark WHERE date='$date'");
	}

    echo '<script>location.href="'.$_SERVER['PHP_SELF'].'?date='.$date.'";</script>';
}

if(isset($_POST['delete_id'])) {
	$shift_staff_id = $_POST['delete_id'];
	$mysqli->query("DELETE FROM shift_staff WHERE shift_staff_id=$shift_staff_id");
}

require_once("../header.php");
?>

<script src="<?php echo JS_ROOT ?>/shift.js"></script>
<div class="main" id="shift">

    <h1>シフト管理</h1>

    <div id="sub_menu">
        <ul class="cf">
            <li><a href="index.php" class="current">日別シフト</a></li>
            <li><a href="user.php">利用者別シフト</a></li>
            <li><a href="staff.php">スタッフ別シフト</a></li>
        </ul>
    </div>

    <form method="get" action="">
        <div>
            <input type="text" name="date" value="<?php echo $date; ?>" class="datepicker" />
            <input type="submit" name="search" value="検索" />
        </div>
    </form>

    <div class="btn_area">
        <button class="print" name="print" date="<?php echo $date; ?>" print="all">全体印刷</button>
        <button class="print" name="print" date="<?php echo $date; ?>" print="fowaie">フォワイエ印刷</button>
    </div>
    
    <div class="block">
        <form method="post" action="">
            <div class="btn_area">
                <input type="submit" name="submit" value="更新" />
            </div>
    
                <?php
					for($service=0; $service<count($servicejp); $service++) {
						echo '<table class="shift_table">';
						echo '<caption>●'.$servicejp[$service].'</caption>';
						echo '<tr><th>利用者</th><th>利用時間</th><th>担当</th><th>行追加</th></tr>';
						for($sex=1; $sex>=0; $sex--) {
							$res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.date='$date' AND user.sex=$sex AND shift_user.service=$service ORDER BY user.kana, shift_user.user_start");
							while($shift_user = $res_shift_user->fetch_assoc()) {
								$shift_user_id = $shift_user['shift_user_id'];
								echo '<tr>';
								echo '<td class="left ';
								echo ($sex ? 'pink' : 'blue'); 
								echo '"><a href="./user.php?year='.$year.'&month='.$month.'&user_id='.$shift_user['user_id'].'">'.$shift_user['name'].'</a></td>';
								echo '<td';
								if($shift_user['cancel']) echo ' class="cancel"';
								echo '>'.$shift_user['user_start'].'～'.$shift_user['user_end'].'</td>';
								echo '<td class="left" shift_user_id="'.$shift_user_id.'">';
								$i = 0;
								$res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE shift_user_id=$shift_user_id");
								if($res_shift_staff->num_rows) {
									while($shift_staff = $res_shift_staff->fetch_assoc()) {
										echo '<span class="shift_row">';
										echo '<input type="tel" class="time" name="staff_start[]" value="'.$shift_staff['staff_start'].'" />～<input type="tel" class="time" name="staff_end[]" value="'.$shift_staff['staff_end'].'" />';
										echo '<select name="staff_id_1[]">';
										echo '<option value="">--</option>';
										$res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE (register=1 OR retire_day>='$date')");
										while($staff = $res_staff->fetch_assoc()) {
											echo '<option value="'.$staff['staff_id'].'"';
											if($staff['staff_id'] == $shift_staff['staff_id_1']) echo ' selected';
											echo '>'.$staff['name'].'</option>';
										}
										echo '</select>';
										echo '<select name="staff_id_2[]">';
										echo '<option value="">--</option>';
										$res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE (register=1 OR retire_day>='$date')");
										while($staff = $res_staff->fetch_assoc()) {
											echo '<option value="'.$staff['staff_id'].'"';
											if($staff['staff_id'] == $shift_staff['staff_id_2']) echo ' selected';
											echo '>'.$staff['name'].'</option>';
										}
										echo '</select>';
										echo '<button type="button" class="delete_shift" name="delete" value="'.$shift_staff['shift_staff_id'].'">削除</button>';
										echo '<input type="hidden" name="shift_user_id[]" value="'.$shift_user['shift_user_id'].'">';
										echo '<input type="hidden" name="shift_staff_id[]" value="'.$shift_staff['shift_staff_id'].'">';
										echo '</span>';
										$i++;
									}
								} else {
									echo '<span class="shift_row">';
									echo '<input type="tel" class="time" name="staff_start[]" />～<input type="tel" class="time" name="staff_end[]" />';
									echo '<select name="staff_id_1[]">';
									echo '<option value="">--</option>';
									$res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE (register=1 OR retire_day>='$date')");
									while($staff = $res_staff->fetch_assoc()) {
										echo '<option value="'.$staff['staff_id'].'">'.$staff['name'].'</option>';
									}
									echo '</select>';
									echo '<select name="staff_id_2[]">';
									echo '<option value="">--</option>';
									$res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE (register=1 OR retire_day>='$date')");
									while($staff = $res_staff->fetch_assoc()) {
										echo '<option value="'.$staff['staff_id'].'">'.$staff['name'].'</option>';
									}
									echo '</select>';
									echo '<input type="hidden" name="shift_user_id[]" value="'.$shift_user['shift_user_id'].'">';
									echo '<input type="hidden" name="shift_staff_id[]" value="">';
									echo '</span>';
								}
								echo '</td>';
								echo '<td><button type="button" id="add_row" shift_user_id="'.$shift_user_id.'">行追加</button></td>';
								echo '</tr>';
							}
						}
						echo '</table>';
					}
                ?>
    
            <table class="shift_table">
                <caption>●その他予定</caption>
                <tr><th>項目</th><th>勤務</th><th>時間</th><th>スタッフ</th></tr>
                <?php
                    $i = 0;
                    $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE shift_user_id=0 AND date='$date'");
                    while($shift_staff = $res_shift_staff->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td><input type="text" name="other['.$i.']" value="'.$shift_staff['other'].'" /></td>';
                        echo '<td><input type="hidden" name="work['.$i.']" value="0" /><input type="checkbox" name="work['.$i.']" value="1"';
                        if($shift_staff['work']) echo ' checked';
                        echo ' /></td>';
                        echo '<td><input type="tel" class="time" name="other_start['.$i.']" value="'.$shift_staff['staff_start'].'" />～<input type="tel" class="time" name="other_end[]" value="'.$shift_staff['staff_end'].'" /></td>';
                        echo '<td><select name="other_staff_id[]">';
                        echo '<option value="">--</option>';
                        $res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE (register=1 OR retire_day>='$date')");
                        while($staff = $res_staff->fetch_assoc()) {
                            echo '<option value="'.$staff['staff_id'].'"';
                            if($staff['staff_id'] == $shift_staff['staff_id_1']) echo ' selected';
                            echo '>'.$staff['name'].'</option>';
                        }
                        echo '</select></td>';
                        echo '<input type="hidden" name="other_shift_id[]" value="'.$shift_staff['shift_staff_id'].'">';
                        echo '</tr>';
                        $i++;
                    }
                ?>
            </table>
    
            <table class="shift_table">
                <caption>●備考</caption>
                <tr>
                    <td>
                        <?php
                            $res_remark = $mysqli->query("SELECT * FROM remark WHERE date='$date'");
                            while($remark = $res_remark->fetch_assoc()) {
                                $content = $remark['content'];
                            }
                        ?>
                        <textarea cols="60" rows="8" name="content"><?php echo $content; ?></textarea>
                    </td>
                </tr>
            </table>
        </form>
        
    </div>
    
    <div class="block">
    	<table class="shift_table2">
            <caption>●シフト表</caption>
        	<tr>
				<?php
					$i = 1;
                    $res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE (register=1 OR retire_day>='$date') ORDER BY display ASC");
                    while($staff = $res_staff->fetch_assoc()) {
						$res_day_off = $mysqli->query("SELECT * FROM day_off WHERE date='$date' AND staff_id=".$staff['staff_id']);
                        echo '<th';
						if($res_day_off->num_rows) echo ' class="day_off"';
						echo '><a href="./staff.php?year='.$year.'&month='.$month.'&staff_id='.$staff['staff_id'].'">'.$staff['name'].'</a></th>';
						echo '<td class="left';
						if($res_day_off->num_rows) echo ' day_off';
						echo '">';
                        $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE date='$date' AND (staff_id_1=".$staff['staff_id']." OR staff_id_2=".$staff['staff_id'].") ORDER BY staff_start ASC");
						while($shift_staff = $res_shift_staff->fetch_assoc()) {
							if($shift_staff['shift_user_id']) {
								$res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.shift_user_id=".$shift_staff['shift_user_id']);
								while($shift_user = $res_shift_user->fetch_assoc()) {
									echo '<span class="shift_row';
									if($shift_user['cancel']) echo ' cancel';
									echo '">';
									echo $servjp[$shift_user['service']].$shift_staff['staff_start'].'～'.$shift_staff['staff_end'].'('.$shift_user['name'].'さん)';
									echo '</span>';
									if($staff['staff_id'] == $shift_staff['staff_id_1']) {
										$accompany = $shift_staff['staff_id_2'];
									} elseif($staff['staff_id'] == $shift_staff['staff_id_2']) {
										$accompany = $shift_staff['staff_id_1'];
									} else {
										$accompany = 0;
									}
									if($accompany) {
										$res_staff2 = $mysqli->query("SELECT name FROM staff WHERE staff_id=$accompany");
										while($staff2 = $res_staff2->fetch_assoc()) {
											echo '<span class="shift_row center';
											if($shift_user['cancel']) echo ' cancel';
											echo'">[同行]'.$staff2['name'].'</span>';
										}
									}
								}
							} else {
								echo '<span class="shift_row">[他]'.$shift_staff['staff_start'].'～'.$shift_staff['staff_end'].'('.$shift_staff['other'].')</span>';
							}
						}
						echo '</td>';
						if($i%3 == 0) echo '</tr><tr>';
                        $i++;
                    }
                ?>
            </tr>
        </table>
    </div>
</div>

<?php
require_once("../footer.php");
?>
<?php endif; ?>