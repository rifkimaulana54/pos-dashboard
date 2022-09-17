<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;
use JWTAuth;
use GlobalHelper;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CustomAuth
{   
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route()->uri;
        // dd($route);
        // echo $route;die();
        $cookies = Cookie::get();
        
        $bypass = array(
            'purchase-order/approval/code/{code}',
            'purchase-request/approval/code/{code}',
            'stock-transport/approval/code/{code}',
            'stock-adjustment/approval/code/{code}',
            'stock-adjustment/consignment/approval/code/{code}',
            'quality-inspection/approval/code/{code}',
            'sales-quotation/approval/code/{code}',
            'jurnal'
        );

        if(in_array($route,$bypass))
        {
            return $next($request);
        }
        // if($route == 'records/approve/{record}' || $route == 'records/reject/{record}' || $route == 'records/detail/{record}' || $route == 'records/detail/{record}/{dispen?}')
        //         return $next($request);
        // dump(session('authenticated'));
        if (!empty(session('authenticated'))) 
        {
            // echo '<pre>';
            // print_r($_REQUEST);
            // echo '</pre>';
            // echo '<pre>';
            // print_r($_COOKIE);
            // echo '</pre>';
            // echo '<pre>';
            // print_r($cookies);
            // echo '</pre>';

            // echo 'cookie = '.Cookie::get(session('authenticated'));
            // die();
            // dump(Cache::has(session('authenticated')));
            if (Cache::has(session('authenticated')))
            // if(!empty(Cookie::get(session('authenticated'))))
            {
                try {
                    $decrypt = GlobalHelper::decodeJWT(Cache::get(session('authenticated')));
                    $token = JWTAuth::setToken($decrypt);
                    $apy = JWTAuth::getPayload()->toArray();

                    // dd($apy);

                    $request->request->add(['user_token' => $decrypt]);
                    $request->request->add(['user_id' => $apy['sub']]);
                    $request->request->add(['user_name' => $apy['nm']]);
                    $request->request->add(['user_can' => $apy['acl']]);
                    $request->request->add(['user_role' => $apy['rl']]);
                    if(!empty($apy['ip']))
                        $request->request->add(['ip_address' => $apy['ip']]);

                    return $next($request);
                }
                catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                    dd('expired');
                    Auth::logout();
                    // return response()->json(['token_expired'], 500);

                } 
                catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                    dd('invalid');
                    Auth::logout();
                    // return response()->json(['token_invalid'], 500);

                } 
                catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                    dd('exeption');
                    Auth::logout();
                    // return response()->json(['token_absent' => $e->getMessage()], 500);

                }
            }
        }
        
        // if(!empty($cookies))
        // {
        //     foreach($cookies as $cookie_name => $cookie)
        //     {
        //         if(!empty(Cookie::get($cookie_name)))
        //             Cookie::queue(Cookie::forget($cookie_name));
        //     }
        // }
        if($request->ajax())
            return response()->json(['error' => 'loggedout'], 419);

        \Session::flash('flash_error', '<small>Sorry, Your Session Has Expired. Please Re-Login.</small>');
        return redirect('/login');

        // if($route !== 'login' && $route !== 'login/login_action')
        // { 
            
        // }
        // else
        // {
        //     if (!empty(session('authenticated')))
        //     {
        //         return redirect('/');
        //     }

        //     return $next($request);
        // }
    }
}
