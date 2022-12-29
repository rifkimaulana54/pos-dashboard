<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UserApi;
use GlobalHelper;
use AssetApi;

class StoreController extends Controller
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
        if(!GlobalHelper::userCan($request,'read-store'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        return view('setting.store.index', [
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
        if(!GlobalHelper::userCan($request,'create-store'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/company',
            'form_params' => array(
                'sort' => 'asc',
                'filter' => json_encode(['status' => 1])
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $posApi = UserApi::postData( $postParam );
        $dataDecode = json_decode($posApi);

        if(!empty($dataDecode->data) && !empty($dataDecode->data->companies))
            $companies = $dataDecode->data->companies;        

        return view('setting.store.edit', [
            'request' => $request,
            'companies' => !empty($companies) ? $companies : []
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
        if(!GlobalHelper::userCan($request,'create-store'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $validated = $request->validate([
            'store_name'      => 'required|string|max:255'
        ]);

        if ($request->hasFile('file'))
            $picture = GlobalHelper::uploadFile($request->file('file'), 'Logo_Store_' . str_replace(" ", "_", $request->name), 'user', $user_token);

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/our-store/store',
            'form_params' => array(
                'store_name'  => $request->store_name,
                'company_id'  => !empty($request->company_id) ? $request->company_id : '',
                'no_telepone' => !empty($request->no_telepone) ? $request->no_telepone : '',
                'store_address' => !empty($request->store_address) ? $request->store_address : '',
                'store_description' => !empty($request->store_description) ? $request->store_description : '',
                'meta'  => !empty($request->meta) ? $request->meta : null,
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        if(!empty($picture))
            $postParam['form_params']['meta']['image'] = GlobalHelper::maybe_serialize($picture);

        // dd($postParam);

        $posApi = UserApi::postData( $postParam );
        $dataDecode = json_decode($posApi);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            // if(!empty($dataDecode->type))
            //     return redirect('reporting/types/'.$dataDecode->type->id.'/edit/');

            return redirect('stores/create')->withInput();
        }



        \Session::flash('flash_success', $dataDecode->message);
        return redirect('stores');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if(!GlobalHelper::userCan($request,'read-store'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $getParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/our-store/'.$id,
            'form_params' => array(),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $posApi = UserApi::getData( $getParam );
        $dataDecode = json_decode($posApi);

        if(!empty($dataDecode) && $dataDecode->code !== 200)
        {
            \Session::flash('flash_error', $dataDecode->message);
            return redirect('stores');
        }

        $store = $dataDecode->data->store;

        if(!empty($store->metas))
        {
            foreach($store->metas as $meta)
            {
                $store->meta[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);
            }
        }
        return view('setting.store.detail', [
            'request' => $request,
            'store' => !empty($store) ? $store : array()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if(!GlobalHelper::userCan($request,'update-store'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $getParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/our-store/'.$id,
            'form_params' => array(),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $posApi = UserApi::getData( $getParam );
        $dataDecode = json_decode($posApi);

        if(!empty($dataDecode) && $dataDecode->code !== 200)
        {
            \Session::flash('flash_error', $dataDecode->message);
            return redirect('stores');
        }

        $store = $dataDecode->data->store;
        if(!empty($store->metas))
        {
            foreach($store->metas as $meta)
            {
                $store->meta[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);
            }
        }

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/company',
            'form_params' => array(
                'sort_by' => 'display_name',
                'sort' => 'asc',
                'filter' => json_encode(['status' => 1]),
                'filter_not' => json_encode(['id' => $id])
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $posApi = UserApi::postData( $postParam );
        $dataDecode = json_decode($posApi);

        if(!empty($dataDecode->data) && !empty($dataDecode->data->companies))
            $companies = $dataDecode->data->companies;  

        return view('setting.store.edit', [
            'request' => $request,
            'store' => !empty($store) ? $store : array(),
            'companies' => !empty($companies) ? $companies : []
        ]);
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
        if(!GlobalHelper::userCan($request,'update-store'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $validated = $request->validate([
            'store_name'      => 'required|string|max:255'
        ]);

        if ($request->hasFile('file')) {
            $picture = GlobalHelper::uploadFile($request->file('file'), 'Logo_Store_' . str_replace(" ", "_", $request->name), 'user', $user_token);
        }
        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/our-store/' . $id,
            'form_params' => array(
                'store_name'  => $request->store_name,
                'company_id'  => !empty($request->company_id) ? $request->company_id : '',
                'no_telepone' => !empty($request->no_telepone) ? $request->no_telepone : '',
                'store_address' => !empty($request->store_address) ? $request->store_address : '',
                'store_description' => !empty($request->store_description) ? $request->store_description : '',
                'meta'  => !empty($request->meta) ? $request->meta : null,
                'status'       => !empty($request->status) ? 1 : 2,
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        if(!empty($picture))
            $postParam['form_params']['meta']['image'] = GlobalHelper::maybe_serialize($picture);

        $posApi = UserApi::updateData( $postParam );
        $dataDecode = json_decode($posApi);
        // dd($dataDecode);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            return redirect('stores/' . $id .'/edit')->withInput();
        }
        
        \Session::flash('flash_success', $dataDecode->message);
        return redirect('stores');
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
            'error' => 'Delete store gagal. Silahkan coba lagi'
        );

        try
        {
            if(!GlobalHelper::userCan($request,'delete-store'))
            {
                $return['error'] = 'You don\'t have permission to access the page you requested.';
                return response()->json($return, $this->successStatus);
            }

            $user_token = $request->user_token;

            $putParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/our-store/' . $id,
                'form_params' => array(
                    'status'      => 0,
                ),
                'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
            );

            $prodApi = UserApi::updateData( $putParam );

            $dataDecode = json_decode($prodApi);

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
            'error' => 'Restore store gagal. Silahkan coba lagi'
        );

        try
        {
            if(!GlobalHelper::userCan($request,'update-store'))
            {
                $return['error'] = 'You don\'t have permission to access the page you requested.';
                return response()->json($return, $this->successStatus);
            }

            $user_token = $request->user_token;

            $putParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/our-store/' . $id,
                'form_params' => array(
                    'status'      => 1,
                ),
                'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
            );

            $prodApi = UserApi::updateData( $putParam );

            $dataDecode = json_decode($prodApi);

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

    public function getStoreList(Request $request)
    {
        $return = array(
            'data' => '',
            'all' => 0,
            'iTotalRecords' => 0,
            'iTotalDisplayRecords' => 0,
            'sEcho' => (int)$request->input('sEcho',true)
        );

        if(!GlobalHelper::userCan($request,'read-store'))
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
            default:$order_by='store_name';break;
        }

        $order = $request->input('sSortDir_0',true);

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver'). '/our-store',
            'form_params' => array(
                'page' => $page,
                'per_page' => $this->limit,
                'sort_by' => $order_by,
                'keyword' => '',
                'sort' => $order,
                'filter' => json_encode(array(
                    // 'company_id' => !empty(session('companies')) ? array_column(session('companies'), 'id') : 1,
                )),
                'filter_not' => null,
                'date_filter' => null
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        if(!empty($keyword = $request->input('sSearch')))
            $postParam['form_params']['keyword'] = $keyword;

        $posApi = UserApi::postData( $postParam );
        $dataDecode = json_decode($posApi);
        // dd($dataDecode);

        if(!empty($dataDecode->code))
        {
            switch($dataDecode->code)
            {
                case 200:  
                    if(!empty($dataDecode->data) && !empty($dataDecode->data->stores))
                    {
                        $return['all'] = $return['iTotalRecords'] = $return['iTotalDisplayRecords'] = $dataDecode->data->total_records;

                        foreach($dataDecode->data->stores as $l)
                        {
                            $l->created_html = date('M, d-Y H:i:s', strtotime($l->created_at));
                            $l->updated_html = date('M, d-Y H:i:s', strtotime($l->updated_at));

                            switch($l->status)
                            {
                                case 2:
                                    $l->status_html = '<span class="badge badge-info">' . $l->status_label . '</span>';
                                    break;
                                case 0:
                                    $l->status_html = '<span class="badge badge-danger">' . $l->status_label . '</span>';
                                    break;
                                default:
                                    $l->status_html = '<span class="badge badge-success">' . $l->status_label . '</span>';
                                    break;
                            }

                            // if(!empty($u->roles))
                            // {
                            //     $user_roles = array_column($u->roles, 'display_name');
                            //     $u->role_name = implode(', ',$user_roles);
                            // }

                            if(GlobalHelper::userRole($request,'superadmin') || GlobalHelper::userRole($request, 'admin') || (GlobalHelper::userCan($request,'update-item-store') && !empty(session('company')['id']) && session('company')['id'] == $p->company_id))
                                $l->update = 1;

                            if (GlobalHelper::userRole($request, 'superadmin') || GlobalHelper::userRole($request, 'admin') || (GlobalHelper::userCan($request, 'delete-item-store') && !empty(session('company')['id']) && session('company')['id'] == $p->company_id))
                                $l->delete = 1;

                            // if(!empty($u->metas))
                            // {
                            //     $metas = array();
                            //     foreach($u->metas as $meta)
                            //         $metas[$meta->meta_key] = JPHelper::maybe_unserialize($meta->meta_value);

                            //     if(!empty($metas))
                            //         $u->metas = $metas;
                            // }
                        }

                        $return['data'] = $dataDecode->data->stores;
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

    public function getCompanyStrukturList(Request $request)
    {
        $return = array(
            'data' => '',
            'all' => 0,
            // 'iTotalRecords' => 0,
            // 'iTotalDisplayRecords' => 0,
            // 'sEcho' => (int)$request->input('sEcho',true)
        );

        if(!GlobalHelper::userCan($request,'read-store'))
        {
            $return['error'] = 'You don\'t have permission to access the page you requested.';
            return response()->json($return, $this->successStatus);
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/stores',
            'form_params' => array(
                'sort_by' => 'parent_id',
                'sort' => 'asc',
                // 'filter_not' => null,
                // 'date_filter' => null
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        // if(!empty($keyword = $request->input('sSearch')))
        //     $postParam['form_params']['keyword'] = $keyword;

        $posApi = UserApi::postData( $postParam );
        $dataDecode = json_decode($posApi);
        // dd($dataDecode);
        $stores = array();

        if(!empty($dataDecode->code))
        {
            switch($dataDecode->code)
            {
                case 200:  
                    if(!empty($dataDecode->data) && !empty($dataDecode->data->stores))
                    {
                        $return['all'] = $return['iTotalRecords'] = $return['iTotalDisplayRecords'] = $dataDecode->data->total_records;
                        $i = 0;
                        foreach($dataDecode->data->stores as $company)
                        {
                            if(empty($company->parent_id))
                            {
                                $stores[$i] = array(
                                    'id' => $company->id,
                                    'content' => $company->company_name,
                                );

                                if(!empty($this->hierarcyCompany($dataDecode->data->stores, $company->id)))
                                    $stores[$i]['children'] = $this->hierarcyCompany($dataDecode->data->stores, $company->id);
                                $i++;
                            }

                        }
                        $return['data'] = $stores;
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

    private function hierarcyCompany($stores, $parent_id)
    {
        $stores_susun = array();
        $i = 0;
        foreach ($stores as $company) 
        {
            if($company->parent_id == $parent_id)
            {
                $stores_susun[$i] = array(
                    'id' => $company->id,
                    'content' => $company->company_name,
                );

                if(!empty($this->hierarcyCompany($stores, $company->id)))
                    $stores_susun[$i]['children'] = $this->hierarcyCompany($stores, $company->id);
                $i++;
            }
        }

        return $stores_susun;
    }
}
