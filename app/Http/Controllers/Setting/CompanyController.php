<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UserApi;
use GlobalHelper;
use AssetApi;

class CompanyController extends Controller
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
        if(!GlobalHelper::userCan($request,'read-company'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        return view('setting.company.index', [
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
        if(!GlobalHelper::userCan($request,'create-company'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

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

        return view('setting.company.edit', [
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
        if(!GlobalHelper::userCan($request,'create-company'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $validated = $request->validate([
            'name'      => 'required|string|max:255'
        ]);

        if ($request->hasFile('file'))
            $picture = GlobalHelper::uploadFile($request->file('file'), 'Logo_Company_' . str_replace(" ", "_", $request->name), 'user', $user_token);

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/company/store',
            'form_params' => array(
                'company_name' => $request->name,
                'parent_id'     => !empty($request->parent_id) ? $request->parent_id : null,
                'meta'  => !empty($request->meta) ? $request->meta : null,
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        // dd($postParam);
        if(!empty($picture))
            $postParam['form_params']['meta']['image'] = GlobalHelper::maybe_serialize($picture);

        $posApi = UserApi::postData( $postParam );
        $dataDecode = json_decode($posApi);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            // if(!empty($dataDecode->type))
            //     return redirect('reporting/types/'.$dataDecode->type->id.'/edit/');

            return redirect('companies/create')->withInput();
        }



        \Session::flash('flash_success', $dataDecode->message);
        return redirect('companies');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if(!GlobalHelper::userCan($request,'read-company'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $getParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/company/'.$id,
            'form_params' => array(),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $posApi = UserApi::getData( $getParam );
        $dataDecode = json_decode($posApi);

        if(!empty($dataDecode) && $dataDecode->code !== 200)
        {
            \Session::flash('flash_error', $dataDecode->message);
            return redirect('companies');
        }

        $company = $dataDecode->data->company;

        if(!empty($company->metas))
        {
            foreach($company->metas as $meta)
            {
                $company->meta[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);
            }
        }
        return view('setting.company.detail', [
            'request' => $request,
            'company' => !empty($company) ? $company : array()
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
        if(!GlobalHelper::userCan($request,'update-company'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $getParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/company/'.$id,
            'form_params' => array(),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $posApi = UserApi::getData( $getParam );
        $dataDecode = json_decode($posApi);

        if(!empty($dataDecode) && $dataDecode->code !== 200)
        {
            \Session::flash('flash_error', $dataDecode->message);
            return redirect('companies');
        }

        $company = $dataDecode->data->company;
        if(!empty($company->metas))
        {
            foreach($company->metas as $meta)
            {
                $company->meta[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);
            }
        }
        // dd($company);

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

        return view('setting.company.edit', [
            'request' => $request,
            'company' => !empty($company) ? $company : array(),
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
        if(!GlobalHelper::userCan($request,'update-company'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $validated = $request->validate([
            'name'      => 'required|string|max:255'
        ]);

        if ($request->hasFile('file')) {
            $picture = GlobalHelper::uploadFile($request->file('file'), 'Logo_Company_' . str_replace(" ", "_", $request->name), 'user', $user_token);
        }
        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/company/' . $id,
            'form_params' => array(
                'company_name' => $request->name,
                // 'parent_id'    => $request->company_id,
                'status'       => !empty($request->status) ? 1 : 2,
                'meta'         => !empty($request->meta) ? $request->meta : null,
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

            return redirect('companies/' . $id .'/edit')->withInput();
        }
        
        \Session::flash('flash_success', $dataDecode->message);
        return redirect('companies');
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
            'error' => 'Delete company gagal. Silahkan coba lagi'
        );

        try
        {
            if(!GlobalHelper::userCan($request,'delete-company'))
            {
                $return['error'] = 'You don\'t have permission to access the page you requested.';
                return response()->json($return, $this->successStatus);
            }

            $user_token = $request->user_token;

            $putParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/company/' . $id,
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
            'error' => 'Restore company gagal. Silahkan coba lagi'
        );

        try
        {
            if(!GlobalHelper::userCan($request,'update-company'))
            {
                $return['error'] = 'You don\'t have permission to access the page you requested.';
                return response()->json($return, $this->successStatus);
            }

            $user_token = $request->user_token;

            $putParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/company/' . $id,
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

    public function setStrukturCompany(Request $request)
    {
        if(!GlobalHelper::userCan($request,'update-company'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        return view('setting.company.struktur', [
            'request' => $request,
        ]);
    }

    public function storeStrukturCompany(Request $request)
    {
        if(!GlobalHelper::userCan($request,'update-company'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $validated = $request->validate([
            'company_json'      => 'required',
        ]);
        // dd($request->company_json);
        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/companies/update-parent',
            'form_params' => array(
                'companies' => $request->company_json
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $posApi = UserApi::postData( $postParam );
        $dataDecode = json_decode($posApi);

        // dd($dataDecode);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            // if(!empty($dataDecode->type))
            //     return redirect('reporting/types/'.$dataDecode->type->id.'/edit/');

            return redirect('companies/structure');
        }
        else
        {
            if(!empty($dataDecode->error))
                \Session::flash('flash_error', implode(', ', $dataDecode->error));

            \Session::flash('flash_success', $dataDecode->message);
            return redirect('companies/structure');
        }
    }

    public function getCompanyList(Request $request)
    {
        $return = array(
            'data' => '',
            'all' => 0,
            'iTotalRecords' => 0,
            'iTotalDisplayRecords' => 0,
            'sEcho' => (int)$request->input('sEcho',true)
        );

        if(!GlobalHelper::userCan($request,'read-company'))
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
            'endpoint'  => 'v'.config('app.api_ver'). '/company',
            'form_params' => array(
                'page' => $page,
                'per_page' => $this->limit,
                'sort_by' => $order_by,
                'keyword' => '',
                'sort' => $order,
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
                    if(!empty($dataDecode->data) && !empty($dataDecode->data->companies))
                    {
                        $return['all'] = $return['iTotalRecords'] = $return['iTotalDisplayRecords'] = $dataDecode->data->total_records;

                        foreach($dataDecode->data->companies as $l)
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

                            if(GlobalHelper::userCan($request,'update-company'))
                                $l->update = 1;

                            if(GlobalHelper::userCan($request,'delete-company'))
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

                        $return['data'] = $dataDecode->data->companies;
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

        if(!GlobalHelper::userCan($request,'read-company'))
        {
            $return['error'] = 'You don\'t have permission to access the page you requested.';
            return response()->json($return, $this->successStatus);
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/companies',
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
        $companies = array();

        if(!empty($dataDecode->code))
        {
            switch($dataDecode->code)
            {
                case 200:  
                    if(!empty($dataDecode->data) && !empty($dataDecode->data->companies))
                    {
                        $return['all'] = $return['iTotalRecords'] = $return['iTotalDisplayRecords'] = $dataDecode->data->total_records;
                        $i = 0;
                        foreach($dataDecode->data->companies as $company)
                        {
                            if(empty($company->parent_id))
                            {
                                $companies[$i] = array(
                                    'id' => $company->id,
                                    'content' => $company->company_name,
                                );

                                if(!empty($this->hierarcyCompany($dataDecode->data->companies, $company->id)))
                                    $companies[$i]['children'] = $this->hierarcyCompany($dataDecode->data->companies, $company->id);
                                $i++;
                            }

                        }
                        $return['data'] = $companies;
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

    private function hierarcyCompany($companies, $parent_id)
    {
        $companies_susun = array();
        $i = 0;
        foreach ($companies as $company) 
        {
            if($company->parent_id == $parent_id)
            {
                $companies_susun[$i] = array(
                    'id' => $company->id,
                    'content' => $company->company_name,
                );

                if(!empty($this->hierarcyCompany($companies, $company->id)))
                    $companies_susun[$i]['children'] = $this->hierarcyCompany($companies, $company->id);
                $i++;
            }
        }

        return $companies_susun;
    }
}
