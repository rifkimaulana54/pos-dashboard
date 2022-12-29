<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GlobalHelper;
use OrderApi;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('custom_auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }


    public function heartbeat(Request $request)
    {
        return response()->json(['success' => true], 200);
    }

    public function getOrderReportSummary(Request $request)
    {
        // dd($request->all());
        $return = array(
            'error' => 'Order tidak ditemukan',
            'orders' => 0,
            'order_waiting_lists' => 0,
            'income' => 0,
            'all' => 0,
            'data' => null,
            'iTotalRecords' => 0,
            'iTotalDisplayRecords' => 0,
            'sEcho' => (int)$request->input('sEcho', true)
        );

        if(!GlobalHelper::userCan($request,'read-orders')) 
        {
            $return['error'] = 'You don\'t have permission to access the page you requested.';
            return response()->json($return, $this->successStatus);
        }

        $user_token = $request->user_token;

        // $postParam = array(
        //         'keyword' => '',
        //         'sort' => 'asc',
        //         'filter'  => [],
        //         'date_filter' => null
        //     );
        $postParam = array(
            'endpoint'  => 'v'.config('app.api_ver'). '/order/filter',
            'form_params' => array(
                'keyword' => '',
                'sort' => 'asc',
                'filter_not' => null,
                'filter' => array(
                    'company_id' => !empty(session('companies')) ? array_column(session('companies'), 'id') : 1,
                ),
                'date_filter' => null
            ),
            'headers' => [ 'Authorization' => 'Bearer '.$user_token ]
        );

        if (!empty($request->order_time)) 
        {
            $dates = explode(" - ", $request->order_time);
            $postParam['form_params']['date_filter'] = GlobalHelper::maybe_serialize([
                'updated_at'  => [
                    'start'  => date('Y-m-d', strtotime($dates[0])) . ' 00:00:00',
                    'end'       => date('Y-m-d', strtotime($dates[1])) . ' 23:59:59'
                ]
            ]);

            // dd($dates);

            if($dates[0] == $dates[1])
                $postParam['form_params']['group_by'] = 'order_date';
        }

        if(!GlobalHelper::userRole($request, 'superadmin'))
            $postParam['form_params']['filter']['store_id'] = $request->store_id;
        if(!empty($postParam['form_params']['filter']))
            $postParam['form_params']['filter'] = json_encode($postParam['form_params']['filter']);

        $orderApi = OrderApi::postData( $postParam );
        $dataDecode = json_decode($orderApi);
        // dd($dataDecode);

        if(!empty($dataDecode->data) && !empty($dataDecode->data->total_records)) 
        {
            unset($return['error']);

            $average_times = array_column($dataDecode->data->orders, 'total_income_time');

            if (!empty($dataDecode->data->orders)) {
                $reports = $dataDecode->data->orders;
                // $dates = [];
                // $min_date = strtotime($dates[0]);
                // $max_date = strtotime($dates[1]);
                // $date = false;
                // $report_datas = [];
                // dd($reports);
                foreach ($reports as $report) {
                    if (!empty($report->transaction_date)) {
                        $report->transaction_date = date('d/m/Y', strtotime($report->transaction_date));
                    }
                }

                $return['data'] = $reports;
            }
            // dd($average_times);
            // $return['average_times'] = round((array_sum($average_times) / count($dataDecode->data->queue_transactions)) / 60);
                
            $filters = $dataDecode->data->filters;
            $return['orders'] = $filters->orders;
            if(isset($filters->order_waiting_lists))
                $return['order_waiting_lists'] = $filters->order_waiting_lists;
            if(isset($filters->income))
                $return['income'] = $filters->income;
        }

        return response()->json($return, $this->successStatus);
    }
}
