<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GlobalHelper;
use ProductApi;
use OrderApi;

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
        if(!GlobalHelper::userCan($request,'read-store'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('home');
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

        if(!empty($catDecode->data->categories))
            $categories = $catDecode->data->categories;

        $postParam = array(
            'endpoint'  => 'v' . config('app.api_ver') . '/order',
            'form_params' => array(
                'filter' => json_encode(array(
                    'company_id' => !empty(session('companies')) ? array_column(session('companies'), 'id') : 1,
                ))
            ),
            'headers' => ['Authorization' => 'Bearer ' . $user_token]
        );

        $orderApi = OrderApi::postData($postParam);
        $orderDecode = json_decode($orderApi);

        if (!empty($orderDecode->data->orders))
            $orders = $orderDecode->data->orders;

        // dd($orders);
        return view('kasir.index', [
            'request' => $request,
            'categories' => !empty($categories) ? $categories : array(),
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
        $test = $request;
        foreach ($request->items as $value) {
            if($value['column']['product_id'] == null)
                continue;
            dump($value['column']);
        }
        dd($test);
        // dd(array_sum($request->items['subtotal']));
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
        //
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
        {
            $postParam['form_params']['filter'] = json_encode($postParam['form_params']['filter']);
        }
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

    public function addToCart(Request $request)
    {
        if(!GlobalHelper::userCan($request,'read-orders'))
        {
            \Session::flash('flash_error', 'You don\'t have permission to access the page you requested.');
            return redirect('users/categories');
        }

        $user_token = $request->user_token;

        $items = [];
        $items[] = $request->id;
        $items[] = $request->qty;
        $items[] = $request->subtotal;

        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver').'/order/store',
            'form_params' => array(
                'items'  => json_decode(json_encode($items)),
                'status' => 5,
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        // dd($postParam);

        $orderApi = OrderApi::postData( $postParam );
        $dataDecode = json_decode($orderApi);

        if(!empty($dataDecode->code) && $dataDecode->code != 200)
        {
            $error_message = $dataDecode->message;
            \Session::flash('flash_error', $error_message);

            return response()->json('Gagal add to cart', $this->successStatus);
        }
        else
        {
            \Session::flash('flash_success', $dataDecode->message);
            return response()->json('Berhasil add to cart', $this->successStatus);
        }
    }
}
