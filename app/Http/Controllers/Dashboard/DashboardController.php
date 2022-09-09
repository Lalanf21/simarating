<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Model\CWS_MASTER_ADDON_ROOM;
use App\Model\CWS_MASTER_MITRA;
use App\Model\CWS_MASTER_MITRA_USER;
use Auth;
use DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
    public function index()
    {
        if (Auth::user()->level === '1') {
            return $this->dashboardAdmin();
        }else{
            return $this->dashboardPengguna();
        }
    }
    
    
    public function dashboardAdmin()
    {
        $schedules = DB::table('CWS_transaksi_head as a')
            ->select(db::raw('COUNT(b.id_mitra_user) as total_seat'), 'c.nama_perusahaan as nama_mitra', 'a.booking_date','a.id')
            ->join('CWS_transaksi_mitra_user as b','a.id','b.id_transaksi_head')
            ->join('CWS_master_mitra as c','c.id','a.id_mitra')
            ->groupBy('c.nama_perusahaan','a.booking_date','a.id')
            ->where('a.is_deleted','!=', 1)
            ->get();
        
        $dataSchedule = [];
        if (!empty($schedules)) {
            foreach ($schedules as $schedule ) {
                $dataSchedule[] = [
                    'id' => $schedule->id,
                    'title' => '('.$schedule->total_seat.') '.$schedule->nama_mitra,
                    'start' => $schedule->booking_date,
                    'color' => $this->randomColor(\rand(0,50)),
                ];
            }
        }

        $data['schedule'] = $dataSchedule;

        return view('dashboard.v_dashboard_admin', $data);
    }

    public function randomColor($n){
        $n = crc32($n);
        $n &= 0xffffffff;
        return ("#".substr("000000".dechex($n),-6));
    }

    public function timeTable()
    {
        $schedules = DB::table('CWS_transaksi_ruang_addon as a')
        ->select('a.id','b.booking_date','c.nama as nama_addon')
        ->join('CWS_transaksi_head as b','b.id','a.id_transaksi_head')
        ->join('CWS_master_ruang_addon as c','c.id','a.id_ruang_addon')
        ->where('a.is_deleted','!=',1)
        ->get();

        $dataSchedule = [];
        if(!empty($schedules)){
            foreach ($schedules as $schedule ) {
                $dataSchedule[] = [
                    'id' => $schedule->id,
                    'title' => $schedule->nama_addon,
                    'start' => $schedule->booking_date,
                    'color' => $this->randomColor(\rand(0,50)),
                ];
            }
        }
        $data['schedule'] = $dataSchedule;
        return view('dashboard.v_timeline_addon', $data);
    }

    public function getAddonTimeline(Request $request)
    {
        $addons = CWS_MASTER_ADDON_ROOM::where('is_deleted','!=',1)
            ->where('status', 1)
            ->select('id','nama')
            ->get();
        foreach ($addons as $addon) {
            $dataAddon[] = [
                'id' => $addon->id,
                'title' => $addon->nama,
            ];
        }
        $scheduleAddon = DB::table('CWS_transaksi_ruang_addon as a')
                         ->select(
                             'a.start_time','a.end_time','a.id_ruang_addon',
                             'b.booking_date','b.id as id_transaksi_head','c.nama_perusahaan'
                            )
                         ->join('CWS_transaksi_head as b','a.id_transaksi_head','b.id')
                         ->join('CWS_master_mitra as c','b.id_mitra','c.id')
                         ->whereDate('b.booking_date',$request->tanggal)
                         ->get();
        $dataScheduleTimeline = [];
        if ($scheduleAddon != null) {
            foreach ($scheduleAddon as $schedule) {
                $dataScheduleTimeline[] = [
                    'id' =>$schedule->id_transaksi_head,
                    'resourceId' => $schedule->id_ruang_addon,
                    'start' => $schedule->booking_date.'T'.$schedule->start_time,
                    'end' => $schedule->booking_date.'T'.$schedule->end_time,
                    'title' => $schedule->nama_perusahaan,
                    'color' => $this->randomColor(\rand(0,50)),
                ];
            }
        }
        
      
        $data['schedule_timeline'] = $dataScheduleTimeline;
        $data['addon'] = $dataAddon;
        return response()->json($data);
    }

    public function getChartBooking1(Request $req){
        $year = $req->year;
        $month = $req->month;

        if(isset($year) && isset($month)){
            //filter by month year
            $d=cal_days_in_month(CAL_GREGORIAN,$month,$year);
            $daysArr = [];
            $valArr = [];
            for($i=0;$i<$d;$i++){
                array_push($daysArr, $i+1);
                array_push($valArr, 0);
            }
            $query = DB::table('CWS_transaksi_head as a')
            ->select(
                DB::raw("day(a.booking_date) as date"),
                DB::raw("(select count(*) from CWS_transaksi_mitra_user as x where day(x.booking_date) = day(a.booking_date) and month(x.booking_date) = month(a.booking_date) and year(x.booking_date) = year(a.booking_date)) as jumlah_booking")
            )
            ->distinct()
            ->whereRaw("year(a.booking_date) = $year")
            ->whereRaw("month(a.booking_date) = $month")
            ->where('a.is_deleted','!=', 1)
            ->get();
            foreach($query as $q){
                $valArr[$q->date-1] = (int)$q->jumlah_booking;
            }
            $data['datasets'] = $valArr;
            $data['labels'] = $daysArr;
            return response()->json([
                'type' => 'date',
                'data' => $data
            ]);
        }else if(isset($year)){
            //filter by year
            $monthArr = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $valArr = [];
            for($i = 0; $i <12;$i++){
                $valArr[$i] = 0;
            }
            $query = DB::table('CWS_transaksi_head as a')
            ->select(
                DB::raw("month(a.booking_date) as month"),
                DB::raw("(select count(*) from CWS_transaksi_mitra_user as x where month(x.booking_date) = month(a.booking_date) and year(x.booking_date) = year(a.booking_date)) as jumlah_booking")
            )
            ->distinct()
            ->whereRaw("year(a.booking_date) = $year")
            ->where('a.is_deleted','!=', 1)
            ->get();
            foreach($query as $q){
                $valArr[$q->month-1] = (int)$q->jumlah_booking;
            }
            $data['datasets'] = $valArr;
            $data['labels'] = $monthArr;
            return response()->json([
                'type' => 'month',
                'data' => $data
            ]);
        }else{
            //no filter
            $yearArr = [];
            $valArr = [];
            $query = DB::table('CWS_transaksi_head as a')
            ->select(
                DB::raw("year(a.booking_date) as year"),
                DB::raw("(select count(*) from CWS_transaksi_mitra_user as x where year(x.booking_date) = year(a.booking_date)) as jumlah_booking")
            )
            ->distinct()
            ->get();
            foreach($query as $q){
                // $yearArr[$q->year] = (int)$q->jumlah_booking;
                array_push($yearArr, $q->year);
                array_push($valArr, (int)$q->jumlah_booking);
            }
            $data['datasets'] = $valArr;
            $data['labels'] = $yearArr;
            return response()->json([
                'type' => 'year',
                'data' => $data
            ]);
        }
    }

    public function getChartBooking2(Request $req){
        $year = $req->year;
        $month = $req->month;

        if(isset($year) && isset($month)){
            //filter by month year
            $d=cal_days_in_month(CAL_GREGORIAN,$month,$year);
            $daysArr = [];
            $valArr = [];
            for($i=0;$i<$d;$i++){
                array_push($daysArr, $i+1);
            }
            $mitra = [];
            $registeredMitra = [];
            $dataset = [];
            $query = DB::table('CWS_transaksi_head as a')
            ->select(
                DB::raw("day(a.booking_date) as date"),
                DB::raw("(select count(*) from CWS_transaksi_mitra_user as x where day(x.booking_date) = day(a.booking_date) and month(x.booking_date) = month(a.booking_date) and year(x.booking_date) = year(a.booking_date) and x.id_mitra = b.id) as jumlah_booking")
                , 'b.nama', 'b.id'
            )
            ->join('CWS_master_mitra as b', 'a.id_mitra', 'b.id')
            ->distinct()
            ->whereRaw("year(a.booking_date) = $year")
            ->whereRaw("month(a.booking_date) = $month")
            ->get();
            foreach($query as $q){
                if(!in_array($q->nama, $registeredMitra)){
                    array_push($mitra, [
                        'nama'=>$q->nama,
                        'id' => $q->id
                    ]);
                    $dataset[$q->id] = [];
                    $dataset[$q->id] = array_pad($dataset[$q->id], count($daysArr), 0);
                    array_push($registeredMitra, $q->nama);
                }
                $dataset[$q->id][$q->date-1] = (int)$q->jumlah_booking;
            }
            foreach($mitra as $m){
                array_push($valArr, [
                    "label"=>$m['nama'],
                    "data"=>$dataset[$m['id']],
                    "borderColor"=>$this->randomColor($m['id']),
                    'yAxisID'=>'y'
                ]);
            }
            $data['datasets'] = $valArr;
            $data['labels'] = $daysArr;
            return response()->json([
                'type' => 'date',
                'data' => $data
            ]);
        }else if(isset($year)){
            //filter by year
            $monthArr = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $valArr = [];
            $mitra = [];
            $registeredMitra = [];
            $dataset = [];
            // for($i = 0; $i <12;$i++){
            //     $valArr[$i] = 0;
            // }
            $query = DB::table('CWS_transaksi_head as a')
            ->select(
                DB::raw("month(a.booking_date) as month"),
                DB::raw("(select count(*) from CWS_transaksi_mitra_user as x where month(x.booking_date) = month(a.booking_date) and year(x.booking_date) = year(a.booking_date) and x.id_mitra = b.id) as jumlah_booking")
                , 'b.nama', 'b.id'
            )
            ->join('CWS_master_mitra as b', 'a.id_mitra', 'b.id')
            ->distinct()
            ->whereRaw("year(a.booking_date) = $year")
            ->get();
            foreach($query as $q){
                if(!in_array($q->nama, $registeredMitra)){
                    array_push($mitra, [
                        'nama'=>$q->nama,
                        'id' => $q->id
                    ]);
                    $dataset[$q->id] = [];
                    $dataset[$q->id] = array_pad($dataset[$q->id], count($monthArr), 0);
                    array_push($registeredMitra, $q->nama);
                }
                $dataset[$q->id][$q->month-1] = (int)$q->jumlah_booking;
            }
            foreach($mitra as $m){
                array_push($valArr, [
                    "label"=>$m['nama'],
                    "data"=>$dataset[$m['id']],
                    "borderColor"=>$this->randomColor($m['id']),
                    'yAxisID'=>'y'
                ]);
            }
            $data['datasets'] = $valArr;
            $data['labels'] = $monthArr;
            return response()->json([
                'type' => 'month',
                'data' => $data
            ]);
        }else{
            //no filter
            $yearArr = [];
            $valArr = [];
            $mitra = [];
            $registeredMitra = [];
            $dataset = [];
            $query = DB::table('CWS_transaksi_head as a')
            ->select(
                DB::raw("year(a.booking_date) as year"),
                DB::raw("(select count(*) from CWS_transaksi_mitra_user as x where year(x.booking_date) = year(a.booking_date) and x.id_mitra = b.id) as jumlah_booking")
                , 'b.nama', 'b.id'
            )
            ->join('CWS_master_mitra as b', 'a.id_mitra', 'b.id')
            ->distinct()
            ->get();
            foreach($query as $q){
                // $yearArr[$q->year] = (int)$q->jumlah_booking;
                if(!in_array($q->year, $yearArr)){
                    array_push($yearArr, $q->year);
                }
            }
            foreach($query as $q){
                if(!in_array($q->nama, $registeredMitra)){
                    array_push($mitra, [
                        'nama'=>$q->nama,
                        'id' => $q->id
                    ]);
                    $dataset[$q->id] = [];
                    $dataset[$q->id] = array_pad($dataset[$q->id], count($yearArr), 0);
                    array_push($registeredMitra, $q->nama);
                }
                $dataset[$q->id][array_search($q->year, $yearArr)] = (int)$q->jumlah_booking;
            }
            foreach($mitra as $m){
                array_push($valArr, [
                    "label"=>$m['nama'],
                    "data"=>$dataset[$m['id']],
                    "borderColor"=>$this->randomColor($m['id']),
                    'yAxisID'=>'y'
                ]);
            }

            // array_push($valArr, (int)$q->jumlah_booking);

            $data['datasets'] = $valArr;
            $data['labels'] = $yearArr;
            return response()->json([
                'type' => 'year',
                'data' => $data
            ]);
        }
    }

    public function getChartKuotaTerpakai(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $data = db::table('CWS_master_mitra as a')
            ->select(db::raw('COUNT(c.id_mitra_user) as total_seat'),'a.nama_perusahaan as nama_mitra','a.kuota')
            ->join('CWS_master_mitra_user as b','a.id','b.id_mitra')
            ->leftJoin('CWS_transaksi_mitra_user as c','c.id_mitra_user','b.id')
            ->where('a.is_deleted','!=', 1);

        if (isset($year) && isset($month)) {
            $data = $data->whereYear('c.booking_date',$year);
            $data = $data->whereMonth('c.booking_date',$month);
            $data = $data->groupBy('a.nama_perusahaan','a.kuota')->get();
        }else if(isset($year)){
            $data = $data->whereYear('c.booking_date',$year);
            $data = $data->groupBy('a.nama_perusahaan','a.kuota')->get();
        }else if(isset($month)){
            $data =$data->whereMonth('c.booking_date',$month);
            $data = $data->groupBy('a.nama_perusahaan','a.kuota')->get();
        }else{
            $data = [];
        }

       return response()->json([
            'data' => $data
        ]);
    }
    
    
    public function dashboardPengguna()
    {
        $user = CWS_MASTER_MITRA_USER::where('email', Auth::user()->email)->first();
        
        $data['bookings'] = DB::table('CWS_transaksi_mitra_user as a')
                ->select('a.booking_date','a.qr_code_string','b.transaksi_no','b.id')
                ->join('CWS_transaksi_head as b', 'b.id', 'a.id_transaksi_head')
                ->where('a.id_mitra_user', $user->id)
                ->where('a.is_deleted','!=', 1)
                ->where('a.status','=', 0)
                ->whereDate('a.booking_date','>=', date('Y-m-d'))
                ->orderBy('a.booking_date','asc')
                ->limit(5)
                ->get();
        
        $data['terpakai'] = DB::table('CWS_transaksi_mitra_user as a')
                ->join('CWS_master_mitra_user as b', 'b.id', 'a.id_mitra_user')
                ->where('b.id_mitra','=', $user->id_mitra)
                ->whereMonth('a.booking_date', '=', date('m'))
                ->where('a.is_deleted','!=',1)
                ->count();

                
        $data['kuota'] = CWS_MASTER_MITRA::where('id', $user->id_mitra)->select('kuota')->first()->kuota;

        return view('dashboard.v_dashboard_pengguna', $data);
    }

    
    
}
