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

<script src="<?php echo JS_ROOT ?>/user.js"></script>

<div class="main" id="user">
    <h1>利用者管理</h1>
    
    <div id="delete_dialog" title="利用者の削除"></div>

    <div>
		<?php
            $query = "SELECT * FROM user";
            require_once("search.php");
            $res_user = $mysqli->query($query);
        ?>

        <button class="add" name="add" onclick="location.href='add.php'">追加</button>

        <table>
            <tr><th>名前</th><th>性別</th><th>生年月日</th><th>年齢</th><th>住所</th><th>TEL</th><th>携帯電話</th><th>FAX</th><th>メールアドレス</th><th>登録年月日</th><th>退所年月日</th><th>在所</th><th>編集</th><th>定期利用</th><th>利用日</th><th>削除</th></tr>
            <?php
                while($user = $res_user->fetch_assoc()) {
                    echo '<tr><td>'.$user['name'].'</td><td>'.$sexjp[$user['sex']].'</td><td>'.change_date($user['birth']).'</td><td>'.calc_age($user['birth']).'</td><td class="left">〒'.$user['zip'].'<br>'.$user['address'].'</td><td>'.$user['tel'].'</td><td>'.$user['mobile'].'</td><td>'.$user['fax'].'</td><td>'.$user['mail'].'</td><td>'.change_date($user['enter_day']).'</td><td>'.change_date($user['retire_day']).'</td><td>'.$register2jp[$user['register']].'</td><td><button class="trans" name="edit" value="'.$user['user_id'].'">編集</button></td><td><button class="trans" name="regular" value="'.$user['user_id'].'">定期利用</button></td><td><button class="trans" name="schedule" value="'.$user['user_id'].'">利用日</button></td><td><button class="delete" name="delete" value="'.$user['user_id'].'">削除</button></td></tr>';
                }
            ?>
        </table>
    </div>
</div>

<?php
require_once("../footer.php");
?>
<?php endif; ?>