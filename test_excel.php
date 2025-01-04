<?php

//require 'PhpSpreadsheet/src/Bootstrap.php';
require 'PhpSpreadsheet/src/PhpSpreadsheet/Spreadsheet.php';
require 'PhpSpreadsheet/src/PhpSpreadsheet/Writer/Xlsx.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set some properties for the Excel file
$spreadsheet->getProperties()->setCreator('Your Name')
                             ->setLastModifiedBy('Your Name')
                             ->setTitle('Excel Export')
                             ->setSubject('Exporting Data to Excel')
                             ->setDescription('Example Excel export')
                             ->setKeywords('excel export data');

// Add data to the cells
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello');
$sheet->setCellValue('B1', 'World');

// Save the Excel file
$writer = new Xlsx($spreadsheet);
$writer->save('output.xlsx');

?>