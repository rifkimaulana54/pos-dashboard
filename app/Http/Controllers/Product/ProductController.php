<?php

namespace App\Http\Controllers\Product;

use GlobalHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use AssetApi;
use File;

use ProductApi;
use UserApi;
use Excel;
use stdClass;


class ProductController extends Controller
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
        if(!GlobalHelper::userCan($request,'read-product'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        return view('product.index', 
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
        if(!GlobalHelper::userCan($request,'create-product'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('products');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v' . config('app.api_ver') . '/category',
            'form_params' => array(
                'filter' => json_encode(array(
                    'status' => 1,
                    'company_id' => !empty(session('companies')) ? array_column(session('companies'), 'id') : 1,
                ))
            ),
            'headers' => ['Authorization' => 'Bearer ' . $user_token]
        );

        $catApi = ProductApi::postData($postParam);
        $catDecode = json_decode($catApi);

        if(!empty($catDecode) && $catDecode->code !== 200)
        {
            \Session::flash('flash_error', $catDecode->message);
            return redirect('products');
        }

        if(!empty($catDecode->data->categories))
            $categories = $catDecode->data->categories;
        
        $getParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/our-store',
            'form_params' => array(
                'filter' => json_encode(array(
                    'status' => 1,
                    'company_id' => !empty(session('companies')) ? array_column(session('companies'), 'id') : 1,
                ))
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $posApi = UserApi::postData( $getParam );
        $dataDecode = json_decode($posApi);

        if(!empty($dataDecode) && $dataDecode->code !== 200)
        {
            \Session::flash('flash_error', $dataDecode->message);
            return redirect('products');
        }

        if(!empty($dataDecode->data->stores))
            $stores = $dataDecode->data->stores;

        return view('product.edit', 
            [
                'request'    => $request,
                'categories' => !empty($categories) ? $categories : array(),
                'stores' => !empty($stores) ? $stores : array()
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
        if(!GlobalHelper::userCan($request,'create-product'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('products');
        }

        // dump($request->all());
        $user_token = $request->user_token;

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'price'      => 'required',
        ]);

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/product/store',
            'form_params' => array(
                'product_name'        => $request->input('name'),
                'product_price'       => (int)str_replace(',','',$request->input('price')),
                'category_id'         => !empty($request->category_id) ? $request->category_id : '',
                'product_description' => !empty($request->product_description) ? $request->product_description : '',
                'company_id'          => !empty(session('company')['id']) ? session('company')['id'] : 1,
                'meta'                => !empty($request->meta) ? GlobalHelper::maybe_unserialize($request->meta) : array()
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $productApi = ProductApi::postData( $postParam );
        $dataDecode = json_decode($productApi);
        // dd($dataDecode);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            return redirect('products/create')->withInput();
        }
        else
        {
            \Session::flash('flash_success', $dataDecode->message);
            return redirect('products');
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
        if(!GlobalHelper::userCan($request, 'read-product'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/product/' . $id,
            'form_params' => array(),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $productApi = ProductApi::getData($postParam);
        $dataDecode = json_decode($productApi);

        if(!empty($dataDecode) && $dataDecode->code !== 200)
        {
            \Session::flash('flash_error', $dataDecode->message);
            return redirect('products');
        }

        if(!empty($dataDecode->data->product))
        {
            $product = $dataDecode->data->product;
            if(!empty($product->metas))
            {
                foreach ($product->metas as $meta) 
                {
                    $product->meta[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);
                }
            }
        }
        // dd($product);
        return view('product.detail', 
            [
                'request'   => $request,
                'product'   => !empty($product) ? $product : array(),
                'meta'      => !empty($product->meta) ? $product->meta : array(),
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
        if(!GlobalHelper::userCan($request,'update-product'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('products');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/product/' . $id,
            'form_params' => array(),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $productApi = ProductApi::getData($postParam);
        $dataDecode = json_decode($productApi);

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
            $categories = $catDecode->data->categories;

        if(!empty($dataDecode->data->product))
        {
            $product = $dataDecode->data->product;
            if(!empty($product->metas))
            {
                foreach ($product->metas as $meta) 
                {
                    $product->meta[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);
                }
            }
        }

        return view('product.edit',
            [
                'request'    => $request,
                'product'    => !empty($product) ? $product : array(),
                'categories' => !empty($categories) ? $categories : array(),
                'metas'      => !empty($product->meta) ? $product->meta : array()
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
        if(!GlobalHelper::userCan($request,'read-product'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('products');
        }

        $user_token = $request->user_token;
        // dd($request->all());
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
        ]);

        $putParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/product/'.$id,
            'form_params' => array(
                'product_name'        => $request->input('name'),
                'product_price'       => (int)str_replace(',','',$request->input('price')),
                'category_id'         => $request->category_id,
                'product_description' => !empty($request->product_description) ? $request->product_description : '',
                'company_id'          => !empty(session('company')['id']) ? session('company')['id'] : 1,
                'meta'                => !empty($request->meta) ? GlobalHelper::maybe_unserialize($request->meta) : array(),
                'status'              => !empty($request->status) ? 1 : 2,
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $userApi = ProductApi::updateData( $putParam );
        $dataDecode = json_decode($userApi);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            return redirect('products')->withInput();
        }
        else
        {
            \Session::flash('flash_success', $dataDecode->message);
            return redirect('products');
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
            if(!GlobalHelper::userCan($request,'update-product'))
            {
                $return['error'] = 'You don\'t have permission to access the page you requested.';
                return response()->json($return, $this->successStatus);
            }

            $user_token = $request->user_token;

            $putParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/product/'.$id,
                'form_params' => array(
                    'status'      => 0,
                ),
                'headers' => [ 'Authorization' => 'Bearer '. $user_token ]
            );

            $productApi = ProductApi::updateData( $putParam );

            $dataDecode = json_decode($productApi);
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
            if(!GlobalHelper::userCan($request,'update-product'))
            {
                $return['error'] = 'You don\'t have permission to access the page you requested.';
                return response()->json($return, $this->successStatus);
            }

            $user_token = $request->user_token;

            $putParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/product/'.$id,
                'form_params' => array(
                    'status'  => 1,
                ),
                'headers' => [ 'Authorization' => 'Bearer '. $user_token ]
            );

            $productApi = ProductApi::updateData( $putParam );

            $dataDecode = json_decode($productApi);
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

    public function getProductList(Request $request)
    {
        $return = array(
            'data' => '',
            'all' => 0,
            'iTotalRecords' => 0,
            'iTotalDisplayRecords' => 0,
            'sEcho' => (int)$request->input('sEcho',true)
        );

        if(!GlobalHelper::userCan($request,'read-product'))
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
            // case 0:$order_by='id';break;
            default:$order_by='product_display_name';break;
        }

        $order = $request->input('sSortDir_0',true);

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/product',
            'form_params' => array(
                'page' => $page,
                'per_page' => $this->limit,
                'sort_by' => $order_by,
                'keyword' => '',
                'sort' => $order,
                'filter' => json_encode(array(
                    'company_id' => !empty(session('companies')) ? array_column(session('companies'), 'id') : 1,
                )),
                'date_filter' => null
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        if(!empty($keyword = $request->input('sSearch')))
            $postParam['form_params']['keyword'] = $keyword;

        $productApi = ProductApi::postData( $postParam );
        $dataDecode = json_decode($productApi);

        if(!empty($dataDecode->code))
        {
            switch($dataDecode->code)
            {
                case 200:  
                    if(!empty($dataDecode->data) && !empty($dataDecode->data->products))
                    {
                        $return['all'] = $return['iTotalRecords'] = $return['iTotalDisplayRecords'] = $dataDecode->data->total_records;

                        foreach($dataDecode->data->products as $product)
                        {
                            $product->created_html = date('M, d-Y H:i:s', strtotime($product->created_at));
                            $product->updated_html = date('M, d-Y H:i:s', strtotime($product->updated_at));

                            switch($product->status)
                            {
                                case 2:
                                    $product->status_html = '<span class="badge badge-info">' . $product->status_label . '</span>';
                                    break;
                                case 0:
                                    $product->status_html = '<span class="badge badge-danger">' . $product->status_label . '</span>';
                                    break;
                                default:
                                    $product->status_html = '<span class="badge badge-success">' . $product->status_label . '</span>';
                                    break;
                            }

                            if(GlobalHelper::userRole($request,'superadmin') || GlobalHelper::userRole($request, 'admin') || (GlobalHelper::userCan($request,'update-item-products') && !empty(session('company')['id']) && session('company')['id'] == $product->company_id))
                                $product->update = 1;

                            if (GlobalHelper::userRole($request, 'superadmin') || GlobalHelper::userRole($request, 'admin') || (GlobalHelper::userCan($request, 'delete-item-products') && !empty(session('company')['id']) && session('company')['id'] == $product->company_id))
                                $product->delete = 1;
                            
                            if(!empty($product->metas))
                            {
                                $metas = array();
                                foreach($product->metas as $meta)
                                    $metas[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);

                                if(!empty($metas['image']))
                                    $product->image_html = '<div style="background-image: url(' . $metas['image']['media_path'] . '); height: 75px; width: 75px; background-repeat: no-repeat;background-position: center;background-repeat: no-repeat;background-size: cover;"></div>';
                            }
                        }

                        $return['data'] = $dataDecode->data->products;
                        // dd($return['data']);
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

    public function upload(Request $request)
    {
        if(!GlobalHelper::userCan($request,'create-product'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        return view('product.upload', [
            'request' => $request
        ]);
    }

    public function confirm_upload(Request $request)
    {
        if(!GlobalHelper::userCan($request,'create-product'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $validated = $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        $rows = array();
        if($request->hasFile('file'))
        {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $filepath = $file->getPathName();
            $filetype = $file->getMimeType();

            $postParam = array(
                'endpoint'  => 'v'.config('app.api_ver').'/store',
                'form_params' => array(
                    'multipart' => [
                        [
                            'name'      => 'file',
                            'filename'  => $filename,
                            'Mime-Type' => $filetype,
                            'contents'  => fopen( $filepath, 'r' )
                        ]
                    ]
                ),
                'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
            );

            $assetApi = AssetApi::postData( $postParam );

            $dataDecode = json_decode($assetApi);

            if(!empty($dataDecode->data) && !empty($dataDecode->data->media))
                $media = $dataDecode->data->media;

        }

        if($request->hasfile('file'))
        {
            if(!File::isDirectory(public_path('excel')))
            {
                File::makeDirectory(public_path('excel'));
            }

            $fileNameWithExt = $request->file('file')->getClientOriginalName();

            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $fileExt = $request->file('file')->getClientOriginalExtension();

            $fileNameToStore = time().'.'.$fileExt;

            $path = $request->file('file')->storeAs('public/excel',$fileNameToStore);

            $savePath = "app/".$path;

            $request->session()->put('upload_excel', $savePath);

            return redirect('products/upload/show');

        }

        \Session::flash('flash_error', 'Excel File not found.');
        return redirect('products');
    }

    public function show_upload(Request $request)
    {
        if(!GlobalHelper::userCan($request,'create-product'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('product');
        }

        if (empty( $excel = session('upload_excel')))
        {
            \Session::flash('flash_error', 'Excel File not found.');
            return redirect('product');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/product',
            'form_params' => array(),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );
        
        $posApi = ProductApi::postData( $postParam );
        $dataDecode = json_decode($posApi);

        
        $product_exist = !empty($dataDecode->data->products) ? array_column($dataDecode->data->products, 'product_display_name', 'id') : array();

        $rows = Excel::toArray(new stdClass, storage_path($excel));

        $datas = array();
        $error = array();
        $total = array();
        $product_exist_excel = array();
        $er = false;

        foreach($rows[0] as $d => $data)
        {
            if ($d > 0) 
            {
                $total[$d] = 0;
                $empty = 0;

                foreach($data as $c => $val)
                {
                    if(empty($val) && $c < 4)
                        $empty++;
                }

                if($empty == 4)
                    continue;

                if(empty($data[0]) || empty($data[1]) || empty($data[3]))
                {
                    $error[$d][] = 'Mohon isi yang tertanda bintang';
                    $er = true;
                }

                if(!empty($data[1]) && in_array($data[1], $product_exist))
                {
                    $error[$d][] = "Nama product sudah ada didatabase";
                    $er = true;
                }

                if(!empty($data[1]) && in_array($data[1], $product_exist_excel))
                {
                    $error[$d][] = "Product sudah ada diexcel";
                    $er = true;
                }

                $data[3] = 'Rp. '.number_format($data[3]);

                if(!empty($error[$d]))
                    $data[4] = '<strong>'.implode(', ',$error[$d]).'</strong>';
                else
                    $data[4] = '';

                if(!empty($data[1]))
                    $product_exist_excel[] = $data[1];
            }

            $datas[] = $data;
        }

        $datas[0][4] = 'Error Info';

        return view('product.upload_show',[
            'rows' => $datas,
            'request' => $request
        ]);
    }

    public function finish_upload(Request $request)
    {        
        if(!GlobalHelper::userCan($request,'create-product'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('products');
        }

        $user_token = $request->user_token;

        if(!empty($request->input('cancel')))
        {
            // dd(session('upload_excel'));
            $request->session()->forget('upload_excel');
            return redirect('/products/upload');
        }

        if (empty( $excel = session('upload_excel')))
        {
            \Session::flash('flash_error', 'Excel File not found.');
            return redirect('/products/upload');
        }

        $rows = \Excel::toArray(new stdClass, storage_path($excel));

        $user_token = $request->user_token;

        $data_mapping = array();

        foreach($rows[0] as $d => $data)
        {
            if ($d > 0) 
            {   
                $empty = 0;

                foreach($data as $c => $val)
                {
                    if(empty($val) && $c < 4)
                        $empty++;
                }

                if($empty == 4)
                    continue;

                $data_mapping[] = array(
                    'category_id' => $data[0],
                    'product_name' => $data[1],
                    'product_description' => $data[2],
                    'product_price' => $data[3],
                );
            }
        }

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/product/import',
            'form_params' => array(
                'products'     => json_encode($data_mapping),
                'company_id' => !empty(session('company')['id']) ? session('company')['id'] : 1
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $postApi = ProductApi::postData( $postParam );
        $dataDecode = json_decode($postApi);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            // $error_message = $dataDecode->data->error;
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            return redirect('products')->withInput();
        }
        else
        {
            $error_message = $dataDecode->data->error;
            $success_message[] = $dataDecode->message;
        }

        if(!empty($error_message))
        {
            return $this->errorUpload($request, $error_message);
        }
        else
        {
            if (!empty($success_message)) 
            {
                $message_success = '<strong>'.implode(', ',array_unique($success_message)).'</strong>';
                \Session::flash('flash_success', $message_success);

                $request->session()->forget('upload_excel');

                return redirect('products');
            }
        }
    }
}
