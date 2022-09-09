<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifikasiEmail;
use App\Model\CWS_MASTER_MITRA;
use App\Model\CWS_MASTER_MITRA_USER;
use App\Model\CWS_USERS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function proses_login(Request $request)
    {
        $request->validate( 
        [
            'no_hp' => 'required|numeric',
            'password' => 'required',
        ],
        [
            'required' => 'required !',
            'numeric' => 'Only number is allowed !',
        ]);
        
        $no_hp = $request->no_hp;
        $password = $request->password;
        
        if (Auth::attempt(['no_hp' => $no_hp, 'password' => $password,'status'=>1, 'is_deleted'=>0])) 
        {
            $user = DB::table('CWS_master_mitra_user as a')
            ->select('b.nama_perusahaan')
            ->join('CWS_master_mitra as b','b.id','a.id_mitra')
            ->where('email',Auth::user()->email)
            ->first();
            if ($user) {
                $request->session()->put('nama_perusahaan',$user->nama_perusahaan);
            }else{
                $request->session()->put('nama_perusahaan','PT. Summarecon');
            }
            return \redirect()->intended('/dashboard');
        }else{
            Session::flash('alert_type', 'warning');
            Session::flash('alert_message', 'Wrong email / password !');
            return \redirect()->back();
        }
    }

    public function proses_register(Request $request)
    {
        $request->validate( 
        [
            'nama' => 'required',
            'no_hp' => 'required|numeric|digits_between:1,15',
            'asal_perusahaan' => 'required',
            'email' => 'required|email|unique:CWS_users,email',
            'password' => 'required|confirmed',
        ],
        [
            'required' => 'Required !',
            'digits_between' => 'Mobile numbercan not be more than 12!',
            'numeric' => 'Only number is allowed !',
            'email' => 'Email not valid !',
            'confirmed' => 'Password not same !'
        ]);

        if($request->syaratKetentuan != 1) {
            Session::flash('alert_type', 'warning');
            Session::flash('alert_message', 'You have not agreed to the terms and conditions !');
            return \redirect()->route('register');
        }

        $perusahaan = $request->asal_perusahaan;
        $cc = $request->corporate_code;
        $token = Str::random(64);
        $check = CWS_MASTER_MITRA::where([
            ['corporation_code','=',$cc],
            ['id','=',$perusahaan],
        ])->first();

        if ($check) {
            DB::beginTransaction();
            try {
                //store data to  master mitra user
                $mitra_user = CWS_MASTER_MITRA_USER::create([
                    'id_mitra' => $request->asal_perusahaan,
                    'nama' => $request->nama,
                    'email' => $request->email,
                    'no_hp' => $request->no_hp,
                    'created_by' =>'Generate by register system',
                    'is_deleted' =>'0',
                ]);
                $mitra_user->save();
                
                //Store data account users
                $users = CWS_USERS::create([
                    'nama' => $request->nama,
                    'email' => $request->email,
                    'no_hp' => $request->no_hp,
                    'level' => 2,
                    'status' => 1,
                    'password' => Hash::make($request->password),
                    'is_deleted' =>'0',
                    'token' => $token,
                    'created_by' =>'Generate by register system',
                ]);
                $users->save();
                
                // send email verifikasi
                $data = [
                    'token' => $token,
                    'nama' => $request->nama,
                ];
                Mail::to($request->email)->send(new VerifikasiEmail($data));

                DB::commit();
                Session::flash('alert_type', 'success');
                Session::flash('alert_message', 'Success register, Please check your email to activate the account !');
                return \redirect()->route('login');
                
            } catch (\Throwable $th) {
                DB::rollback();
                Session::flash('alert-type', 'warning');
                Session::flash('alert_message', 'Register failed !');
                return \redirect()->route('login');
            }
        }else{
            Session::flash('alert_type', 'warning');
            Session::flash('alert_message', 'Corporate code not found !');
            return \redirect()->route('register');
        }
    }

    public function getMitraPerusahaan()
    {
        $data = CWS_MASTER_MITRA::where('is_deleted','!=',1)
        ->select('nama_brand','id','logo')
        ->get();
        return json_encode($data);
    }

    public function verifikasi(Request $request, $token)
    {
        $verifikasi = CWS_USERS::where('token', $token)->first();
  
        if(!is_null($verifikasi) ){
            // update table verifikasi
            $verifikasi->update([
                'is_email_verified' => true,
                'token' => null,
            ]);
           
            // redirect langsung login setelah verifikasi berhasil
            Auth::login($verifikasi);

            // get nama perusahaan
            $user = DB::table('CWS_master_mitra_user as a')
                ->select('b.nama_perusahaan')
                ->join('CWS_master_mitra as b','b.id','a.id_mitra')
                ->where('email',Auth::user()->email)
                ->first();

            // set session nama perusahaan
            if ($user) {
                $request->session()->put('nama_perusahaan',$user->nama_perusahaan);
            }else{
                $request->session()->put('nama_perusahaan','PT. Summarecon');
            }

            // message 
            Session::flash('alert_type', 'info');
            Session::flash('alert_message', 'Your account has been activated successfully !');
            return \redirect()->intended('/dashboard');
        }else{
            Session::flash('alert_type', 'info');
            Session::flash('alert_message', 'The account has been activated or the token is no longer valid !');
            return \redirect()->route('login');
        }

       

       
        
    }

    public function logout(Request $request)
    {
        Auth::logout();
		$request->session()->flush();
		return redirect('/');
    }
}
