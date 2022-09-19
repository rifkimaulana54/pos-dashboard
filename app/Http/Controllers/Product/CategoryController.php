<?php

namespace App\Http\Controllers\Product;

use GlobalHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ProductApi;
use UserApi;

class CategoryController extends Controller
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
        if(!GlobalHelper::userCan($request,'read-category'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        return view('product.category.index', 
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
        if(!GlobalHelper::userCan($request,'read-category'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('users/category');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v' . config('app.api_ver') . '/category',
            'form_params' => array(
                'filter' => json_encode(array(
                    'status' => 1
                ))
            ),
            'headers' => ['Authorization' => 'Bearer ' . $user_token]
        );

        $catApi = ProductApi::postData($postParam);
        $catDecode = json_decode($catApi);

        if(!empty($catDecode->data->categories))
            $categories = $catDecode->data->categories;

        $postParam = array(
            'endpoint'  => 'v' . config('app.api_ver') . '/company',
            'form_params' => array(
                'filter' => json_encode(array(
                    'status' => 1
                ))
            ),
            'headers' => ['Authorization' => 'Bearer ' . $user_token]
        );

        $postApi = UserApi::postData($postParam);
        $companyDecode = json_decode($postApi);

        if(!empty($companyDecode->data->companies) && $companyDecode->data->total_records > 1)
            $companies = $companyDecode->data->companies;
        
        return view('product.category.edit', 
            [
                'request'    => $request,
                'categories' => !empty($categories) ? $categories : array(),
                'companies' => !empty($companies) ? $companies : array()
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
        if(!GlobalHelper::userCan($request,'read-category'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('users/categories');
        }

        // dd($request->all());
        $user_token = $request->user_token;

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
        ]);

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/category/store',
            'form_params' => array(
                'category_name'        => $request->input('name'),
                'parent_id'            => !empty($request->parent_id) ? $request->parent_id : '',
                'category_description' => !empty($request->category_description) ? $request->category_description : '',
                // 'company_id'           => !empty($request->company_id) ? $request->company_id : []
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $categoryApi = ProductApi::postData( $postParam );
        $dataDecode = json_decode($categoryApi);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            return redirect('categories/create')->withInput();
        }
        else
        {
            \Session::flash('flash_success', $dataDecode->message);
            return redirect('categories');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if(!GlobalHelper::userCan($request, 'read-category'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/category/' . $id,
            'form_params' => array(),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $categoryApi = ProductApi::getData($postParam);
        $dataDecode = json_decode($categoryApi);

        if(!empty($dataDecode) && $dataDecode->code !== 200)
        {
            \Session::flash('flash_error', $dataDecode->message);
            return redirect('categories');
        }
        // dd($dataDecode);
        return view('product.category.detail', 
            [
                'request'    => $request,
                'category'   => !empty($dataDecode->data->category) ? $dataDecode->data->category : [],
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
        if(!GlobalHelper::userCan($request,'update-category'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('users/category');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/category/' . $id,
            'form_params' => array(),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $categoryApi = ProductApi::getData($postParam);
        $dataDecode = json_decode($categoryApi);

        if(!empty($dataDecode) && $dataDecode->code !== 200)
        {
            \Session::flash('flash_error', $dataDecode->message);
            return redirect('categories');
        }

        $catParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/category',
            'form_params' => array(
                'filter' => json_encode(array(
                    'status' => 1
                ))
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $catApi = ProductApi::postData( $catParam );
        $catDecode = json_decode($catApi);

        if(!empty($catDecode->data->categories))
        {
            $categories = $catDecode->data->categories;
        }

        $postParam = array(
            'endpoint'  => 'v' . config('app.api_ver') . '/company',
            'form_params' => array(
                'filter' => json_encode(array(
                    'status' => 1
                ))
            ),
            'headers' => ['Authorization' => 'Bearer ' . $user_token]
        );

        $postApi = UserApi::postData($postParam);
        $companyDecode = json_decode($postApi);
        
        if(!empty($companyDecode->data->companies) && $companyDecode->data->total_records > 1)
            $companies = $companyDecode->data->companies;
        
        return view('product.category.edit',
            [
                'request'    => $request,
                'category'   => !empty($dataDecode->data->category) ? $dataDecode->data->category : [],
                'categories' => !empty($categories) ? $categories : array(),
                'companies' => !empty($companies) ? $companies : array(),
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
        if(!GlobalHelper::userCan($request,'read-category'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('users/category');
        }

        $user_token = $request->user_token;
        // dd($request->all());
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
        ]);

        $putParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/category/'.$id,
            'form_params' => array(
                'category_name'        => $request->input('name'),
                'parent_id'            => !empty($request->parent_id) ? $request->parent_id : '',
                'category_description' => !empty($request->category_description) ? $request->category_description : '',
                // 'company_id'           => !empty($request->company_id) ? $request->company_id : []
                'status'               => !empty($request->status) ? 1 : 2,
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $userApi = ProductApi::updateData( $putParam );
        $dataDecode = json_decode($userApi);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            return redirect('categories/'.$id)->withInput();
        }
        else
        {
            \Session::flash('flash_success', $dataDecode->message);
            return redirect('categories');
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

            if(!GlobalHelper::userCan($request,'update-category'))
            {
                $return['error'] = 'You don\'t have permission to access the page you requested.';
                return response()->json($return, $this->successStatus);
            }

            $user_token = $request->user_token;

            $putParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/category/'.$id,
                'form_params' => array(
                    'status'      => 0,
                ),
                'headers' => [ 'Authorization' => 'Bearer '. $user_token ]
            );

            $userApi = ProductApi::updateData( $putParam );

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

            if(!GlobalHelper::userCan($request,'update-category'))
            {
                $return['error'] = 'You don\'t have permission to access the page you requested.';
                return response()->json($return, $this->successStatus);
            }

            $user_token = $request->user_token;

            $putParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/category/'.$id,
                'form_params' => array(
                    'status'  => 1,
                ),
                'headers' => [ 'Authorization' => 'Bearer '. $user_token ]
            );

            $userApi = ProductApi::updateData( $putParam );

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
        }
    }

    public function getCategoryList(Request $request)
    {
        $return = array(
            'data' => '',
            'all' => 0,
            'iTotalRecords' => 0,
            'iTotalDisplayRecords' => 0,
            'sEcho' => (int)$request->input('sEcho',true)
        );

        if(!GlobalHelper::userCan($request,'read-category'))
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
            default:$order_by='category_display_name';break;
        }

        $order = $request->input('sSortDir_0',true);

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/category',
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

        $categoryApi = ProductApi::postData( $postParam );
        $dataDecode = json_decode($categoryApi);

        if(!empty($dataDecode->code))
        {
            switch($dataDecode->code)
            {
                case 200:  
                    if(!empty($dataDecode->data) && !empty($dataDecode->data->categories))
                    {
                        $return['all'] = $return['iTotalRecords'] = $return['iTotalDisplayRecords'] = $dataDecode->data->total_records;

                        foreach($dataDecode->data->categories as $category)
                        {
                            $category->created_html = date('d/M/Y H:i:s', strtotime($category->created_at));
                            $category->updated_html = date('d/M/Y H:i:s', strtotime($category->updated_at));

                            switch($category->status)
                            {
                                case 2:
                                    $category->status_html = '<span class="badge badge-info">' . $category->status_label . '</span>';
                                    break;
                                case 0:
                                    $category->status_html = '<span class="badge badge-danger">' . $category->status_label . '</span>';
                                    break;
                                default:
                                    $category->status_html = '<span class="badge badge-success">' . $category->status_label . '</span>';
                                    break;
                            }

                            if(GlobalHelper::userCan($request, 'delete-category'))
                                $category->update = 1;

                            if(GlobalHelper::userCan($request,'delete-category'))
                                $category->delete = 1;
                        }

                        $return['data'] = $dataDecode->data->categories;
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
