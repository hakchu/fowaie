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
    $staff_id = $_GET['staff_id'];

    if($staff_id) {
        $pdf->AddPage();

        $res_staff = $mysqli->query("SELECT name FROM staff WHERE staff_id=$staff_id");
        while($staff = $res_staff->fetch_assoc()) {
            $staff_name = $staff['name'];
        }
        
        $pdf->SetFontSize(12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell($w*19, $h, "スタッフ別シフト表｜".$year."年".$month."月｜".$staff_name, 0, 1, 'L');
        
        $pdf->SetFontSize(10);
        
        $pdf->Cell($w*68, $h, "", 0, 1, 'L');
        
        $x0 = $pdf->GetX();
        $y0 = $pdf->GetY();
        
        $pdf->Cell($w*3, $h, "日", 1, 0, 'C', 1);
        $pdf->Cell($w, $h, "曜", 1, 0, 'C', 1);
        $pdf->Cell($w*5, $h, "時間", 1, 0, 'C', 1);
        $pdf->Cell($w*7, $h, "利用者/その他予定", 1, 0, 'C', 1);
        $pdf->Cell($w*3, $h, "同行スタッフ", 1, 1, 'C', 1);
        $y1 = $pdf->GetY();
        
        for($d=0; $d<$n_day; $d++) {
            $ymd = date('Y-m-d', mktime(0, 0, 0, $month, ($d+1), $year));
            $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE date='$ymd' AND (staff_id_1=$staff_id OR staff_id_2=$staff_id) ORDER BY staff_start ASC");
            $rows = $res_shift_staff->num_rows;
            $row = 0;
            while($shift_staff = $res_shift_staff->fetch_assoc()) {
                if(!$row) {
                    $pdf->Cell($w*3, $h*$rows, date('m月d日', mktime(0, 0, 0, $month, ($d+1), $year)), 1, 0, 'C');
                    $pdf->Cell($w, $h*$rows, $weekjp[date('w', mktime(0, 0, 0, $month, ($d+1), $year))], 1, 0, 'C');
                    $x1 = $pdf->GetX();
                }
                if($shift_staff['shift_user_id']) {
                    if($shift_staff['cancel']) $pdf->SetTextColor(128, 128, 128);
                    $pdf->Cell($w*5, $h, $shift_staff['staff_start']."～".$shift_staff['staff_end']."(".calc_time($shift_staff['staff_start'], $shift_staff['staff_end']).")", 1, 0, 'C');
                    $res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.shift_user_id=".$shift_staff['shift_user_id']);
                    while($shift_user = $res_shift_user->fetch_assoc()) {
                        $pdf->Cell($w*7, $h, $servjp[$shift_user['service']].$shift_user['name']."さん", 1, 0, 'L');
                    }
                    if($staff_id == $shift_staff['staff_id_1']) {
                        $accompany = $shift_staff['staff_id_2'];
                    } elseif($staff_id == $shift_staff['staff_id_2']) {
                        $accompany = $shift_staff['staff_id_1'];
                    } else {
                        $accompany = 0;
                    }
                    if($accompany) {
                        $res_staff = $mysqli->query("SELECT name FROM staff WHERE staff_id=$accompany");
                        while($staff = $res_staff->fetch_assoc()) {
                            $pdf->Cell($w*3, $h, $staff['name'], 1, 1, 'L');
                        }
                    } else {
                        $pdf->Cell($w*3, $h, "", 1, 1, 'L');
                    }
                } else {
                    $pdf->Cell($w*5, $h, $shift_staff['staff_start']."～".$shift_staff['staff_end']."(".calc_time($shift_staff['staff_start'], $shift_staff['staff_end']).")", 1, 0, 'C');
                    $pdf->Cell($w*7, $h, "[他]".$shift_staff['other'], 1, 0, 'L');
                    $pdf->Cell($w*3, $h, "", 1, 1, 'L');
                }
                $y1 = $pdf->GetY();
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY($x1, $y1);
                $row++;
            }
            $pdf->SetXY($x0, $y1);
        }
    } else {
        $res_staff = $mysqli->query("SELECT name, staff_id FROM staff WHERE EXISTS (SELECT * FROM shift_staff WHERE (staff.staff_id=shift_staff.staff_id_1 OR staff.staff_id=shift_staff.staff_id_2) AND shift_staff.date LIKE '$ym%') ORDER BY display ASC");
        while($staff = $res_staff->fetch_assoc()) {
            $pdf->AddPage();
        
            $pdf->SetFontSize(12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell($w*19, $h, "スタッフ別シフト表｜".$year."年".$month."月｜".$staff['name'], 0, 1, 'L');
            
            $pdf->SetFontSize(10);
            
            $pdf->Cell($w*68, $h, "", 0, 1, 'L');
            
            $x0 = $pdf->GetX();
            $y0 = $pdf->GetY();
            
            $pdf->Cell($w*3, $h, "日", 1, 0, 'C', 1);
            $pdf->Cell($w, $h, "曜", 1, 0, 'C', 1);
            $pdf->Cell($w*5, $h, "時間", 1, 0, 'C', 1);
            $pdf->Cell($w*7, $h, "利用者/その他予定", 1, 0, 'C', 1);
            $pdf->Cell($w*3, $h, "同行スタッフ", 1, 1, 'C', 1);
            $y1 = $pdf->GetY();
            
            for($d=0; $d<$n_day; $d++) {
                $ymd = date('Y-m-d', mktime(0, 0, 0, $month, ($d+1), $year));
                $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE date='$ymd' AND (staff_id_1=".$staff['staff_id']." OR staff_id_2=".$staff['staff_id'].") ORDER BY staff_start ASC");
                $rows = $res_shift_staff->num_rows;
                $row = 0;
                while($shift_staff = $res_shift_staff->fetch_assoc()) {
                    if(!$row) {
                        $pdf->Cell($w*3, $h*$rows, date('m月d日', mktime(0, 0, 0, $month, ($d+1), $year)), 1, 0, 'C');
                        $pdf->Cell($w, $h*$rows, $weekjp[date('w', mktime(0, 0, 0, $month, ($d+1), $year))], 1, 0, 'C');
                        $x1 = $pdf->GetX();
                    }
                    if($shift_staff['shift_user_id']) {
                        if($shift_staff['cancel']) $pdf->SetTextColor(128, 128, 128);
                        $pdf->Cell($w*5, $h, $shift_staff['staff_start']."～".$shift_staff['staff_end']."(".calc_time($shift_staff['staff_start'], $shift_staff['staff_end']).")", 1, 0, 'C');
                        $res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.shift_user_id=".$shift_staff['shift_user_id']);
                        while($shift_user = $res_shift_user->fetch_assoc()) {
                            $pdf->Cell($w*7, $h, $servjp[$shift_user['service']].$shift_user['name']."さん", 1, 0, 'L');
                        }
                        if($staff['staff_id'] == $shift_staff['staff_id_1']) {
                            $accompany = $shift_staff['staff_id_2'];
                        } elseif($staff['staff_id'] == $shift_staff['staff_id_2']) {
                            $accompany = $shift_staff['staff_id_1'];
                        } else {
                            $accompany = 0;
                        }
                        if($accompany) {
                            $res_staff2 = $mysqli->query("SELECT name FROM staff WHERE staff_id=$accompany");
                            while($staff2 = $res_staff2->fetch_assoc()) {
                                $pdf->Cell($w*3, $h, $staff2['name'], 1, 1, 'L');
                            }
                        } else {
                            $pdf->Cell($w*3, $h, "", 1, 1, 'L');
                        }
                    } else {
                        $pdf->Cell($w*5, $h, $shift_staff['staff_start']."～".$shift_staff['staff_end']."(".calc_time($shift_staff['staff_start'], $shift_staff['staff_end']).")", 1, 0, 'C');
                        $pdf->Cell($w*7, $h, "[他]".$shift_staff['other'], 1, 0, 'L');
                        $pdf->Cell($w*3, $h, "", 1, 1, 'L');
                    }
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