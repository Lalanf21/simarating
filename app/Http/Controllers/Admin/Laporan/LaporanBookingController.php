<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Model\CWS_MASTER_MITRA;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use DB;

class LaporanBookingController extends Controller
{
    var $maxRow = 10; 
    public function index()
    {
        $data['mitra'] = CWS_MASTER_MITRA::get();
        $data['max_row'] = $this->maxRow;
        return view('laporan.booking.v_laporan', $data);
    }

    public function exportExcel(Request $request)
    {
        $data =  DB::table('CWS_TRANSAKSI_HEAD as a')
            ->select(
                'a.id','a.transaksi_no','a.booking_date','b.nama as nama_perusahaan'
            )
            ->join('CWS_master_mitra as b', 'a.id_mitra', '=', 'b.id')
            ->where('a.is_deleted','!=',1);
                
         if ($request->hide_text_search) {
             $textSearch = $request->hide_text_search;
             $data->where(function($q) use ($textSearch) {
                 $q->where('a.transaksi_no','LIKE', '%'.$textSearch.'%')
                 ->orWhere('e.nama', 'LIKE', '%'.$textSearch.'%');
             });
             
         }
         
         // search dropdown asal perusahaan  
         if ($request->hide_asal_perusahaan_search) {
             $data->where('a.id_mitra', $request->hide_asal_perusahaan_search);
         }

        //  search tanggal
         if ($request->hide_tanggal) {
             $data->whereDate('a.booking_date', $request->hide_tanggal);
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
          ->setTitle("Laporan booking");

          $objPHPExcel
          ->setActiveSheetIndex(0)
                ->setCellValue('A1', 'No transaksi')
                ->setCellValue('B1', 'Tanggal booking')                
                ->setCellValue('C1', 'Asal perusahaan');
        
          $no = 2;

          foreach ($results as $result) {
              $transaksi_no     = $result->transaksi_no;
              $booking_date     = $result->booking_date;
              $nama_perusahaan  = $result->nama_perusahaan;

              $objPHPExcel
                ->setActiveSheetIndex(0)
                ->setCellValue('A'.$no, $transaksi_no)
                ->setCellValue('B'.$no, $booking_date)
                ->setCellValue('C'.$no, $nama_perusahaan);
                $no++;
          }

          foreach(range('A','C') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
          }
          
          $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, "Xlsx");
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          if ($request->hide_tanggal) {
              header('Content-Disposition: attachment; filename="Laporan booking_'. date_id($request->hide_tanggal) .'.xlsx"');
          }else{
              header('Content-Disposition: attachment; filename="Laporan booking.xlsx"');
          }
          $writer->save("php://output");
 
    }
}
