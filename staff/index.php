<?php
require_once("../connect.php");
?>

<?php if(!$a_role): ?>
<?php
if(!isset($_POST['search']) || isset($_POST['reset'])) {
	unset($_SESSION['name']);
	unset($_SESSION['status']);
	unset($_SESSION['where']);
	unset($_SESSION['sort']);
	unset($_SESSION['order']);
}

require_once("../header.php");
?>

<script src="<?php echo JS_ROOT ?>/staff.js"></script>

<div class="main" id="salary">
    <h1>スタッフ管理</h1>
    
    <div id="delete_dialog" title="スタッフの削除"></div>
    
    <div class="block">
		<?php
            $query = "SELECT * FROM staff";
            require_once("search.php");
            $res_staff = $mysqli->query($query);
        ?>

        <button class="add" name="add" onclick="location.href='add.php'">追加</button>

        <table>
            <tr><th>名前</th><th>性別</th><th>生年月日</th><th>年齢</th><th>住所</th><th>TEL</th><th>携帯電話</th><th>FAX</th><th>メールアドレス</th><th>就職年月日</th><th>退職年月日</th><th>給与形態</th><th>表示順位</th><th>在籍</th><th>編集</th><th>予定</th><th>休日</th><th>削除</th></tr>
            <?php
                while($staff = $res_staff->fetch_assoc()) {
                    echo '<tr><td>'.$staff['name'].'</td><td>'.$sexjp[$staff['sex']].'</td><td>'.change_date($staff['birth']).'</td><td>'.calc_age($staff['birth']).'</td><td class="left">〒'.$staff['zip'].'<br>'.$staff['address'].'</td><td>'.$staff['tel'].'</td><td>'.$staff['mobile'].'</td><td>'.$staff['fax'].'</td><td>'.$staff['mail'].'</td><td>'.change_date($staff['enter_day']).'</td><td>'.change_date($staff['retire_day']).'</td><td>'.$salarytypejp[$staff['salary_type']].'</td><td>'.$staff['display'].'</td><td>'.$registerjp[$staff['register']].'</td><td><button class="trans" name="edit" value="'.$staff['staff_id'].'">編集</button></td><td><button class="trans" name="schedule" value="'.$staff['staff_id'].'">予定</button></td><td><button class="trans" name="day_off" value="'.$staff['staff_id'].'">休日</button></td><td><button class="delete" name="delete" value="'.$staff['staff_id'].'">削除</button></td></tr>';
                }
            ?>
        </table>
    </div>
</div>

<?php
require_once("../footer.php");
?>
<?php endif; ?>