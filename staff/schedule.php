<?php
require_once("../connect.php");
?>

<?php if(!$a_role): ?>
<?php
$year = (isset($_GET['year'])) ? $_GET['year'] : date('Y');
$month = (isset($_GET['month'])) ? $_GET['month'] : date('n');
$ym = $year.'-'.sprintf('%02d', $month);
$n_day = date('d', mktime(0, 0, 0, $month + 1, 0, $year));

if(isset($_POST['submit'])) {
	$staff_id = $_POST['staff_id'];
	$date = $ym.'-'.sprintf('%02d', $_POST['day']);
	$staff_start = $_POST['staff_start'];
	$staff_end = $_POST['staff_end'];
	$other = $_POST['other'];
	$work = isset($_POST['work']) ? 1 : 0;
	if($other && $staff_start && $staff_end) {
		$mysqli->query("INSERT INTO shift_staff (shift_user_id, date, staff_id_1, staff_id_2, staff_start, staff_end, other, work) VALUES (0, '$date', $staff_id, 0, '$staff_start', '$staff_end', '$other', $work)");
	}

    echo '<script>location.href="'.$_SERVER['PHP_SELF'].'?year='.$year.'&month='.$month.'&staff_id='.$staff_id.'";</script>';
}

if(isset($_POST['delete_id'])) {
	$shift_staff_id = $_POST['delete_id'];
	$mysqli->query("DELETE FROM shift_staff WHERE shift_staff_id=$shift_staff_id");
}

require_once("../header.php");
?>

<?php
$staff_id = $_GET['staff_id'];
$res_staff = $mysqli->query("SELECT * FROM staff WHERE staff_id=$staff_id");
while($staff = $res_staff->fetch_assoc()) {
	$name = $staff['name'];
}
?>

<script src="<?php echo JS_ROOT ?>/staff.js"></script>
<script src="https://ajaxzip3.github.io/ajaxzip3.js"></script>

<div class="main" id="user">
    <h1>予定編集</h1>
    <h2><?php echo $name; ?></h2>

    <form method="get" action="">
        <div>
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
            <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>" />
            <input type="submit" name="search" value="検索" />
        </div>
    </form>

	<div class="block">
        <table>
            <tr><th>日</th><th>曜</th><th>項目</th><th>勤務</th><th>時間</th><th>削除</th></tr>
            <?php
                for($d=0; $d<$n_day; $d++) {
                    $ymd = date('Y-m-d', mktime(0, 0, 0, $month, ($d+1), $year));
                    $res_shift = $mysqli->query("SELECT * FROM shift_staff WHERE shift_user_id=0 AND date='$ymd' AND staff_id_1=$staff_id ORDER BY staff_start ASC");
                    $rows = $res_shift->num_rows;
                    $row = 0;
                    while($shift = $res_shift->fetch_assoc()) {
                        $weekno = date('w', mktime(0, 0, 0, $month, ($d+1), $year));
                        echo '<tr>';
                        if(!$row) {
                            echo '<td rowspan="'.$rows.'">'.date('m月d日', mktime(0, 0, 0, $month, ($d+1), $start_y)).'</td>';
                            echo '<td rowspan="'.$rows.'"';
                            if($weekno == 0) echo ' class="red"';
                            elseif($weekno == 6) echo ' class="blue"';
                            echo '>'.$weekjp[$weekno].'</td>';
                        }
						echo '<td>'.$shift['other'].'</td>';
						echo '<td>'.($shift['work'] ? '○' : '').'</td>';
						echo '<td>'.$shift['staff_start'].'～'.$shift['staff_end'].'</td>';
						echo '<td><button class="delete_shift" name="delete" value="'.$shift['shift_staff_id'].'">削除</button></td>';
                        echo '</tr>';
                        $row++;
                    }
                }
            ?>
        </table>
    </div>

	<div class="block">
        <form method="post" action="" autocomplete="off">
            <table>
                <tr><th>日</th><th>項目</th><th>勤務</th><th>時間</th><th>追加</th></tr>
                <tr>
					<?php
                        echo '<td>'.sprintf('%02d', $month).'月<select name="day">';
                        for($d = 1; $d <= $n_day; $d++) {
                            echo '<option value="'.$d.'">'.sprintf('%02d', $d).'</option>';
                        }
                        echo '</select>日</td>';
                    ?>
                    <td><input type="text" name="other" /></td>
                    <td><input type="checkbox" name="work" value="1" /></td>
                    <td><input type="tel" class="time" name="staff_start" />～<input type="tel" class="time" name="staff_end" /></td>
                    <td><input type="submit" name="submit" value="追加" /></td>
                </tr>
            </table>
            <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>" />
        </form>
    </div>
</div>

<?php
require_once("../footer.php");
?>
<?php endif; ?>