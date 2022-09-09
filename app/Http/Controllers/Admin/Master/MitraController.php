<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Model\CWS_MASTER_MITRA;
use App\Model\CWS_MASTER_MITRA_USER;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Image;

class MitraController extends Controller
{
    var $maxRow = 5; 
    public function index()
    {
        $data['max_row'] = $this->maxRow;
        return view('master.partner.v_master_partner', $data);
    }

    public function searchData(Request $request)
    {
        $rs_data =  CWS_MASTER_MITRA::whereNotNull('nama_perusahaan')->where('is_deleted','!=',1)->with('pic');
        
        if ($request->has('text_search')) {
            
            $textSearch = $request->text_search;
            $rs_data->where(function($q) use ($textSearch) {
                $q->where('nama_perusahaan','LIKE', '%'.$textSearch.'%')
                ->orWhere('corporation_code', 'LIKE', '%'.$textSearch.'%')
                ->orWhere('kuota', 'LIKE', '%'.$textSearch.'%');
            });
            
        }
        
        if ($request->has('id')) {
            $rs_data->where('id', $request->id);
            $mitra_user = CWS_MASTER_MITRA_USER::where([
                ['is_deleted','!=', 1],
                ['id_mitra','=', $request->id],
            ])->select('nama','id')->get();
            $data['mitra_user'] = $mitra_user;
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

        $arrColumn = ['nama_perusahaan','kuota', 'corporation_code'];
        
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
        $validate = Validator::make($request->all(), 
        [
            'nama_perusahaan' => 'required',
            'nama_brand' => 'required',
            'logo' => 'required|image|mimes:jpg,bmp,png',
            'corporation_code' => 'required|max:10|unique:CWS_master_mitra,corporation_code',
            'kuota' => 'required|numeric|digits_between:1,3',
        ],
        [
            'required' => 'Field is required !',
            'logo.required' => 'Please upload a photo !',
            'numeric' => 'Only number allowed!',
            'max' => 'Maximal 10 character !',
            'digits_between' => 'Maximal 999',
            'mimes'  => 'Please use format photo : jpg,png,bmp !',
            'image'  => 'Only photo file allowed !',
            'unique'  => 'Corporation code has been used for another company !',
        ]);

        if ($validate->fails()) {
            $errorArr = json_decode($validate->errors());//$validator->messages();
            $errorStr ='';

            foreach ($errorArr as $item) {
                $errorStr .= $item[0].'<br>';
            }

            return \redirect()->back()->with('error_message',$errorStr);
        }
        
        $fileLogo = $request->file('logo');
        $path = public_path('/upload/img/mitra/');
        $namaPerusahaan =  \str_replace([' '],'_',$request->nama_perusahaan);
        $namaFile = $namaPerusahaan."_".date('YnjGis').'.'.$fileLogo->getClientOriginalExtension();
        // thumbnail logo
        $logo = Image::make($fileLogo->path());
        $logo->resize(100, 100, function ($constraint) {
            $constraint->aspectRatio();
        })->save($path.'thumbnail/'.$namaFile);

        // logo full
        $fileLogo->move($path.'full/', $namaFile);

        // Store data
        $mitra = CWS_MASTER_MITRA::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'nama_brand' => $request->nama_brand,
            'corporation_code' => $request->corporation_code,
            'kuota' => $request->kuota,
            'logo' => $namaFile,
            'is_deleted' => 0,
            'created_by' => Auth::user()->email,
        ]);
        $mitra->save();

        return \redirect()->back()->with('success_message','Success insert data !');
    }

    
    public function update(Request $request)
    {
        // validasi input
        $validate = Validator::make($request->all(), 
        [
            'nama_perusahaan' => 'required',
            'nama_brand' => 'required',
            'logo' => 'image|mimes:jpg,bmp,png',
            'corporation_code' => 'required|max:10',
            'kuota' => 'required|numeric|digits_between:1,3',
        ],
        [
            'required' => 'Field is required !',
            'numeric' => 'Only number allowed!',
            'max' => 'Maximal 10 character !',
            'digits_between' => 'Maximal 999',
            'mimes'  => 'Please use format photo : jpg,png,bmp !',
            'image'  => 'Only photo file allowed !',
            'unique'  => 'Corporation code has been used for another company !',
        ]);

        if ($validate->fails()) {
            $errorArr = json_decode($validate->errors());//$validator->messages();
            $errorStr ='';

            foreach ($errorArr as $item) {
                $errorStr .= $item[0];
            }

            return \redirect()->back()->with('error_message',$errorStr);
        }

        // find data by id
        $id = $request->id;
        $item = CWS_MASTER_MITRA::find($id);
        // cek apakah ada update logo
        if ($request->hasFile('logo')) {
            $foto_full = 'upload/img/mitra/full/'.$item->logo;
            $foto_thumbnail = 'upload/img/mitra/thumbnail/'.$item->logo;
            if (is_file($foto_full)) {
                unlink($foto_full);
            }
            if (is_file($foto_thumbnail)) {
                unlink($foto_thumbnail);
            }

            $namaPerusahaan =  \str_replace([' '],'_',$request->nama_perusahaan);
            $fileLogo = $request->file('logo');
            $namaFile = $namaPerusahaan."_".date('YnjGis').'.'.$fileLogo->getClientOriginalExtension();
            // dd($namaFile);
            $path = public_path('/upload/img/mitra/');
            // thumbnail logo
            $logo = Image::make($fileLogo->path());
            $logo->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path.'thumbnail/'.$namaFile);

            // logo full
            $fileLogo->move($path.'full/', $namaFile);
            
            // update nama logo di DB
            $item->update(['logo' =>$namaFile]);
        }
        
        $item->update(['updated_by' => Auth::user()->email]);
        $item->update([
            'nama_perusahaan' => $request->nama_perusahaan,
            'nama_brand' => $request->nama_brand,
            'corporation_code' => $request->corporation_code,
            'kuota' => $request->kuota,
            'pic' => $request->pic,
        ]);


       return \redirect()->back()->with('success_message','Success edit data !');
    }

    
    public function destroy(Request $request)
    {
        $id = $request->id;
        $item = CWS_MASTER_MITRA::find($id);
        $item->update([
            'is_deleted' => 1,
            'deleted_by' => Auth::user()->email,
        ]);

        http_response_code(200);
        exit(json_encode(['Message' => 'Success delete data']));
    }
}
