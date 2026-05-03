<?php
require_once("../connect.php");
?>

<?php if(!$a_role): ?>
<?php
if(isset($_POST['submit'])) {
    $desc = '';
    $desc .= valid_required("事務局名", $_POST['name']);
    $desc .= valid_required("全権管理者パスワード", $_POST['password0']);
    $desc .= valid_required("記録票管理者パスワード", $_POST['password1']);
    
    if(!$desc) {
        $name = $_POST['name'];
        $zip = $_POST['zip'];
        $address = $_POST['address'];
        $tel = $_POST['tel'];
        $fax = $_POST['fax'];
        $mail = $_POST['mail'];
        $password0 = $_POST['password0'];
        $password1 = $_POST['password1'];
        
        $mysqli->query("UPDATE company SET name='$name', zip='$zip', address='$address', tel='$tel', fax='$fax', mail='$mail' WHERE company_id=1");
        $mysqli->query("UPDATE account SET password='$password0' WHERE role=0");
        $mysqli->query("UPDATE account SET password='$password1' WHERE role=1");

        echo '<script>location.href="index.php";</script>';
    }
}

$res_company = $mysqli->query("SELECT * FROM company WHERE company_id=1");
while($company = $res_company->fetch_assoc()) {
    $name = $company['name'];
    $zip = $company['zip'];
    $address = $company['address'];
    $tel = $company['tel'];
    $fax = $company['fax'];
    $mail = $company['mail'];
}

$res_account = $mysqli->query("SELECT * FROM account WHERE role=0");
while($account = $res_account->fetch_assoc()) {
    $password0 = $account['password'];
}

$res_account = $mysqli->query("SELECT * FROM account WHERE role=1");
while($account = $res_account->fetch_assoc()) {
    $password1 = $account['password'];
}

require_once("../header.php");
?>

<script src="https://ajaxzip3.github.io/ajaxzip3.js"></script>

<div class="main" id="index">
	<h1>システム管理</h1>
    
    <form method="post" action="">
        <div class="block">
            <p class="red"><?php echo $desc; ?></p>
            <table class="form_table">
                <tr>
                    <th>事務局名<span class="red">※</span></th>
                    <td><input type="text" name="name" size="50" value="<?php echo $name; ?>" /></td>
                </tr>
                <tr>
                    <th>所在地</th>
                    <td>
                        〒<input type="text" name="zip" size="7" value="<?php echo $zip; ?>" onKeyUp="AjaxZip3.zip2addr(this,'','address','address');" /><br>
                        <input type="text" name="address" size="50" value="<?php echo $address; ?>" />
                    </td>
                </tr>
                <tr>
                    <th>TEL</th>
                    <td><input type="text" name="tel" value="<?php echo $tel; ?>" /></td>
                </tr>
                <tr>
                    <th>FAX</th>
                    <td><input type="text" name="fax" value="<?php echo $fax; ?>" /></td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td><input type="mail" name="mail" value="<?php echo $mail; ?>" /></td>
                </tr>
                <tr>
                    <th>全権管理者パスワード<span class="red">※</span></th>
                    <td><input type="password" name="password0" value="<?php echo $password0; ?>" /></td>
                </tr>
                <tr>
                    <th>記録票管理者パスワード<span class="red">※</span></th>
                    <td><input type="password" name="password1" value="<?php echo $password1; ?>" /></td>
                </tr>
            </table>
        </div>

        <input type="submit" name="submit" value="更新" />
    </form>
</div>

<?php
require_once("../footer.php");
?>
<?php endif; ?>