<?php
/*
    Template Name: Download excel sample
*/
require_once get_template_directory() . '/vendor/autoload.php';
//Khai báo sử dụng các thư viện cần thiết
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$filename = "khach_moi.xlsx";
//Khởi tạo đối tượng spreadsheet
$spreadsheet = new Spreadsheet();

//Lấy bảng tính thao tác
$sheet = $spreadsheet->getActiveSheet();

if (isset($_GET['type']) && ($_GET['type'] == "short" )) {

    $sheet->setCellValue('A1', 'Khách mời');
    $sheet->setCellValue('B1', 'Mời cùng');
    $sheet->setCellValue('C1', 'Cách mình gọi họ');
    $sheet->setCellValue('D1', 'Cách họ gọi mình');
    $sheet->setCellValue('E1', 'Số điện thoại');

    $sheet->setCellValue('A2', 'Anh Duy');
    $sheet->setCellValue('B2', 'gia đình');
    $sheet->setCellValue('C2', 'Anh');
    $sheet->setCellValue('D2', 'Em');
    $sheet->setCellValue('E2', '0987654321');

    $sheet->setCellValue('A3', 'Em Vân Anh');
    $sheet->setCellValue('B3', '');
    $sheet->setCellValue('C3', 'Em');
    $sheet->setCellValue('D3', 'Anh');
    $sheet->setCellValue('E3', '0987654321');

    $sheet->setCellValue('A4', 'Đức');
    $sheet->setCellValue('B4', 'người thương');
    $sheet->setCellValue('C4', 'Cậu');
    $sheet->setCellValue('D4', 'Tớ');
    $sheet->setCellValue('E4', '0987654321');
} else {
    
    $sheet->setCellValue('A1', 'Khách mời');
    $sheet->setCellValue('B1', 'Mời cùng');
    $sheet->setCellValue('C1', 'Cách mình gọi họ');
    $sheet->setCellValue('D1', 'Cách họ gọi mình');
    $sheet->setCellValue('E1', 'Số điện thoại');
    $sheet->setCellValue('F1', 'Khách của');
    $sheet->setCellValue('G1', 'Nhóm');
    
    $sheet->setCellValue('A2', 'Anh Duy');
    $sheet->setCellValue('B2', 'gia đình');
    $sheet->setCellValue('C2', 'Anh');
    $sheet->setCellValue('D2', 'Em');
    $sheet->setCellValue('E2', '0987654321');
    $sheet->setCellValue('F2', 'Nhà trai');
    $sheet->setCellValue('G2', 'Công ty cũ');
    
    $sheet->setCellValue('A3', 'Em Vân Anh');
    $sheet->setCellValue('B3', '');
    $sheet->setCellValue('C3', 'Em');
    $sheet->setCellValue('D3', 'Anh');
    $sheet->setCellValue('E3', '0987654321');
    $sheet->setCellValue('F3', 'Nhà trai');
    $sheet->setCellValue('G3', 'Công ty mới');
    
    $sheet->setCellValue('A4', 'Đức');
    $sheet->setCellValue('B4', 'người thương');
    $sheet->setCellValue('C4', 'Cậu');
    $sheet->setCellValue('D4', 'Tớ');
    $sheet->setCellValue('E4', '0987654321');
    $sheet->setCellValue('F4', 'Nhà gái');
    $sheet->setCellValue('G4', 'Bạn cấp 3');
}

$objWriter = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
$objWriter->save('php://output');
exit;
