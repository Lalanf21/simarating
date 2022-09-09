<?php

namespace App\Http\Controllers\Pengguna\Booking;

use App\Http\Controllers\Controller;
use App\Mail\SuksesBookingEmail;
use App\Model\CWS_CONFIG;
use App\Model\CWS_MASTER_ADDON_ROOM;
use App\Model\CWS_MASTER_MITRA;
use App\Model\CWS_MASTER_MITRA_USER;
use App\Model\CWS_TRANSAKSI_ADDON_ROOM;
use App\Model\CWS_TRANSAKSI_HEAD;
use App\Model\CWS_TRANSAKSI_MITRA_USER;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class BookingController extends Controller
{
    var $maxRow = 5;
    public function index()
    {
        $user = CWS_MASTER_MITRA_USER::where('email', Auth::user()->email)->first();
        
        $data['partner'] = CWS_MASTER_MITRA_USER::with('mitra')
            ->where([
                ['id_mitra', '=', $user->id_mitra],
                ['email', '!=', Auth::user()->email],
                ['is_deleted','!=',1]
            ])
            ->get();
            
        $kuota = CWS_MASTER_MITRA::where('id', $user->id_mitra)->select('kuota')->first()->kuota;
            
        $seatTerpakai = DB::table('CWS_transaksi_mitra_user as a')
                        ->join('CWS_master_mitra_user as b', 'b.id', 'a.id_mitra_user')
                        ->where('b.id_mitra','=', $user->id_mitra)
                        ->whereMonth('a.booking_date', '=', date('m'))
                        ->where('a.is_deleted','!=',1)
                        ->count();
        
        if ($seatTerpakai > $kuota) {
            return \redirect()->route('dashboard')->with('booking', 'This month reservation credit has run out !');
        }
        // data all event / schedule
        $user = CWS_MASTER_MITRA_USER::where('email', Auth::user()->email)->first();
        
        $allData = DB::table('CWS_transaksi_mitra_user as a')
                        ->select(
                             'a.booking_date','b.id as id_transaksi_head','b.transaksi_no'
                            )
                         ->join('CWS_transaksi_head as b','a.id_transaksi_head','b.id')
                         ->where('a.id_mitra_user', $user->id)
                         ->where('a.is_deleted','!=',1)
                        ->get();

        $arrData = [];
        if ($allData) {
            foreach ($allData as $value ) {
                $arrData[] = [
                    'id' => $value->id_transaksi_head,
                    'title' => $value->transaksi_no,
                    'start' => $value->booking_date,
                    'color' => $this->randomColor(\rand(0,50)),
                ];
            }
        }
        $data['allSchedule'] = $arrData;
        // end data all event / schedule
        $data['addon'] = CWS_MASTER_ADDON_ROOM::where('status', 1)->orderBy('nama', 'asc')->get();
        return \view('booking.formAdd', $data);
    }

    public function randomColor($n){
        $n = crc32($n);
        $n &= 0xffffffff;
        return ("#".substr("000000".dechex($n),-6));
    }
    

    public function confirmation(Request $request)
    {
        // dd($request->all());
        $validate = Validator::make($request->all(), 
        [
            'tanggal_booking' => 'required',
            'shift' => 'required',
            'jumlah_seat' => 'required|numeric|min:1',
            'addon' => 'array',
        ],
        [
            'required' => 'Field is required !',
            'numeric' => 'Only number is allowed !',
            'addon.array' => 'Please select an add-on first!',
            'min' => 'Minimal 1 seat !',
        ]);
       
        // validasi input
        if ($validate->fails()) {
           return \redirect()->back()->withErrors($validate);
        }
        
        $id_mitra_user = CWS_MASTER_MITRA_USER::where('email', Auth::user()->email)->first()->id;

         // init variabel invite
        $arrInvite[] = [
            'id' => $id_mitra_user,
            'nama_user' => Auth::user()->nama,
            'email' => Auth::user()->email,
        ];
        
       // init variabel arrayAddon room
        $arrAddon[] = [
            'nama_addon' => 'No add-on',
            'id' => '-',
            'start_time' => '',
            'end_time' => '',
        ];
        
        if (!empty($request->invite)) {
            
            // validasi orang yang di undang tidak lebih dari total seat
            if ( count($request->invite) > ($request->jumlah_seat)-1 ) {
                return \redirect()->back()->with('error_message', 'The number of people is more than the number of seats !');
            }

            // validasi jika seat lebih banyak dari orang yang di ibnvite
            if ( count($request->invite) < ($request->jumlah_seat)-1 ) {
                return \redirect()->back()->with('error_message', 'The number of people is less than the number of seats !');
            }
            
            // validasi jika seat lebih dari satu namun tidak invite orang
            if ( count($request->invite) < ($request->jumlah_seat)-1 ) {
                return \redirect()->back()->with('error_message', 'The number of people is less than the number of seats ! !');
            }

            // validasi jika user book dengan addon dan tidak memilih jam
            if ($request->isAddon != null) {
                if ($request->start_time == null && $request->end_time == null) {
                    return redirect()->back()->with('error_message', 'Start and end hours must be filled in !');
                }
                
                // perulangan untuk validasi start time dan end time dari addon room
                foreach ($request->start_time as $key => $start) {
                    if (\strtotime($start) == \strtotime($request->end_time[$key]) ) {
                        return redirect()->back()->with('error_message', 'Start and finish hours can not be same');
                    }
                    if (\strtotime($start) > \strtotime($request->end_time[$key]) ) {
                        return redirect()->back()->with('error_message', 'Start hour cannot be greater than the finish hour !');
                    }
                }
                
                // unset variabel arrAddon dan ganti dengan yang baru
                unset($arrAddon);
                
                // Jika validasi start time dan end time berhasil, maka timpa variabel arrayAddon
                 foreach ($request->addon as $index => $addon) {
                    $arrAddon[] = [
                        'nama_addon' => CWS_MASTER_ADDON_ROOM::where('id',$addon[0])->select('nama')->first()->nama,
                        'id' => $addon[0],
                        'start_time' => $request->start_time[$index],
                        'end_time' => $request->end_time[$index],
                    ];
                }
            }

            // foreach untuk menimpa variabel arrayInvite jika user memilih seat lebih dari 1
            foreach ($request->invite as $value) {
                $rs_mitra_invited = CWS_MASTER_MITRA_USER::where('id', $value)->select('nama','email','id')->first();
                $arrInvite[] = [
                    'id' => $rs_mitra_invited->id,
                    'nama_user' => $rs_mitra_invited->nama,
                    'email' => $rs_mitra_invited->email,
                ];
            }
        }

        // init semua variabel untuk di lembar ke view confirmation
        $data['user_invite'] =  $arrInvite;
        $data['addons'] = $arrAddon;
        $data['shift'] = $request->shift;
        $data['isAddon'] = $request->isAddon;
        $data['tanggal'] = $request->tanggal_booking;
        $data['seat'] = $request->jumlah_seat;

        // set session
        $request->session()->put('data_booking',$data);
        return \view('booking.confirmation');

    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), 
        [
            'booking_date' => 'required',
            'user_invited' => 'required',
        ],
        [
            'required' => 'Failed confirm reservation !',
        ]);
       
        // validasi input
        if ($validate->fails()) {
           return \redirect()->route('user-booking')->with('error_message','Failed confirm reservation');
        }

       // unserialize user invited
       $usersInvited = \unserialize($request->user_invited);

       // unserialize addons
       $addons = \unserialize($request->addons);

       $data_mitra = DB::table('CWS_master_mitra_user as a')
            ->select('a.id_mitra','b.nama_perusahaan as nama_perusahaan')
            ->join('CWS_master_mitra as b', 'b.id','a.id_mitra')
            ->where('a.email', Auth::user()->email)
            ->where('a.is_deleted','!=', 1)
            ->first();

        $isAddon = $request->isAddon;
        $iteration = 1;
        $transaksi_no = \set_transaksi_no();
        $transaksi_date = date('Y-m-d');
        $booking_date = $request->booking_date;
        $created_by = Auth::user()->email;

        // hardcode Shift
        $shift = $request->shift;
        if ($shift == 1) {
            $booking_start_time = '08:00';
            $booking_end_time = '13:00';
        }else{
            $booking_start_time = '13:00';
            $booking_end_time = '18:00';
        }
        
        // start transaction
        DB::beginTransaction();
        try {
            //store data to  master mitra user
            $header = CWS_TRANSAKSI_HEAD::create([
                'transaksi_no' => $transaksi_no,
                'transaksi_date' => $transaksi_date,
                'booking_date' => $booking_date,
                'id_mitra' => $data_mitra->id_mitra,
                'start_time' => $booking_start_time,
                'end_time' => $booking_end_time,
                'is_deleted' => 0,
                'created_by' => $created_by,
            ]);
            $header->save();

            if ($isAddon != null) {
                foreach ($addons as $addon) {
                    $addon_room = CWS_TRANSAKSI_ADDON_ROOM::create([
                        'id_transaksi_head' => $header->id,
                        'id_ruang_addon' => $addon['id'],
                        'start_time' => $addon['start_time'],
                        'end_time' => $addon['end_time'],
                        'is_deleted' => 0,
                        'created_by' => $created_by,
                    ]);
                }
            }
            
            // column status 0 = not check in, 1 = check in, 2 = expired
            foreach ($usersInvited as $user) {
                $qrCodeString = $transaksi_no.$iteration;
                $qrCodeStringHTML = URL::to('/')."/api/reservation/".$qrCodeString;

                $mitra_user_invite = CWS_TRANSAKSI_MITRA_USER::create([
                    'id_transaksi_head' => $header->id,
                    'id_mitra_user' => $user['id'],
                    'qr_code_string' => $qrCodeString,
                    'booking_date' => $booking_date,
                    'status' => 0,
                    'is_deleted' => 0,
                    'created_by' => $created_by,
                ]);
                $mitra_user_invite->save();
                
                // create qrcode
                $qrCode = QrCode::format('png')->generate($qrCodeStringHTML);
                $output = '/img/qr-codes/'.$qrCodeString.'/qrcode.png';
                Storage::disk('public')->put($output, $qrCode);

                // send email
                $data = [
                    'booking_date' => $booking_date,
                    'nama' => $user['nama_user'],
                    'nama_perusahaan' => $data_mitra->nama_perusahaan,
                    'no_transaksi' => $transaksi_no,
                    'qrCode' => $qrCodeString,
                ];
                // Mail::to($user['email'])->send(new SuksesBookingEmail($data));
                
                $iteration++;
            }
            // unset session
            $request->session()->forget('data_booking');

            // commit DB
            DB::commit();
            return \redirect()->route('success_booking', $transaksi_no)->with('booking','Success !');
        } catch (\Throwable $th) {
            DB::rollback();
            return \redirect()->route('user-booking')->with('error_message', $th);
        }
    }

    public function riwayat_booking()
    {
        $id_mitra_user = CWS_MASTER_MITRA_USER::where('email', Auth::user()->email)->first()->id;

        $allData = DB::table('CWS_transaksi_mitra_user as a')
                         ->select(
                             'a.booking_date','b.id as id_transaksi_head','b.transaksi_no'
                            )
                         ->join('CWS_transaksi_head as b','a.id_transaksi_head','b.id')
                         ->where('a.id_mitra_user', $id_mitra_user)
                         ->where('a.is_deleted','!=',1)
                         ->get();
        $arrData = [];
        if ($allData) {
            foreach ($allData as $value ) {
                $arrData[] = [
                    'id' => $value->id_transaksi_head,
                    'title' => $value->transaksi_no,
                    'start' => $value->booking_date,
                    'color' => $this->randomColor(\rand(0,50)),
                ];
            }
        }

        $data['allSchedule'] = $arrData;
        return view('booking.v_riwayat_booking', $data);
    }

    public function search_riwayat_booking(Request $request)
    {
        $user = CWS_MASTER_MITRA_USER::where('email', Auth::user()->email)->first();
        
        $data['header'] = DB::table('CWS_transaksi_head as a')
            ->select(
                'a.id','a.transaksi_no','a.start_time','a.end_time',
                'a.booking_date','b.nama_perusahaan as nama_perusahaan',
            )
            ->join('CWS_master_mitra as b', 'a.id_mitra', '=', 'b.id')
            ->where('a.id','=', $request->id)
            ->where('a.id_mitra', '=', $user->id_mitra)
            ->where('a.is_deleted','!=', 1)
            ->first();
           
        if(isset($data['header']))
        {
            $data['userInvited'] = DB::table('CWS_transaksi_mitra_user as a')
                ->select('b.nama as nama_mitra','b.email','a.qr_code_string')
                ->join('CWS_master_mitra_user as b', 'b.id', 'a.id_mitra_user')
                ->where('a.id_transaksi_head','=', $data['header']->id)
                ->where('a.is_deleted','!=', 1)
                ->get();
    
            $data['addons'] = DB::table('CWS_transaksi_ruang_addon as a')
                ->select('a.start_time','a.end_time','b.nama as nama_addon')
                ->join('CWS_master_ruang_addon as b', 'b.id', 'a.id_ruang_addon')
                ->where('id_transaksi_head','=', $data['header']->id)
                ->where('a.is_deleted','!=', 1)
                ->get();
    
            $data['qrCode'] = DB::table('CWS_transaksi_mitra_user')
                ->where([
                    ['id_transaksi_head','=',$data['header']->id],
                    ['id_mitra_user','=', $user->id],
                    ['is_deleted','!=', 1],
                ])->first()->qr_code_string;
        }

        return json_encode($data);
        
    }

    public function success_booking($transaksi_no)
    {
        $id_mitra_user = CWS_MASTER_MITRA_USER::where('email', Auth::user()->email)->first();

        $data['header'] = DB::table('CWS_transaksi_head as a')
            ->select(
                'a.id','a.transaksi_no','a.start_time','a.end_time',
                'a.booking_date','b.nama_perusahaan as nama_perusahaan',
            )
            ->join('CWS_master_mitra as b', 'a.id_mitra', '=', 'b.id')
            ->where('a.transaksi_no','=', $transaksi_no)
            ->where('a.is_deleted','!=', 1)
            ->first();
        
        $data['userInvited'] = DB::table('CWS_transaksi_mitra_user as a')
            ->select('b.nama as nama_mitra','b.email','a.qr_code_string')
            ->join('CWS_master_mitra_user as b', 'b.id', 'a.id_mitra_user')
            ->where('a.id_transaksi_head','=', $data['header']->id)
            ->where('a.is_deleted','!=', 1)
            ->get();

        $data['addons'] = DB::table('CWS_transaksi_ruang_addon as a')
            ->select('a.start_time','a.end_time','b.nama as nama_addon')
            ->join('CWS_master_ruang_addon as b', 'b.id', 'a.id_ruang_addon')
            ->where('id_transaksi_head','=', $data['header']->id)
            ->where('a.is_deleted','!=', 1)
            ->get();

        $data['qrCode'] = DB::table('CWS_transaksi_mitra_user')
            ->where([
                ['id_transaksi_head','=',$data['header']->id],
                ['id_mitra_user','=', $id_mitra_user->id],
                ['is_deleted','!=', 1],
            ])->first()->qr_code_string;
          
        return view('booking.success',$data);
    }

    public function cekAllData(Request $request)
    {
        $query = DB::table('CWS_transaksi_mitra_user as a')
            ->join('CWS_transaksi_head as b','a.id_transaksi_head','b.id')
            ->whereDate('a.booking_date', $request->booking_date)
            ->where('a.is_deleted','!=', 1);
        
        if ($request->shift === '1') {
            $usedSeat = $query->where('b.start_time','=', '08:00')->count();
        }else{
            $usedSeat = $query->where('b.start_time','=', '13:00')->count();
        }
        $setting = CWS_CONFIG::first();
        if (!$setting) {
            http_response_code(405);
            exit(json_encode(['Message' => 'Kapasitas belum di atur !']));
        }else{
            $data['sisa_seat'] = $setting->capacity - $usedSeat;
        }

        return json_encode($data);
    }

    public function getAddonTime(Request $request)
    {
        if ($request->shift == 1) {
            $time = [
                '08:00',
                '09:00',
                '10:00',
                '11:00',
                '12:00',
                '13:00',
            ];
        }else{
            $time = [
                '13:00',
                '14:00',
                '15:00',
                '16:00',
                '17:00',
                '18:00',
            ];
        }
        
        $query = DB::table('CWS_transaksi_ruang_addon as a')
            ->join('CWS_transaksi_head as b', 'a.id_transaksi_head','b.id')
            ->where('a.id_ruang_addon', $request->id_addon)
            ->whereDate('b.booking_date','=', $request->booking_date);
        $start_time = \str_replace(':00', '' , $query->pluck('a.start_time')->toArray());
        $end_time = \str_replace(':00', '', $query->pluck('a.end_time')->toArray());
        if( $start_time != null ){
            $arr = \array_merge($start_time, $end_time);
            $dataRange = range($arr[0], \max($arr));
            $newData = [];
            foreach($dataRange as $data){
                if ($data== '8'|| $data =='9') {
                    $newData[] = '0'.$data.':00';
                }else{
                    $newData[] = $data.':00';
                }
            }
            array_pop($newData);
            $data = $newData;
        }else{
            $data = [];
        }

        return response()->json([
            'time' => $time,
            'data' => $data,
        ]);
    }
}
