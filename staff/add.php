<?php
require_once("../connect.php");
?>

<?php if(!$a_role): ?>
<?php
if(isset($_POST['submit'])) {
    $desc = '';
	$desc .= valid_required("名前", $_POST['name']);
	$desc .= valid_required("フリガナ", $_POST['kana']);
	
	if(!$desc) {
		$name = $_POST['name'];
		$kana = $_POST['kana'];
		$sex = $_POST['sex'];
		$birth = $_POST['birth'] ? "'".$_POST['birth']."'" : 'NULL';
		$zip = $_POST['zip'];
		$address = $_POST['address'];
		$tel = $_POST['tel'];
		$mobile = $_POST['mobile'];
		$fax = $_POST['fax'];
		$mail = $_POST['mail'];
		$enter_day = $_POST['enter_day'] ? "'".$_POST['enter_day']."'" : 'NULL';
		$retire_day = $_POST['retire_day'] ? "'".$_POST['retire_day']."'" : 'NULL';
		$salary_type = $_POST['salary_type'];
		$display = $_POST['display'] ? $_POST['display'] : 0;
		$register = isset($_POST['register']) ? $_POST['register'] : 0;
		$notes = $_POST['notes'];
		
		$mysqli->query("INSERT INTO staff (name, kana, sex, birth, zip, address, tel, mobile, fax, mail, enter_day, retire_day, salary_type, display, register, notes) VALUES ('$name', '$kana', $sex, $birth, '$zip', '$address', '$tel', '$mobile', '$fax', '$mail', $enter_day, $retire_day, $salary_type, $display, $register, '$notes')");

        echo '<script>location.href="index.php";</script>';
	}
}

require_once("../header.php");
?>

<script src="<?php echo JS_ROOT ?>/staff.js"></script>
<script src="https://ajaxzip3.github.io/ajaxzip3.js"></script>

<div class="main" id="staff">
    <h1>スタッフ追加</h1>
    
    <p class="red"><?php echo $desc; ?></p>

    <form method="post" action="" autocomplete="off">
        <input type="submit" name="submit" value="追加" />
        <table class="form_table">
            <tr>
                <th>名前<span class="red">※</span></th>
                <td><input type="text" name="name" value="<?php echo $_POST['name']; ?>" class="w200" /></td>
            </tr>
            <tr>
                <th>フリガナ<span class="red">※</span></th>
                <td><input type="text" name="kana" value="<?php echo $_POST['kana']; ?>" class="w200" /></td>
            </tr>
            <tr>
                <th>性別</th>
                <td><input type="radio" name="sex" value="0"<?php if($_POST['sex']==0) echo " checked"; ?> />男性　<input type="radio" name="sex" value="1"<?php if($_POST['sex']==1) echo " checked"; ?> />女性</td>
            </tr>
            <tr>
                <th>生年月日</th>
                <td><input type="text" name="birth" value="<?php echo $_POST['birth']; ?>" class="datepicker" placeholder="0000-00-00" /></td>
            </tr>
            <tr>
                <th>郵便番号</th>
                <td>〒<input type="text" name="zip" value="<?php echo $_POST['zip']; ?>" onKeyUp="AjaxZip3.zip2addr(this,'','address','address');" class="w100" placeholder="000-0000" /></td>
            </tr>
            <tr>
                <th>住所</th>
                <td><input type="text" name="address" value="<?php echo $_POST['address']; ?>" class="w400" /></td>
            </tr>
            <tr>
                <th>TEL</th>
                <td><input type="tel" name="tel" value="<?php echo $_POST['tel']; ?>" class="w100" /></td>
            </tr>
            <tr>
                <th>携帯電話</th>
                <td><input type="tel" name="mobile" value="<?php echo $_POST['mobile']; ?>" class="w100" /></td>
            </tr>
            <tr>
                <th>FAX</th>
                <td><input type="tel" name="fax" value="<?php echo $_POST['fax']; ?>" class="w100" /></td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td><input type="mail" name="mail" value="<?php echo $_POST['mail']; ?>" class="w300" /></td>
            </tr>
            <tr>
                <th>就職年月日</th>
                <td><input type="text" name="enter_day" value="<?php echo $_POST['enter_day']; ?>" class="datepicker" placeholder="0000-00-00" /></td>
            </tr>
            <tr>
                <th>退職年月日</th>
                <td><input type="text" name="retire_day" value="<?php echo $_POST['retire_day']; ?>" class="datepicker" placeholder="0000-00-00" /></td>
            </tr>
            <tr>
                <th>給与形態</th>
                <td>
                    <input type="radio" name="salary_type" value="0"<?php if($_POST['salary_type']==0) echo " checked"; ?> />月給　
                    <input type="radio" name="salary_type" value="1"<?php if($_POST['salary_type']==1) echo " checked"; ?> />時給
                </td>
            </tr>
            <tr>
                <th>表示順位</th>
                <td>
                    <input type="number" name="display" value="<?php echo $_POST['display']; ?>" />
                </td>
            </tr>
            <tr>
                <th>在籍</th>
                <td><input type="radio" name="register" value="1"<?php if(!isset($_POST['register']) || ($_POST['register']==1)) echo " checked"; ?> />在籍　<input type="radio" name="register" value="0"<?php if(isset($_POST['register']) && ($_POST['register']==0)) echo " checked"; ?> />退職</td>
            </tr>
            <tr>
                <th>備考</th>
                <td><textarea name="notes" rows="5" cols="50"><?php echo $_POST['notes']; ?></textarea></td>
            </tr>
        </table>
    </form>
</div>

<?php
require_once("../footer.php");
?>
<?php endif; ?>