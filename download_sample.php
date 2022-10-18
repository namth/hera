<?php
/*
    Template Name: Download excel sample
*/
require_once get_template_directory() . '/lib/PHPExcel.php';
require_once get_template_directory() . '/lib/PHPExcel/Writer/Excel2007.php';

$filename = "Danh sách khách mời.xlsx";
$objPHPExcel = new PHPExcel();

if (isset($_GET['type']) && ($_GET['type'] == "short" )) {
    $objPHPExcel->getProperties()->setCreator("QLCV")
    ->setLastModifiedBy("QLCV")
    ->setTitle("Mẫu danh sách khách mời")
    ->setSubject("Danh sách mẫu")
    ->setDescription("Mẫu danh sách khách mời để tạo thiệp mời online HERA")
    ->setKeywords("Hera thiệp cưới")
    ->setCategory("Example");

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Khách mời')
    ->setCellValue('B1', 'Mời cùng')
    ->setCellValue('C1', 'Cách mình gọi họ')
    ->setCellValue('D1', 'Cách họ gọi mình')
    ->setCellValue('E1', 'Số điện thoại');

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A2', 'Anh Duy')
    ->setCellValue('B2', 'gia đình')
    ->setCellValue('C2', 'Anh')
    ->setCellValue('D2', 'Em')
    ->setCellValue('E2', '0987654321');

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A3', 'Em Vân Anh')
    ->setCellValue('B3', '')
    ->setCellValue('C3', 'Em')
    ->setCellValue('D3', 'Anh')
    ->setCellValue('E3', '0987654321');

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A4', 'Đức')
    ->setCellValue('B4', 'người thương')
    ->setCellValue('C4', 'Cậu')
    ->setCellValue('D4', 'Tớ')
    ->setCellValue('E4', '0987654321');
} else {
    $objPHPExcel->getProperties()->setCreator("QLCV")
        ->setLastModifiedBy("QLCV")
        ->setTitle("Mẫu danh sách khách mời")
        ->setSubject("Danh sách mẫu")
        ->setDescription("Mẫu danh sách khách mời để tạo thiệp mời online HERA")
        ->setKeywords("Hera thiệp cưới")
        ->setCategory("Example");
    
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Khách mời')
        ->setCellValue('B1', 'Mời cùng')
        ->setCellValue('C1', 'Cách mình gọi họ')
        ->setCellValue('D1', 'Cách họ gọi mình')
        ->setCellValue('E1', 'Số điện thoại')
        ->setCellValue('F1', 'Khách của')
        ->setCellValue('G1', 'Nhóm');
    
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', 'Anh Duy')
        ->setCellValue('B2', 'gia đình')
        ->setCellValue('C2', 'Anh')
        ->setCellValue('D2', 'Em')
        ->setCellValue('E2', '0987654321')
        ->setCellValue('F2', 'Nhà trai')
        ->setCellValue('G2', 'Công ty cũ');
    
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A3', 'Em Vân Anh')
        ->setCellValue('B3', '')
        ->setCellValue('C3', 'Em')
        ->setCellValue('D3', 'Anh')
        ->setCellValue('E3', '0987654321')
        ->setCellValue('F3', 'Nhà trai')
        ->setCellValue('G3', 'Công ty mới');
    
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A4', 'Đức')
        ->setCellValue('B4', 'người thương')
        ->setCellValue('C4', 'Cậu')
        ->setCellValue('D4', 'Tớ')
        ->setCellValue('E4', '0987654321')
        ->setCellValue('F4', 'Nhà gái')
        ->setCellValue('G4', 'Bạn cấp 3');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

