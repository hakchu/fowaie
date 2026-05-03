<?php
require_once("../connect.php");
?>

<?php if(!$a_role): ?>
<?php
$staff_id = $_GET['staff_id'];
$ym = $_GET['month'];
$service = $_GET['service'];
list($year, $month) = explode("-", $ym);
$n_day = date('d', mktime(0, 0, 0, $month + 1, 0, $year));

if($staff_id) {
	$res_staff = $mysqli->query("SELECT * FROM staff WHERE staff_id=$staff_id");
    while($staff = $res_staff->fetch_assoc()) {
		$staff_name = $staff['name'];
	}
}

require_once("../header.php");
?>

<script src="<?php echo JS_ROOT ?>/salary.js"></script>

<div class="main" id="report">
    <h1>出勤簿</h1>
    <h2><?php echo $staff_name; ?></h2>
    
    <div class="block">
        <div class="block">
            <table>
                <tr><th>日</th><th>曜</th><th>時間</th><th>勤務時間</th></tr>
                <?php
					if($service < 0) {
						$start_time = array();
						$end_time = array();
						$start_min = array();
						$end_max = array();
						$work_time = array();
						for($d=0; $d<$n_day; $d++) {
							$i = 0;
							$if_work = array();
							$ymd = date('Y-m-d', mktime(0, 0, 0, $month, ($d+1), $year));
							$weekno = date('w', mktime(0, 0, 0, $month, ($d+1), $year));
							echo '<tr>';
							echo '<td>'.date('m月d日', mktime(0, 0, 0, $month, ($d+1), $year)).'</td>';
							echo '<td';
							if($weekno == 0) echo ' class="red"';
							elseif($weekno == 6) echo ' class="blue"';
							echo '>'.$weekjp[$weekno].'</td>';
							$res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE date='$ymd' AND (staff_id_1=$staff_id OR staff_id_2=$staff_id) AND work=1 AND cancel=0 ORDER BY staff_start ASC");
							if($res_shift_staff->num_rows) {
								echo '<td class="left">';
								while($shift_staff = $res_shift_staff->fetch_assoc()) {
									if($shift_staff['shift_user_id']) {
										$res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.shift_user_id=".$shift_staff['shift_user_id']);
										while($shift_user = $res_shift_user->fetch_assoc()) {
											echo '<span class="shift_row';
											if($shift_user['cancel']) echo ' cancel';
											echo '">';
											echo $servjp[$shift_user['service']].$shift_staff['staff_start'].'～'.$shift_staff['staff_end'].'('.$shift_user['name'].'さん)';
											echo '</span>';
										}
									} else {
										echo '<span class="shift_row">[他]'.$shift_staff['staff_start'].'～'.$shift_staff['staff_end'].'('.$shift_staff['other'].')</span>';
									}
									$start_time[$d][$i] = change_to_minutes($shift_staff['staff_start']);
									$end_time[$d][$i] = change_to_minutes($shift_staff['staff_end']);
									$i++;
								}
								echo '</td>';
								$start_min[$d] = min($start_time[$d]);
								$end_max[$d] = max($end_time[$d]);
								for($j=$start_min[$d]; $j<$end_max[$d]; $j++) {
									for($k=0; $k<$i; $k++) {
										if(($j >= $start_time[$d][$k]) && ($j < $end_time[$d][$k]) && ($if_work[$j] == 0)) {
											$work_time[$d]++;
											$if_work[$j] = 1;
										}
									}
								}
								echo '<td class="right">'.change_to_hm($work_time[$d]).'</td>';
							} else {
								echo '<td></td><td></td>';
							}
							echo '</tr>';
						}
						echo '<tr><th colspan="3">合計</th><td>'.change_to_hm(array_sum($work_time)).'</td></tr>';
					} else {
						$start_time = array();
						$end_time = array();
						$start_min = array();
						$end_max = array();
						$work_time = array();
						for($d=0; $d<$n_day; $d++) {
							$i = 0;
							$if_work = array();
							$ymd = date('Y-m-d', mktime(0, 0, 0, $month, ($d+1), $year));
							$weekno = date('w', mktime(0, 0, 0, $month, ($d+1), $year));
							echo '<tr>';
							echo '<td>'.date('m月d日', mktime(0, 0, 0, $month, ($d+1), $year)).'</td>';
							echo '<td';
							if($weekno == 0) echo ' class="red"';
							elseif($weekno == 6) echo ' class="blue"';
							echo '>'.$weekjp[$weekno].'</td>';
							$res_shift_staff = $mysqli->query("SELECT * FROM shift_staff JOIN shift_user ON shift_staff.shift_user_id=shift_user.shift_user_id WHERE shift_staff.date='$ymd' AND (shift_staff.staff_id_1=$staff_id OR shift_staff.staff_id_2=$staff_id) AND shift_user.service=$service AND shift_staff.cancel=0 ORDER BY shift_staff.staff_start ASC");
							if($res_shift_staff->num_rows) {
								echo '<td class="left">';
								while($shift_staff = $res_shift_staff->fetch_assoc()) {
									$res_user = $mysqli->query("SELECT name FROM user WHERE user_id=".$shift_staff['user_id']);
									while($user = $res_user->fetch_assoc()) {
										echo '<span class="shift_row">';
										echo $servjp[$shift_staff['service']].$shift_staff['staff_start'].'～'.$shift_staff['staff_end'].'('.$user['name'].'さん)';
										echo '</span>';
									}
									$start_time[$d][$i] = change_to_minutes($shift_staff['staff_start']);
									$end_time[$d][$i] = change_to_minutes($shift_staff['staff_end']);
									$i++;
								}
								echo '</td>';
								$start_min[$d] = min($start_time[$d]);
								$end_max[$d] = max($end_time[$d]);
								for($j=$start_min[$d]; $j<$end_max[$d]; $j++) {
									for($k=0; $k<$i; $k++) {
										if(($j >= $start_time[$d][$k]) && ($j < $end_time[$d][$k]) && ($if_work[$j] == 0)) {
											$work_time[$d]++;
											$if_work[$j] = 1;
										}
									}
								}
								echo '<td class="right">'.change_to_hm($work_time[$d]).'</td>';
							} else {
								echo '<td></td><td></td>';
							}
							echo '</tr>';
						}
						echo '<tr><th colspan="3">合計</th><td>'.change_to_hm(array_sum($work_time)).'</td></tr>';
					}
                ?>
            </table>
        </div>
    </div>
</div>

<?php
require_once("../footer.php");
?>
<?php endif; ?>