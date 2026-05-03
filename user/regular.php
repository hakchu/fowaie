<?php
require_once("../connect.php");
?>

<?php if(!$a_role): ?>
<?php
if(isset($_POST['submit'])) {
	$user_id = $_POST['user_id'];
	$regular_start = $_POST['regular_start'];
	$regular_end = $_POST['regular_end'];
	$week_every = $_POST['week_every'];
	$week_num = $_POST['week_num'];
	$service = $_POST['service'];
	if($regular_start && $regular_end) {
		$mysqli->query("INSERT INTO regular (user_id, regular_start, regular_end, week_every, week_num, service) VALUES ($user_id, '$regular_start', '$regular_end', $week_every, $week_num, $service)");
	}

    echo '<script>location.href="'.$_SERVER['PHP_SELF'].'?user_id='.$user_id.'";</script>';
}

if(isset($_POST['delete_id'])) {
	$regular_id = $_POST['delete_id'];
	$mysqli->query("DELETE FROM regular WHERE regular_id=$regular_id");
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
    <h1>定期利用編集</h1>
    <h2><?php echo $name; ?></h2>

	<div class="block">
        <table>
            <tr><th>サービス</th><th>週・曜日</th><th>時間</th><th>削除</th></tr>
            <?php
				$res_regular = $mysqli->query("SELECT * FROM regular WHERE user_id=$user_id");
				while($regular = $res_regular->fetch_assoc()) {
					echo '<tr>';
					echo '<td>'.$servicejp[$regular['service']].'</td>';
					echo '<td>'.$weekeveryjp[$regular['week_every']].$weekjp[$regular['week_num']].'曜日</td>';
					echo '<td>'.$regular['regular_start'].'～'.$regular['regular_end'].'</td>';
					echo '<td><button class="delete_regular" name="delete" value="'.$regular['regular_id'].'">削除</button></td>';
					echo '</tr>';
				}
            ?>
        </table>
    </div>

	<div class="block">
        <form method="post" action="" autocomplete="off">
            <table>
                <tr><th>サービス</th><th>週・曜日</th><th>時間</th><th>追加</th></tr>
                <tr>
                	<td>
                    	<select name="service">
                            <?php
                            foreach($servicejp as $k => $val) {
                                echo '<option value="'.$k.'">'.$val.'</option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <?php
                            echo '<select name="week_every">';
                            for($e = 0; $e <= 5; $e++) {
                                echo '<option value="'.$e.'">'.$weekeveryjp[$e].'</option>';
                            }
                            echo '</select>';
                            echo '<select name="week_num">';
                            for($w = 0; $w < 7; $w++) {
                                echo '<option value="'.$w.'">'.$weekjp[$w].'</option>';
                            }
                            echo '</select>曜日';
                        ?>
                    </td>
                    <td><input type="tel" class="time" name="regular_start" />～<input type="tel" class="time" name="regular_end" /></td>
                    <td><input type="submit" name="submit" value="追加" /></td>
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