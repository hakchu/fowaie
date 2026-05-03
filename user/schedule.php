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
	$user_id = $_POST['user_id'];
	$date = $ym.'-'.sprintf('%02d', $_POST['day']);
	$user_start = $_POST['user_start'];
	$user_end = $_POST['user_end'];
	$service = $_POST['service'];
	if($user_start && $user_end) {
		$mysqli->query("INSERT INTO shift_user (date, user_id, user_start, user_end, service) VALUES ('$date', $user_id, '$user_start', '$user_end', $service)");
	}

    echo '<script>location.href="'.$_SERVER['PHP_SELF'].'?year='.$year.'&month='.$month.'&user_id='.$user_id.'";</script>';
}

if(isset($_POST['submit2'])) {
	$user_id = $_POST['user_id'];
	$res_regular = $mysqli->query("SELECT * FROM regular WHERE user_id=$user_id");
	while($regular = $res_regular->fetch_assoc()) {
		$user_start = $regular['regular_start'];
		$user_end = $regular['regular_end'];
		$service = $regular['service'];
		if($regular['week_every']) {
			$date = date('Y-m-d', strtotime($weekeveryen[$regular['week_every']].' '.$weeken[$regular['week_num']].' of '.$ym));
			$mysqli->query("INSERT INTO shift_user (date, user_id, user_start, user_end, service) VALUES ('$date', $user_id, '$user_start', '$user_end', $service)");
		} else {
			for($e = 1; $e <= 5; $e++) {
				$date = date('Y-m-d', strtotime($weekeveryen[$e].' '.$weeken[$regular['week_num']].' of '.$ym));
				$mysqli->query("INSERT INTO shift_user (date, user_id, user_start, user_end, service) VALUES ('$date', $user_id, '$user_start', '$user_end', $service)");
			}
		}
	}

    echo '<script>location.href="'.$_SERVER['PHP_SELF'].'?year='.$year.'&month='.$month.'&user_id='.$user_id.'";</script>';
}

if(isset($_POST['delete_id'])) {
	$shift_user_id = $_POST['delete_id'];
	$mysqli->query("DELETE FROM shift_user WHERE shift_user_id=$shift_user_id");
	$mysqli->query("DELETE FROM shift_staff WHERE shift_user_id=$shift_user_id");
}

require_once("../header.php");
?>

<?php
$user_id = $_GET['user_id'];
$res_user = $mysqli->query("SELECT * FROM user WHERE user_id=$user_id");
while($user = $res_user->fetch_assoc()) {
	$name = $user['name'];
}
?>

<script src="<?php echo JS_ROOT ?>/user.js"></script>
<script src="https://ajaxzip3.github.io/ajaxzip3.js"></script>

<div class="main" id="user">
    <h1>利用日編集</h1>
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
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
            <input type="submit" name="search" value="検索" />
        </div>
    </form>

	<div class="block">
        <table>
            <tr><th>日</th><th>曜</th><th>時間</th><th>サービス</th><th>削除</th></tr>
            <?php
                for($d=0; $d<$n_day; $d++) {
                    $ymd = date('Y-m-d', mktime(0, 0, 0, $month, ($d+1), $year));
                    $res_shift = $mysqli->query("SELECT * FROM shift_user WHERE date='$ymd' AND user_id=$user_id ORDER BY user_start ASC");
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
                        echo '<td class="right">';
                        if($shift['regular']) echo '[定]';
						echo $shift['user_start'].'～'.$shift['user_end'].'</td>';
                        echo '<td>'.$servicejp[$shift['service']].'</td>';
						echo '<td><button class="delete_shift" name="delete" value="'.$shift['shift_user_id'].'">削除</button></td>';
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
                <tr><th>日</th><th>時間</th><th>サービス</th><th>追加</th></tr>
                <tr>
					<?php
                        echo '<td>'.sprintf('%02d', $month).'月<select name="day">';
                        for($d = 1; $d <= $n_day; $d++) {
                            echo '<option value="'.$d.'">'.sprintf('%02d', $d).'</option>';
                        }
                        echo '</select>日</td>';
                    ?>
                    <td><input type="tel" class="time" name="user_start" />～<input type="tel" class="time" name="user_end" /></td>
                	<td>
                    	<select name="service">
                            <?php
                            foreach($servicejp as $k => $val) {
                                echo '<option value="'.$k.'">'.$val.'</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td><input type="submit" name="submit" value="追加" /></td>
                </tr>
            </table>

            <table>
                <tr><th>定期利用</th><th>追加</th></tr>
                <tr>
                	<td class="left">
                    <?php
                        $res_regular = $mysqli->query("SELECT * FROM regular WHERE user_id=$user_id");
                        while($regular = $res_regular->fetch_assoc()) {
                            echo $weekeveryjp[$regular['week_every']].$weekjp[$regular['week_num']].'曜日 ';
                            echo $regular['regular_start'].'～'.$regular['regular_end'].' ';
                            echo $servicejp[$regular['service']].'<br>';
                        }
                    ?>
                    </td>
                    <td><input type="submit" name="submit2" value="追加" /></td>
                </tr>
            </table>
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
        </form>
    </div>
</div>

<?php
require_once("../footer.php");
?>
<?php endif; ?>