<?php
function make_random($length) {
    $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
    $r_str = null;
    for ($i = 0; $i < $length; $i++) {
        $r_str .= $str[rand(0, count($str) - 1)];
    }
    return $r_str;
}

function valid_engnum($item, $data) {
	
	if ($data == "") {
		return $item."を入力してください</br>";
	} elseif (!preg_match("/^[a-zA-Z0-9]+$/" , $data)) {
		return $item."を半角英数字で入力してください</br>";
	}
}

function valid_mail($data) {
	if ($data == "") {
		return "メールアドレスを入力してください</br>";
	} elseif(!preg_match('/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/iD', $data)){
		return '正しくないメールアドレスです</br>';
	}
}

function valid_pass($data1, $data2) {
	if ($data1 != $data2){
		return 'パスワード（確認）が異なります</br>';
	} else {
	    return '';	
    }
}

function valid_required($item, $data) {
	if ($data == "") {
		return $item."を入力してください</br>";
	} else {
	    return '';	
    }
}

function change_date($date) {
	$date2 = str_replace("-", "/", $date);
	return $date2;
}

function calc_age($birth) {
	if($birth) {
		$now = date("Ymd");
		$birthday = str_replace("-", "", $birth);
		return floor(($now-$birthday)/10000)."歳";
	} else {
		return "";
	}
}

function change_time($time) {
	if($time) {
		$jikan = str_replace(":", "時", $time)."分";
	} else {
		$jikan = "  時  分";
	}
	return $jikan;
}

function change_to_minutes($time) {
    if (empty($time) || strpos($time, ':') === false) return 0;

    list($h, $m) = array_pad(explode(':', $time), 2, 0);

    return ((int)$h * 60) + (int)$m;
}

function change_to_hm($time) {
    if (!is_numeric($time)) $time = 0;

    $time = (int)$time;
    return floor($time / 60) . 'h' . sprintf('%02d', $time % 60) . 'm';
}

function change_sum_to_hm($time) {
    if (!is_numeric($time)) $time = 0;

    $time = (int)$time;
    return sprintf('%03d', floor($time / 60)) . ':' . sprintf('%02d', $time % 60);
}

function calc_time($start, $end) {
    $start_m = change_to_minutes($start);
    $end_m = change_to_minutes($end);

    $diff = $end_m - $start_m;

    if ($diff < 0) $diff = 0;

    return floor($diff / 60) . 'h' . sprintf('%02d', $diff % 60) . 'm';
}

function disp_om($type, $basic, $om) {
	if($type) {
		return '時間額'.number_format(ceil($basic * 0.25)).'円';
	} else {
		return '月額'.number_format($om).'円';
	}
}

function disp_record($date) {
    $today = new DateTime('today');
    $base  = new DateTime($date);
    if($today >= (clone $base)->modify('first day of this month')->modify('-1 day')) {
        return true;
    } else {
        return false;
    }
}
?>