<?php

namespace App\Http\Controllers\User;

use App\Helpers\GlobalHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use stdClass;
use UserApi;
use AssetApi;
use File;
use Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $successStatus = 200;
    public $limit = 25;

    public function __construct()
    {
        $this->middleware('custom_auth');
    }

    public function index(Request $request)
    {
        //
        if(!GlobalHelper::userCan($request,'read-users'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        return view('user.user.index', [
            'request' => $request
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(!GlobalHelper::userCan($request,'create-users'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/acl/roles',
            'form_params' => array(
                'filter' => json_encode(array(
                    'status'    => 1
                ))
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $userApi = UserApi::postData($postParam);
        $dataDecode = json_decode($userApi);

        if(!empty($dataDecode->data->roles))
            $roles = $dataDecode->data->roles;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/our-store',
            'form_params' => array(
                'filter' => json_encode(array(
                    'status'    => 1,
                    'company_id' => !empty(session('company')['id']) ? session('company')['id'] : 1,
                ))
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $userApi = UserApi::postData($postParam);
        $dataDecode = json_decode($userApi);

        if(!empty($dataDecode->data->stores))
            $stores = $dataDecode->data->stores;

        return view('user.user.edit', [
            'request' => $request,
            'roles' => !empty($roles) ? $roles : [],
            'stores' => !empty($stores) ? $stores : [],
            // 'settings' => $settings
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!GlobalHelper::userCan($request,'create-users'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;
        // dd($request->all());
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255',
            'role'      => 'required',
        ]);

        
        if(!empty($request->password))
        $validated = $request->validate(['password'  => 'required|string|min:8|confirmed']);

        if($request->hasFile('file'))
        {
            $picture = GlobalHelper::uploadFile($request->file('file'), 'Profile_Picture_' . str_replace(" ", "_", $request->name), 'user', $user_token);
        }
        
        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/register',
            'form_params' => array(
                'name'      => $request->input('name'),
                'email'     => strtolower($request->input('email')),
                'phone'     => !empty($request->input('phone')) ? $request->input('phone') : '',
                'password'  => !empty($request->input('password')) ? $request->input('password') : null,
                'password_confirmation'  => !empty($request->input('password_confirmation')) ? $request->input('password_confirmation') : null,
                'status'    => 1,
                'store_id'  => $request->store,
                'meta'      => !empty($request->meta) ? $request->meta : array(),
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        if(!empty($request->role_name) && strtolower($request->role_name))
            $postParam['form_params']['role'] = $request->role;
        if(!empty($picture))
            $postParam['form_params']['meta']['profile_pic']  = GlobalHelper::maybe_serialize($picture);
        
        // dd($postParam);
        $userApi = UserApi::postData( $postParam );
        $dataDecode = json_decode($userApi);
        // dd($dataDecode);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            // if(!empty($dataDecode->type))
            //     return redirect('reporting/types/'.$dataDecode->type->id.'/edit/');

            return redirect('users/create')->withInput();
        }
        
        \Session::flash('flash_success', $dataDecode->message);
        return redirect('users');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if($request->user_id != $id)
        {
            if(!GlobalHelper::userCan($request,'read-users'))
            {
                \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
                return redirect('home');
            }
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/detail/' . $id,
            'form_params' => array(),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $userApi = UserApi::getData($postParam);
        $dataDecode = json_decode($userApi);
        // dd($dataDecode);

        if(!empty($dataDecode->data->user))
            $user = $dataDecode->data->user;

        if(!empty($user->metas))
        {
            foreach ($user->metas as $meta) 
                $metas[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);
        }

        if(GlobalHelper::userRole($request,'superadmin') || (GlobalHelper::userCan($request,'update-users') && !empty(session('company')['id']) && session('company')['id'] == $user->company_id))
            $user->update = 1;

        if(GlobalHelper::userRole($request,'superadmin') || (GlobalHelper::userCan($request,'delete-users') && $user->id != 1 && !empty(session('company')['id']) && session('company')['id'] == $user->company_id))
            $user->delete = 1;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/companies',
            'form_params' => array(
                'sort_by' => 'company_name',
                'sort' => 'asc',
                'filter' => json_encode(['status' => 1])
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $posApi = UserApi::postData( $postParam );
        $dataDecode = json_decode($posApi);

        if(!empty($dataDecode->data) && !empty($dataDecode->data->companies))
            $companies = $dataDecode->data->companies;

        
        return view('user.user.detail', 
            [
                'request' => $request,
                'user' => !empty($user) ? $user : new stdClass,
                'metas' => !empty($metas) ? $metas : array(),
                'companies' => !empty($companies) ? $companies : array()
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if(!GlobalHelper::userCan($request,'update-users'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/acl/roles',
            'form_params' => array(
                'filter' => json_encode(array(
                    'status'    => 1
                ))
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $userApi = UserApi::postData($postParam);
        $dataDecode = json_decode($userApi);
        // dd($dataDecode);
        if(!empty($dataDecode->data->roles))
            $roles = $dataDecode->data->roles;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/detail/' . $id,
            'form_params' => array(),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $userApi = UserApi::getData($postParam);
        $dataDecode = json_decode($userApi);
        // dd($dataDecode);

        if(!empty($dataDecode) && $dataDecode->code !== 200)
        {
            \Session::flash('flash_error', $dataDecode->message);
            return redirect('users');
        }

        if(!empty($dataDecode->data->user))
            $user = $dataDecode->data->user;

        if(!empty($user->metas))
        {
            foreach ($user->metas as $meta) 
                $metas[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);
        }

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/our-store',
            'form_params' => array(
                'filter' => json_encode(array(
                    'status'    => 1,
                    'company_id' => !empty(session('company')['id']) ? session('company')['id'] : 1,
                ))
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $userApi = UserApi::postData($postParam);
        $dataDecode = json_decode($userApi);

        if(!empty($dataDecode->data->stores))
            $stores = $dataDecode->data->stores;

        if(!empty($user->stores))
            $stores_selected = array_column($user->stores, 'id');
        return view('user.user.edit', 
            [
                'request' => $request,
                'roles' => !empty($roles) ? $roles : new stdClass,
                'user' => !empty($user) ? $user : new stdClass,
                'metas' => !empty($metas) ? $metas : array(),
                'stores' => !empty($stores) ? $stores : array(),
                'companies' => !empty($companies) ? $companies : array(),
                'stores_selected' => !empty($stores_selected) ? $stores_selected : [],
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        if(!GlobalHelper::userCan($request,'update-users'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }
        
        $user_token = $request->user_token;
        // dd($request->all());
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255',
            'role'      => 'required',
        ]);

        
        if(!empty($request->password))
            $validated = $request->validate(['password'  => 'required|string|min:8|confirmed']);

        if($request->hasFile('file'))
        {
            $picture = GlobalHelper::uploadFile($request->file('file'), 'Profile_Picture_' . str_replace(" ", "_", $request->name), 'user', $user_token);
        }

        if(!empty($request->location))
            $location = array_filter($request->location);
        
        $postParam = array(
            'endpoint'      => 'v'.config('app.api_ver').'/update/' . $id,
            'form_params'   => array(
                'fullname'  => $request->input('name'),
                'email'     => strtolower($request->input('email')),
                'phone'     => !empty($request->input('phone')) ? $request->input('phone') : '',
                'password'  => !empty($request->input('password')) ? $request->input('password') : null,
                'password_confirmation'  => !empty($request->input('password_confirmation')) ? $request->input('password_confirmation') : null,
                'role'      => $request->input('role'),
                'store_id'  => $request->store,
                'status'    => !empty($request->status) ? 1 : 2,
                'meta'      => !empty($request->meta) ? $request->meta : array(),
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        if(!empty($picture))
            $postParam['form_params']['meta']['profile_pic']  = GlobalHelper::maybe_serialize($picture);

        // dd($postParam);
        // echo '<pre>';
        // print_r($postParam);
        // echo '</pre>';

        $userApi = UserApi::updateData( $postParam );
        $dataDecode = json_decode($userApi);

        // dd($dataDecode);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            // if(!empty($dataDecode->type))
            //     return redirect('reporting/types/'.$dataDecode->type->id.'/edit/');

            return redirect('users/' . $id . '/edit')->withInput();
        }
        
        \Session::flash('flash_success', $dataDecode->message);
        return redirect('users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
        $return = array(
            'success' => '',
            'error' => 'Delete user gagal. Silahkan coba lagi'
        );

        try
        {
            if(!GlobalHelper::userCan($request,'delete-users'))
            {
                $return['error'] = 'You don\'t have permission to access the page you requested.';
                return response()->json($return, $this->successStatus);
            }

            $user_token = $request->user_token;

            $putParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/update/'.$id,
                'form_params' => array(
                    'status'      => 2,
                ),
                'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
            );

            $userApi = UserApi::updateData( $putParam );

            $dataDecode = json_decode($userApi);
            if(!empty($dataDecode->code) && $dataDecode->code != 200)
                $return['error'] = $dataDecode->message;
            else
            {
                unset($return['error']);
                $return['success'] = $dataDecode->message;
            }

            return response()->json($return, $this->successStatus);
        }
        catch (\Exception $e) 
        {
            return response()->json($return, $this->successStatus);
            // return response()->json(['message' => 'user not found!'], 404);
        }
    }

    public function restore(Request $request, $id)
    {
        //
        $return = array(
            'success' => '',
            'error' => 'Restore user gagal. Silahkan coba lagi'
        );

        try
        {
            if(!GlobalHelper::userCan($request,'update-users'))
            {
                $return['error'] = 'You don\'t have permission to access the page you requested.';
                return response()->json($return, $this->successStatus);
            }

            $user_token = $request->user_token;

            $putParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/update/'.$id,
                'form_params' => array(
                    'status'      => 1,
                ),
                'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
            );

            $userApi = UserApi::updateData( $putParam );

            $dataDecode = json_decode($userApi);
            if(!empty($dataDecode->code) && $dataDecode->code != 200)
                $return['error'] = $dataDecode->message;
            else
            {
                unset($return['error']);
                $return['success'] = $dataDecode->message;
            }

            return response()->json($return, $this->successStatus);
        }
        catch (\Exception $e) 
        {
            return response()->json($return, $this->successStatus);
            // return response()->json(['message' => 'user not found!'], 404);
        }
    }

    public function getUserList(Request $request)
    {
        $return = array(
            'data' => '',
            'all' => 0,
            'iTotalRecords' => 0,
            'iTotalDisplayRecords' => 0,
            'sEcho' => (int)$request->input('sEcho',true)
        );

        if(!GlobalHelper::userCan($request,'read-users'))
        {
            $return['error'] = 'You don\'t have permission to access the page you requested.';
            return response()->json($return, $this->successStatus);
        }

        $user_token = $request->user_token;

        $this->limit = (!empty($request->input('iDisplayLength'))) ? $request->input('iDisplayLength') : $this->limit;
        $page = (!empty($request->input('iDisplayStart'))) ? ($request->input('iDisplayStart')/$this->limit)+1 : 0;

        $sort_by = $request->input('iSortCol_0',true);
        switch($sort_by)
        {
            case 5:$order_by='status';break;
            case 4:$order_by= 'created_at';break;
            case 3:$order_by='role_name';break;
            case 2:$order_by='email';break;
            case 0:$order_by='id';break;
            default:$order_by='fullname';break;
        }

        // dd($request->all());

        $order = $request->input('sSortDir_0',true);
        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/lists',
            'form_params' => array(
                'page' => $page,
                'per_page' => $this->limit,
                'sort_by' => $order_by,
                'keyword' => '',
                'sort' => $order,
                'filter' => array(
                    // 'company_id' => !empty(session('companies')) ? array_column(session('companies'), 'id') : 1
                ),
                'filter_not' => json_encode(array(
                    'role_name' => 'superadmin',
                )),
                'date_filter' => null
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        if(!empty($keyword = $request->input('sSearch')))
            $postParam['form_params']['keyword'] = $keyword;

        if(!empty($role = $request->input('role')))
            $postParam['form_params']['filter']['role_name'] = $role;

        if(!empty($postParam['form_params']['filter']))
            $postParam['form_params']['filter'] = json_encode($postParam['form_params']['filter']);

        $userApi = UserApi::postData($postParam);
        $dataDecode = json_decode($userApi);

        if(!empty($dataDecode->code))
        {
            switch($dataDecode->code)
            {
                case 200:  
                    if(!empty($dataDecode->data) && !empty($dataDecode->data->users))
                    {
                        $return['all'] = $return['iTotalRecords'] = $return['iTotalDisplayRecords'] = $dataDecode->data->total_records;

                        foreach($dataDecode->data->users as $u)
                        {
                            $u->created_html = date('M, d-Y', strtotime($u->created_at));

                            switch($u->status)
                            {
                                case 2:
                                    $u->status_html = '<span class="badge badge-danger">' . $u->status_label . '</span>';
                                    break;
                                case 0:
                                    $u->status_html = '<span class="badge badge-info">' . $u->status_label . '</span>';
                                    break;
                                default:
                                    $u->status_html = '<span class="badge badge-success">' . $u->status_label . '</span>';
                                    break;
                            }

                            if(!empty($u->roles))
                            {
                                // $user_roles = array_column($u->roles, 'display_name');
                                $u->role_name = implode(', ',array_map('ucwords',$u->roles));
                            }

                            if(GlobalHelper::userRole($request,'superadmin') || (GlobalHelper::userCan($request,'update-users') && !empty(session('company')['id']) && session('company')['id'] == $u->company_id))
                                $u->update = 1;

                            if(GlobalHelper::userRole($request,'superadmin') || (GlobalHelper::userCan($request,'delete-users') && $u->id != 1 && !empty(session('company')['id']) && session('company')['id'] == $u->company_id))
                                $u->delete = 1;

                            if(!empty($u->metas))
                            {
                                $metas = array();
                                foreach($u->metas as $meta)
                                    $metas[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);

                                if(!empty($metas['profile_pic']))
                                {
                                    $u->meta_html = '<img src="' . $metas['profile_pic']['media_path'] . '" class="img-circle" width="70px">';
                                }
                            }
                        }

                        $return['data'] = $dataDecode->data->users;
                        // unset($return['error']);
                    }
                    break;
                default:
                    $return['error'] = !empty($dataDecode->message) ? $dataDecode->message : 'Something went wrong. Please try again.';
                    $this->successStatus = $dataDecode->code;
                    break;
            }
        }

        return response()->json($return, $this->successStatus);
    }

    public function setCompany(Request $request)
    {
        $return = array(
            'success' => '',
            'error' => 'Set Company gagal. Silahkan coba lagi'
        );

        try
        {
            if(!empty($request->company))
            {
                unset($return['error']);
                $company = GlobalHelper::maybe_unserialize($request->company);
                
                $request->session()->put('company', $company);
                $return['success'] = 'Set company berhasil';
            }

            return response()->json($return, $this->successStatus);
        }
        catch (\Exception $e) 
        {
            return response()->json($return, $this->successStatus);
            // return response()->json(['message' => 'user not found!'], 404);
        }
    }

    public function jurnal(Request $request)
    {
        if(empty($request->input('access_token')))
            abort(404);
        
        return view('jurnal.landing-access-token');
    }
}
