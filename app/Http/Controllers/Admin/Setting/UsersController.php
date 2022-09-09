<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Model\CWS_MASTER_MITRA_USER;
use App\Model\CWS_USERS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;

class UsersController extends Controller
{
    var $maxRow = 10; 
    public function index()
    {
        $data['max_row'] = $this->maxRow;
        return view('setting.setting_users', $data);
    }

    public function searchData(Request $request)
    {
         $rs_data =  CWS_USERS::whereNotNull('nama')->where('is_deleted','!=',1);
             
         if ($request->has('text_search')) {
             
             $textSearch = $request->text_search;
             $rs_data->where(function($q) use ($textSearch) {
                 $q->where('nama','LIKE', '%'.$textSearch.'%')
                 ->orWhere('email', 'LIKE', '%'.$textSearch.'%')
                 ->orWhere('no_hp', 'LIKE', '%'.$textSearch.'%')
                 ->orWhere('level', 'LIKE', '%'.$textSearch.'%');
             });
             
         }
         
        //  untuk edit
         if ($request->has('id')) {
             $rs_data->where('id', $request->id);
         }

         // search dropdown asal perusahaan  
         if ($request->levelSearch) {
            $rs_data->where('level', $request->levelSearch);
        }
 
         if ($request->has('page')) {
             $page = $request->page;
             $rs_data->offset(($page * $this->maxRow ) - $this->maxRow);
         }
 
         //order_search sort_search
         $orderSearch = 0;
         if ($request->has('order_search')) {
             $orderSearch = $request->order_search;
         }
         
         $sortSearch = 'asc';
         if ($request->has('sort_search')) {
             $sortSearch = $request->sort_search;
         }
 
         $arrColumn = ['nama','email','nik','level'];
         
         if($orderSearch < count($arrColumn)) $order = $arrColumn[$orderSearch];
         else $order = $arrColumn[0];
 
         $qrData = $rs_data->orderBy($order, $sortSearch)->paginate($this->maxRow);
         
         $data['rs_data'] = $qrData;
         $data['pagination'] =  (string) $qrData->links();
 
         return json_encode($data);
    }
    
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), 
        [
            'nama' => 'required',
            'email' => 'required|email',
            'level' => 'required',
            'status' => 'required',
            'no_hp' => 'required|numeric|digits_between:1,15',
        ],
        [
            'required' => 'Field :attribute wajib di isi !',
            'digits_between' => 'Field :attribute maksimal 15 karakter !',
            'email' => 'Field :attribute tidak valid !',
            'numeric' => 'Field :attribute tidak valid !',
        ]);
    
        if ($validate->fails()) {
            $errorArr = json_decode($validate->errors());//$validator->messages();
            $errorStr ='';

            foreach ($errorArr as $item) {
                $errorStr .= '<div>'.$item[0].'</div>';
            }

            return \redirect()->back()->with('error_message',$errorStr);
        }
        
        // Store data
        $user = CWS_USERS::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'level' => $request->level,
            'status' => $request->status,
            'password' => Hash::make('123456'),
            'created_by' => Auth::user()->email,
            'is_deleted' => 0,
        ]);
        $user->save();

        return \redirect()->back()->with('success_message','Success insert data !');
    }

    
    public function update(Request $request)
    {
        $validate = Validator::make($request->all(), 
        [
            'nama' => 'required',
            'email' => 'required|email',
            'level' => 'required',
            'status' => 'required',
            'no_hp' => 'required|digits_between:1,15|numeric',
            'numeric' => 'Field :attribute tidak valid !',
        ],
        [
            'required' => 'Field :attribute wajib di isi !',
            'digits_between' => 'Field :attribute maksimal 15 karakter !',
            'email' => 'Field :attribute tidak valid !',
        ]);
    
        if ($validate->fails()) {
            $errorArr = json_decode($validate->errors());//$validator->messages();
            $errorStr ='';

            foreach ($errorArr as $item) {
                $errorStr .= '<div>'.$item[0].'</div>';
            }

            return \redirect()->back()->with('error_message',$errorStr);
        }
        
        // Store data
        $id = $request->id;
        $item = CWS_USERS::find($id);
        $item->update(['updated_by' => Auth::user()->email]);
        $item->update($request->all());

        return \redirect()->back()->with('success_message','Success edit data !');
    }

    
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $email = $request->email;
            
            // softDelet akun 
            $akunUser = CWS_USERS::where('email', $email);
            $akunUser->update([
                'is_deleted' => 1,
                'status' => 0,
                'deleted_by' => Auth::user()->email,
            ]);
            
            // Jika data ada di table mitra user, maka hapus
            $mitraUser = CWS_MASTER_MITRA_USER::where('email', $email);
            if ($mitraUser) {
                $mitraUser->update([
                    'is_deleted' => 1,
                    'deleted_by' => Auth::user()->email,
                ]);
            }
            
            DB::commit();

            http_response_code(200);
            exit(json_encode(['Message' => 'Success']));
            
        } catch (\Exception $th) {
            DB::rollback();
            http_response_code(405);
            exit(json_encode(['Message' => 'Failed delete data, Something went wrong !']));
        }
    }

    public function proses_ubah_password(Request $request)
    {
        $request->validate([
            'passLama' => 'required',
            'password' => 'required|confirmed',
        ],
        [
            'required' => 'Required !',
            'confirmed' => 'Password not same !'
        ]);

        $user = CWS_USERS::where('email', '=', Auth::user()->email)->first();
        $cekPassword = Hash::check($request->passLama, $user->password);

        if ($cekPassword) {
           $user->update([
               'password'=> Hash::make($request->password)
           ]);
           return \redirect()->back()->with('success_message','Success change password !');
        }else{
            return \redirect()->back()->with('error_message','Current password is wrong !');
        }
    }

    public function view_profile()
    {
        $user = DB::table('CWS_master_mitra_user as a')
            ->select('b.nama_perusahaan as nama_perusahaan','a.nama','a.no_hp','a.email')
            ->join('CWS_master_mitra as b','b.id','a.id_mitra')
            ->where('a.email', Auth::user()->email)
            ->first();
        return view('setting.v_profile', \compact('user'));
    }
}
