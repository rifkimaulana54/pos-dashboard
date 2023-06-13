<?php

namespace App\Http\Controllers\User\ACL;

use GlobalHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UserApi;

class RoleController extends Controller
{
    public $successStatus = 200;
    public $limit = 25;

    public function __construct()
    {
        $this->middleware('custom_auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!GlobalHelper::userRole($request,'superadmin') && !GlobalHelper::userRole($request, 'admin'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        return view('user.acl.roles.index', 
            [
                'request' => $request
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(!GlobalHelper::userRole($request,'superadmin') && !GlobalHelper::userRole($request,'admin'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('users/acl/roles');
        }

        $user_token = $request->user_token;

        $apiData['endpoint'] = 'v'.config('app.api_ver').'/acl/permissions/groups';
        $apiData['form_params'] = array();
        $apiData['headers'] = array(
            'Authorization' => 'Bearer '.$user_token
        ); 

        $userApi = UserApi::postData($apiData);
        $permDecode = json_decode($userApi);

        if(!empty($permDecode->data) && !empty($permDecode->data->permissions))
            $permissions = $permDecode->data->permissions;

        return view('user.acl.roles.edit', 
            [
                'request' => $request,
                'permissions' => !empty($permissions) ? $permissions : array(),
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!GlobalHelper::userRole($request,'superadmin') && !GlobalHelper::userRole($request, 'admin'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('users/acl/roles');
        }

        $user_token = $request->user_token;

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'permission'     => 'required'
        ]);

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/acl/roles/store',
            'form_params' => array(
                'name'      => $request->input('name'),
                'permissions'     => !empty($request->input('permission')) ? $request->input('permission') : null,
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $userApi = UserApi::postData( $postParam );
        $dataDecode = json_decode($userApi);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            // if(!empty($dataDecode->type))
            //     return redirect('reporting/types/'.$dataDecode->type->id.'/edit/');

            return redirect('users/acl/roles/create')->withInput();
        }
        else
        {
            \Session::flash('flash_success', $dataDecode->message);
            return redirect('users/acl/roles');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if(!GlobalHelper::userRole($request,'superadmin') && !GlobalHelper::userRole($request,'admin'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('users/acl/roles');
        }

        $user_token = $request->user_token;

        $apiData['endpoint'] = 'v'.config('app.api_ver').'/acl/roles/'.$id;
        $apiData['form_params'] = array();
        $apiData['headers'] = array(
            'Authorization' => 'Bearer '.$user_token
        ); 

        $roleApi = UserApi::getData($apiData);
        $roleDecode = json_decode($roleApi);

        if(!empty($roleDecode->code) && $roleDecode->code != 200)
        {
            $error_message = $roleDecode->message;
            \Session::flash('flash_error', $error_message);

            return redirect('users/acl/roles');
        }
        else
        {
            $apiData['endpoint'] = 'v'.config('app.api_ver').'/acl/permissions/groups';
            $apiData['form_params'] = array();
            $apiData['headers'] = array(
                'Authorization' => 'Bearer '.$user_token
            ); 

            $userApi = UserApi::postData($apiData);
            $permDecode = json_decode($userApi);

            if(!empty($permDecode->data) && !empty($permDecode->data->permissions))
                $permissions = $permDecode->data->permissions;

            return view('user.acl.roles.edit', 
                [
                    'request' => $request,
                    'role'  => $roleDecode->data->role,
                    'permissions' => !empty($permissions) ? $permissions : array()
                ]
            );
        }
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
        if(!GlobalHelper::userRole($request,'superadmin') && !GlobalHelper::userRole($request,'admin'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('users/acl/roles');
        }

        $user_token = $request->user_token;

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'permission'   => 'required'
            // 'password'  => 'required|string|min:8|confirmed'
        ]);

        // echo '<pre>';
        // print_r($request->all());
        // echo '</pre>';
        // die();

        $putParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/acl/roles/'.$id,
            'form_params' => array(
                'name'      => $request->input('name'),
                'status'    => !empty($request->input('status')) ? $request->input('status') : 2,
                'permissions'     => !empty($request->input('permission')) ? $request->input('permission') : null,
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $userApi = UserApi::updateData( $putParam );

        $dataDecode = json_decode($userApi);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            // if(!empty($dataDecode->type))
            //     return redirect('reporting/types/'.$dataDecode->type->id.'/edit/');

            return redirect('users/acl/roles/'.$id)->withInput();
        }
        else
        {
            \Session::flash('flash_success', $dataDecode->message);
            return redirect('users/acl/roles');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $return = array(
            'success' => '',
            'error' => 'Delete role gagal. Silahkan coba lagi'
        );

        try
        {
            //$user = unserialize(Cookie::get(session('authenticated')));

            if(!GlobalHelper::userRole($request,'superadmin') && !GlobalHelper::userRole($request,'admin'))
            {
                $return['error'] = 'You don\'t have permission to access the page you requested.';
                return response()->json($return, $this->successStatus);
            }

            $user_token = $request->user_token;

            // $delParam = array(
            //     'endpoint'  => 'users/'.$id,
            //     'headers' => [ 'Authorization' => 'Bearer '.$user->token ]
            // );

            // $userApi = UserApi::deleteData( $delParam );

            $putParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/acl/roles/'.$id,
                'form_params' => array(
                    'status'      => 0,
                ),
                'headers' => [ 'Authorization' => 'Bearer '. $user_token ]
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
        $return = array(
            'success' => '',
            'error' => 'Restore role gagal. Silahkan coba lagi'
        );

        try
        {
            //$user = unserialize(Cookie::get(session('authenticated')));

            if(!GlobalHelper::userRole($request,'superadmin') && !GlobalHelper::userRole($request,'admin'))
            {
                $return['error'] = 'You don\'t have permission to access the page you requested.';
                return response()->json($return, $this->successStatus);
            }

            $user_token = $request->user_token;

            $putParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/acl/roles/'.$id,
                'form_params' => array(
                    'status'      => 1,
                ),
                'headers' => [ 'Authorization' => 'Bearer '. $user_token ]
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

    public function getRoleList(Request $request)
    {
        $return = array(
            'data' => '',
            'all' => 0,
            'iTotalRecords' => 0,
            'iTotalDisplayRecords' => 0,
            'sEcho' => (int)$request->input('sEcho',true)
        );

        if(!GlobalHelper::userRole($request,'superadmin') && !GlobalHelper::userRole($request,'admin'))
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
            case 4:$order_by='status';break;
            case 3:$order_by='updated_at';break;
            case 2:$order_by='created_at';break;
            case 0:$order_by='id';break;
            default:$order_by='display_name';break;
        }

        $order = $request->input('sSortDir_0',true);

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/acl/roles',
            'form_params' => array(
                'page' => $page,
                'per_page' => $this->limit,
                'sort_by' => $order_by,
                'keyword' => '',
                'sort' => $order,
                'filter'  => null,
                'date_filter' => null
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        if(!empty($keyword = $request->input('sSearch')))
            $postParam['form_params']['keyword'] = $keyword;

        $userApi = UserApi::postData( $postParam );

        $dataDecode = json_decode($userApi);

        if(!empty($dataDecode->code))
        {
            switch($dataDecode->code)
            {
                case 200:  
                    if(!empty($dataDecode->data) && !empty($dataDecode->data->roles))
                    {
                        $return['all'] = $return['iTotalRecords'] = $return['iTotalDisplayRecords'] = $dataDecode->data->total_records;

                        foreach($dataDecode->data->roles as $role)
                        {
                            $role->created_html = date('d/M/Y H:i:s', strtotime($role->created_at));
                            $role->updated_html = date('d/M/Y H:i:s', strtotime($role->updated_at));

                            switch($role->status)
                            {
                                case 2:
                                    $role->status_html = '<span class="badge badge-info">' . $role->status_label . '</span>';
                                    break;
                                case 0:
                                    $role->status_html = '<span class="badge badge-danger">' . $role->status_label . '</span>';
                                    break;
                                default:
                                    $role->status_html = '<span class="badge badge-success">' . $role->status_label . '</span>';
                                    break;
                            }

                            // if(JPHelper::userCan($request,'update-users') && $role->id != 1)
                                $role->update = 1;

                            $protected_ids = [1,2];

                            if(GlobalHelper::userCan($request,'delete-users') && !in_array($role->id,$protected_ids))
                                $role->delete = 1;
                        }

                        $return['data'] = $dataDecode->data->roles;
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
}
