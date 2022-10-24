var loadAjax, userAjax, arr = [];

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if($('#form-user').length)
    {
        $('#form-user').on('submit', function()
        {
            $('select').prop('disabled', false)
        })
    }

    if($('#orderList').length)
	{
		get_order_list();

        $(document).on('change', '.filter-table', function()
        {
            $('#orderList').DataTable().ajax.reload();
        })
    }

    if($('.btnSubmit').length)
    {
        $(document).on('click', '.btnSubmit', function(e, params)
        {
            // e.preventDefault();
            var localParams = params || {};
            var cur = $(this);

            if (!localParams.send && $('#form-user')[0].checkValidity()) 
            {
                e.preventDefault();
                swalConfirmSubmit(cur, e.type);
            }
        })
    }

    $(document).on('change', '#status', function()
    {
        var id_status = $(this).val();
        $('#filter_status').val(id_status);
    })

    $(document).on('change', '#store', function()
    {
        var id_store = $(this).val();
        console.log(id_store);
        $('#filter_store').val(id_store);
    })
});


function get_order_list()
{
	before_load_data();

	var table = jQuery('#orderList').DataTable({
        iDisplayLength: 25,
        responsive: true,
        processing:true,
        bServerSide: true,
        sAjaxSource: base_url + '/orders/getOrderList',
        sAjaxDataProp: 'data',
        sServerMethod: 'POST',
        language: {
            "emptyTable": "Product tidak tersedia.",
            processing: image_loading + 'Memuat data...',
        },
        fixedColumns: true,
        fnServerParams: function ( aoData ) {
          aoData.push(
            { "name": "status", "value": jQuery('select[name="status"]').val() },
            { "name": "store", "value": jQuery('select[name="store"]').val() },
          );
        },
        "initComplete":function( settings, data)
        {
            
        },
        "fnDrawCallback": function( oSettings )
        {
            $('[data-toggle="tooltip"]').tooltip();
        },
        columns: [
            {
                data:'id',
                className: "text-center",
            },
            {
                data:'order_code',
                defaultContent: "<i>-</i>",
            },
            {
                data:'customer_name',
                "width": "250px",
                defaultContent: "<i>-</i>",
            },
            {
                data:'subtotal_html',
                "width": "200px",
                // render: function(data)
                // {
                //     return 'Rp. ' + DecimalAsString(data.total_order);
                // },
                defaultContent: "<i>-</i>",
            },
            {
                data:'store_html',
                defaultContent: "<i>-</i>",
            },
            {
                data:'created_html',
                "width": "200px",
                defaultContent: "<i>-</i>",
            },
            {
                data:'updated_html',
                "width":"200px",
                defaultContent: "<i>-</i>",
            },
            {
                data:'status_html',
                defaultContent: "<i>-</i>",
            },
            {
                data:null,
                render: function(data)
                {
                    action_html = '';
                    action_html += '<a data-toggle="tooltip" class="btn btn-info btn-xs" href="' +base_url+ '/orders/' + data.id + '" role="button" title="Detail Order"><i class="fa fa-eye"></i> </a> ';
                    return action_html;
                },
                "width":"80px",
                className: "text-center align-middle",
                "orderable": false,
            },
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // converting to interger to find total
            var intVal = function ( i ) {
                return typeof i === 'string' ? i.replace(/[\Rp. ,]/g, '')*1 : typeof i === 'number' ? i : 0;
            };
 
            // computing column Total of the complete result 
	        var grandtotal = api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
				
            // Update footer by showing the total with the reference of the column index 
	        $( api.column( 0 ).footer() ).html('<b>Total:</b>');
            $( api.column( 3 ).footer() ).html('<b>Rp. '+DecimalAsString(grandtotal)+'</b>');
        },
        
        'order': [[1, 'desc']] // sort order by id
    }); 
    
    $.fn.dataTable.ext.errMode = 'none';

    table.on('error.dt', function (e, settings, techNote, message) 
    {
        errorHandling(e, settings, techNote, message);

        return true;
    });
}