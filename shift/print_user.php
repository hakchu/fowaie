<?php
require_once("../connect.php");
require_once('../tcpdf/tcpdf.php');

if(!$a_role) {
    ob_start();
    $pdf = new tcpdf('P','mm','A4');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAutoPageBreak(false);
    $pdf->SetMargins(10, 10);

    $font = new TCPDF_FONTS();
    $myFont = $font->addTTFfont('../tcpdf/fonts/ARIALUNI.TTF');
    $pdf->SetFont($myFont, "", 10);
    $pdf->SetLineWidth(0.1);
    $pdf->SetFillColor(200);


    $h = 5;
    $w = 10;

    $ym = $_GET['month'];
    list($year, $month) = explode("-", $ym);
    $n_day = date('d', mktime(0, 0, 0, $month + 1, 0, $year));
    $user_id = $_GET['user_id'];

    if($user_id) {
        $pdf->AddPage();
        
        $res_user = $mysqli->query("SELECT name FROM user WHERE user_id=$user_id");
        while($user = $res_user->fetch_assoc()) {
            $user_name = $user['name'];
        }
        
        $pdf->SetFontSize(12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell($w*19, $h, "利用者別シフト表｜".$year."年".$month."月｜".$user_name."さん", 0, 1, 'L');
        
        $pdf->SetFontSize(10);
        
        $pdf->Cell($w*68, $h, "", 0, 1, 'L');
        
        $x0 = $pdf->GetX();
        $y0 = $pdf->GetY();
        
        $pdf->Cell($w*3, $h, "日", 1, 0, 'C', 1);
        $pdf->Cell($w, $h, "曜", 1, 0, 'C', 1);
        $pdf->Cell($w*5, $h, "時間", 1, 0, 'C', 1);
        $pdf->Cell($w*10, $h, "担当", 1, 1, 'C', 1);
        $y1 = $pdf->GetY();
        
        for($d=0; $d<$n_day; $d++) {
            $ymd = date('Y-m-d', mktime(0, 0, 0, $month, ($d+1), $year));
            $res_shift_user = $mysqli->query("SELECT * FROM shift_user WHERE date='$ymd' AND user_id=$user_id ORDER BY user_start ASC");
            $rows = $res_shift_user->num_rows;
            $row = 0;
            while($shift_user = $res_shift_user->fetch_assoc()) {
                if(!$row) {
                    $pdf->Cell($w*3, $h*$rows, date('m月d日', mktime(0, 0, 0, $month, ($d+1), $year)), 1, 0, 'C');
                    $pdf->Cell($w, $h*$rows, $weekjp[date('w', mktime(0, 0, 0, $month, ($d+1), $year))], 1, 0, 'C');
                    $x1 = $pdf->GetX();
                }
                if($shift_user['cancel']) $pdf->SetTextColor(128, 128, 128);
                $pdf->Cell($w*5, $h, $servjp[$shift_user['service']].$shift_user['user_start']."～".$shift_user['user_end']."(".calc_time($shift_user['user_start'], $shift_user['user_end']).")", 1, 0, 'C');
                $staff_name = array();
                $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE shift_user_id=".$shift_user['shift_user_id']);
                while($shift_staff = $res_shift_staff->fetch_assoc()) {
                    $res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE staff_id=".$shift_staff['staff_id_1']." OR staff_id=".$shift_staff['staff_id_2']);
                    while($staff = $res_staff->fetch_assoc()) {
                        $staff_name[] = $staff['name'];
                    }
                }
                $pdf->Cell($w*10, $h, implode( " / ", $staff_name), 1, 1, 'L');
                $y1 = $pdf->GetY();
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY($x1, $y1);
                $row++;
            }
            $pdf->SetXY($x0, $y1);
        }
    } else {
        $res_user = $mysqli->query("SELECT name, user_id FROM user WHERE EXISTS (SELECT * FROM shift_user WHERE user.user_id=shift_user.user_id AND shift_user.date LIKE '$ym%')");
        while($user = $res_user->fetch_assoc()) {
            $pdf->AddPage();
        
            $pdf->SetFontSize(12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell($w*19, $h, "利用者別シフト表｜".$year."年".$month."月｜".$user['name']."さん", 0, 1, 'L');
            
            $pdf->SetFontSize(10);
            
            $pdf->Cell($w*68, $h, "", 0, 1, 'L');
            
            $x0 = $pdf->GetX();
            $y0 = $pdf->GetY();
            
            $pdf->Cell($w*3, $h, "日", 1, 0, 'C', 1);
            $pdf->Cell($w, $h, "曜", 1, 0, 'C', 1);
            $pdf->Cell($w*5, $h, "時間", 1, 0, 'C', 1);
            $pdf->Cell($w*10, $h, "担当", 1, 1, 'C', 1);
            $y1 = $pdf->GetY();
            
            for($d=0; $d<$n_day; $d++) {
                $ymd = date('Y-m-d', mktime(0, 0, 0, $month, ($d+1), $year));
                $res_shift_user = $mysqli->query("SELECT * FROM shift_user WHERE date='$ymd' AND user_id=".$user['user_id']." ORDER BY user_start ASC");
                $rows = $res_shift_user->num_rows;
                $row = 0;
                while($shift_user = $res_shift_user->fetch_assoc()) {
                    if(!$row) {
                        $pdf->Cell($w*3, $h*$rows, date('m月d日', mktime(0, 0, 0, $month, ($d+1), $year)), 1, 0, 'C');
                        $pdf->Cell($w, $h*$rows, $weekjp[date('w', mktime(0, 0, 0, $month, ($d+1), $year))], 1, 0, 'C');
                        $x1 = $pdf->GetX();
                    }
                    if($shift_user['cancel']) $pdf->SetTextColor(128, 128, 128);
                    $pdf->Cell($w*5, $h, $servjp[$shift_user['service']].$shift_user['user_start']."～".$shift_user['user_end']."(".calc_time($shift_user['user_start'], $shift_user['user_end']).")", 'TRB', 0, 'C');
                    $staff_name = array();
                    $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE shift_user_id=".$shift_user['shift_user_id']);
                    while($shift_staff = $res_shift_staff->fetch_assoc()) {
                        $res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE staff_id=".$shift_staff['staff_id_1']." OR staff_id=".$shift_staff['staff_id_2']);
                        while($staff = $res_staff->fetch_assoc()) {
                            $staff_name[] = $staff['name'];
                        }
                    }
                    $pdf->Cell($w*10, $h, implode( " / ", $staff_name), 1, 1, 'L');
                    $y1 = $pdf->GetY();
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetXY($x1, $y1);
                    $row++;
                }
                $pdf->SetXY($x0, $y1);
            }
        }
    }
    ob_end_clean();
    $pdf->Output();
}
?>