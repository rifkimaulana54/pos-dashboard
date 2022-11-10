<?php

namespace App\Http\Controllers\Order;

use GlobalHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OrderApi;
use UserApi;
use Excel;
use App\Exports\MappingExport;

class OrderController extends Controller
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
        if(!GlobalHelper::userCan($request,'read-orders'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v' . config('app.api_ver') . '/our-store',
            'form_params' => array(
                'filter' => json_encode(array(
                    'status' => 1
                ))
            ),
            'headers' => ['Authorization' => 'Bearer ' . $user_token]
        );

        $storeApi = UserApi::postData($postParam);
        $storeDecode = json_decode($storeApi);
        
        if (!empty($storeDecode->data->stores))
            $stores = $storeDecode->data->stores;

        return view('order.index', 
            [
                'request' => $request,
                'stores' => !empty($stores) ? $stores : []
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
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if(!GlobalHelper::userCan($request, 'read-orders'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/order/' . $id,
            'form_params' => array(),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $orderApi = OrderApi::getData($postParam);
        $dataDecode = json_decode($orderApi);

        if(!empty($dataDecode->data->order))
            $order = $dataDecode->data->order;

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);
        }

        return view('order.detail', 
            [
                'request'   => $request,
                'order'   => !empty($order) ? $order : array(),
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
        if(!GlobalHelper::userCan($request,'update-orders'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('users/orders');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/product/' . $id,
            'form_params' => array(),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $productApi = OrderApi::getData($postParam);
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

        $catApi = OrderApi::postData( $catParam );
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

        return view('order.edit',
            [
                'request'    => $request,
                'orders'    => !empty($product) ? $product : array(),
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
        if(!GlobalHelper::userCan($request,'read-orders'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('users/orders');
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

        $userApi = OrderApi::updateData( $putParam );
        $dataDecode = json_decode($userApi);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            return redirect('orders/'.$id)->withInput();
        }
        else
        {
            \Session::flash('flash_success', $dataDecode->message);
            return redirect('orders');
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
            if(!GlobalHelper::userCan($request,'update-orders'))
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

            $productApi = OrderApi::updateData( $putParam );

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
            if(!GlobalHelper::userCan($request,'update-orders'))
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

            $productApi = OrderApi::updateData( $putParam );

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

    public function getOrderList(Request $request)
    {
        $return = array(
            'data' => '',
            'all' => 0,
            'iTotalRecords' => 0,
            'iTotalDisplayRecords' => 0,
            'sEcho' => (int)$request->input('sEcho',true)
        );

        if(!GlobalHelper::userCan($request,'read-orders'))
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
            case 7:$order_by='status';break;
            case 6:$order_by='updated_at';break;
            case 5:$order_by='created_at';break;
            case 4:$order_by='store_id';break;
            case 3:$order_by='total_order';break;
            case 2:$order_by='customer_name';break;
            case 0:$order_by='id';break;
            default:$order_by='order_code';break;
        }

        $order = $request->input('sSortDir_0');

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/order',
            'form_params' => array(
                'page' => $page,
                'per_page' => $this->limit,
                'sort_by' => $order_by,
                'keyword' => '',
                'sort' => !empty($order) ? $order : 'desc',
                'filter' => array(
                    'company_id' => !empty(session('companies')) ? array_column(session('companies'), 'id') : 1,
                ),
                'date_filter' => null
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        if(!empty($request->input('sSearch')))
            $postParam['form_params']['keyword'] = $request->input('sSearch');
        if(!empty($request->input('search')))
        {
            $postParam['form_params']['sort'] = 'asc';
            $postParam['form_params']['keyword'] = $request->input('search');
        }
        if(!empty($request->store))
            $postParam['form_params']['filter']['store_id'] = $request->store;
        if($request->user_role[0] == 'kasir')
            $postParam['form_params']['filter']['store_id'] = $request->store_id;
        if(!empty($request->status))
            $postParam['form_params']['filter']['status'] = $request->status;
        if(!empty($postParam['form_params']['filter']))
            $postParam['form_params']['filter'] = json_encode($postParam['form_params']['filter']);

        $productApi = OrderApi::postData( $postParam );
        $dataDecode = json_decode($productApi);

        if(!empty($dataDecode->code))
        {
            switch($dataDecode->code)
            {
                case 200:  
                    if(!empty($dataDecode->data) && !empty($dataDecode->data->orders))
                    {
                        $return['all'] = $return['iTotalRecords'] = $return['iTotalDisplayRecords'] = $dataDecode->data->total_records;

                        foreach($dataDecode->data->orders as $order)
                        {
                            $order->created_html = date('M, d-Y H:i:s', strtotime($order->created_at));
                            $order->updated_html = date('M, d-Y H:i:s', strtotime($order->updated_at));
                            $order->store_html = $order->store->store_name;
                            $order->subtotal_html = 'Rp. '.number_format($order->total_order);

                            // if(empty($request->input('search')))
                            // {
                            //     switch($order->status)
                            //     {
                            //         case 2:
                            //             $order->status_html = '<span class="badge badge-info">' . $order->status_label . '</span>';
                            //             break;
                            //         case 3:
                            //             $order->status_html = '<span class="badge badge-warning">' . $order->status_label . '</span>';
                            //             break;
                            //         default:
                            //             $order->status_html = '<span class="badge badge-success">' . $order->status_label . '</span>';
                            //             break;
                            //     }
                            // }

                            // if(GlobalHelper::userRole($request,'superadmin') || (GlobalHelper::userCan($request,'update-item-orders') && !empty(session('company')['id']) && session('company')['id'] == $order->company_id))
                            //     $order->update = 1;

                            // if (GlobalHelper::userRole($request, 'superadmin') || (GlobalHelper::userCan($request, 'delete-item-orders') && !empty(session('company')['id']) && session('company')['id'] == $order->company_id))
                            //     $order->delete = 1;
                        }
                        
                        $return['data'] = $dataDecode->data->orders;
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

    public function export(Request $request)
    {
        if (!GlobalHelper::userCan($request, 'read-orders')) {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        // dump($request->all());

        $name_xls = 'Order_Transaction_';

        $postParam = [
            'endpoint' => 'v' . config('app.api_ver') . '/order',
            'form_params' => [
                'keyword' => '',
                'sort_by' => 'updated_at',
                'sort' => 'desc',
            ],
            'headers' => ['Authorization' => 'Bearer ' . $request->user_token]
        ];

        $name_xls .= date('d_M_Y_H_i') . '.xlsx';

        if (!empty($request->keyword))
            $postParam['form_params']['keyword'] = $request->keyword;
        if (!empty(session('company')['id']))
            $postParam['form_params']['filter']['company_id'] = session('company')['id'];
        if ($request->user_role[0] == 'kasir')
            $postParam['form_params']['filter']['store_id'] = $request->store_id;
        if (!empty($request->filter_store))
            $postParam['form_params']['filter']['store_id'] = $request->filter_store;
        if (!empty($request->filter_status))
            $postParam['form_params']['filter']['status'] = $request->filter_status;
        if (!empty($postParam['form_params']['filter']))
            $postParam['form_params']['filter'] = json_encode($postParam['form_params']['filter']);

        // dump($postParam);

        $orderApi = OrderApi::postData($postParam);
        $dataDecode = json_decode($orderApi);

        // dd($dataDecode);

        $exports = array(
            array(
                'order_code',
                'customer_name',
                'product',
                'total_order',
                'store',
                'updated_at',
                'updated_name',
                'status'
            )
        );

        $rows = array();

        if (!empty($dataDecode->data) && !empty($dataDecode->data->orders)) {
            $orders = $dataDecode->data->orders;
            foreach ($orders as $order) {

                $product = '';
                foreach($order->mapping as $item)
                {
                    $product .= '['.$item->product->product_name . '],';
                }

                $rows[] = array(
                    'order_code'    => !empty($order->order_code) ? $order->order_code : '',
                    'customer_name' => !empty($order->customer_name) ? $order->customer_name : '',
                    'total_order'   => !empty($order->total_order) ? $order->total_order : '',
                    'product'       => !empty($product) ? $product : '',
                    'store'         => !empty($order->store->store_name) ? $order->store->store_name : '',
                    'updated_at'    => !empty($order->updated_at) ? date('Y-m-d H:i:s', strtotime($order->updated_at)) : '',
                    'updated_name'  => !empty($order->updated_name) ? $order->updated_name : '',
                    'status'        => !empty($order->status_label) ? $order->status_label : '',
                );
                
            }

            if (!empty($rows)) {
                foreach ($rows as $row){
                    $exports[] = $row;
                }
            }

            $export = new MappingExport($exports);
            return Excel::download($export, $name_xls);
        } else {
            \Session::flash('flash_error', 'Nothing to Export');
            return redirect('orders');
        }
    }
}
