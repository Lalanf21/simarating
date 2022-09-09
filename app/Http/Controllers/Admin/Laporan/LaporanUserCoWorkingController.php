<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Model\CWS_MASTER_MITRA;
use App\Model\CWS_MASTER_MITRA_USER;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class LaporanUserCoWorkingController extends Controller
{
    var $maxRow = 10; 
    public function index()
    {
        $data['mitra'] = CWS_MASTER_MITRA::get();
        $data['max_row'] = $this->maxRow;
        return view('laporan.user_co_working.v_laporan', $data);
    }

    public function exportExcel(Request $request)
    {
        $data =  CWS_MASTER_MITRA_USER::whereNotNull('nama')->where('is_deleted','!=',1)->with('mitra');
             
         if ($request->has('hide_txt_search')) {
             
             $textSearch = $request->hide_txt_search;
             $data->where(function($q) use ($textSearch) {
                 $q->where('nama','LIKE', '%'.$textSearch.'%')
                 ->orWhere('email', 'LIKE', '%'.$textSearch.'%')
                 ->orWhere('no_hp', 'LIKE', '%'.$textSearch.'%');
             });
             
         }
         
         // search dropdown asal perusahaan  
         if ($request->hide_asal_perusahaan_search) {
             $data->where('id_mitra', $request->hide_asal_perusahaan_search);
         }
 
         $results = $data->get();

         error_reporting(E_ALL);
         ini_set('display_errors', TRUE);
         ini_set('display_startup_errors', TRUE);
         date_default_timezone_set('Asia/Jakarta');
  
         // Create new Spreadsheet object
         $objPHPExcel = new Spreadsheet();

         $objPHPExcel
          ->getProperties()->setCreator("Pradita Partner Lounge")
          ->setTitle("Laporan user co-working");

          $objPHPExcel
          ->setActiveSheetIndex(0)
                ->setCellValue('A1', 'Nama')
                ->setCellValue('B1', 'Asal perusahaan')
                ->setCellValue('C1', 'No hp')                
                ->setCellValue('D1', 'Email');
        
          $no = 2;

          foreach ($results as $result) {
              $nama             = $result->nama;
              $email            = $result->email;
              $no_hp            = $result->no_hp;
              $asal_perusahan   = $result->mitra->nama;

              $objPHPExcel
                ->setActiveSheetIndex(0)
                ->setCellValue('A'.$no, $nama)
                ->setCellValue('B'.$no, $asal_perusahan)
                ->setCellValue('C'.$no, $no_hp)
                ->setCellValue('D'.$no, $email);
                $no++;
          }

          foreach(range('A','D') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
          }
          
          $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, "Xlsx");
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment; filename="Laporan user co-working.xlsx"');
          $writer->save("php://output");
 
    }
}
