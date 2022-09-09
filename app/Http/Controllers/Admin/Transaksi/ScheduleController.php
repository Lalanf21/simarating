<?php

namespace App\Http\Controllers\Admin\Transaksi;

use App\Http\Controllers\Controller;
use App\Mail\SuksesBookingEmail;
use App\Model\CWS_MASTER_MITRA;
use App\Model\CWS_MASTER_ADDON_ROOM;
use App\Model\CWS_MASTER_MITRA_USER;
use App\Model\CWS_TRANSAKSI_ADDON_ROOM;
use App\Model\CWS_TRANSAKSI_HEAD;
use App\Model\CWS_TRANSAKSI_MITRA_USER;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Mail;
use DB;
use Illuminate\Support\Facades\URL;

class ScheduleController extends Controller
{
    var $maxRow = 10; 
    public function index()
    {
        $data['max_row'] = $this->maxRow;
        $data['mitra'] = CWS_MASTER_MITRA::get();
        return view('transaksi.v_schedule', $data);
    }

    public function randomColor($n){
        $n = crc32($n);
        $n &= 0xffffffff;
        return ("#".substr("000000".dechex($n),-6));
    }

   
    public function searchData(Request $request)
    {
         $rs_data =  DB::table('CWS_transaksi_head as a')
            ->select(
                'a.id','a.transaksi_no','a.booking_date','e.nama_perusahaan as nama_perusahaan'
            )
            ->join('CWS_master_mitra as e', 'a.id_mitra', '=', 'e.id')
            ->where('a.is_deleted','!=',1);
                
         if ($request->text_search) {
             $textSearch = $request->text_search;
             $rs_data->where(function($q) use ($textSearch) {
                 $q->where('a.transaksi_no','LIKE', '%'.$textSearch.'%')
                 ->orWhere('e.nama', 'LIKE', '%'.$textSearch.'%');
             });
             
         }
         
        //  untuk edit
         if ($request->has('id')) {
             $rs_data->where('id', $request->id);
         }
        
         // search dropdown asal perusahaan  
         if ($request->id_asal_perusahaan) {
             $rs_data->where('a.id_mitra', $request->id_asal_perusahaan);
         }

         if ($request->tanggal) {
             $rs_data->whereDate('a.booking_date', $request->tanggal);
         }
 
         if ($request->has('page')) {
             $page = $request->page;
             $rs_data->offset(($page * $this->maxRow ) - $this->maxRow);
         }
 
         //order_search sort_search
         $orderSearch = 0;
         if ($request->order_search) {
             $orderSearch = $request->order_search;
         }
         
         $sortSearch = 'asc';
         if ($request->sort_search) {
             $sortSearch = $request->sort_search;
         }
 
         $arrColumn = ['a.transaksi_no'];
         
         if($orderSearch < count($arrColumn)) $order = $arrColumn[$orderSearch];
         else $order = $arrColumn[0];
 
         $qrData = $rs_data->orderBy($order, $sortSearch)->paginate($this->maxRow);
         
         $data['rs_data'] = $qrData;
         $data['pagination'] =  (string) $qrData->links();
 
         return json_encode($data);
    }

    public function detailDataSchedule(Request $request)
    {
        $data['header'] = DB::table('CWS_transaksi_head as a')
            ->select(
                'a.id','a.transaksi_no','a.start_time','a.end_time',
                'a.booking_date','b.nama_perusahaan as nama_perusahaan',
            )
            ->join('CWS_master_mitra as b', 'a.id_mitra', '=', 'b.id')
            ->where('a.id','=', $request->id)
            ->where('a.is_deleted','!=',1)
            ->first();
        
        $data['userInvited'] = DB::table('CWS_transaksi_mitra_user as a')
            ->select('b.nama as nama_mitra','b.email')
            ->join('CWS_master_mitra_user as b', 'b.id', 'a.id_mitra_user')
            ->where('id_transaksi_head','=', $request->id)
            ->where('a.is_deleted','!=',1)
            ->get();

        $data['addons'] = DB::table('CWS_transaksi_ruang_addon as a')
            ->select('a.start_time','a.end_time','b.nama as nama_addon')
            ->join('CWS_master_ruang_addon as b', 'b.id', 'a.id_ruang_addon')
            ->where('id_transaksi_head','=', $request->id)
            ->where('a.is_deleted','!=',1)
            ->get();

        return json_encode($data);
    }

    public function create()
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

        $data['reservasi'] = $dataSchedule;
        $data['mitra'] = CWS_MASTER_MITRA_USER::with('mitra')->where('is_deleted','!=',1)->get();
        $data['addon'] = CWS_MASTER_ADDON_ROOM::where('status',1)->where('is_deleted','!=',1)->get();
        return view('transaksi.formAdd', $data);
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
           return \redirect()->route('add_schedule')->with('error_message','Failed confirm reservation');
        }
        // unserialize user invited
        $usersInvited = \unserialize($request->user_invited);

        // unserialize addons
        $addons = \unserialize($request->addons);

        $id_mitra = $request->id_mitra;
        $dataPerusahaan = CWS_MASTER_MITRA::where('id',$id_mitra)
            ->select('nama_perusahaan')
            ->where('is_deleted','!=', 1)
            ->first();
        $isAddon = $request->isAddon;
        $transaksi_no = \set_transaksi_no();
        $iteration = 1;
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
                'id_mitra' => $id_mitra,
                'start_time' => $booking_start_time,
                'end_time' => $booking_end_time,
                'is_deleted' => 0,
                'created_by' => $created_by,
            ]);
            $header->save();

            // jika ada addon di pilih
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

                //  send email
                $data = [
                    'booking_date' => $booking_date,
                    'nama' => $user['nama_user'],
                    'nama_perusahaan' => $dataPerusahaan->nama_perusahaan,
                    'no_transaksi' => $transaksi_no,
                    'qrCode' => $qrCodeString,
                ];
                // Mail::to($user['email'])->send(new SuksesBookingEmail($data));
                
                $iteration++;
            }
            // unset session data booking
            $request->session()->forget('data_booking');

            // commit DB
            DB::commit();
            return \redirect()->route('schedule')->with('success_message', 'Success create reservation');
        } catch (\Exception $th) {
            DB::rollback();
            return \redirect()->route('add_schedule')->with('error_message', $th);
        }
    }

    public function confirmation(Request $request)
    {
        $validate = Validator::make($request->all(), 
        [
            'id_mitra_user' => 'required',
            'tanggal_booking' => 'required',
            'shift' => 'required',
            'jumlah_seat' => 'required|numeric|min:1',
            'addon' => 'array',
        ],
        [
            'required' => 'Field is required !',
            'shift.required' => 'Please choose shift !',
            'numeric' => 'Only number is allowed !',
            'min' => 'Minimal 1 seat !',
            'addon.array' => 'Please select an add-on first !',
        ]);
       
        // validasi input
        if ($validate->fails()) {
           return \redirect()->back()->withErrors($validate);
        }
        
        $id_mitra_user = CWS_MASTER_MITRA_USER::where('id', $request->id_mitra_user)->first();

        // init variabel invite
        $arrInvite[] = [
            'id' => $id_mitra_user->id,
            'nama_user' => $id_mitra_user->nama,
            'email' => $id_mitra_user->email,
        ];

        // init variabel arrayAddon room
        $arrAddon[] = [
            'nama_addon' => 'No add-on',
            'id' => '-',
            'start_time' => '',
            'end_time' => '',
        ];

        // cek jika user memilih seat lebih dari satu
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
                        return redirect()->back()->with('error_message', 'Start and finish hours can not be the same !');
                    }
                    if (\strtotime($start) > \strtotime($request->end_time[$key]) ) {
                        return redirect()->back()->with('error_message', 'The start hour cannot be greater than the finish hour !');
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
        $data['isAddon'] = $request->isAddon;
        $data['shift'] = $request->shift;
        $data['id_mitra'] = $request->id_mitra;
        $data['id_mitra_user'] = $request->id_mitra_user;
        $data['tanggal'] = $request->tanggal_booking;
        $data['seat'] = $request->jumlah_seat;
        // set session
        $request->session()->put('data_booking',$data);
        return \view('transaksi.confirmation');

    }
      
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            //Delete data transakshi header
            $header = CWS_TRANSAKSI_HEAD::where('id', $request->id);
            $header->update([
                'is_deleted' => 1,
                'deleted_by' => Auth::user()->email,
            ]);
            
            // delete data transaksi mitra user
            $mitra_user = CWS_TRANSAKSI_MITRA_USER::where('id_transaksi_head', $request->id);
            $mitra_user->update([
                'is_deleted' => 1,
                'deleted_by' => Auth::user()->email,
            ]);
            
            // delete data transaksi ruang addon
            $addon = CWS_TRANSAKSI_ADDON_ROOM::where('id_transaksi_head', $request->id);
            $addon->update([
                'is_deleted' => 1,
                'deleted_by' => Auth::user()->email,
            ]);

            DB::commit();
            http_response_code(200);
            exit(json_encode(['Message' => 'Success']));
        } catch (\Exception $th) {
            DB::rollback();
            http_response_code(405);
            exit(json_encode(['Message' => 'Failed delete data !']));
        }
    }

    public function getPerusahaan(Request $request)
    {
        
        $data = CWS_MASTER_MITRA_USER::where('id', $request->id)->with('mitra')->first();
        return json_encode($data);
    }

    public function getListMitraById(Request $request)
    {
        
        $data = CWS_MASTER_MITRA_USER::with('mitra')
            ->where([
                ['id_mitra','=',$request->id_mitra],
                ['id','!=',$request->id_mitra_user],
                ['is_deleted','!=',1],
            ])
            ->get();
        return json_encode($data);
    }


    
}
