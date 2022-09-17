<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;

use Cookie;
use UserApi;
use JWTAuth;
use GlobalHelper;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        if (!empty(session('authenticated'))) {
            if (Cache::has(session('authenticated')))
                return redirect($this->redirectTo);
        }

        return view('auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        //
        if ($request->ajax()) {
            return response()->json(['success' => true, 'token' => csrf_token()], 200);
        }
    }

    protected function attemptLogin(Request $request)
    {

        // dd($request->all());

        $email = $request->input('email');
        // if(strpos($username,'@') !== false)
        // {
        //     $username_explode = explode("@",$username);
        //     $username = $username_explode[0];
        // }

        $apiData = array(
            'endpoint' => 'v' . config('app.api_ver') . '/login',
            'form_params' => array(
                'email' => $email,
                'password' => $request->input('password')
            )
        );

        // dd($apiData);

        // echo '<pre>';
        // print_r($apiData);
        // echo '</pre>';

        if (!empty($request->remember)) {
            $apiData['form_params']['remember'] = 1;
            // $apiData['form_params']['expired_in'] = 120;
        }

        $userApi = UserApi::postData($apiData);
        $responses = json_decode($userApi);

        // dd($responses);

        // $login = true;
        if (!empty($responses->code) && $responses->code == 200 && !empty($responses->data->token)) {
            // dd($responses->data->token);

            $token = $responses->data->token;
            Log::info($token);

            $encrypt = GlobalHelper::encodeJWT($token);

            $token = str_replace('Bearer ', '', $token);
            $set_token = JWTAuth::setToken($token)->user();
            $apy = JWTAuth::getPayload($set_token)->toArray();
            // dd($apy);
            // $request->request->add(['user_role' => $apy['rl']]);

            // if(!GlobalHelper::userRole($request, 'superadmin'))
            // {

            //     // dd($request->all());
            //     $getData = array(
            //         'endpoint' => '/profile',
            //         'form_params' => array(),
            //         'headers' => [ 'Authorization' => 'Bearer '.$token ]
            //     );

            //     // dd($apiData);

            //     // // echo '<pre>';
            //     // // print_r($apiData);
            //     // // echo '</pre>';

            //     $userApi = UserApi::getData($getData);
            //     $userDecode = json_decode($userApi);

            //     if(!empty($userDecode->code) && $userDecode->code == 200)
            //     {
            //         $user = $userDecode->data->user;

            //         // dd($user->roles);

            //         if($user->roles[0] == 'customer' || $user->status != 1)
            //         {
            //             $request->message = 'Anda tidak memiliki izin untuk mengakses halaman ini!';
            //             $this->sendFailedLoginResponse($request);
            //             return false;
            //         }

            //         $request->session()->put('company', json_decode(json_encode($user->company), true));
            //         if(!empty($user->companies));
            //             $request->session()->put('companies', json_decode(json_encode($user->companies), true));
            //         if(!empty($user) && !empty($user->jurnal_access_token) && !empty($user->jurnal_access_token->access_token))
            //             $request->session()->put('jurnal_token', $user->jurnal_access_token->access_token);
            //         if(!empty($user) && !empty($user->warehouses))
            //         {
            //             $request->session()->put('warehouses', array_column($user->warehouses, 'id'));
            //             // $getWarehouse = false;
            //         }
            //     }
            //     else
            //     {
            //         $request->message = $userDecode->message;
            //         $this->sendFailedLoginResponse($request);
            //         return false;
            //     }
            // }
            // else
            // {
            //     $postParam = array(
            //         'endpoint'  => '/companies',
            //         'form_params' => array(
            //             'sort_by' => 'id',
            //             'sort' => 'asc',
            //         ),
            //         'headers' => [ 'Authorization' => 'Bearer '.$token ]
            //     );

            //     $posApi = UserApi::postData( $postParam );
            //     $companiesDecode = json_decode($posApi, true);
            //     // dd($companiesDecode);

            //     if(!empty($companiesDecode['code']) && $companiesDecode['code'] == 200)
            //     {
            //         $companies = $companiesDecode['data']['companies'];
            //         if(!empty($companies));
            //             $request->session()->put('companies', $companies);
            //     }
            //     $request->session()->put('company', ['id' => 1]);
            // }         


            // dd('stop'); 

            // dd($apy);

            $time = $apy['sub'] . '_' . time();

            $expiry = (20 * 60);
            if (!empty($request->remember)) {
                Log::info('remember me');
                Cache::forever($time, $encrypt);
            } else
                // Cache::store('redis')->put($time, $encrypt, $expiry); // 10 Minutes
                Cache::put($time, $encrypt, $expiry); // 10 Minutes

            // if (Cache::has($time))
            //     dd(Cache::get($time));

            // Cookie::queue(Cookie::make($time, $encrypt));
            // Cookie::queue($time, $encrypt);
            $request->session()->put('authenticated', $time);

            // dd($encrypt);
            // dd(Cookie::get($time));
            // setcookie($time, $responses->data->token, time() + $responses->data->expires_in, "/",env('APP_URL'),0,1); // 86400 = 1 day
            // echo 'ciike = '.Cookie::get($time);
            // die();

            //  $data = $_COOKIE[$time];
            // $name = $time;   
            // echo 'Cookie name length : ' . mb_strlen($name) . "<br>\n";
            // echo 'Cookie content length : ' . mb_strlen($data) . "<br>\n";
            // echo 'Cookie size : ~' . ($size) . ' Bytes<br>'."\n";

            // die();
            // $getWarehouse = true;


            // if($getWarehouse)
            // {
            //     $postParam = array(
            //         'endpoint'  => 'v'.config('app.api_ver').'/warehouse',
            //         'form_params' => array(
            //             'sort_by' => 'id',
            //             'sort' => 'asc',
            //             'filter' => json_encode(['type' => 'warehouse', ])
            //         ),
            //         'headers' => [ 'Authorization' => 'Bearer '.$token ]
            //     );

            //     $posApi = ProductApi::postData( $postParam );
            //     $companiesDecode = json_decode($posApi, true);

            //     if(!empty($companiesDecode['code']) && $companiesDecode['code'] == 200)
            //     {
            //         $warehouses = $companiesDecode['data']['warehouses'];
            //         if(!empty($warehouses));
            //             $request->session()->put('warehouses', array_column($warehouses, 'id'));
            //     }
            // }

            // dd(session('authenticated'));
            // $cookies = Cookie::get();

            // echo '<pre>';
            // print_r($_COOKIE);
            // echo '</pre>';
            // die();
            return true;

            // $apiData['endpoint'] = 'profile';
            // $apiData['form_params'] = array();
            // $apiData['headers'] = array(
            //     'Authorization' => 'Bearer '.$responses->data->token
            // ); 

            // $userApi = UserApi::getData($apiData);
            // $userDecode = json_decode($userApi);

            // if(!empty($userDecode->code) && $userDecode->code == 200 && !empty($userDecode->data) && !empty($userDecode->data->user))
            // {
            //     $userDecode->data->user->token = $responses->data->token;
            //     unset($userDecode->data->user->created_at);
            //     unset($userDecode->data->user->updated_at);
            //     unset($userDecode->data->user->nasabah_id);
            //     unset($userDecode->data->user->type);
            //     unset($userDecode->data->user->address);
            //     unset($userDecode->data->user->kelurahan);
            //     unset($userDecode->data->user->kecamatan);
            //     unset($userDecode->data->user->kota);
            //     unset($userDecode->data->user->provinsi);
            //     unset($userDecode->data->user->negara);
            //     unset($userDecode->data->user->phone);

            //     $user = serialize($userDecode->data->user);
            //     // $cookie = Cookie::forever('name', 'value');

            //     Cookie::queue(Cookie::make($time, $user, time()+$responses->data->expires_in));
            //     Cookie::queue($time, $user, time()+$responses->data->expires_in);   
            //     $request->session()->put('authenticated', $time);

            //     return true;
            // }
        }

        $request->message = $responses->message;
        $this->sendFailedLoginResponse($request);
        return false;
    }

    public function logout(Request $request)
    {
        if (empty(session('authenticated'))) {
            $cookies = Cookie::get();
            if (!empty($cookies)) {
                foreach ($cookies as $cookie_name => $cookie) {
                    if (!empty(Cookie::get($cookie_name)))
                        Cookie::queue(Cookie::forget($cookie_name));
                }
            }
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/login');
    }
}
