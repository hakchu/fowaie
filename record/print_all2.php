<?php
require_once("../connect.php");
require_once('../tcpdf/tcpdf.php');

$pdf = new tcpdf('L','mm','B5');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false);
$pdf->SetMargins(15, 7);

$font = new TCPDF_FONTS();
$myFont = $font->addTTFfont('../tcpdf/fonts/ARIALUNI.TTF');
$pdf->SetFont($myFont, "", 10);
$pdf->SetLineWidth(0.1);
$pdf->SetFillColor(200);

$date = $_GET['date'];
$year = $_GET['year'];
$month = $_GET['month'];
$user_id = $_GET['user_id'];
$ym = $year.'-'.sprintf('%02d', $month);

if(!$a_role || disp_record($ym.-'01')) {
    $res_shift_user = $mysqli->query("SELECT * FROM shift_user JOIN user ON shift_user.user_id=user.user_id WHERE shift_user.date LIKE '%$ym%' AND shift_user.user_id=$user_id AND shift_user.cancel=0 ORDER BY shift_user.date, shift_user.user_start");
    while($shift_user = $res_shift_user->fetch_assoc()) {
        $shift_user_id = $shift_user['shift_user_id'];
        $name = $shift_user['name'];
        $date = $shift_user['date'];
        $user_start = $shift_user['user_start'];
        $user_end = $shift_user['user_end'];

        $start = NULL;
        $end = NULL;
        $staff = NULL;
        $company = NULL;
        $data = array();
        $message = NULL;
        $res_record = $mysqli->query("SELECT * FROM record WHERE shift_user_id=".$shift_user_id);
        while($record = $res_record->fetch_assoc()) {
            $start = $record['start'];
            $end = $record['end'];
            $staff = $record['staff'];
            $company = $record['company'];
            $data = json_decode($record['data'], true);
            $message = $record['message'];

            $staffs = array();
            $res_shift_staff = $mysqli->query("SELECT * FROM shift_staff WHERE shift_user_id=".$shift_user_id);
            while($shift_staff = $res_shift_staff->fetch_assoc()) {
                $res_staff = $mysqli->query("SELECT staff_id, name FROM staff WHERE staff_id=".$shift_staff['staff_id_1']." OR staff_id=".$shift_staff['staff_id_2']);
                while($staff2 = $res_staff->fetch_assoc()) {
                    array_push($staffs, $staff2['name']);
                }
            }

            for($p = 0; $p < 2; $p++) {
                $pdf->AddPage();

                $pdf->SetTextColor(0, 0, 0);

                $pdf->SetFontSize(8);
                if($p) {
                    $pdf->Cell(220, 3, "〔事務所控〕", 0, 1, 'R');
                } else {
                    $pdf->Cell(220, 3, "〔利用者控〕", 0, 1, 'R');
                }
        
                $pdf->SetFontSize(15);
                $pdf->Cell(220, 8, "サービス提供記録票", 0, 1, 'C');

                $x = $pdf->GetX();
                $y = $pdf->GetY();

                $pdf->SetXY(219.7, 16.8);
                $pdf->SetFontSize(10);
                $pdf->Cell(10, 6, "印", 0, 1, 'R');

                $pdf->SetLineStyle(array('dash' => 2));
                $pdf->Circle(227, 20, 4);

                $pdf->SetXY($x, $y);
                $pdf->SetLineStyle(array('dash' => false));

                $pdf->SetFontSize(10);
                $pdf->Cell(20, 6, "ご利用者名", 'B', 0, 'C');
                $pdf->SetFontSize(12);
                $pdf->Cell(40, 6, $name, 'B', 0, 'C');
                $pdf->Cell(5, 6, "様", 'B', 0, 'C');

                $pdf->Cell(70, 6, "", 0, 0, 'C');

                $pdf->SetFontSize(10);
                $pdf->Cell(20, 6, "ヘルパー名", 'B', 0, 'C');
                $pdf->Cell(50, 6, (isset($staff) ? $staff : implode(' / ', $staffs)), 'B', 1, 'C');

                $h = 4.7;

                $x0 = $pdf->GetX();
                $y0 = $pdf->GetY() + 2;
                
                $pdf->SetLineWidth(0.1);
                
                $pdf->SetXY($x0, $y0);
                $pdf->Cell(36, $h, "サービス準備・記録等", 1, 0, 'C', 1);
                $pdf->Cell(14, $h, ((isset($data['service_junbi']) && in_array(0, $data['service_junbi'])) ? "☑" : "☐")."体温〔", 0, 0, 'L');
                $pdf->Cell(8, $h, $data['taion'], 0, 0, 'C');
                $pdf->Cell(23, $h, "℃〕  ".((isset($data['service_junbi']) && in_array(1, $data['service_junbi'])) ? "☑" : "☐")."血圧〔", 0, 0, 'L');
                $pdf->Cell(8, $h, $data['ketsuatsu'][0], 0, 0, 'C');
                $pdf->Cell(2, $h, "/", 0, 0, 'C');
                $pdf->Cell(8, $h, $data['ketsuatsu'][1], 0, 0, 'C');
                $pdf->Cell(20, $h, "〕  ".((isset($data['service_junbi']) && in_array(2, $data['service_junbi'])) ? "☑" : "☐")."脈拍〔", 0, 0, 'L');
                $pdf->Cell(8, $h, $data['myakuhaku'], 0, 0, 'C');
                $pdf->Cell(13, $h, "/分〕", 0, 1, 'L');
                $pdf->Cell(57, $h, "状態 ： ".(($data['joutai'] == 1) ? "☑" : "☐")."良好  ・".(($data['joutai'] == 2) ? "☑" : "☐")."普通  ・".(($data['joutai'] == 3) ? "☑" : "☐")."不良〔", 0, 0, 'L');
                $pdf->Cell(74.5, $h, $data['furyou'], 0, 0, 'L');
                $pdf->Cell(8.5, $h, "〕", 0, 1, 'L');
                
                $x1 = $pdf->GetX() + 6;
                $y1 = $pdf->GetY();
                $h1 = 12;
                
                $pdf->Cell(6, ($h*18-$h1*4)/2, "", 'LTR', 2, 'C', 1);
                $pdf->Cell(6, 12, "身", 'LR', 2, 'C', 1);
                $pdf->Cell(6, 12, "体", 'LR', 2, 'C', 1);
                $pdf->Cell(6, 12, "介", 'LR', 2, 'C', 1);
                $pdf->Cell(6, 12, "護", 'LR', 2, 'C', 1);
                $pdf->Cell(6, ($h*18-$h1*4)/2, "", 'LBR', 2, 'C', 1);
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "【排　泄】", 'T', 0, 'L');
                $pdf->Cell(18, $h, ((isset($data['haisetsu']) && in_array(0, $data['haisetsu'])) ? "☑" : "☐")."ﾄｲﾚ介助", 'T', 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['haisetsu']) && in_array(1, $data['haisetsu'])) ? "☑" : "☐")."Pﾄｲﾚ介助", 'T', 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['haisetsu']) && in_array(2, $data['haisetsu'])) ? "☑" : "☐")."尿器介助", 'T', 0, 'L');
                $pdf->Cell(51, $h, ((isset($data['haisetsu']) && in_array(3, $data['haisetsu'])) ? "☑" : "☐")."陰部（".(($data['inbu'] == 1) ? "☑" : "☐")."洗浄 / ".(($data['inbu'] == 2) ? "☑" : "☐")."清拭）", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "", 0, 0, 'L');
                $pdf->Cell(18, $h, ((isset($data['haisetsu']) && in_array(4, $data['haisetsu'])) ? "☑" : "☐")."ｵﾑﾂ交換", 0, 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['haisetsu']) && in_array(5, $data['haisetsu'])) ? "☑" : "☐")."ﾊﾟｯﾄ交換", 0, 0, 'L');
                $pdf->Cell(24, $h, ((isset($data['haisetsu']) && in_array(6, $data['haisetsu'])) ? "☑" : "☐")."排尿・尿ﾊｷ（", 0, 0, 'L');
                $pdf->Cell(31, $h, $data['hainyou'], 0, 0, 'L');
                $pdf->Cell(7, $h, "）", 0, 1, 'L');
                
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "", 0, 0, 'L');
                $pdf->Cell(40, $h, ((isset($data['haisetsu']) && in_array(7, $data['haisetsu'])) ? "☑" : "☐")."排便（".(($data['haiben'] == 1) ? "☑" : "☐")."少  ・".(($data['haiben'] == 2) ? "☑" : "☐")."中  ・".(($data['haiben'] == 3) ? "☑" : "☐")."多）", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "【食　事】", 'T', 0, 'L');
                $pdf->Cell(18, $h, ((isset($data['shokuji']) && in_array(0, $data['shokuji'])) ? "☑" : "☐")."全介助", 'T', 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['shokuji']) && in_array(1, $data['shokuji'])) ? "☑" : "☐")."一部介助", 'T', 0, 'L');
                $pdf->Cell(17, $h, ((isset($data['shokuji']) && in_array(2, $data['shokuji'])) ? "☑" : "☐")."食事量（", 'T', 0, 'L');
                $pdf->Cell(26, $h, (($data['shokujiryou'] == 1) ? "☑" : "☐")."完食  ・".(($data['shokujiryou'] == 2) ? "☑" : "☐")."残量", 'T', 0, 'L');
                $pdf->Cell(7, $h, $data['zanryou'][0], 'T', 0, 'C');
                $pdf->Cell(2, $h, "/", 'T', 0, 'C');
                $pdf->Cell(7, $h, $data['zanryou'][1], 'T', 0, 'C');
                $pdf->Cell(7, $h, "）", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "", 0, 0, 'L');
                $pdf->Cell(22, $h, ((isset($data['shokuji']) && in_array(3, $data['shokuji'])) ? "☑" : "☐")."水分補給（", 0, 0, 'L');
                $pdf->Cell(8, $h, $data['suibun'], 0, 0, 'C');
                $pdf->Cell(8, $h, "cc）", 0, 0, 'L');
                $pdf->Cell(24, $h, ((isset($data['shokuji']) && in_array(4, $data['shokuji'])) ? "☑" : "☐")."特段の調理（", 0, 0, 'L');
                $pdf->Cell(35, $h, $data['sp_chouri'], 0, 0, 'L');
                $pdf->Cell(7, $h, "）", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(11.5, $h, "【保清", 'T', 0, 'L');
                $pdf->Cell(2.5, $h, "・", 'T', 0, 'L');
                $pdf->Cell(11, $h, "整容】", 'T', 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['hosei']) && in_array(0, $data['hosei'])) ? "☑" : "☐")."入浴介助", 'T', 0, 'L');
                $pdf->Cell(16, $h, ((isset($data['hosei']) && in_array(1, $data['hosei'])) ? "☑" : "☐")."ｼｬﾜｰ浴", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['hosei']) && in_array(2, $data['hosei'])) ? "☑" : "☐")."手浴", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['hosei']) && in_array(3, $data['hosei'])) ? "☑" : "☐")."足浴", 'T', 0, 'L');
                $pdf->Cell(42, $h, ((isset($data['hosei']) && in_array(4, $data['hosei'])) ? "☑" : "☐")."洗髪", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "", 0, 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['hosei']) && in_array(5, $data['hosei'])) ? "☑" : "☐")."全身清拭", 0, 0, 'L');
                $pdf->Cell(21, $h, ((isset($data['hosei']) && in_array(6, $data['hosei'])) ? "☑" : "☐")."部分清拭（", 0, 0, 'L');
                $pdf->Cell(63, $h, ((isset($data['bubun']) && in_array(0, $data['bubun'])) ? "☑" : "☐")."顔  ・".((isset($data['bubun']) && in_array(1, $data['bubun'])) ? "☑" : "☐")."手  ・".((isset($data['bubun']) && in_array(2, $data['bubun'])) ? "☑" : "☐")."足  ・".((isset($data['bubun']) && in_array(3, $data['bubun'])) ? "☑" : "☐")."上半身）", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "", 0, 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['hosei']) && in_array(7, $data['hosei'])) ? "☑" : "☐")."洗面", 0, 0, 'L');
                $pdf->Cell(16, $h, ((isset($data['hosei']) && in_array(8, $data['hosei'])) ? "☑" : "☐")."口腔ｹｱ", 0, 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['hosei']) && in_array(9, $data['hosei'])) ? "☑" : "☐")."義歯洗浄", 0, 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['hosei']) && in_array(10, $data['hosei'])) ? "☑" : "☐")."更衣介助", 0, 0, 'L');
                $pdf->Cell(35, $h, ((isset($data['hosei']) && in_array(11, $data['hosei'])) ? "☑" : "☐")."ｼｰﾂ交換", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "", 0, 0, 'L');
                $pdf->Cell(104, $h, ((isset($data['hosei']) && in_array(12, $data['hosei'])) ? "☑" : "☐")."整容（".((isset($data['seiyou']) && in_array(0, $data['seiyou'])) ? "☑" : "☐")."髪  ・".((isset($data['seiyou']) && in_array(1, $data['seiyou'])) ? "☑" : "☐")."髭  ・".((isset($data['seiyou']) && in_array(2, $data['seiyou'])) ? "☑" : "☐")."耳  ・".((isset($data['seiyou']) && in_array(3, $data['seiyou'])) ? "☑" : "☐")."爪切り  ・".((isset($data['seiyou']) && in_array(4, $data['seiyou'])) ? "☑" : "☐")."化粧）", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(11.5, $h, "【移動", 'T', 0, 'L');
                $pdf->Cell(2.5, $h, "・", 'T', 0, 'L');
                $pdf->Cell(11, $h, "外出】", 'T', 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['idou']) && in_array(0, $data['idou'])) ? "☑" : "☐")."体位変換", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['idou']) && in_array(1, $data['idou'])) ? "☑" : "☐")."移乗", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['idou']) && in_array(2, $data['idou'])) ? "☑" : "☐")."移動", 'T', 0, 'L');
                $pdf->Cell(24, $h, ((isset($data['idou']) && in_array(3, $data['idou'])) ? "☑" : "☐")."車椅子介助", 'T', 0, 'L');
                $pdf->Cell(34, $h, ((isset($data['idou']) && in_array(4, $data['idou'])) ? "☑" : "☐")."歩行介助", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "", 0, 0, 'L');
                $pdf->Cell(27, $h, ((isset($data['idou']) && in_array(5, $data['idou'])) ? "☑" : "☐")."外出準備介助", 0, 0, 'L');
                $pdf->Cell(77, $h, ((isset($data['idou']) && in_array(6, $data['idou'])) ? "☑" : "☐")."帰宅受入介助", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "", 0, 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['idou']) && in_array(7, $data['idou'])) ? "☑" : "☐")."買物同行（", 0, 0, 'L');
                $pdf->Cell(26, $h, $data['idou_kaimono'], 0, 0, 'L');
                $pdf->Cell(5, $h, "）", 0, 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['idou']) && in_array(8, $data['idou'])) ? "☑" : "☐")."通院同行（", 0, 0, 'L');
                $pdf->Cell(26, $h, $data['idou_tsuuin'], 0, 0, 'L');
                $pdf->Cell(7, $h, "）", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(11.5, $h, "【起床", 'T', 0, 'L');
                $pdf->Cell(2.5, $h, "・", 'T', 0, 'L');
                $pdf->Cell(11, $h, "就寝】", 'T', 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['kishou']) && in_array(0, $data['kishou'])) ? "☑" : "☐")."起床介助", 'T', 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['kishou']) && in_array(1, $data['kishou'])) ? "☑" : "☐")."就寝介助", 'T', 0, 'L');
                $pdf->Cell(64, $h, ((isset($data['kishou']) && in_array(2, $data['kishou'])) ? "☑" : "☐")."苦痛軽減のためのさすり", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "【服　薬】", 'T', 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['fukuyaku']) && in_array(0, $data['fukuyaku'])) ? "☑" : "☐")."服薬介助", 'T', 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['fukuyaku']) && in_array(1, $data['fukuyaku'])) ? "☑" : "☐")."服薬確認", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['fukuyaku']) && in_array(2, $data['fukuyaku'])) ? "☑" : "☐")."湿布", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['fukuyaku']) && in_array(3, $data['fukuyaku'])) ? "☑" : "☐")."軟膏", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['fukuyaku']) && in_array(4, $data['fukuyaku'])) ? "☑" : "☐")."点眼", 'T', 0, 'L');
                $pdf->Cell(25, $h, ((isset($data['fukuyaku']) && in_array(5, $data['fukuyaku'])) ? "☑" : "☐")."点鼻", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "【医療行為】", 'T', 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['iryou']) && in_array(0, $data['iryou'])) ? "☑" : "☐")."痰の吸引", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['iryou']) && in_array(1, $data['iryou'])) ? "☑" : "☐")."浣腸", 'T', 0, 'L');
                $pdf->Cell(71, $h, ((isset($data['iryou']) && in_array(2, $data['iryou'])) ? "☑" : "☐")."準備 ・後片付け", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "", 0, 0, 'L');
                $pdf->Cell(51, $h, ((isset($data['iryou']) && in_array(3, $data['iryou'])) ? "☑" : "☐")."注入（".((isset($data['chuunyuu']) && in_array(0, $data['chuunyuu'])) ? "☑" : "☐")."経鼻  ・".((isset($data['chuunyuu']) && in_array(1, $data['chuunyuu'])) ? "☑" : "☐")."胃  ・".((isset($data['chuunyuu']) && in_array(2, $data['chuunyuu'])) ? "☑" : "☐")."腸）", 0, 0, 'L');
                $pdf->Cell(53, $h, ((isset($data['iryou']) && in_array(4, $data['iryou'])) ? "☑" : "☐")."水分補給", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "【自立支援】", 'T', 0, 'L');
                $pdf->Cell(104, $h, ((isset($data['jiritsu']) && in_array(0, $data['jiritsu'])) ? "☑" : "☐")."共に行う家事（".((isset($data['kaji']) && in_array(0, $data['kaji'])) ? "☑" : "☐")."調理  ・".((isset($data['kaji']) && in_array(1, $data['kaji'])) ? "☑" : "☐")."掃除  ・".((isset($data['kaji']) && in_array(2, $data['kaji'])) ? "☑" : "☐")."洗濯  ・".((isset($data['kaji']) && in_array(3, $data['kaji'])) ? "☑" : "☐")."その他）", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "", 0, 0, 'L');
                $pdf->Cell(17, $h, ((isset($data['jiritsu']) && in_array(1, $data['jiritsu'])) ? "☑" : "☐")."その他（", 0, 0, 'L');
                $pdf->Cell(80, $h, $data['jiritsu_sonota'], 0, 0, 'L');
                $pdf->Cell(7, $h, "）", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                $h1 = 4.5;
                $pdf->SetXY($x0, $y1);
                $pdf->Cell(6, ($h*9-$h1*7)/2, "", 'LTR', 2, 'C', 1);
                $pdf->Cell(6, $h1, "生", 'LR', 2, 'C', 1);
                $pdf->Cell(6, $h1, "活", 'LR', 2, 'C', 1);
                $pdf->Cell(6, $h1, "  ・", 'LR', 2, 'C', 1);
                $pdf->Cell(6, $h1, "家", 'LR', 2, 'C', 1);
                $pdf->Cell(6, $h1, "事", 'LR', 2, 'C', 1);
                $pdf->Cell(6, $h1, "援", 'LR', 2, 'C', 1);
                $pdf->Cell(6, $h1, "助", 'LR', 2, 'C', 1);
                $pdf->Cell(6, ($h*9-$h1*7)/2, "", 'LBR', 2, 'C', 1);
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "【掃　除】", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['souji']) && in_array(0, $data['souji'])) ? "☑" : "☐")."居間", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['souji']) && in_array(1, $data['souji'])) ? "☑" : "☐")."寝室", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['souji']) && in_array(2, $data['souji'])) ? "☑" : "☐")."台所", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['souji']) && in_array(3, $data['souji'])) ? "☑" : "☐")."玄関", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['souji']) && in_array(4, $data['souji'])) ? "☑" : "☐")."浴室", 'T', 0, 'L');
                $pdf->Cell(11, $h, ((isset($data['souji']) && in_array(5, $data['souji'])) ? "☑" : "☐")."ﾄｲﾚ", 'T', 0, 'L');
                $pdf->Cell(28, $h, ((isset($data['souji']) && in_array(6, $data['souji'])) ? "☑" : "☐")."Pﾄｲﾚ", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "", 0, 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['souji']) && in_array(7, $data['souji'])) ? "☑" : "☐")."廊下", 0, 0, 'L');
                $pdf->Cell(18, $h, ((isset($data['souji']) && in_array(8, $data['souji'])) ? "☑" : "☐")."ｺﾞﾐ出し", 0, 0, 'L');
                $pdf->Cell(17, $h, ((isset($data['souji']) && in_array(9, $data['souji'])) ? "☑" : "☐")."その他（", 0, 0, 'L');
                $pdf->Cell(49, $h, $data['souji_sonota'], 0, 0, 'L');
                $pdf->Cell(7, $h, "）", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "【洗　濯】", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['sentaku']) && in_array(0, $data['sentaku'])) ? "☑" : "☐")."洗濯", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['sentaku']) && in_array(1, $data['sentaku'])) ? "☑" : "☐")."干し", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['sentaku']) && in_array(2, $data['sentaku'])) ? "☑" : "☐")."取込", 'T', 0, 'L');
                $pdf->Cell(16, $h, ((isset($data['sentaku']) && in_array(3, $data['sentaku'])) ? "☑" : "☐")."たたみ", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['sentaku']) && in_array(4, $data['sentaku'])) ? "☑" : "☐")."収納", 'T', 0, 'L');
                $pdf->Cell(36, $h, ((isset($data['sentaku']) && in_array(5, $data['sentaku'])) ? "☑" : "☐")."ｱｲﾛﾝ", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "", 0, 0, 'L');
                $pdf->Cell(17, $h, ((isset($data['sentaku']) && in_array(6, $data['sentaku'])) ? "☑" : "☐")."その他（", 0, 0, 'L');
                $pdf->Cell(80, $h, $data['sentaku_sonota'], 0, 0, 'L');
                $pdf->Cell(7, $h, "）", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "【ﾍﾞｯﾄﾞﾒｲｸ】", 'T', 0, 'L');
                $pdf->Cell(18, $h, ((isset($data['bed']) && in_array(0, $data['bed'])) ? "☑" : "☐")."ｼｰﾂ交換", 'T', 0, 'L');
                $pdf->Cell(27, $h, ((isset($data['bed']) && in_array(1, $data['bed'])) ? "☑" : "☐")."布団ｶﾊﾞｰ交換", 'T', 0, 'L');
                $pdf->Cell(59, $h, ((isset($data['bed']) && in_array(2, $data['bed'])) ? "☑" : "☐")."布団干し  ・取込", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "【衣　類】", 'T', 0, 'L');
                $pdf->Cell(24, $h, ((isset($data['irui']) && in_array(0, $data['irui'])) ? "☑" : "☐")."衣類の整理", 'T', 0, 'L');
                $pdf->Cell(24, $h, ((isset($data['irui']) && in_array(1, $data['irui'])) ? "☑" : "☐")."衣服の補修", 'T', 0, 'L');
                $pdf->Cell(22, $h, ((isset($data['irui']) && in_array(2, $data['irui'])) ? "☑" : "☐")."ﾃﾞｲの準備（", 'T', 0, 'L');
                $pdf->Cell(27, $h, $data['irui_junbi'], 'T', 0, 'L');
                $pdf->Cell(7, $h, "）", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "【調　理】", 'T', 0, 'L');
                $pdf->Cell(39, $h, ((isset($data['chouri']) && in_array(0, $data['chouri'])) ? "☑" : "☐")."一般的な調理　献立〔", 'T', 0, 'L');
                $pdf->Cell(59, $h, $data['kondate'], 'T', 0, 'L');
                $pdf->Cell(6, $h, "〕", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "", 0, 0, 'L');
                $pdf->Cell(26, $h, ((isset($data['chouri']) && in_array(1, $data['chouri'])) ? "☑" : "☐")."配膳  ・下膳", 0, 0, 'L');
                $pdf->Cell(78, $h, ((isset($data['chouri']) && in_array(2, $data['chouri'])) ? "☑" : "☐")."後片付け", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x1, $y1);
                $pdf->Cell(25, $h, "【買　物】", 'T', 0, 'L');
                $pdf->Cell(27, $h, ((isset($data['kaimono']) && in_array(0, $data['kaimono'])) ? "☑" : "☐")."日常品の買物", 'T', 0, 'L');
                $pdf->Cell(23, $h, ((isset($data['kaimono']) && in_array(1, $data['kaimono'])) ? "☑" : "☐")."薬の受取り", 'T', 0, 'L');
                $pdf->Cell(13, $h, ((isset($data['kaimono']) && in_array(2, $data['kaimono'])) ? "☑" : "☐")."代行（", 'T', 0, 'L');
                $pdf->Cell(34, $h, $data['kaimono_daikou'], 'T', 0, 'L');
                $pdf->Cell(7, $h, "）", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x0, $y1);
                $pdf->Cell(15, $h, "預り金（", 'T', 0, 'L');
                $pdf->Cell(20, $h, ($data['money'][0] ? number_format($data['money'][0]) : ""), 'T', 0, 'C');
                $pdf->Cell(24, $h, "円）- 使用金（", 'T', 0, 'L');
                $pdf->Cell(20, $h, ($data['money'][1] ? number_format($data['money'][1]) : ""), 'T', 0, 'C');
                $pdf->Cell(25, $h, "円）= おつり（", 'T', 0, 'L');
                $pdf->Cell(20.5, $h, ($data['money'][2] ? number_format($data['money'][2]) : ""), 'T', 0, 'C');
                $pdf->Cell(10.5, $h, "円）", 'T', 1, 'L');
                
                $x2 = $x0 + 135;
                $x3 = $x2 + 5;
                $x4 = $x3 + 5;
                
                $pdf->SetXY($x3, $y0);
                $pdf->Cell(80, $h, "サ　ー　ビ　ス　提　供　日　時", 1, 2, 'C', 1);
                $pdf->Cell(80, $h, date('Y年m月d日', strtotime($date)).'（'.$weekjp[date('w', strtotime($date))].'）', 0, 2, 'C');
                $pdf->Cell(80, $h, (isset($start) ? $start : $user_start).'～'.(isset($end) ? $end : $user_end).'（'.((isset($start) && isset($end)) ? calc_time($start, $end) : calc_time($user_start, $user_end)).'）', 'T', 2, 'C');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x3, $y1);
                $pdf->Cell(18, $h, "予定変更", 0, 0, 'C', 1);
                $pdf->Cell(20, $h, (($data['henkou'] == 1) ? "☑" : "☐")."有  ・".(($data['henkou'] == 2) ? "☑" : "☐")."無", 'L', 0, 'L');
                $pdf->Cell(12, $h, "予定（", 'L', 0, 'L');
                $pdf->Cell(8, $h, $data['yotei'][0], 0, 0, 'C');
                $pdf->Cell(8, $h, "～", 0, 0, 'C');
                $pdf->Cell(8, $h, $data['yotei'][1], 0, 0, 'C');
                $pdf->Cell(6, $h, "）", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x3, $y1);
                $pdf->Cell(40, $h, (($data['kasan'] == 1) ? "☑" : "☐")."初回加算", 'T', 0, 'C');
                $pdf->Cell(40, $h, (($data['kasan'] == 2) ? "☑" : "☐")."緊急時加算", 'T', 1, 'C');
                
                $y1 = $pdf->GetY();
                $h1 = 5;
                
                $pdf->SetXY($x2, $y1);
                $pdf->Cell(5, ($h*9-$h1*6)/2, "", 'LTR', 2, 'C', 1);
                $pdf->Cell(5, $h1, "サ", 'LR', 2, 'C', 1);
                $pdf->Cell(5, $h1, "|", 'LR', 2, 'C', 1);
                $pdf->Cell(5, $h1, "ビ", 'LR', 2, 'C', 1);
                $pdf->Cell(5, $h1, "ス", 'LR', 2, 'C', 1);
                $pdf->Cell(5, $h1, "種", 'LR', 2, 'C', 1);
                $pdf->Cell(5, $h1, "類", 'LR', 2, 'C', 1);
                $pdf->Cell(5, ($h*9-$h1*6)/2, "", 'LBR', 2, 'C', 1);
                
                $pdf->SetXY($x3, $y1);
                $pdf->Cell(5, $h, "介", 'LTR', 2, 'C', 1);
                $pdf->Cell(5, $h, "護", 'LBR', 2, 'C', 1);
                
                $pdf->SetXY($x4, $y1);
                $pdf->Cell(32, $h, ((isset($data['service']) && in_array(0, $data['service'])) ? "☑" : "☐")."訪問介護　身体〔", 0, 0, 'L');
                $pdf->Cell(11, $h, $data['service_houmon'][0], 0, 0, 'L');
                $pdf->Cell(16, $h, "〕 生活〔", 0, 0, 'L');
                $pdf->Cell(11, $h, $data['service_houmon'][1], 0, 0, 'L');
                $pdf->Cell(5, $h, "〕", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x4, $y1);
                $pdf->Cell(20, $h, ((isset($data['service']) && in_array(1, $data['service'])) ? "☑" : "☐")."介護予防", 0, 0, 'L');
                $pdf->Cell(31, $h, ((isset($data['service']) && in_array(2, $data['service'])) ? "☑" : "☐")."通院等乗降介助（", 0, 0, 'L');
                $pdf->Cell(24, $h, ((isset($data['service_joukou']) && in_array(0, $data['service_joukou'])) ? "☑" : "☐")."往 ・".((isset($data['service_joukou']) && in_array(1, $data['service_joukou'])) ? "☑" : "☐")."復）", 0, 1, 'L');
                
                $pdf->SetFontSize(9);
                
                $y1 = $pdf->GetY();
                $h1 = $h * 6 / 7;
                
                $pdf->SetXY($x3, $y1);
                $pdf->Cell(5, $h1, "障", 'LTR', 2, 'C', 1);
                $pdf->Cell(5, $h1, "害", 'LR', 2, 'C', 1);
                $pdf->Cell(5, $h1, "福", 'LR', 2, 'C', 1);
                $pdf->Cell(5, $h1, "祉", 'LR', 2, 'C', 1);
                $pdf->Cell(5, $h1, "  ・", 'LR', 2, 'C', 1);
                $pdf->Cell(5, $h1, "地", 'LR', 2, 'C', 1);
                $pdf->Cell(5, $h1, "域", 'LRB', 2, 'C', 1);
                
                $pdf->SetFontSize(10);
                
                $pdf->SetXY($x4, $y1);
                $pdf->Cell(20, $h, ((isset($data['service']) && in_array(3, $data['service'])) ? "☑" : "☐")."身体介護", 'T', 0, 'L');
                $pdf->Cell(20, $h, ((isset($data['service']) && in_array(4, $data['service'])) ? "☑" : "☐")."家事援助", 'T', 0, 'L');
                $pdf->Cell(35, $h, ((isset($data['service']) && in_array(5, $data['service'])) ? "☑" : "☐")."乗降介助", 'T', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x4, $y1);
                $pdf->Cell(20, $h, ((isset($data['service']) && in_array(6, $data['service'])) ? "☑" : "☐")."通院介助（", 0, 0, 'L');
                $pdf->Cell(55, $h, (($data['service_tsuuin'] == 1) ? "☑" : "☐")."身体有　".(($data['service_tsuuin'] == 2) ? "☑" : "☐")."身体無）", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x4, $y1);
                $pdf->Cell(51, $h, ((isset($data['service']) && in_array(7, $data['service'])) ? "☑" : "☐")."重度訪問介護（".(($data['service_juudo'][0] == 1) ? "☑" : "☐")."移動加算有（", 0, 0, 'L');
                $pdf->Cell(8, $h, $data['service_juudo'][1], 0, 0, 'C');
                $pdf->Cell(16, $h, "分）〕", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x4, $y1);
                $pdf->Cell(75, $h, ((isset($data['service']) && in_array(8, $data['service'])) ? "☑" : "☐")."行動援護", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x4, $y1);
                $pdf->Cell(75, $h, ((isset($data['service']) && in_array(9, $data['service'])) ? "☑" : "☐")."同行援護（".(($data['service_doukou'] == 1) ? "☑" : "☐")."身体有　".(($data['service_doukou'] == 2) ? "☑" : "☐")."身体無）", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x4, $y1);
                $pdf->Cell(21, $h, ((isset($data['service']) && in_array(10, $data['service'])) ? "☑" : "☐")."移動支援〔", 'T', 0, 'L');
                $pdf->Cell(54, $h, (($data['service_idou'] == 1) ? "☑" : "☐")."伴う〕", 'T', 1, 'L');
                        
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x3, $y1);
                $pdf->Cell(29, $h, ((isset($data['service']) && in_array(11, $data['service'])) ? "☑" : "☐")."自費  ・その他〔", 'TB', 0, 'L');
                $pdf->Cell(46, $h, $data['service_sonota'], 'TB', 0, 'L');
                $pdf->Cell(5, $h, "〕", 'TB', 1, 'L');
                
                $pdf->SetLineStyle(array('dash' => 2));
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x2, $y1);
                $pdf->Cell(18, $h, "移動距離〔", 0, 0, 'L');
                $pdf->Cell(11, $h, $data['koutsuuhi'][0], 0, 0, 'L');
                $pdf->Cell(16, $h, "km〕", 'R', 0, 'L');
                $pdf->Cell(45, $h, "行先", 0, 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x2, $y1);
                $pdf->Cell(27, $h, "交通費  ・送迎〔", 0, 0, 'L');
                $pdf->Cell(11, $h, ($data['koutsuuhi'][1] ? number_format($data['koutsuuhi'][1]) : ""), 0, 0, 'L');
                $pdf->Cell(7, $h, "円〕", 'R', 1, 'L');
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x2, $y1);
                $pdf->Cell(14, $h, "／請求（", 0, 0, 'L');
                $pdf->Cell(31, $h, (($data['koutsuuhi'][2] == 1) ? "☑" : "☐")."済  ・".(($data['koutsuuhi'][2] == 2) ? "☑" : "☐")."未）", 'R', 1, 'L');
                
                $pdf->SetLineStyle(array('dash' => false));
                
                $y1 = $pdf->GetY();
                
                $pdf->SetXY($x2+45, $y1-$h*2);
                $pdf->MultiCell(40, $h*2, $data['koutsuuhi'][3], 0, 'L');
                
                $pdf->SetXY($x2, $y1);
                $pdf->Cell(85, $h, "【記録  ・連絡事項】", 0, 2, 'L');
                $pdf->MultiCell(85, $h*8, $message, 0, 'L');
                
                $y1 = $y1 + $h * 9;
                
                $pdf->SetXY($x2, $y1);
                $pdf->Cell(37, $h, "確　　　認", 0, 2, 'C', 1);
                $pdf->Cell(37, $h, ((isset($data['kakunin']) && in_array(0, $data['kakunin'])) ? "☑" : "☐")."火　元　".((isset($data['kakunin']) && in_array(1, $data['kakunin'])) ? "☑" : "☐")."電　気", 0, 2, 'C');
                $pdf->Cell(37, $h, ((isset($data['kakunin']) && in_array(2, $data['kakunin'])) ? "☑" : "☐")."水　道　".((isset($data['kakunin']) && in_array(3, $data['kakunin'])) ? "☑" : "☐")."戸　締", 0, 2, 'C');
                
                $pdf->SetFontSize(8);
                
                $pdf->SetXY($x2+37, $y1);
                $pdf->Cell(16, 4, "", 1, 0, 'C');
                $pdf->Cell(16, 4, "責　任　者", 1, 0, 'C');
                $pdf->Cell(16, 4, "ご利用者印", 1, 1, 'C');
                
                $y1 = $pdf->GetY();
                $pdf->SetXY($x2+37, $y1);
                $pdf->Cell(16, $h*3-4, "", 1, 0, 'C');
                $pdf->Cell(16, $h*3-4, "", 1, 0, 'C');
                $pdf->Cell(16, $h*3-4, "", 1, 1, 'C');
                
                $pdf->SetFontSize(12);
                
                $pdf->SetXY($x0, $y0);
                $pdf->SetLineWidth(0.3);
                $pdf->Cell(140, $h*5, "", 'LTR', 0, 'C');
                $pdf->Cell(80, $h*3, "", 1, 2, 'C');
                $pdf->Cell(80, $h*2, "", 1, 1, 'C');
                $pdf->Cell(135, $h*12, "", 'LR', 0, 'C');
                $pdf->Cell(85, $h*12, "", 1, 1, 'C');
                $pdf->Cell(135, $h*9, "", 'LR', 0, 'C');
                $pdf->Cell(85, $h*9, "", 1, 1, 'C');
                $pdf->Cell(135, $h*3, "", 'LR', 0, 'C');
                $pdf->Cell(37, $h*3, "", 1, 0, 'C');
                $pdf->Cell(48, $h*3, "", 1, 1, 'C');
                $pdf->Cell(135, $h, "", 'LRB', 0, 'C');
                $pdf->Cell(85, 6, $company, 0, 1, 'R');
            }
        }
    }
}

ob_end_clean();
$pdf->Output();
?>