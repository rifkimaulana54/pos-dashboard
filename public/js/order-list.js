var loadAjax, userAjax, arr = [];

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('click','.order_list_detail',function(e)
    {
        e.preventDefault();
        var target  = $(this).data('order');
        // console.log(target)
        $('#order_code').html(target.order_code);
        $('#customer_name').html(target.customer_name);
        $('#order_date').html(new Date(target.created_at).toUTCString());
        $('#store').html(target.store.store_name);

        $('#total_bayar').html('Rp. '+DecimalAsString(target.total_order));

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