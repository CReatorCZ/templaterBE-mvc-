<?php

namespace App\Classes;


use Nette\Application\Responses\FileResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportDatabase
{
    public function exportDatabase(): void{
//        $spreadsheet = new Spreadsheet();
//        $activeWorksheet = $spreadsheet->getActiveSheet();
//        $activeWorksheet->setCellValue('A1', 'Hello World !');
//
//        $writer = new Xlsx($spreadsheet);
//        $filePath = __DIR__.'/hello_world.xlsx';
//        $writer->save($filePath);
//
//        $this->sendResponse(new FileResponse($filePath));

    }

}