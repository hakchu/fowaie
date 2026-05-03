<?php
require_once("../connect.php");
require_once("../header.php");

$shift_user_id = isset($_GET['shift_user_id']) ? (int)$_GET['shift_user_id'] : 0;
$backto = $_GET['backto'] ?? '';
$num = isset($_GET['num']) ? (int)$_GET['num'] : 0;

if (isset($_POST['save'])) {
    $empty = 1;

    foreach ($_POST as $k => $val) {
        if ($k != 'save') {
            if (is_array($val)) {
                foreach ($val as $val2) {
                    if ($val2 !== "") $empty = 0;
                }
            } else {
                if ($val !== "") $empty = 0;
            }
        }
    }

    if ($empty) {
        $mysqli->query("DELETE FROM record WHERE shift_user_id={$shift_user_id}");
    } else {
        $record_id = $_POST['record_id'] ?? null;
        $start = $_POST['start'] ?? '';
        $end = $_POST['end'] ?? '';
        $staff = $_POST['staff'] ?? '';
        $company = $_POST['company'] ?? '';

        $data = [
            'service_junbi' => $_POST['service_junbi'] ?? [],
            'taion' => $_POST['taion'] ?? '',
            'ketsuatsu' => $_POST['ketsuatsu'] ?? [],
            'myakuhaku' => $_POST['myakuhaku'] ?? '',
            'joutai' => $_POST['joutai'] ?? '',
            'furyou' => $_POST['furyou'] ?? '',
            'haisetsu' => $_POST['haisetsu'] ?? [],
            'inbu' => $_POST['inbu'] ?? '',
            'hainyou' => $_POST['hainyou'] ?? '',
            'haiben' => $_POST['haiben'] ?? '',
            'shokuji' => $_POST['shokuji'] ?? [],
            'shokujiryou' => $_POST['shokujiryou'] ?? '',
            'zanryou' => $_POST['zanryou'] ?? [],
            'suibun' => $_POST['suibun'] ?? '',
            'sp_chouri' => $_POST['sp_chouri'] ?? '',
            'hosei' => $_POST['hosei'] ?? [],
            'bubun' => $_POST['bubun'] ?? [],
            'seiyou' => $_POST['seiyou'] ?? [],
            'idou' => $_POST['idou'] ?? [],
            'idou_kaimono' => $_POST['idou_kaimono'] ?? '',
            'idou_tsuuin' => $_POST['idou_tsuuin'] ?? '',
            'kishou' => $_POST['kishou'] ?? [],
            'fukuyaku' => $_POST['fukuyaku'] ?? [],
            'iryou' => $_POST['iryou'] ?? [],
            'chuunyuu' => $_POST['chuunyuu'] ?? [],
            'jiritsu' => $_POST['jiritsu'] ?? [],
            'kaji' => $_POST['kaji'] ?? [],
            'jiritsu_sonota' => $_POST['jiritsu_sonota'] ?? '',
            'souji' => $_POST['souji'] ?? [],
            'souji_sonota' => $_POST['souji_sonota'] ?? '',
            'sentaku' => $_POST['sentaku'] ?? [],
            'sentaku_sonota' => $_POST['sentaku_sonota'] ?? '',
            'bed' => $_POST['bed'] ?? [],
            'irui' => $_POST['irui'] ?? [],
            'irui_junbi' => $_POST['irui_junbi'] ?? '',
            'chouri' => $_POST['chouri'] ?? [],
            'kondate' => $_POST['kondate'] ?? '',
            'kaimono' => $_POST['kaimono'] ?? [],
            'kaimono_daikou' => $_POST['kaimono_daikou'] ?? '',
            'money' => $_POST['money'] ?? [],
            'henkou' => $_POST['henkou'] ?? '',
            'yotei' => $_POST['yotei'] ?? [],
            'kasan' => $_POST['kasan'] ?? '',
            'service' => $_POST['service'] ?? [],
            'service_houmon' => $_POST['service_houmon'] ?? [],
            'service_joukou' => $_POST['service_joukou'] ?? [],
            'service_tsuuin' => $_POST['service_tsuuin'] ?? '',
            'service_juudo' => $_POST['service_juudo'] ?? [],
            'service_doukou' => $_POST['service_doukou'] ?? '',
            'service_idou' => $_POST['service_idou'] ?? '',
            'service_sonota' => $_POST['service_sonota'] ?? '',
            'koutsuuhi' => $_POST['koutsuuhi'] ?? [],
            'kakunin' => $_POST['kakunin'] ?? [],
        ];

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $message = $_POST['message'] ?? '';

        if (!empty($record_id)) {
            $record_id = (int)$record_id;
            $mysqli->query("UPDATE record SET 
                start='{$start}', 
                end='{$end}', 
                staff='{$staff}', 
                company='{$company}', 
                data='{$json}', 
                message='{$message}' 
                WHERE record_id={$record_id}");
        } else {
            $mysqli->query("INSERT INTO record 
                (shift_user_id, start, end, staff, company, data, message) 
                VALUES 
                ({$shift_user_id}, '{$start}', '{$end}', '{$staff}', '{$company}', '{$json}', '{$message}')");
        }
    }
}

$res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.shift_user_id=".$shift_user_id);
while($shift_user = $res_shift_user->fetch_assoc()) {
	$name = $shift_user['name'];
	$user_id = $shift_user['user_id'];
	$date = $shift_user['date'];
	$user_start = $shift_user['user_start'];
	$user_end = $shift_user['user_end'];
}

$data = [];
$res_record = $mysqli->query("SELECT * FROM record WHERE shift_user_id=$shift_user_id LIMIT $num, 1");
while($record = $res_record->fetch_assoc()) {
	$record_id = $record['record_id'];
	$start = $record['start'];
	$end = $record['end'];
	$staff = $record['staff'];
	$company = $record['company'];
	$data = json_decode($record['data'], true) ?? [];
	$message = $record['message'];
}

function v($data, $key, $default = '') {
    return htmlspecialchars($data[$key] ?? $default, ENT_QUOTES, 'UTF-8');
}
function va($data, $key, $index, $default = '') {
    return htmlspecialchars($data[$key][$index] ?? $default, ENT_QUOTES, 'UTF-8');
}
function c($data, $key, $val) {
    return (!empty($data[$key]) && in_array($val, $data[$key])) ? ' checked' : '';
}
function r($data, $key, $val) {
    return (isset($data[$key]) && $data[$key] == $val) ? ' checked' : '';
}

$staffs = array();
$res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE shift_user_id=".$shift_user_id);
while($shift_staff = $res_shift_staff->fetch_assoc()) {
	$res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE staff_id=".$shift_staff['staff_id_1']." OR staff_id=".$shift_staff['staff_id_2']);
	while($staff2 = $res_staff->fetch_assoc()) {
		array_push($staffs, $staff2['name']);
	}
}
?>

<script src="<?php echo JS_ROOT ?>/record.js"></script>

<div class="main record">
    <h1>サービス提供記録票</h1>

    <?php if(!$a_role || disp_record($date)): ?>
    <div id="delete_dialog" title="記録票の削除"></div>

    <div>
        <form method="post" action="" autocomplete="off">
            <?php
                $res_record = $mysqli->query("SELECT * FROM record WHERE shift_user_id=$shift_user_id");
            ?>

            <input type="hidden" name="record_id" id="record_id" value="<?php echo $record_id; ?>" />

            <div class="btn_area">
                <input type="submit" name="save" value="保存" />　
                <?php if($record_id): ?>
                    <input type="button" name="print" onclick="window.open('./print.php?shift_user_id=<?php echo $shift_user_id; ?>', '_blank')" value="印刷" />　
                    <input type="button" name="delete" value="削除" />　
                <?php endif; ?>
                <input type="button" name="prev" onclick="window.location.href = './edit.php?shift_user_id=<?php echo $shift_user_id; ?>&num=<?php echo ($num - 1); ?>&backto=<?php echo $backto; ?>'" value="＜"<?php if(!$num) echo ' disabled'; ?> /> 
                <input type="button" name="prev" onclick="window.location.href = './edit.php?shift_user_id=<?php echo $shift_user_id; ?>&num=<?php echo ($num + 1); ?>&backto=<?php echo $backto; ?>'" value="＞"<?php if($num >= ($res_record->num_rows - 1)) echo ' disabled'; ?> />　
                <?php if($backto): ?>
                    <input type="button" name="record_top" onclick="window.location.href = './user.php?year=<?php echo date('Y', strtotime($date)); ?>&month=<?php echo date('n', strtotime($date)); ?>&user_id=<?php echo $user_id; ?>'" value="記録票一覧" />
                <?php else: ?>
                    <input type="button" name="record_top" onclick="window.location.href = './?date=<?php echo $date; ?>'" value="記録票一覧" />
                <?php endif; ?>
            </div>

            <h2>
                ご利用者名：<?php echo $name; ?> 様　
                <?php if($detect->isMobile()): ?><br><?php endif; ?>
                ヘルパー名：<input type="text" name="staff" size="30" value="<?php echo (isset($staff) ? $staff : implode(' / ', $staffs)); ?>" />　
                <?php if($detect->isMobile()): ?><br><?php endif; ?>
                サービス提供会社：<input type="text" name="company" size="30" value="<?php echo (isset($company) ? $company : '訪問介護事業所フォワイエ'); ?>" />
            </h2>
            <table class="form_table" style="float: left;">
                <tr>
                    <th colspan="2">サービス準備・記録等</th>
                    <td>
                        <input type="checkbox" name="service_junbi[]" value="0"<?= c($data,'service_junbi',0) ?> />体温〔<input type="text" name="taion" size="3" value="<?= v($data,'taion') ?>" />℃〕&nbsp;
                        <input type="checkbox" name="service_junbi[]" value="1"<?= c($data,'service_junbi',1) ?> />血圧〔<input type="text" name="ketsuatsu[0]" size="3" value="<?= va($data,'ketsuatsu',0) ?>" />/<input type="text" name="ketsuatsu[1]" size="3" value="<?= va($data,'ketsuatsu',1) ?>" />〕&nbsp;
                        <input type="checkbox" name="service_junbi[]" value="2"<?= c($data,'service_junbi',2) ?> />脈拍〔<input type="text" name="myakuhaku" size="3" value="<?= v($data,'myakuhaku') ?>" />/分〕<br>
                        状態：<input type="radio" name="joutai" id="joutai_1" value="1"<?= r($data,'joutai',1) ?> />良好・<input type="radio" name="joutai" id="joutai_2" value="2"<?= r($data,'joutai',2) ?> />普通・<input type="radio" name="joutai" id="joutai_3" value="3"<?= r($data,'joutai',3) ?> />不良〔<input type="text" name="furyou" size="30" value="<?= v($data,'furyou') ?>" />〕
                    </td>
                </tr>

                <tr>
                    <th rowspan="8">身<br>体<br>介<br>護</th>
                    <th>排泄</th>
                    <td>
                        <input type="checkbox" name="haisetsu[]" value="0"<?= c($data,'haisetsu',0) ?> />ﾄｲﾚ介助&nbsp;
                        <input type="checkbox" name="haisetsu[]" value="1"<?= c($data,'haisetsu',1) ?> />Pﾄｲﾚ介助&nbsp;
                        <input type="checkbox" name="haisetsu[]" value="2"<?= c($data,'haisetsu',2) ?> />尿器介助&nbsp;
                        <input type="checkbox" name="haisetsu[]" value="3"<?= c($data,'haisetsu',3) ?> />陰部(<input type="radio" name="inbu" id="inbu_1" value="1"<?= r($data,'inbu',1) ?> />洗浄/<input type="radio" name="inbu" id="inbu_2" value="2"<?= r($data,'inbu',2) ?> />清拭)<br>
                        <input type="checkbox" name="haisetsu[]" value="4"<?= c($data,'haisetsu',4) ?> />ｵﾑﾂ交換&nbsp;
                        <input type="checkbox" name="haisetsu[]" value="5"<?= c($data,'haisetsu',5) ?> />ﾊﾟｯﾄ交換&nbsp;
                        <input type="checkbox" name="haisetsu[]" value="6"<?= c($data,'haisetsu',6) ?> />排尿・尿ﾊｷ(<input type="text" name="hainyou" size="5" value="<?= v($data,'hainyou') ?>" />)&nbsp;
                        <input type="checkbox" name="haisetsu[]" value="7"<?= c($data,'haisetsu',7) ?> />排便(<input type="radio" name="haiben" id="haiben_1" value="1"<?= r($data,'haiben',1) ?> />少・<input type="radio" name="haiben" id="haiben_2" value="2"<?= r($data,'haiben',2) ?> />中・<input type="radio" name="haiben" id="haiben_3" value="3"<?= r($data,'haiben',3) ?> />多)
                    </td>
                </tr>

                <tr>
                    <th>食事</th>
                    <td>
                        <input type="checkbox" name="shokuji[]" value="0"<?= c($data,'shokuji',0) ?> />全介助&nbsp;
                        <input type="checkbox" name="shokuji[]" value="1"<?= c($data,'shokuji',1) ?> />一部介助&nbsp;
                        <input type="checkbox" name="shokuji[]" value="2"<?= c($data,'shokuji',2) ?> />食事量(<input type="radio" name="shokujiryou" id="shokujiryou_1" value="1"<?= r($data,'shokujiryou',1) ?> />完食・<input type="radio" name="shokujiryou" id="shokujiryou_2" value="2"<?= r($data,'shokujiryou',2) ?> />残量<input type="text" name="zanryou[0]" size="3" value="<?= va($data,'zanryou',0) ?>" />/<input type="text" name="zanryou[1]" size="3" value="<?= va($data,'zanryou',1) ?>" />)<br>
                        <input type="checkbox" name="shokuji[]" value="3"<?= c($data,'shokuji',3) ?> />水分補給(<input type="text" name="suibun" size="3" value="<?= v($data,'suibun') ?>" />cc)&nbsp;
                        <input type="checkbox" name="shokuji[]" value="4"<?= c($data,'shokuji',4) ?> />特段の調理(<input type="text" name="sp_chouri" size="20" value="<?= v($data,'sp_chouri') ?>" />)&nbsp;
                    </td>
                </tr>

                <tr>
                    <th>保清・整容</th>
                    <td>
                        <input type="checkbox" name="hosei[]" value="0"<?= c($data,'hosei',0) ?> />入浴介助&nbsp;
                        <input type="checkbox" name="hosei[]" value="1"<?= c($data,'hosei',1) ?> />ｼｬﾜｰ浴&nbsp;
                        <input type="checkbox" name="hosei[]" value="2"<?= c($data,'hosei',2) ?> />手浴&nbsp;
                        <input type="checkbox" name="hosei[]" value="3"<?= c($data,'hosei',3) ?> />足浴&nbsp;
                        <input type="checkbox" name="hosei[]" value="4"<?= c($data,'hosei',4) ?> />洗髪<br>

                        <input type="checkbox" name="hosei[]" value="5"<?= c($data,'hosei',5) ?> />全身清拭&nbsp;

                        <input type="checkbox" name="hosei[]" value="6"<?= c($data,'hosei',6) ?> />部分清拭(
                        <input type="checkbox" name="bubun[]" value="0"<?= c($data,'bubun',0) ?> />顔・
                        <input type="checkbox" name="bubun[]" value="1"<?= c($data,'bubun',1) ?> />手・
                        <input type="checkbox" name="bubun[]" value="2"<?= c($data,'bubun',2) ?> />足・
                        <input type="checkbox" name="bubun[]" value="3"<?= c($data,'bubun',3) ?> />上半身)&nbsp;

                        <input type="checkbox" name="hosei[]" value="7"<?= c($data,'hosei',7) ?> />洗面<br>

                        <input type="checkbox" name="hosei[]" value="8"<?= c($data,'hosei',8) ?> />口腔ｹｱ&nbsp;
                        <input type="checkbox" name="hosei[]" value="9"<?= c($data,'hosei',9) ?> />義歯洗浄&nbsp;
                        <input type="checkbox" name="hosei[]" value="10"<?= c($data,'hosei',10) ?> />更衣介助&nbsp;

                        <input type="checkbox" name="hosei[]" value="11"<?= c($data,'hosei',11) ?> />ｼｰﾂ交換<br>

                        <input type="checkbox" name="hosei[]" value="12"<?= c($data,'hosei',12) ?> />整容(
                        <input type="checkbox" name="seiyou[]" value="0"<?= c($data,'seiyou',0) ?> />髪・
                        <input type="checkbox" name="seiyou[]" value="1"<?= c($data,'seiyou',1) ?> />髭・
                        <input type="checkbox" name="seiyou[]" value="2"<?= c($data,'seiyou',2) ?> />耳・
                        <input type="checkbox" name="seiyou[]" value="3"<?= c($data,'seiyou',3) ?> />爪切り・
                        <input type="checkbox" name="seiyou[]" value="4"<?= c($data,'seiyou',4) ?> />化粧)
                    </td>
                </tr>

                <tr>
                    <th>移動・外出</th>
                    <td>
                        <input type="checkbox" name="idou[]" value="0"<?= c($data,'idou',0) ?> />体位変換&nbsp;
                        <input type="checkbox" name="idou[]" value="1"<?= c($data,'idou',1) ?> />移乗&nbsp;
                        <input type="checkbox" name="idou[]" value="2"<?= c($data,'idou',2) ?> />移動&nbsp;
                        <input type="checkbox" name="idou[]" value="3"<?= c($data,'idou',3) ?> />車椅子介助&nbsp;
                        <input type="checkbox" name="idou[]" value="4"<?= c($data,'idou',4) ?> />歩行介助<br>

                        <input type="checkbox" name="idou[]" value="5"<?= c($data,'idou',5) ?> />外出準備介助&nbsp;
                        <input type="checkbox" name="idou[]" value="6"<?= c($data,'idou',6) ?> />帰宅受入介助<br>

                        <input type="checkbox" name="idou[]" value="7"<?= c($data,'idou',7) ?> />買物同行(
                        <input type="text" name="idou_kaimono" size="15" value="<?= v($data,'idou_kaimono') ?>" />)&nbsp;

                        <input type="checkbox" name="idou[]" value="8"<?= c($data,'idou',8) ?> />通院同行(
                        <input type="text" name="idou_tsuuin" size="15" value="<?= v($data,'idou_tsuuin') ?>" />)
                    </td>
                </tr>

                <tr>
                    <th>起床・就寝</th>
                    <td>
                        <input type="checkbox" name="kishou[]" value="0"<?= c($data,'kishou',0) ?> />起床介助&nbsp;
                        <input type="checkbox" name="kishou[]" value="1"<?= c($data,'kishou',1) ?> />就寝介助&nbsp;
                        <input type="checkbox" name="kishou[]" value="2"<?= c($data,'kishou',2) ?> />苦痛軽減のためのさすり
                    </td>
                </tr>

                <tr>
                    <th>服薬</th>
                    <td>
                        <input type="checkbox" name="fukuyaku[]" value="0"<?= c($data,'fukuyaku',0) ?> />服薬介助&nbsp;
                        <input type="checkbox" name="fukuyaku[]" value="1"<?= c($data,'fukuyaku',1) ?> />服薬確認&nbsp;
                        <input type="checkbox" name="fukuyaku[]" value="2"<?= c($data,'fukuyaku',2) ?> />湿布&nbsp;
                        <input type="checkbox" name="fukuyaku[]" value="3"<?= c($data,'fukuyaku',3) ?> />軟膏&nbsp;
                        <input type="checkbox" name="fukuyaku[]" value="4"<?= c($data,'fukuyaku',4) ?> />点眼&nbsp;
                        <input type="checkbox" name="fukuyaku[]" value="5"<?= c($data,'fukuyaku',5) ?> />点鼻
                    </td>
                </tr>

                <tr>
                    <th>医療行為</th>
                    <td>
                        <input type="checkbox" name="iryou[]" value="0"<?= c($data,'iryou',0) ?> />痰の吸引&nbsp;
                        <input type="checkbox" name="iryou[]" value="1"<?= c($data,'iryou',1) ?> />浣腸&nbsp;
                        <input type="checkbox" name="iryou[]" value="2"<?= c($data,'iryou',2) ?> />準備・後片付け&nbsp;

                        <input type="checkbox" name="iryou[]" value="3"<?= c($data,'iryou',3) ?> />注入(
                        <input type="checkbox" name="chuunyuu[]" value="0"<?= c($data,'chuunyuu',0) ?> />経鼻・
                        <input type="checkbox" name="chuunyuu[]" value="1"<?= c($data,'chuunyuu',1) ?> />胃・
                        <input type="checkbox" name="chuunyuu[]" value="2"<?= c($data,'chuunyuu',2) ?> />腸)&nbsp;

                        <input type="checkbox" name="iryou[]" value="4"<?= c($data,'iryou',4) ?> />水分補給
                    </td>
                </tr>

                <tr>
                    <th>自立支援</th>
                    <td>
                        <input type="checkbox" name="jiritsu[]" value="0"<?= c($data,'jiritsu',0) ?> />共に行う家事(
                        <input type="checkbox" name="kaji[]" value="0"<?= c($data,'kaji',0) ?> />調理・
                        <input type="checkbox" name="kaji[]" value="1"<?= c($data,'kaji',1) ?> />掃除・
                        <input type="checkbox" name="kaji[]" value="2"<?= c($data,'kaji',2) ?> />洗濯・
                        <input type="checkbox" name="kaji[]" value="3"<?= c($data,'kaji',3) ?> />その他)<br>

                        <input type="checkbox" name="jiritsu[]" value="1"<?= c($data,'jiritsu',1) ?> />その他(
                        <input type="text" name="jiritsu_sonota" size="30" value="<?= v($data,'jiritsu_sonota') ?>" />)
                    </td>
                </tr>

                <tr>
                    <th rowspan="6">生<br>活<br>・<br>家<br>事<br>援<br>助</th>
                    <th>掃除</th>
                    <td>
                        <input type="checkbox" name="souji[]" value="0"<?= c($data,'souji',0) ?> />居間&nbsp;
                        <input type="checkbox" name="souji[]" value="1"<?= c($data,'souji',1) ?> />寝室&nbsp;
                        <input type="checkbox" name="souji[]" value="2"<?= c($data,'souji',2) ?> />台所&nbsp;
                        <input type="checkbox" name="souji[]" value="3"<?= c($data,'souji',3) ?> />玄関&nbsp;
                        <input type="checkbox" name="souji[]" value="4"<?= c($data,'souji',4) ?> />浴室&nbsp;
                        <input type="checkbox" name="souji[]" value="5"<?= c($data,'souji',5) ?> />ﾄｲﾚ&nbsp;
                        <input type="checkbox" name="souji[]" value="6"<?= c($data,'souji',6) ?> />Pﾄｲﾚ<br>
                        <input type="checkbox" name="souji[]" value="7"<?= c($data,'souji',7) ?> />廊下&nbsp;
                        <input type="checkbox" name="souji[]" value="8"<?= c($data,'souji',8) ?> />ｺﾞﾐ出し&nbsp;
                        <input type="checkbox" name="souji[]" value="9"<?= c($data,'souji',9) ?> />その他(
                        <input type="text" name="souji_sonota" size="20" value="<?= v($data,'souji_sonota') ?>" />)
                    </td>
                </tr>

                <tr>
                    <th>洗濯</th>
                    <td>
                        <input type="checkbox" name="sentaku[]" value="0"<?= c($data,'sentaku',0) ?> />洗濯&nbsp;
                        <input type="checkbox" name="sentaku[]" value="1"<?= c($data,'sentaku',1) ?> />干し&nbsp;
                        <input type="checkbox" name="sentaku[]" value="2"<?= c($data,'sentaku',2) ?> />取込&nbsp;
                        <input type="checkbox" name="sentaku[]" value="3"<?= c($data,'sentaku',3) ?> />たたみ&nbsp;
                        <input type="checkbox" name="sentaku[]" value="4"<?= c($data,'sentaku',4) ?> />収納&nbsp;
                        <input type="checkbox" name="sentaku[]" value="5"<?= c($data,'sentaku',5) ?> />ｱｲﾛﾝ<br>
                        <input type="checkbox" name="sentaku[]" value="6"<?= c($data,'sentaku',6) ?> />その他(
                        <input type="text" name="sentaku_sonota" size="30" value="<?= v($data,'sentaku_sonota') ?>" />)
                    </td>
                </tr>

                <tr>
                    <th>ベッドメイク</th>
                    <td>
                        <input type="checkbox" name="bed[]" value="0"<?= c($data,'bed',0) ?> />ｼｰﾂ交換&nbsp;
                        <input type="checkbox" name="bed[]" value="1"<?= c($data,'bed',1) ?> />布団ｶﾊﾞｰ交換&nbsp;
                        <input type="checkbox" name="bed[]" value="2"<?= c($data,'bed',2) ?> />布団干し・取込
                    </td>
                </tr>

                <tr>
                    <th>衣類</th>
                    <td>
                        <input type="checkbox" name="irui[]" value="0"<?= c($data,'irui',0) ?> />衣類の整理&nbsp;
                        <input type="checkbox" name="irui[]" value="1"<?= c($data,'irui',1) ?> />被服の補修&nbsp;
                        <input type="checkbox" name="irui[]" value="2"<?= c($data,'irui',2) ?> />ﾃﾞｲの準備(
                        <input type="text" name="irui_junbi" value="<?= v($data,'irui_junbi') ?>" />)
                    </td>
                </tr>

                <tr>
                    <th>調理</th>
                    <td>
                        <input type="checkbox" name="chouri[]" value="0"<?= c($data,'chouri',0) ?> />一般的な調理 献立〔
                        <input type="text" name="kondate" value="<?= v($data,'kondate') ?>" />〕<br>
                        <input type="checkbox" name="chouri[]" value="1"<?= c($data,'chouri',1) ?> />配膳・下膳&nbsp;
                        <input type="checkbox" name="chouri[]" value="2"<?= c($data,'chouri',2) ?> />後片付け
                    </td>
                </tr>

                <tr>
                    <th>買物</th>
                    <td>
                        <input type="checkbox" name="kaimono[]" value="0"<?= c($data,'kaimono',0) ?> />日常品の買物&nbsp;
                        <input type="checkbox" name="kaimono[]" value="1"<?= c($data,'kaimono',1) ?> />薬の受取り&nbsp;
                        <input type="checkbox" name="kaimono[]" value="2"<?= c($data,'kaimono',2) ?> />代行(
                        <input type="text" name="kaimono_daikou" value="<?= v($data,'kaimono_daikou') ?>" />)
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        預り金(<input type="number" name="money[0]" value="<?= va($data,'money',0) ?>" />円)
                        - 使用金(<input type="number" name="money[1]" value="<?= va($data,'money',1) ?>" />円)
                        = おつり(<input type="number" name="money[2]" value="<?= va($data,'money',2) ?>" />円)
                    </td>
                </tr>
            </table>
            <table class="form_table" style="margin: 0;">
                <tr>
                    <th colspan="3">サービス提供日時</th>
                </tr>

                <tr>
                    <td colspan="3" class="center">
                        <?= date('Y年m月d日', strtotime($date)) ?>
                        (<?= $weekjp[date('w', strtotime($date))] ?? '' ?>)
                    </td>
                </tr>

                <tr>
                    <td colspan="3" class="center">
                        <input type="tel" class="time" name="start" value="<?= $start ?? $user_start ?>" />
                        ～<input type="tel" class="time" name="end" value="<?= $end ?? $user_end ?>" />
                        （<?= (isset($start,$end) ? calc_time($start,$end) : calc_time($user_start,$user_end)) ?>）

                        <input type="button" name="add"
                            onclick="location.href='./edit.php?shift_user_id=<?= $shift_user_id ?>&num=<?= $res_record->num_rows ?>'"
                            value="追加"/>
                    </td>
                </tr>

                <tr>
                    <th colspan="2">予定変更</th>
                    <td>
                        <input type="radio" name="henkou" value="1"<?= r($data,'henkou',1) ?> />有・
                        <input type="radio" name="henkou" value="2"<?= r($data,'henkou',2) ?> />無&nbsp;

                        予定(
                        <input type="tel" class="time" name="yotei[0]" value="<?= va($data,'yotei',0) ?>" />～
                        <input type="tel" class="time" name="yotei[1]" value="<?= va($data,'yotei',1) ?>" />)
                    </td>
                </tr>

                <tr>
                    <td colspan="3" class="center">
                        <input type="radio" name="kasan" value="1"<?= r($data,'kasan',1) ?> />初回加算&nbsp;
                        <input type="radio" name="kasan" value="2"<?= r($data,'kasan',2) ?> />緊急時加算
                    </td>
                </tr>

                <tr>
                    <th rowspan="4">サ<br>|<br>ビ<br>ス<br>種<br>類</th>
                    <th>介護</th>
                    <td>
                        <input type="checkbox" name="service[]" value="0"<?= c($data,'service',0) ?> />訪問介護
                        身体〔<input type="text" name="service_houmon[0]" size="5" value="<?= va($data,'service_houmon',0) ?>" />〕
                        生活〔<input type="text" name="service_houmon[1]" size="5" value="<?= va($data,'service_houmon',1) ?>" />〕<br>

                        <input type="checkbox" name="service[]" value="1"<?= c($data,'service',1) ?> />介護予防&nbsp;

                        <input type="checkbox" name="service[]" value="2"<?= c($data,'service',2) ?> />通院等乗降介助(
                        <input type="checkbox" name="service_joukou[]" value="0"<?= c($data,'service_joukou',0) ?> />往・
                        <input type="checkbox" name="service_joukou[]" value="1"<?= c($data,'service_joukou',1) ?> />復)
                    </td>
                </tr>

                <tr>
                    <th rowspan="2">障害福祉・地域</th>
                    <td>
                        <input type="checkbox" name="service[]" value="3"<?= c($data,'service',3) ?> />身体介護&nbsp;
                        <input type="checkbox" name="service[]" value="4"<?= c($data,'service',4) ?> />家事援助&nbsp;
                        <input type="checkbox" name="service[]" value="5"<?= c($data,'service',5) ?> />乗降介助<br>

                        <input type="checkbox" name="service[]" value="6"<?= c($data,'service',6) ?> />通院介助(
                        <input type="radio" name="service_tsuuin" value="1"<?= r($data,'service_tsuuin',1) ?> />身体有&nbsp;
                        <input type="radio" name="service_tsuuin" value="2"<?= r($data,'service_tsuuin',2) ?> />身体無)<br>

                        <input type="checkbox" name="service[]" value="7"<?= c($data,'service',7) ?> />重度訪問介護〔
                        <input type="hidden" name="service_juudo[0]" value="" />
                        <input type="checkbox" name="service_juudo[0]" value="1"<?= (va($data,'service_juudo',0)==1?' checked':'') ?> />移動加算有(
                        <input type="text" name="service_juudo[1]" size="5" value="<?= va($data,'service_juudo',1) ?>" />分)〕<br>

                        <input type="checkbox" name="service[]" value="8"<?= c($data,'service',8) ?> />行動援護<br>

                        <input type="checkbox" name="service[]" value="9"<?= c($data,'service',9) ?> />同行援護(
                        <input type="radio" name="service_doukou" value="1"<?= r($data,'service_doukou',1) ?> />身体有&nbsp;
                        <input type="radio" name="service_doukou" value="2"<?= r($data,'service_doukou',2) ?> />身体無)
                    </td>
                </tr>

                <tr>
                    <td>
                        <input type="checkbox" name="service[]" value="10"<?= c($data,'service',10) ?> />移動支援〔
                        <input type="checkbox" name="service_idou" value="1"<?= r($data,'service_idou',1) ?> />伴う〕
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <input type="checkbox" name="service[]" value="11"<?= c($data,'service',11) ?> />自費・その他〔
                        <input type="text" name="service_sonota" size="20" value="<?= v($data,'service_sonota') ?>" />〕
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        移動距離〔<input type="text" name="koutsuuhi[0]" size="5" value="<?= va($data,'koutsuuhi',0) ?>" />km〕<br>

                        交通費・送迎〔<input type="text" name="koutsuuhi[1]" size="5" value="<?= va($data,'koutsuuhi',1) ?>" />円〕
                        請求(
                        <input type="hidden" name="koutsuuhi[2]" value="" />
                        <input type="radio" name="koutsuuhi[2]" value="1"<?= (va($data,'koutsuuhi',2)==1?' checked':'') ?> />済・
                        <input type="radio" name="koutsuuhi[2]" value="2"<?= (va($data,'koutsuuhi',2)==2?' checked':'') ?> />未)<br>

                        行先〔<input type="text" name="koutsuuhi[3]" size="30" value="<?= va($data,'koutsuuhi',3) ?>" />〕
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        記録・連絡事項<br>
                        <textarea name="message" rows="5"><?= $message ?></textarea>
                    </td>
                </tr>

                <tr>
                    <th colspan="2">確認</th>
                    <td>
                        <input type="checkbox" name="kakunin[]" value="0"<?= c($data,'kakunin',0) ?> />火元&nbsp;
                        <input type="checkbox" name="kakunin[]" value="1"<?= c($data,'kakunin',1) ?> />電気&nbsp;
                        <input type="checkbox" name="kakunin[]" value="2"<?= c($data,'kakunin',2) ?> />水道&nbsp;
                        <input type="checkbox" name="kakunin[]" value="3"<?= c($data,'kakunin',3) ?> />戸締
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php endif; ?>
</div>

<?php
require_once("../footer.php");
?>