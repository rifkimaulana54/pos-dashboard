<?php

namespace App\Http\Controllers\User\ACL;

use App\Helpers\GlobalHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use UserApi;

class PermissionController extends Controller
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
        //
        if(!GlobalHelper::userRole($request,'superadmin') && !GlobalHelper::userRole($request,'admin'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        return view('user.acl.permission.index',[
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
        //
        if(!GlobalHelper::userRole($request,'superadmin') && !GlobalHelper::userRole($request, 'admin'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        return view('user.acl.permission.edit',[
            'request' => $request
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
        //

        if(!GlobalHelper::userRole($request,'superadmin') && !GlobalHelper::userRole($request, 'admin'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $validator = $request->validate([
            'name'      => 'required|string|max:255',
            'action'    => 'required|array'
        ]);

        // $validated = $request->validate([
        //     'name'      => 'required|string|max:255',
        //     'action'    => 'required'
        // ]);

        $name = $request->input('name');
        $actions = $request->input('action');

        $error_perm = array();
        foreach($actions as $action)
        {
            $perm_name = $action.' '.$name;
            $postParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/acl/permissions/store',
                'form_params' => array(
                    'name'      => $perm_name,
                    'protected' => 0
                ),
                'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
            );

            $userApi = UserApi::postData( $postParam );

            $dataDecode = json_decode($userApi);
            if(!empty($dataDecode->code) && $dataDecode->code != 200)
            {
                $error_perm[] = $dataDecode->message;
                // $validator->errors()->add('action',$dataDecode->message);
            }
        }

        if(!empty($error_perm))
        {
            // $error_message = $dataDecode->message;
            // \Session::flash('flash_error', $error_message);

            // if(!empty($dataDecode->type))
            //     return redirect('reporting/types/'.$dataDecode->type->id.'/edit/');

            return redirect('users/acl/permissions/create')->withInput()->withErrors($error_perm);
        }
        else
        {
            \Session::flash('flash_success', 'Permission '.$name.' berhasil dibuat.');
            return redirect('users/acl/permissions');
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
            return redirect('users/acl/permissions');
        }

        $user_token = $request->user_token;

        $apiData['endpoint'] = 'v'.config('app.api_ver').'/acl/permissions/'.$id;
        $apiData['form_params'] = array();
        $apiData['headers'] = array(
            'Authorization' => 'Bearer '.$user_token
        ); 

        $permissionApi = UserApi::getData($apiData);
        $permissionDecode = json_decode($permissionApi);
        // dd($permissionDecode);

        if(!empty($permissionDecode->code) && $permissionDecode->code != 200)
        {
            $error_message = $permissionDecode->message;
            \Session::flash('flash_error', $error_message);

            return redirect('users/acl/permissions');
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

            return view('user.acl.permission.edit', 
                [
                    'request' => $request,
                    'permission'  => $permissionDecode->data->permission
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
            return redirect('users/acl/permissions');
        }

        $user_token = $request->user_token;

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            // 'password'  => 'required|string|min:8|confirmed'
        ]);

        $putParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/acl/permissions/'.$id,
            'form_params' => array(
                'name'      => $request->input('name'),
                'status'    => !empty($request->input('status')) ? $request->input('status') : 2
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

            return redirect('users/acl/permissions/'.$id)->withInput();
        }
        else
        {
            \Session::flash('flash_success', $dataDecode->message);
            return redirect('users/acl/permissions');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        //
        $return = array(
            'success' => '',
            'error' => 'Delete permission gagal. Silahkan coba lagi'
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
                'endpoint'  => 'v'.config('app.api_ver').'/acl/permissions/'.$id,
                'form_params' => array(
                    'status'      => 0,
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
        $return = array(
            'success' => '',
            'error' => 'Restore permission gagal. Silahkan coba lagi'
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
                'endpoint'  => 'v'.config('app.api_ver').'/acl/permissions/'.$id,
                'form_params' => array(
                    'status'      => 1,
                ),
                'headers' => [ 'Authorization' => 'Bearer '.$user_token]
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

    public function getPermissionList(Request $request)
    {
        $return = array(
            'data' => '',
            'all' => 0,
            'iTotalRecords' => 0,
            'iTotalDisplayRecords' => 0,
            'sEcho' => (int)$request->input('sEcho',true)
        );

        if(!GlobalHelper::userRole($request,'superadmin') && !GlobalHelper::userRole($request, 'admin'))
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
            case 1:$order_by='display_name';break;
            default:$order_by='id';break;
        }

        $order = $request->input('sSortDir_0',true);

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/acl/permissions',
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
                    if(!empty($dataDecode->data) && !empty($dataDecode->data->permissions))
                    {
                        $return['all'] = $return['iTotalRecords'] = $return['iTotalDisplayRecords'] = $dataDecode->data->total_records;

                        foreach($dataDecode->data->permissions as $permission)
                        {
                            $permission->created_html = date('d/M/Y H:i:s', strtotime($permission->created_at));
                            $permission->updated_html = date('d/M/Y H:i:s', strtotime($permission->updated_at));

                            switch($permission->status)
                            {
                                case 2:
                                    $permission->status_html = '<span class="badge badge-info">' . $permission->status_label . '</span>';
                                    break;
                                case 0:
                                    $permission->status_html = '<span class="badge badge-danger">' . $permission->status_label . '</span>';
                                    break;
                                default:
                                    $permission->status_html = '<span class="badge badge-success">' . $permission->status_label . '</span>';
                                    break;
                            }

                            if(empty($permission->protected))
                                $permission->delete = 1;
                        }

                        $return['data'] = $dataDecode->data->permissions;
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
