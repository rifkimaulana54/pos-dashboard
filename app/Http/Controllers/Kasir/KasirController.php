<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GlobalHelper;
use ProductApi;
use OrderApi;
use UserApi;

class KasirController extends Controller
{
    public $successStatus = 200;

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
        // dd($request);

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v' . config('app.api_ver') . '/detail/'.$request->user_id,
            'form_params' => array(),
            'headers' => ['Authorization' => 'Bearer ' . $user_token]
        );

        $userApi = UserApi::getData($postParam);
        $userDecode = json_decode($userApi);

        if (!empty($userDecode->data->user))
            $user = $userDecode->data->user;
        
        if(!empty($user->metas))
            foreach ($user->metas as $meta) 
                $metas[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);

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

        if(!empty($catDecode->data->categories))
            $categories = $catDecode->data->categories;

        $postParam = array(
            'endpoint'  => 'v' . config('app.api_ver') . '/order',
            'form_params' => array(
                'filter' => json_encode(array(
                    'store_id' => $request->store_id,
                    'status' => 2,
                    'company_id' => !empty(session('companies')) ? array_column(session('companies'), 'id') : 1,
                ))
            ),
            'headers' => ['Authorization' => 'Bearer ' . $user_token]
        );

        $orderApi = OrderApi::postData($postParam);
        $orderDecode = json_decode($orderApi);

        if (!empty($orderDecode->data->orders))
            $orders = $orderDecode->data->orders;

        // dd($orderDecode);
        return view('kasir.index', [
            'request' => $request,
            'metas' => !empty($metas) ? $metas : array(),
            'categories' => !empty($categories) ? $categories : array(),
            'count_orders' => !empty($orderDecode->data->total_records) ? $orderDecode->data->total_records : array(),
            'orders' => !empty($orders) ? $orders : array(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        if(!GlobalHelper::userCan($request, 'create-orders'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;
        // dd($request->all());

        $item_orders = [];
        if(!empty($request->items))
        {
            foreach($request->items as $i => $items)
            {
                if (is_int($i) && !empty($items['column']['product_id'])) 
                {
                    $item_orders[] = array(
                        'product_id' => !empty($items['column']['product_id']) ? $items['column']['product_id'] : '',
                        'order_qty' => !empty($items['column']['claim_qty']) ? $items['column']['claim_qty'] : '',
                        'default_price' => !empty($items['column']['default_price']) ? $items['column']['default_price'] : '',
                        'order_subtotal' => !empty($items['column']['subtotal']) ? $items['column']['subtotal'] : '',
                    );
                }
                
            }
        }

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/order/store',
            'form_params' => array(
                'company_id' => !empty(session('companies')) ? array_column(session('companies'), 'id') : 1,
                'customer_name' => !empty($request->customer) ? $request->customer : '',
                'total_order' => !empty($request->grandtotal) ? $request->grandtotal : '',
                'items' => !empty($item_orders) ? GlobalHelper::maybe_serialize($item_orders) : '',
                'status'      => 2,
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        $orderApi = OrderApi::postData( $postParam );
        $dataDecode = json_decode($orderApi);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            return redirect('kasir')->withInput();
        }
        else
        {
            \Session::flash('flash_success', $dataDecode->message);
            if(!empty($request->btn_bayar))
            {
                return redirect('kasir/order-list');
            }
            else
            {
                return redirect('kasir');
            }
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
            // $error_message = $dataDecode->message;
            // \Session::flash('flash_error', $error_message);

            return response()->json('Gagal add to cart', $this->successStatus);
        }
        else
        {
            return response()->json($order);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        dd('masuk update');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    

    public function getProductList(Request $request) 
    {
        $return = array(
            'data' => '',
            'all' => 0,
            'iTotalRecords' => 0,
            'iTotalDisplayRecords' => 0,
            'sEcho' => (int)$request->input('sEcho', true)
        );

        if(!GlobalHelper::userCan($request,'read-product'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;
        $postParam = array(
            'endpoint'  => 'v' . config('app.api_ver') . '/product',
            'form_params' => array(
                'keyword' => '',
                'sort_by' => 'product_name',
                'sort'    => 'asc',
                'filter' => array(
                    'company_id' => !empty(session('companies')) ? array_column(session('companies'), 'id') : 1,
                ),
            ),
            'headers' => ['Authorization' => 'Bearer ' . $user_token]
        );


        $postParam['form_params']['filter']['status'] = 1;
        if(!empty($keyword = $request->search))
            $postParam['form_params']['keyword'] = addcslashes($keyword, "'");
        if(!empty($request->category_id))
            $postParam['form_params']['filter']['category_id'] = $request->category_id;
        if(!empty($postParam['form_params']['filter']))
            $postParam['form_params']['filter'] = json_encode($postParam['form_params']['filter']);
        $catApi = ProductApi::postData($postParam);
        $dataDecode = json_decode($catApi);

        if (!empty($dataDecode->data->products))
        {
            $return['all'] = $return['iTotalRecords'] = $return['iTotalDisplayRecords'] = $dataDecode->data->total_records;
            $return['data'] = $dataDecode->data->products;
        }
        // dd($products);
        return response()->json($return, $this->successStatus);

    }

    public function orderList(Request $request)
    {
        if(!GlobalHelper::userCan($request, 'read-orders'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
        }

        $user_token = $request->user_token;

        $postParam = array(
            'endpoint'  => 'v' . config('app.api_ver') . '/detail/'.$request->user_id,
            'form_params' => array(),
            'headers' => ['Authorization' => 'Bearer ' . $user_token]
        );

        $userApi = UserApi::getData($postParam);
        $userDecode = json_decode($userApi);

        if (!empty($userDecode->data->user))
            $user = $userDecode->data->user;
        
        if(!empty($user->metas))
            foreach ($user->metas as $meta) 
                $metas[$meta->meta_key] = GlobalHelper::maybe_unserialize($meta->meta_value);

        $postParam = array(
            'endpoint'  => 'v' . config('app.api_ver') . '/order',
            'form_params' => array(
                'filter' => json_encode(array(
                    'store_id' => $request->store_id,
                    'status' => 2,
                    'company_id' => !empty(session('companies')) ? array_column(session('companies'), 'id') : 1,
                ))
            ),
            'headers' => ['Authorization' => 'Bearer ' . $user_token]
        );

        $orderApi = OrderApi::postData($postParam);
        $orderDecode = json_decode($orderApi);

        if (!empty($orderDecode->data->orders))
            $orders = $orderDecode->data;

        // dd($orderDecode);
        return view('kasir.order-list', [
            'request' => $request,
            'metas' => !empty($metas) ? $metas : array(),
            'orders' => !empty($orders) ? $orders : array(),
        ]);
    }
}
