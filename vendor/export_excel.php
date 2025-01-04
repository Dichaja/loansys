<?php
/*

Include the PHPExcel library
require_once 'PHPExcel.php';

// Create a new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set the active sheet
$objPHPExcel->setActiveSheetIndex(0);

// Define your data
$data = array(
    array('Name', 'Email', 'Phone'),
    array('John Doe', 'johndoe@example.com', '1234567890'),
    array('Jane Smith', 'janesmith@example.com', '0987654321'),
);

// Loop through the data and add it to the worksheet
$row = 1;
foreach ($data as $rowData) {
    $col = 0;
    foreach ($rowData as $cellData) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $cellData);
        $col++;
    }
    $row++;
}

// Set the appropriate headers for Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="data.xlsx"');
header('Cache-Control: max-age=0');

// Save the Excel file to the output
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
*/

require 'autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello');
$sheet->setCellValue('B1', 'World');

$writer = new Xlsx($spreadsheet);
$writer->save('output.xlsx');


?>