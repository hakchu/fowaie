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
	$mysqli->query("INSERT INTO day_off (date, staff_id) VALUES ('$date', $staff_id)");

    echo '<script>location.href="'.$_SERVER['PHP_SELF'].'?year='.$year.'&month='.$month.'&staff_id='.$staff_id.'";</script>';
}

if(isset($_POST['delete_id'])) {
	$day_off_id = $_POST['delete_id'];
	$mysqli->query("DELETE FROM day_off WHERE day_off_id=$day_off_id");
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
    <h1>休日編集</h1>
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
            <tr><th>日</th><th>曜</th><th>削除</th></tr>
            <?php
                for($d=0; $d<$n_day; $d++) {
                    $ymd = date('Y-m-d', mktime(0, 0, 0, $month, ($d+1), $year));
                    $res_day_off = $mysqli->query("SELECT * FROM day_off WHERE date='$ymd' AND staff_id=$staff_id");
                    while($day_off = $res_day_off->fetch_assoc()) {
                        $weekno = date('w', mktime(0, 0, 0, $month, ($d+1), $year));
                        echo '<tr>';
						echo '<td>'.date('m月d日', mktime(0, 0, 0, $month, ($d+1), $start_y)).'</td>';
						echo '<td';
						if($weekno == 0) echo ' class="red"';
						elseif($weekno == 6) echo ' class="blue"';
						echo '>'.$weekjp[$weekno].'</td>';
						echo '<td><button class="delete_day_off" name="delete" value="'.$day_off['day_off_id'].'">削除</button></td>';
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
                <tr><th>日</th><th>追加</th></tr>
                <tr>
					<?php
                        echo '<td>'.sprintf('%02d', $month).'月<select name="day">';
                        for($d = 1; $d <= $n_day; $d++) {
                            echo '<option value="'.$d.'">'.sprintf('%02d', $d).'</option>';
                        }
                        echo '</select>日</td>';
                    ?>
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