<?php
require_once("../connect.php");
?>

<?php if(!$a_role): ?>
<?php
$year = (isset($_GET['year'])) ? $_GET['year'] : date('Y');
$month = (isset($_GET['month'])) ? $_GET['month'] : date('n');
$staff_id = $_GET['staff_id'];
$ym = $year.'-'.sprintf('%02d', $month);
$date = $ym.'-01';
$n_day = date('d', mktime(0, 0, 0, $month + 1, 0, $year));

if(isset($_POST['submit'])) {
	for($i = 0; $i < count($_POST['shift_staff_id'] ?? []); $i++) {
		$shift_staff_id = $_POST['shift_staff_id'][$i];
		$foyer = isset($_POST['foyer'][$i]) ? 1 : 0;
		$res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE shift_staff_id=$shift_staff_id");
		while($shift_staff = $res_shift_staff->fetch_assoc()) {
			if($staff_id == $shift_staff['staff_id_1']) {
				$mysqli->query("UPDATE shift_staff SET foyer_1=$foyer WHERE shift_staff_id=$shift_staff_id");
			} elseif($staff_id == $shift_staff['staff_id_2']) {
				$mysqli->query("UPDATE shift_staff SET foyer_2=$foyer WHERE shift_staff_id=$shift_staff_id");
			}
		}
	}

    echo '<script>location.href="'.$_SERVER['PHP_SELF'].'?year='.$year.'&month='.$month.'&staff_id='.$staff_id.'";</script>';
}

require_once("../header.php");
?>

<script src="<?php echo JS_ROOT ?>/shift.js"></script>

<div class="main" id="shift">
    <h1>シフト管理</h1>

    <div id="sub_menu">
        <ul class="cf">
            <li><a href="index.php">日別シフト</a></li>
            <li><a href="user.php">利用者別シフト</a></li>
            <li><a href="staff.php" class="current">スタッフ別シフト</a></li>
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
            スタッフ：<select name="staff_id">
            <option value="">--</option>
            <?php
				$res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE (register=1 OR retire_day>='$date')");
				while($staff = $res_staff->fetch_assoc()) {
					echo '<option value="'.$staff['staff_id'].'"';
					if($staff_id == $staff['staff_id']) echo ' selected';
					echo '>'.$staff['name'].'</option>';
				}
			?>
            </select>
            <input type="submit" name="search" value="検索" />
        </div>
    </form>

    <?php if($staff_id): ?>

        <div class="btn_area">
	        <button class="print" name="print_staff" month="<?php echo $ym; ?>" staff_id="<?php echo $staff_id; ?>">印刷</button>
            <button class="print_all" name="print_staff" month="<?php echo $ym; ?>" staff_id="0">一括印刷</button>
        </div>

        <div class="block">
            <form method="post" action="">
                <div class="btn_area">
                    <input type="submit" name="submit" value="更新" />
                </div>
    
                <table>
                    <tr><th>日</th><th>曜</th><th>時間</th><th>利用者/その他予定</th><th>同行スタッフ</th><th>フォワイエ<input type="checkbox" id="check_all" value=""></th></tr>
                    <?php
						$i = 0;
                        for($d=0; $d<$n_day; $d++) {
                            $ymd = date('Y-m-d', mktime(0, 0, 0, $month, ($d+1), $year));
                            $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE date='$ymd' AND (staff_id_1=$staff_id OR staff_id_2=$staff_id) ORDER BY staff_start ASC");
                            $rows = $res_shift_staff->num_rows;
                            $row = 0;
                            while($shift_staff = $res_shift_staff->fetch_assoc()) {
								if($staff_id == $shift_staff['staff_id_1']) {
									$accompany = $shift_staff['staff_id_2'];
									$foyer = $shift_staff['foyer_1'];
								} elseif($staff_id == $shift_staff['staff_id_2']) {
									$accompany = $shift_staff['staff_id_1'];
									$foyer = $shift_staff['foyer_2'];
								}
                                $weekno = date('w', mktime(0, 0, 0, $month, ($d+1), $year));
                                echo '<tr>';
                                if(!$row) {
                                    echo '<td rowspan="'.$rows.'">'.date('m月d日', mktime(0, 0, 0, $month, ($d+1), $year)).'</td>';
                                    echo '<td rowspan="'.$rows.'"';
                                    if($weekno == 0) echo ' class="red"';
                                    elseif($weekno == 6) echo ' class="blue"';
                                    echo '>'.$weekjp[$weekno].'</td>';
                                }
                                if($shift_staff['shift_user_id']) {
                                    $res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.shift_user_id=".$shift_staff['shift_user_id']);
                                    while($shift_user = $res_shift_user->fetch_assoc()) {
                                        echo '<td class="left';
                                        if($shift_user['cancel']) echo ' cancel';
                                        echo '">';
                                        echo $shift_staff['staff_start'].'～'.$shift_staff['staff_end'].'('.calc_time($shift_staff['staff_start'], $shift_staff['staff_end']).')</td>';
                                        echo '<td class="left';
                                        if($shift_user['cancel']) echo ' cancel';
                                        echo '">'.$servjp[$shift_user['service']].$shift_user['name'].'さん</td>';
                                        echo '<td class="left';
                                        if($shift_user['cancel']) echo ' cancel';
                                        echo '">';
                                        if($accompany) {
                                            $res_staff = $mysqli->query("SELECT name FROM staff WHERE staff_id=$accompany");
                                            while($staff = $res_staff->fetch_assoc()) {
                                                echo $staff['name'];
                                            }
                                        } else {
                                            echo '';
                                        }
                                        echo '</td>';
                                    }
                                } else {
                                    echo '<td class="left">'.$shift_staff['staff_start'].'～'.$shift_staff['staff_end'].'('.calc_time($shift_staff['staff_start'], $shift_staff['staff_end']).')</td>';
                                    echo '<td class="left">[他]'.$shift_staff['other'].'</td>';
                                    echo '<td class="left"></td>';
                                }
								echo '<td><input type="checkbox" class="foyer" name="foyer['.$i.']" value="1"';
								if($foyer) echo ' checked';
								echo ' /></td>';
								echo '<input type="hidden" name="shift_staff_id['.$i.']" value="'.$shift_staff['shift_staff_id'].'">';
                                echo '</tr>';
								$i++;
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