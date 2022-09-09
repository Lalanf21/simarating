<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Model\CWS_MASTER_MITRA;
use App\Model\CWS_MASTER_MITRA_USER;
use App\Model\CWS_USERS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Hash;

class UserCoWorkingController extends Controller
{
    var $maxRow = 10; 
    public function index()
    {
        $data['mitra'] = CWS_MASTER_MITRA::where('is_deleted','!=',1)->get();
        $data['max_row'] = $this->maxRow;
        return view('master.co_working.v_master_user_co_working', $data);
    }

    public function searchData(Request $request)
    {
         $rs_data =  CWS_MASTER_MITRA_USER::whereNotNull('nama')->where('is_deleted','!=',1)->with('mitra');
             
         if ($request->has('text_search')) {
             
             $textSearch = $request->text_search;
             $rs_data->where(function($q) use ($textSearch) {
                 $q->where('nama','LIKE', '%'.$textSearch.'%')
                 ->orWhere('email', 'LIKE', '%'.$textSearch.'%')
                 ->orWhere('no_hp', 'LIKE', '%'.$textSearch.'%');
             });
             
         }
         
        //  untuk edit
         if ($request->has('id')) {
             $rs_data->where('id', $request->id);
         }
        
         // search dropdown asal perusahaan  
         if ($request->id_asal_perusahaan) {
             $rs_data->where('id_mitra', $request->id_asal_perusahaan);
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
 
         $arrColumn = ['nama','email','no_hp'];
         
         if($orderSearch < count($arrColumn)) $order = $arrColumn[$orderSearch];
         else $order = $arrColumn[0];
 
         $qrData = $rs_data->orderBy($order, $sortSearch)->paginate($this->maxRow);
         
         $data['rs_data'] = $qrData;
         $data['pagination'] =  (string) $qrData->links();
 
         return json_encode($data);
    }

    
    public function store(Request $request)
    {
        // validasi input
        $validate = Validator::make($request->except('corporate_code'), 
        [
            'nama' => 'required',
            'asal_perusahaan' => 'required',
            'email' => 'required|email|unique:CWS_master_mitra_user,email',
            'no_hp' => 'required|numeric|digits_between:1,15',
        ],
        [
            'required' => 'Required !',
            'digits_between' => 'Max 15 char !',
            'numeric' => 'Only number allowed !',
            'email' => 'Not valid email !',
            'unique' => 'Email has been used for another user!',
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
        DB::beginTransaction();
        try {
            //store data to  master mitra user
            $mitra = CWS_MASTER_MITRA_USER::create([
                'id_mitra' => $request->asal_perusahaan,
                'nama' => $request->nama,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'is_deleted' => 0,
                'created_by' => Auth::user()->email,
            ]);
            $mitra->save();
            
            $user = CWS_USERS::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'level' => 2,
                'status' => 1,
                'password' => Hash::make('123456'),
                'is_deleted' => 0,
                'is_email_verified' => true,
                'created_by' => Auth::user()->email,
            ]);
            $user->save();

            DB::commit();
            return \redirect()->back()->with('success_message','Success insert data !');
            
            
            
        } catch (\Exception $th) {
            DB::rollback();
            return \redirect()->back()->with('error_message','Fail insert data, Something went wrong !');
        }

    }
    
    public function update(Request $request)
    {
        $validate = Validator::make($request->except('corporate_code'), 
        [
            'nama' => 'required',
            'asal_perusahaan' => 'required',
            'email' => 'required|email',
            'no_hp' => 'required|numeric|digits_between:1,15',
        ],
        [
            'required' => 'Field :attribute wajib di isi !',
            'digits_between' => 'Field :attribute maksimal 15 karakter !',
            'numeric' => 'Field :attribute harus angka !',
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
        $item = CWS_MASTER_MITRA_USER::find($id);
        $item->update([
            'id_mitra' => $request->asal_perusahaan,
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'updated_by' => Auth::user()->email,
        ]);

        return \redirect()->back()->with('success_message','Success edit data !');
    }

    
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            // softDelete data mitra di table 
            $email = $request->email;
            $mitraUser = CWS_MASTER_MITRA_USER::where('email', $email);
            $mitraUser->update([
                'is_deleted' => 1,
                'deleted_by' => Auth::user()->email,
            ]);

            // softDelet akun 
            $akunUser = CWS_USERS::where('email', $email);
            $akunUser->update([
                'is_deleted' => 1,
                'deleted_by' => Auth::user()->email,
            ]);
            
            DB::commit();
            http_response_code(200);
            exit(json_encode(['Message' => 'Success']));
            
        } catch (\Exception $th) {
            DB::rollback();
            http_response_code(405);
            exit(json_encode(['Message' => 'Gagal hapus data !']));
        }

    }

    public function getCorporateCode(Request $request)
    {
        
        $data = CWS_MASTER_MITRA::where('id', $request->id)->first();
        return json_encode($data);
    }
}
