<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
</head>
<body onload="window.print();">
    <div style="width: 285px; margin: auto;">
        <h3  style="text-align: center; margin-bottom: 0"><b>{{$order->store->store_name}}</b></h3>
        <div style="text-align: center"><small>{{$order->store->store_address}}</small></div>
        <hr>
        <div style="margin: 15px auto 0 15px">
            <small>{{date('d-m-Y H:i:s')}}</small>
        </div>
        <div style="margin: 0 auto 0 15px">Order Code: {{$order->order_code}}</div>
        <div style="margin: 0 auto 0 15px">Customer: {{$order->customer_name}}</div>
        <table style="margin: 15px; auto 20px; auto">
            <tbody>
                @php
                    $grandtotal = 0;
                    $pay = !empty($_GET['pay']) ? $_GET['pay'] : 0;
                @endphp
                @foreach ($order->mapping as $item)
                    <tr>
                        <td width="130">{{$item->product->product_display_name}}</td>
                        <td width="30" align="center" style="font-size: 15px; cursor: pointer">
                            {{$item->order_qty}}
                        </td>
                        <td width="80"><small>Rp. </small> {{number_format($item->order_subtotal)}}</td>
                    </tr>
                    @php
                        $grandtotal += $item->order_subtotal;
                    @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><hr></td>
                </tr>
                <tr>                        
                    <td colspan="2">Total</td>
                    <td>
                        <small>Rp. </small> {{number_format($grandtotal)}} <input type="hidden" id="grandtotal" value="{{$grandtotal}}"> 
                    </td>
                </tr>
                @if (!empty($_GET['status']) && $_GET['status'] == 2)
                    <tr>
                        <td colspan="3" align="center" style="color: red"><strong>Belum Bayar</strong></td>
                    </tr>
                @elseif(!empty($_GET['status']) && $_GET['status'] == 4)
                    <tr>
                        
                    </tr>
                @else
                    <tr>
                        <td colspan="2">Tunai</td>
                        @if ($pay)
                            <td><small>Rp. </small>{{number_format($pay)}}</td>
                        @else
                            <td><small>Rp. </small> 0</td>
                        @endif
                    </tr>
                    <tr>
                        <td colspan="2">Kembali</td>
                        <td><small>Rp. </small>{{($pay == 0 ? 0 : number_format($pay-$grandtotal))}}</td>
                    </tr>
                @endif
            </tfoot>
        </table>
        <hr>
        <h2 style="text-align: center"><b>Terimakasih</b></h3>
    </div>
</body>
</html>