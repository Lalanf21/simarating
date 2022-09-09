<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Model\CWS_CONFIG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class KapasitasController extends Controller
{
    public function index()
    {
        $data['kapasitas'] = 0;
        $cek = CWS_CONFIG::first();
        if ($cek != null) {
            $data['kapasitas'] = $cek->capacity;
            $data['id'] = $cek->id;
        }
        return view('setting.setting_kapasitas',$data);
    }

   
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), 
        [
            'kapasitas' => 'required|numeric',
        ],
        [
            'required' => 'Field is required !',
            'numeric' => 'Only number is allowed !',
        ]);
    
        if ($validate->fails()) {
            $errorArr = json_decode($validate->errors());//$validator->messages();
            $errorStr ='';

            foreach ($errorArr as $item) {
                $errorStr .= '<div>'.$item[0].'</div>';
            }

            http_response_code(405);
            exit(json_encode(['Message' => $errorStr]));
        }
        
        // Store data
        try {
            $id = $request->id;
            $item = CWS_CONFIG::find($id);
            if($item != null){
                $item->update([
                    'capacity' => $request->kapasitas,
                    'updated_by' => Auth::user()->email,
                ]);
            }else{
                CWS_CONFIG::create([
                    'capacity' => $request->kapasitas,
                    'created_by' => Auth::user()->email,
                ])->save();
            }
    
            Session::flash('success_message', 'Success update capacity !');
            return redirect()->back();
        } catch (\Exception $ex) {
            Session::flash('error_message', 'Something went wr0ng !');
            return redirect()->back();
        }
    }

   
    public function show($id)
    {
        //
    }

   
    public function edit($id)
    {
        //
    }

    
    public function update(Request $request, $id)
    {
        //
    }

    
    public function destroy($id)
    {
        //
    }
}
