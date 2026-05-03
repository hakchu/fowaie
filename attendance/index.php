<?php
require_once("../connect.php");
?>

<?php if(!$a_role): ?>
<?php
if(isset($_POST['reset'])) {
	unset($_SESSION['name']);
	unset($_SESSION['status']);
	unset($_SESSION['where']);
	unset($_SESSION['sort']);
	unset($_SESSION['order']);
}

require_once("../header.php");
?>

<script src="<?php echo JS_ROOT ?>/record.js"></script>

<div class="main">
    <h1>出勤簿</h1>
    
    <div>
		<?php
            $year = (isset($_GET['year'])) ? $_GET['year'] : date('Y');
            $month = (isset($_GET['month'])) ? $_GET['month'] : date('n');
            $ym = $year.'-'.sprintf('%02d', $month);
        ?>
    
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
                <input type="submit" name="span" value="検索" />
            </div>
        </form>

        <form method="post" action="">
        	<input type="hidden" name="year" value="<?php echo $year; ?>" />
        	<input type="hidden" name="month" value="<?php echo $month; ?>" />
        </form>
    
        <?php
            $query = "SELECT * FROM staff";
            require_once("search.php");
            $res_staff = $mysqli->query($query);
        ?>

        <table>
            <tr><th>名前</th><th colspan="5">出勤簿</th></tr>
            <?php
                while($staff = $res_staff->fetch_assoc()) {
                    echo '<tr><td>'.$staff['name'].'</td><td><button class="trans" name="record" staff_id="'.$staff['staff_id'].'" month="'.$ym.'" service="-1">全体</button></td><td><button class="trans" name="record" staff_id="'.$staff['staff_id'].'" month="'.$ym.'" service="0">居宅</button></td><td><button class="trans" name="record" staff_id="'.$staff['staff_id'].'" month="'.$ym.'" service="1">定期</button></td><td><button class="trans" name="record" staff_id="'.$staff['staff_id'].'" month="'.$ym.'" service="2">臨時</button></td><td><button class="trans" name="record" staff_id="'.$staff['staff_id'].'" month="'.$ym.'" service="3">ショート</button></td></tr>';
                }
            ?>
        </table>
    </div>
</div>

<?php
require_once("../footer.php");
?>
<?php endif; ?>