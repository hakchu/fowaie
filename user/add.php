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
		$disability = array();
		for($i = 0; $i < count($_POST['disability'] ?? []); $i++) {
			$disability[$i] = $_POST['disability'][$i];
		}
		$disability_en = str_replace("\\", "\\\\", json_encode($disability));
		$tool = array();
		for($i = 0; $i < count($_POST['tool'] ?? []); $i++) {
			$tool[$i] = $_POST['tool'][$i];
		}
		$tool_en = str_replace("\\", "\\\\", json_encode($tool));
		$enter_day = $_POST['enter_day'] ? "'".$_POST['enter_day']."'" : 'NULL';
		$retire_day = $_POST['retire_day'] ? "'".$_POST['retire_day']."'" : 'NULL';
		$register = $_POST['register'];
		$notes = $_POST['notes'];

		$emergency = array();
		for($i = 0; $i < count($_POST['emergency'] ?? []); $i++) {
			$emergency[$i][0] = $_POST['emergency'][$i][0];
			$emergency[$i][1] = $_POST['emergency'][$i][1];
			$emergency[$i][2] = intval($_POST['emergency'][$i][2]);
			$emergency[$i][3] = intval($_POST['emergency'][$i][3]);
			$emergency[$i][4] = $_POST['emergency'][$i][4];
			$emergency[$i][5] = $_POST['emergency'][$i][5];
			$emergency[$i][6] = $_POST['emergency'][$i][6];
		}
		$emergency_en = str_replace("\\", "\\\\", json_encode($emergency));

		$cooperation = array();
		for($i = 0; $i < count($_POST['cooperation'] ?? []); $i++) {
			$cooperation[$i][0] = $_POST['cooperation'][$i][0];
			$cooperation[$i][1] = $_POST['cooperation'][$i][1];
			$cooperation[$i][2] = $_POST['cooperation'][$i][2];
			$cooperation[$i][3] = $_POST['cooperation'][$i][3];
			$cooperation[$i][4] = $_POST['cooperation'][$i][4];
		}
		$cooperation_en = str_replace("\\", "\\\\", json_encode($cooperation));

		$medical = array();
		for($i = 0; $i < count($_POST['medical'] ?? []); $i++) {
			$medical[$i][0] = $_POST['medical'][$i][0];
			$medical[$i][1] = $_POST['medical'][$i][1];
			$medical[$i][2] = $_POST['medical'][$i][2];
			$medical[$i][3] = $_POST['medical'][$i][3];
			$medical[$i][4] = intval($_POST['medical'][$i][4]);
			$medical[$i][5] = $_POST['medical'][$i][5];
		}
		$medical_en = str_replace("\\", "\\\\", json_encode($medical));
		
		$mysqli->query("INSERT INTO user (name, kana, sex, birth, zip, address, tel, mobile, fax, mail, disability, tool, enter_day, retire_day, register, notes, emergency, cooperation, medical) VALUES ('$name', '$kana', $sex, $birth, '$zip', '$address', '$tel', '$mobile', '$fax', '$mail', '$disability_en', '$tool_en', $enter_day, $retire_day, $register, '$notes', '$emergency_en', '$cooperation_en', '$medical_en')");

        echo '<script>location.href="index.php";</script>';
	}
}

require_once("../header.php");
?>

<script src="<?php echo JS_ROOT ?>/user.js"></script>
<script src="https://ajaxzip3.github.io/ajaxzip3.js"></script>

<div class="main" id="user">
    <h1>利用者追加</h1>

    <p class="red"><?php echo $desc; ?></p>

    <form method="post" action="" autocomplete="off">
        <input type="submit" name="submit" value="追加" />

        <div class="block">    
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
                    <th>身体障がい</th>
                    <td>
                        手帳<input type="text" name="disability[0]" value="<?php echo $_POST['disability'][0]; ?>" class="w20" />種<input type="text" name="disability[1]" value="<?php echo $_POST['disability'][1]; ?>" class="w20" />級<br>
                        障がい名<input type="text" name="disability[2]" value="<?php echo $_POST['disability'][2]; ?>" class="w300" /><br>
                        療育手帳<input type="text" name="disability[3]" value="<?php echo $_POST['disability'][3]; ?>" class="w20" />（A or B）
                    </td>
                </tr>
                <tr>
                    <th>生活用具</th>
                    <td>
                        <?php
                            for($i=0; $i<count($tooljp); $i++) {
                                echo '<input type="hidden" name="tool['.$i.']" value="0" />';
                                echo '<input type="checkbox" name="tool['.$i.']" value="1"';
                                if($_POST['tool'][$i]) echo ' checked';
                                echo' />'.$tooljp[$i]. '　';
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>登録年月日</th>
                    <td><input type="text" name="enter_day" value="<?php echo $_POST['enter_day']; ?>" class="datepicker" placeholder="0000-00-00" /></td>
                </tr>
                <tr>
                    <th>退所年月日</th>
                    <td><input type="text" name="retire_day" value="<?php echo $_POST['retire_day']; ?>" class="datepicker" placeholder="0000-00-00" /></td>
                </tr>
                <tr>
                    <th>在所</th>
                    <td><input type="radio" name="register" value="1"<?php if(!isset($_POST['register']) || ($_POST['register']==1)) echo " checked"; ?> />在所　<input type="radio" name="register" value="0"<?php if(isset($_POST['register']) && ($_POST['register']==0)) echo " checked"; ?> />退所</td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td><textarea name="notes" rows="5" cols="50"><?php echo $_POST['notes']; ?></textarea></td>
                </tr>
            </table>
        </div>

		<div class="block">
            <p>緊急連絡先</p>
            <table class="form_table">
                <tr><th>名前</th><th>続柄</th><th>同居</th><th>連絡順</th><th>TEL</th><th>メールアドレス</th><th>備考</th></tr>
                <?php 
                    for($i=0; $i<5; $i++) {
                        echo '<tr>';
                        echo '<td><input type="text" name="emergency['.$i.'][0]" value="'.$_POST['emergency'][$i][0].'" class="w100" /></td>';
                        echo '<td><input type="text" name="emergency['.$i.'][1]" value="'.$_POST['emergency'][$i][1].'" class="w40" /></td>';
                        echo '<td><input type="hidden" name="emergency['.$i.'][2]" value="0" />';
                        echo '<input type="checkbox" name="emergency['.$i.'][2]" value="1"';
                        if($_POST['emergency'][$i][2]) echo ' checked';
                        echo' /></td>';
                        echo '<td><input type="tel" name="emergency['.$i.'][3]" value="'.$_POST['emergency'][$i][3].'" class="w20" /></td>';
                        echo '<td><input type="tel" name="emergency['.$i.'][4]" value="'.$_POST['emergency'][$i][4].'" class="w100" /></td>';
                        echo '<td><input type="mail" name="emergency['.$i.'][5]" value="'.$_POST['emergency'][$i][5].'" class="w200" /></td>';
                        echo '<td><textarea name="emergency['.$i.'][6]">'.$_POST['emergency'][$i][6].'</textarea></td>';
                        echo '</tr>';
                    }
                ?>
            </table>
			<br>
            <p>連携介護</p>
            <table class="form_table">
                <tr><th>介護機関名/サービス種類</th><th>TEL</th><th>利用頻度</th><th>備考</th></tr>
                <?php
                    for($i=0; $i<6; $i++) {
                        echo '<tr>';
                        echo '<td><input type="text" name="cooperation['.$i.'][0]" value="'.$_POST['cooperation'][$i][0].'" class="w200" /><br>/<input type="text" name="cooperation['.$i.'][1]" value="'.$_POST['cooperation'][$i][1].'" class="w200" /></td>';
                        echo '<td><input type="tel" name="cooperation['.$i.'][2]" value="'.$_POST['cooperation'][$i][2].'" class="w100" /></td>';
                        echo '<td><input type="text" name="cooperation['.$i.'][3]" value="'.$_POST['cooperation'][$i][3].'" class="w100" /></td>';
                        echo '<td><textarea name="cooperation['.$i.'][4]">'.$_POST['cooperation'][$i][4].'</textarea></td>';
                        echo '</tr>';
                    }
                ?>
            </table>
			<br>
            <p>医療機関</p>
            <table class="form_table">
                <tr><th>医療機関名/受診科目</th><th>TEL</th><th>受診頻度</th><th>薬</th><th>備考</th></tr>
                <?php
                    for($i=0; $i<6; $i++) {
                        echo '<tr>';
                        echo '<td><input type="text" name="medical['.$i.'][0]" value="'.$_POST['medical'][$i][0].'" class="w200" /><br>/<input type="text" name="medical['.$i.'][1]" value="'.$_POST['medical'][$i][1].'" class="w200" /></td>';
                        echo '<td><input type="tel" name="medical['.$i.'][2]" value="'.$_POST['medical'][$i][2].'" class="w100" /></td>';
                        echo '<td><input type="text" name="medical['.$i.'][3]" value="'.$_POST['medical'][$i][3].'" class="w100" /></td>';
                        echo '<td><input type="hidden" name="medical['.$i.'][4]" value="0" />';
                        echo '<input type="checkbox" name="medical['.$i.'][4]" value="1"';
                        if($_POST['medical'][$i][4]) echo ' checked';
                        echo' /></td>';
                        echo '<td><textarea name="medical['.$i.'][5]">'.$_POST['medical'][$i][5].'</textarea></td>';
                        echo '</tr>';
                    }
                ?>
            </table>
        </div>
    </form>
</div>

<?php
require_once("../footer.php");
?>
<?php endif; ?>