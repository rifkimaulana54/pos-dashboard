var loadAjax, userAjax, arr = [];

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#search_order').on('keyup',function()
    {
        $value = $(this).val();

        console.log($value);
        $.ajax({
            type: "POST",
            dataType: "json",
            url: base_url + '/orders/getOrderList',    
            data:{'search': $value},
            // processData: false,
            // contentType: false,
            dataType: "json",
            beforeSend: function()
            {
                $('.spinner').addClass('d-none');
            },
            success: function(data)
            {
                if(data.data.length != 0)
                {
                    var output = '';
                    $.each(data.data, function(i, order)
                    {
                        output += renderOrder(order);
                    })

                    $('.order_list').html(output);
                } 
                else
                {
                    $('.order_list').html('<p class="m-auto">Produk <b>#'+$value+'</b> tidak di temukan</p>');
                }
            }
        });
    });

    $(document).on('click','.order_list_detail',function(e)
    {
        $('.order_list_detail').removeAttr('style')
        $(this).css('background', 'rgb(226, 226, 226)');
        e.preventDefault();
        var target  = $(this).data('order');
        console.log(target)
        $('.btn-print-order-list').attr('data-id', target.id);
        $('#order_code').html(target.order_code);
        $('#customer_name').html(target.customer_name);
        $('#order_date').html(new Date(target.created_at).toUTCString());
        $('#store').html(target.store.store_name);
        $('#total_bayar').html('Rp. '+DecimalAsString(target.total_order));

        $('.btn-print-order-list').removeClass('disabled').attr('href', base_url+'/kasir/print/'+target.id).attr('target', '_blank');
        $('.btn-order-order-list').removeClass('disabled').attr('href', base_url + '/kasir?order='+target.id);
        $('.btn-bayar-order-list').removeClass('disabled').attr('href', base_url + '/kasir/bayar/'+target.id);

        var output = '<table class="table text-dark" style="margin-bottom: 13px;">';
            output += '<tr>';
                output += '<th class="text-center">Qty</th>';
                output += '<th>Nama Produk</th>';
                output += '<th>Total</th>';
            output += '</th>';

        $.each(target.mapping, function (i, item) {
            output += buildItem(item);
        })
        output += '</table>';

        $('.table-order-list').html(output);
    })
})

function buildItem(item) {

    var html = '<tr>';
            html += '<td class="text-center">'+item.order_qty+'</td>';
            html += '<td width="50%">'+item.product.product_display_name+'</td>';
            html += '<td>Rp. '+DecimalAsString(item.order_subtotal)+'</td>';
        html += "</tr>";

    return html;
}

function renderOrder(order)
{
    let created_date = new Date(order.created_at)
    let hours = `${created_date.getHours()}`.padStart(2, '0')
    let minutes = `${created_date.getMinutes()}`.padStart(2, '0')

    var html = '<div class="pl-2 pt-4 pr-2 order_list_detail data-order="'+JSON.stringify(order)+'">';
        html += '<div class="row border-bottom border-dark pb-2">';
            html += '<div class="col">';
                html += '<span class="m-0">#'+order.customer_name+'</span><br>';
                html += '<b>'+order.order_code+'</b>';
            html += '</div>';
            html += '<div class="col text-right">';
                html += '<small>'+ hours +':'+ minutes +'</small><br>';
                html += '<b>Rp. '+DecimalAsString(order.total_order)+'</b>';
            html += '</div>';
        html += '</div>';
    html += '</div>';

    return html;
}