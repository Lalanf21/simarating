<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Model\CWS_MASTER_ADDON_ROOM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddonController extends Controller
{
    var $maxRow = 15; 
    public function index()
    {
        $data['max_row'] = $this->maxRow;
        return view('master.addon.v_master_addon', $data);
    }

   
   public function searchData(Request $request)
   {
        $rs_data =  CWS_MASTER_ADDON_ROOM::whereNotNull('nama')->where('is_deleted','!=',1);
            
        if ($request->has('text_search')) {
            
            $textSearch = $request->text_search;
            $rs_data->where(function($q) use ($textSearch) {
                $q->where('nama','LIKE', '%'.$textSearch.'%')
                ->orWhere('status', 'LIKE', '%'.$textSearch.'%');
            });
            
        }
        
        if ($request->has('id')) {
            $rs_data->where('id', $request->id);
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

        $arrColumn = ['nama','status'];
        
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
            'nama' => 'required',
            'status' => 'required',
        ],
        [
            'required' => 'Field is required!',
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
        $addon = CWS_MASTER_ADDON_ROOM::create([
            'nama' => $request->nama,
            'status' => $request->status,
            'is_deleted' => 0,
            'created_by' => Auth::user()->email,
        ]);
        $addon->save();
        
        return \redirect()->back()->with('success_message','Success insert data !');
    }

       
    public function update(Request $request)
    {
        // validasi input
        $validate = Validator::make($request->all(), 
        [
            'nama' => 'required',
            'status' => 'required',
        ],
        [
            'required' => 'Field is required !',
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
        $item = CWS_MASTER_ADDON_ROOM::find($id);
        $item->update(['updated_by' => Auth::user()->email]);
        $item->update($request->all());

        http_response_code(200);
        return \redirect()->back()->with('success_message','Success edit data !');
    }

    
    public function destroy(Request $request)
    {
        $id = $request->id;
        $item = CWS_MASTER_ADDON_ROOM::find($id);
        $item->update([
            'is_deleted' => 1,
            'deleted_by' => Auth::user()->email,
        ]);

        http_response_code(200);
        exit(json_encode(['Message' => 'Success']));
    }
}
