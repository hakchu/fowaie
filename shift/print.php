<?php
require_once("../connect.php");
require_once('../tcpdf/tcpdf.php');

if(!$a_role) {
    ob_start();
    $pdf = new tcpdf('L','mm','A4');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAutoPageBreak(false);
    $pdf->SetMargins(6, 6);

    $font = new TCPDF_FONTS();
    $myFont = $font->addTTFfont('../tcpdf/fonts/ARIALUNI.TTF');
    $pdf->SetFont($myFont, "", 10);
    $pdf->SetLineWidth(0.1);
    $pdf->SetFillColor(200);

    $pdf->AddPage();

    $date = $_GET['date'];
    $print = $_GET['print'];
    list($year, $month, $day) = explode("-", $date);

    $h = 3;
    $w = 4;

    $pdf->SetFontSize(10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($w*20, $h, "日別シフト表｜".$year."年".$month."月".$day."日（".$weekjp[date('w', mktime(0, 0, 0, $month, $day, $year))]."曜日）", 0, 0, 'L');

    $pdf->SetFontSize(5);

    $res_remark = $mysqli->query("SELECT * FROM remark WHERE date='$date'");
    while($remark = $res_remark->fetch_assoc()) {
        $content = $remark['content'];
    }

    if($content) {
        $pdf->SetLineStyle(array('dash' => '2'));
        $pdf->Cell($w*51, $h*1.5, "＜備考＞".str_replace(array("\r\n","\r","\n"), " / ", $content), 1, 1, 'L');
        $pdf->SetLineStyle(array('dash' => '0'));
    } else {
        $pdf->Cell($w*48, $h*1.5, "", 0, 1, 'L');
    }

    $pdf->Cell($w*68, $h, "", 0, 1, 'L');

    $x0 = $pdf->GetX();
    $x1 = $x0;
    $y0 = $pdf->GetY();

    if($print == 'all') {
        for($service=0; $service<count($servicejp); $service++) {
            $y1 = $pdf->GetY();
            $pdf->SetXY($x1, $y1);
            $pdf->Cell($w*18, $h, "●".$servicejp[$service], 0, 1, 'L');
            $y1 = $pdf->GetY();
            $pdf->SetXY($x1, $y1);
            $pdf->Cell($w*4, $h, "利用者", 1, 0, 'C', 1);
            $pdf->Cell($w*3, $h, "利用時間", 1, 0, 'C', 1);
            $pdf->Cell($w*10, $h, "担当", 1, 1, 'C', 1);
            $y1 = $pdf->GetY();
            $pdf->SetXY($x1, $y1);
            
            for($sex=1; $sex>=0; $sex--) {
                $res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.date='$date' AND user.sex=$sex AND shift_user.service=$service ORDER BY user.kana, shift_user.user_start");
                while($shift_user = $res_shift_user->fetch_assoc()) {
                    if($shift_user['cancel']) {
                        $pdf->SetTextColor(128, 128, 128);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE shift_user_id=".$shift_user['shift_user_id']);
                    $rows = ($res_shift_staff->num_rows) ? ($res_shift_staff->num_rows) : 1;
                    if(($service == 2) || ($service == 3)) {
                        $rows_sum += $rows;
                    }
                    $pdf->Cell($w*4, $h*$rows, $shift_user['name'], 1, 0, 'L');
                    $pdf->Cell($w*3, $h*$rows, $shift_user['user_start']."～".$shift_user['user_end'], 1, 0, 'C');
                    $x2 = $pdf->GetX();
                    $y1 = $pdf->GetY();
                    while($shift_staff = $res_shift_staff->fetch_assoc()) {
                        $staff_name = array();
                        $res_staff = $mysqli->query("SELECT name FROM staff WHERE staff_id=".$shift_staff['staff_id_1']." OR staff_id=".$shift_staff['staff_id_2']);
                        while($staff = $res_staff->fetch_assoc()) {
                            $staff_name[] = $staff['name'];
                        }
                        $pdf->Cell($w*10, $h, $shift_staff['staff_start']."～".$shift_staff['staff_end']." ".implode(" / ", $staff_name), 0, 2, 'L');
                    }
                    $pdf->SetXY($x2, $y1);
                    $pdf->Cell($w*10, $h*$rows, "", 1, 1, 'C');
                    $y1 = $pdf->GetY();
                    $pdf->SetXY($x1, $y1);
                }
            }
            
            $pdf->Cell($w*17, $h, "", 0, 1, 'L');
        }
        
        $y1 = $pdf->GetY();
        $pdf->SetXY($x0, max($y1, $y2));
        $pdf->Cell($w*18, $h, "●その他予定", 0, 1, 'L');
        $y1 = $pdf->GetY();
        $pdf->SetXY($x0, $y1);
        $pdf->Cell($w*11, $h, "項目", 1, 0, 'C', 1);
        $pdf->Cell($w*3, $h, "時間", 1, 0, 'C', 1);
        $pdf->Cell($w*3, $h, "スタッフ", 1, 1, 'C', 1);
        $y1 = $pdf->GetY();
        $pdf->SetXY($x0, $y1);
        
        $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE shift_user_id=0 AND date='$date'");
        while($shift_staff = $res_shift_staff->fetch_assoc()) {
            $pdf->Cell($w*11, $h, $shift_staff['other'], 1, 0, 'L');
            $pdf->Cell($w*3, $h, $shift_staff['staff_start']."～".$shift_staff['staff_end'], 1, 0, 'C');
            $res_staff = $mysqli->query("SELECT name FROM staff WHERE staff_id=".$shift_staff['staff_id_1']);
            while($staff = $res_staff->fetch_assoc()) {
                $pdf->Cell($w*3, $h, $staff['name'], 1, 1, 'C');
            }
        }

        $x1 = $x0 + $w*18;
        $x2 = $x1 + $w*18;
        $x3 = $x2 + $w*18;
        $y1 = $y0;
        $pdf->SetXY($x1, $y1);
        
        $pdf->Cell($w*16, $h, "●シフト表", 0, 1, 'L');

        $x0 = $x1;
        $y1 = $pdf->GetY();

        $h1 = 4.5;

        $res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE (register=1 OR retire_day>='$date') ORDER BY display ASC");
        while($staff = $res_staff->fetch_assoc()) {
            $res_day_off = $mysqli->query("SELECT * FROM day_off WHERE date='$date' AND staff_id=".$staff['staff_id']);
            $bg = ($res_day_off->num_rows) ? 1 : 0;
            $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE date='$date' AND (staff_id_1=".$staff['staff_id']." OR staff_id_2=".$staff['staff_id'].") ORDER BY staff_start ASC");
            $rows = ($res_shift_staff->num_rows) ? ($res_shift_staff->num_rows) : 1;
            $rows_sum += $rows;
            if(($x0 == $x1) && ($rows_sum*$h1 > 172)) {
                $x0 = $x2;
                $y1 = $y0 + $h;
                $rows_sum = $rows;
            } elseif(($x0 == $x2) && ($rows_sum*$h1 > 190)) {
                $x0 = $x3;
                $y1 = $y0 + $h;
                $rows_sum = $rows;
            }
            $pdf->SetXY($x0, $y1);
            $pdf->Cell($w*4, $h1*$rows, $staff['name'], 1, 0, 'C', $bg);
            $x_shift = $pdf->GetX();
            while($shift_staff = $res_shift_staff->fetch_assoc()) {
                if($shift_staff['shift_user_id']) {
                    $res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.shift_user_id=".$shift_staff['shift_user_id']);
                    while($shift_user = $res_shift_user->fetch_assoc()) {
                        if($shift_user['cancel']) $pdf->SetTextColor(128, 128, 128);
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
                                $ac_name = "[同行]".$staff2['name'];
                            }
                        } else {
                            $ac_name = "";
                        }
                        $pdf->Cell($w*13, $h1, $servjp[$shift_user['service']].$shift_staff['staff_start']."～".$shift_staff['staff_end']."(".$shift_user['name']."さん)".$ac_name, 0, 2, 'L', $bg);
                        $pdf->SetTextColor(0, 0, 0);
                    }
                } else {
                    $pdf->Cell($w*13, $h1, $shift_staff['staff_start']."～".$shift_staff['staff_end']."(".$shift_staff['other'].")", 0, 2, 'L');
                }
            }
            $pdf->SetXY($x_shift, $y1);
            $pdf->Cell($w*13, $h1*$rows, "", 1, 0, 'C');
            $y1 = $y1 + $h1*$rows;
        }
    } elseif($print == 'fowaie') {
        for($service=0; $service<count($servicejp); $service++) {
            $y1 = $pdf->GetY();
            $pdf->SetXY($x1, $y1);
            $pdf->Cell($w*18, $h, "●".$servicejp[$service], 0, 1, 'L');
            $y1 = $pdf->GetY();
            $pdf->SetXY($x1, $y1);
            $pdf->Cell($w*4, $h, "利用者", 1, 0, 'C', 1);
            $pdf->Cell($w*3, $h, "利用時間", 1, 0, 'C', 1);
            $pdf->Cell($w*10, $h, "担当", 1, 1, 'C', 1);
            $y1 = $pdf->GetY();
            $pdf->SetXY($x1, $y1);
            
            for($sex=1; $sex>=0; $sex--) {
                $res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.date='$date' AND user.sex=$sex AND shift_user.service=$service ORDER BY user.kana, shift_user.user_start");
                while($shift_user = $res_shift_user->fetch_assoc()) {
                    if($shift_user['cancel']) {
                        $pdf->SetTextColor(128, 128, 128);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE shift_user_id=".$shift_user['shift_user_id']." AND ((staff_id_1>0 AND foyer_1=0) OR (staff_id_2>0 AND foyer_2=0))");
                    $rows = ($res_shift_staff->num_rows) ? ($res_shift_staff->num_rows) : 1;
                    if($res_shift_staff->num_rows) {
                        if(($service == 2) || ($service == 3)) {
                            $rows_sum += $rows;
                        }
                        $pdf->Cell($w*4, $h*$rows, $shift_user['name'], 1, 0, 'L');
                        $pdf->Cell($w*3, $h*$rows, $shift_user['user_start']."～".$shift_user['user_end'], 1, 0, 'C');
                        $x2 = $pdf->GetX();
                        $y1 = $pdf->GetY();
                        while($shift_staff = $res_shift_staff->fetch_assoc()) {
                            $staff_name = array();
                            $res_staff = $mysqli->query("SELECT name FROM staff WHERE staff_id=".$shift_staff['staff_id_1']." OR staff_id=".$shift_staff['staff_id_2']);
                            while($staff = $res_staff->fetch_assoc()) {
                                $staff_name[] = $staff['name'];
                            }
                            $pdf->Cell($w*10, $h, $shift_staff['staff_start']."～".$shift_staff['staff_end']." ".implode(" / ", $staff_name), 0, 2, 'L');
                        }
                        $pdf->SetXY($x2, $y1);
                        $pdf->Cell($w*10, $h*$rows, "", 1, 1, 'C');
                        $y1 = $pdf->GetY();
                        $pdf->SetXY($x1, $y1);
                    }
                }
            }
            
            $pdf->Cell($w*17, $h, "", 0, 1, 'L');
        }
        
        $y1 = $pdf->GetY();
        $pdf->SetXY($x0, max($y1, $y2));
        $pdf->Cell($w*18, $h, "●その他予定", 0, 1, 'L');
        $y1 = $pdf->GetY();
        $pdf->SetXY($x0, $y1);
        $pdf->Cell($w*11, $h, "項目", 1, 0, 'C', 1);
        $pdf->Cell($w*3, $h, "時間", 1, 0, 'C', 1);
        $pdf->Cell($w*3, $h, "スタッフ", 1, 1, 'C', 1);
        $y1 = $pdf->GetY();
        $pdf->SetXY($x0, $y1);
        
        $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE shift_user_id=0 AND date='$date' AND foyer_1=0");
        while($shift_staff = $res_shift_staff->fetch_assoc()) {
            $pdf->Cell($w*11, $h, $shift_staff['other'], 1, 0, 'L');
            $pdf->Cell($w*3, $h, $shift_staff['staff_start']."～".$shift_staff['staff_end'], 1, 0, 'C');
            $res_staff = $mysqli->query("SELECT name FROM staff WHERE staff_id=".$shift_staff['staff_id_1']);
            while($staff = $res_staff->fetch_assoc()) {
                $pdf->Cell($w*3, $h, $staff['name'], 1, 1, 'C');
            }
        }

        $x1 = $x0 + $w*18;
        $x2 = $x1 + $w*18;
        $x3 = $x2 + $w*18;
        $y1 = $y0;
        $pdf->SetXY($x1, $y1);
        
        $pdf->Cell($w*16, $h, "●シフト表", 0, 1, 'L');

        $x0 = $x1;
        $y1 = $pdf->GetY();

        $h1 = 4.5;

        $res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE (register=1 OR retire_day>='$date') ORDER BY display ASC");
        while($staff = $res_staff->fetch_assoc()) {
            $res_day_off = $mysqli->query("SELECT * FROM day_off WHERE date='$date' AND staff_id=".$staff['staff_id']);
            $bg = ($res_day_off->num_rows) ? 1 : 0;
            $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE date='$date' AND ((staff_id_1=".$staff['staff_id']." AND foyer_1=0) OR (staff_id_2=".$staff['staff_id']." AND foyer_2=0)) ORDER BY staff_start ASC");
            $rows = ($res_shift_staff->num_rows) ? ($res_shift_staff->num_rows) : 1;
            if($res_shift_staff->num_rows) {
                $rows_sum += $rows;
                if(($x0 == $x1) && ($rows_sum*$h1 > 172)) {
                    $x0 = $x2;
                    $y1 = $y0 + $h;
                    $rows_sum = $rows;
                } elseif(($x0 == $x2) && ($rows_sum*$h1 > 190)) {
                    $x0 = $x3;
                    $y1 = $y0 + $h;
                    $rows_sum = $rows;
                }
                $pdf->SetXY($x0, $y1);
                $pdf->Cell($w*4, $h1*$rows, $staff['name'], 1, 0, 'C', $bg);
                $x_shift = $pdf->GetX();
                while($shift_staff = $res_shift_staff->fetch_assoc()) {
                    if($shift_staff['shift_user_id']) {
                        $res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.shift_user_id=".$shift_staff['shift_user_id']);
                        while($shift_user = $res_shift_user->fetch_assoc()) {
                            if($shift_user['cancel']) $pdf->SetTextColor(128, 128, 128);
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
                                    $ac_name = "[同行]".$staff2['name'];
                                }
                            } else {
                                $ac_name = "";
                            }
                            $pdf->Cell($w*13, $h1, $servjp[$shift_user['service']].$shift_staff['staff_start']."～".$shift_staff['staff_end']."(".$shift_user['name']."さん)".$ac_name, 0, 2, 'L', $bg);
                            $pdf->SetTextColor(0, 0, 0);
                        }
                    } else {
                        $pdf->Cell($w*13, $h1, $shift_staff['staff_start']."～".$shift_staff['staff_end']."(".$shift_staff['other'].")", 0, 2, 'L');
                    }
                }
                $pdf->SetXY($x_shift, $y1);
                $pdf->Cell($w*13, $h1*$rows, "", 1, 0, 'C');
                $y1 = $y1 + $h1*$rows;
            }
        }
    }
    ob_end_clean();
    $pdf->Output();
}
?>