<?php
require_once("../connect.php");
?>

<?php if(!$a_role): ?>
<?php
$year = (isset($_GET['year'])) ? $_GET['year'] : date('Y');
$month = (isset($_GET['month'])) ? $_GET['month'] : date('n');
$user_id = $_GET['user_id'];
$ym = $year.'-'.sprintf('%02d', $month);
$date = $ym.'-01';
$n_day = date('d', mktime(0, 0, 0, $month + 1, 0, $year));

if(isset($_POST['submit'])) {
	for($i = 0; $i < count($_POST['shift_user_id'] ?? []); $i++) {
		$shift_user_id = $_POST['shift_user_id'][$i];
		$cancel = $_POST['cancel'][$i];
		$mysqli->query("UPDATE shift_user SET cancel=$cancel WHERE shift_user_id=$shift_user_id");
		$mysqli->query("UPDATE shift_staff SET cancel=$cancel WHERE shift_user_id=$shift_user_id");
	}

    echo '<script>location.href="'.$_SERVER['PHP_SELF'].'?year='.$year.'&month='.$month.'&user_id='.$user_id.'";</script>';
}

require_once("../header.php");
?>

<script src="<?php echo JS_ROOT ?>/shift.js"></script>

<div class="main" id="shift">
    <h1>シフト管理</h1>

    <div id="sub_menu">
        <ul class="cf">
            <li><a href="index.php">日別シフト</a></li>
            <li><a href="user.php" class="current">利用者別シフト</a></li>
            <li><a href="staff.php">スタッフ別シフト</a></li>
        </ul>
    </div>

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
    
    <?php if($user_id): ?>

        <div class="btn_area">
            <button class="print" name="print_user" month="<?php echo $ym; ?>" user_id="<?php echo $user_id; ?>">印刷</button>
            <button class="print_all" name="print_user" month="<?php echo $ym; ?>" user_id="0">一括印刷</button>
        </div>
    
        <div class="block">
            <form method="post" action="">
                <div class="btn_area">
                    <input type="submit" name="submit" value="更新" />
                </div>
    
                <table>
                    <tr><th>日</th><th>曜</th><th>時間</th><th>担当</th><th>キャンセル</th></tr>
                    <?php
                        for($d=0; $d<$n_day; $d++) {
                            $ymd = date('Y-m-d', mktime(0, 0, 0, $month, ($d+1), $year));
                            $res_shift_user = $mysqli->query("SELECT * FROM shift_user WHERE date='$ymd' AND user_id=$user_id ORDER BY user_start ASC");
                            $rows = $res_shift_user->num_rows;
                            $row = 0;
                            while($shift_user = $res_shift_user->fetch_assoc()) {
                                $weekno = date('w', mktime(0, 0, 0, $month, ($d+1), $year));
                                echo '<tr>';
                                if(!$row) {
                                    echo '<td rowspan="'.$rows.'">'.date('m月d日', mktime(0, 0, 0, $month, ($d+1), $year)).'</td>';
                                    echo '<td rowspan="'.$rows.'"';
                                    if($weekno == 0) echo ' class="red"';
                                    elseif($weekno == 6) echo ' class="blue"';
                                    echo '>'.$weekjp[$weekno].'</td>';
                                }
                                echo '<td class="right';
                                if($shift_user['cancel']) echo ' cancel';
                                echo '">';
                                echo $servjp[$shift_user['service']].$shift_user['user_start'].'～'.$shift_user['user_end'].'('.calc_time($shift_user['user_start'], $shift_user['user_end']).')</td>';
                                echo '<td';
                                if($shift_user['cancel']) echo ' class="cancel"';
                                echo '>';
                                $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE shift_user_id=".$shift_user['shift_user_id']);
                                while($shift_staff = $res_shift_staff->fetch_assoc()) {
                                    $res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE staff_id=".$shift_staff['staff_id_1']." OR staff_id=".$shift_staff['staff_id_2']);
                                    while($staff = $res_staff->fetch_assoc()) {
                                        echo '<span class="shift_row">'.$staff['name'].'</span>';
                                    }
                                }
                                echo '</td>';
                                echo '<td><select name="cancel[]">';
                                echo '<option value="0">--</option>';
                                echo '<option value="1"';
                                if($shift_user['cancel']) echo ' selected';
                                echo '>×</option>';
                                echo '</select></td>';
                                echo '<input type="hidden" name="shift_user_id[]" value="'.$shift_user['shift_user_id'].'">';
                                echo '</tr>';
                                $row++;
                            }
                        }
                    ?>
                </table>
            </form>
        </div>
        
    <?php endif; ?>

</div>

<?php
require_once("../footer.php");
?>
<?php endif; ?>