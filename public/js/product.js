var loadAjax, userAjax;

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

    if($('#productList').length)
	{
		get_product_list();

        $(document).on('change', '.filter-table', function()
        {
            $('#productList').DataTable().ajax.reload();
        })

	    $(document).on('click','.deleteProduct', function(e) 
        {
            e.preventDefault();
            var id = $(this).data('id');
            var cur     = $(this);
            var target	= $(this).closest('tr');

            swalArchivedTable(
                base_url + '/products/'+id, 
                target, 
                '.deleteProduct', 
                6, 
                'Product berhasil di archived', 
                '#productList'
            )
        }) 

        $(document).on('click','.restoreProduct', function(e) 
        {
            e.preventDefault();

            var id = $(this).data('id');
            var cur     = $(this);
            var target  = $(this).closest('tr');

            swalRestoreTable(
                base_url + '/products/'+id+'/restore/', 
                target, 
                '.restoreProduct', 
                6, 
                'Product berhasil direstore', 
                '#productList', 
                'Product'
            )
        });  
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
});


function get_product_list()
{
	before_load_data();

	var table = jQuery('#productList').DataTable({
        iDisplayLength: 25,
        responsive: true,
        processing:true,
        bServerSide: true,
        sAjaxSource: base_url + '/products/getProductList',
        sAjaxDataProp: 'data',
        sServerMethod: 'POST',
        language: {
            "emptyTable": "Product tidak tersedia.",
            processing: image_loading + 'Memuat data...',
        },
        fixedColumns: true,
        fnServerParams: function ( aoData ) {
          aoData.push(
            // { "name": "cob", "value": jQuery('select[name="cob"]').val() },
            // { "name": "toc", "value": jQuery('select[name="toc"]').val() },
            // { "name": "status", "value": jQuery('select[name="status"]').val() },
            { "name": "company_id", "value": jQuery('select[name="company_id"]').val() },
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
                data:'image_html',
                defaultContent: '<div style="background-image: url('+ base_url + '/assets/img/box.jpg' +'); height: 75px; width: 75px; background-repeat: no-repeat;background-position: center;background-repeat: no-repeat;background-size: cover;"></div>',
                className: 'align-middle',
                orderable: false
            },
            {
                data:'product_display_name',
                className: 'align-middle',
            },
            {
                data:'created_html',
                defaultContent: "<i>-</i>",
                className: 'align-middle',
            },
            {
                data:'updated_html',
                defaultContent: "<i>-</i>",
                className: 'align-middle',
            },
            {
                data:'status_html',
                defaultContent: "<i>-</i>",
                className: 'align-middle',
            },
            {
                data:null,
                render: function(data)
                {
                    action_html = '';
                    action_html += '<a data-toggle="tooltip" class="btn btn-info btn-xs" href="' +base_url+ '/products/' + data.id + '" role="button" title="Detail Product"><i class="fa fa-eye"></i> </a> ';
                    if(typeof data.update !== 'undefined' && data.update)
                        action_html += '<a data-toggle="tooltip" class="btn btn-success btn-xs" href="' +base_url+ '/products/' + data.id + '/edit/" role="button" title="Edit Product"><i class="fa fa-fw fa-edit"></i></a> ';
                    if(typeof data.delete !== 'undefined' && data.delete)
                    {
                        if(data.status)
                            action_html += '<a data-toggle="tooltip" class="btn btn-danger btn-xs deleteProduct" href="javascript:void(0)" role="button" title="Archive Product" data-id ="'+data.id+'" data-name="'+ data.display_name +'" ><i class="fa fa-fw fa-trash"></i></a> ';
                        else
                            action_html += '<a data-toggle="tooltip" class="btn btn-primary btn-xs restoreProduct" href="javascript:void(0)" role="button" title="Restore Product" data-id ="'+data.id+'" data-name="'+ data.display_name +'" ><i class="fa fa-fw fa-undo"></i></a> ';
                    }  

                    return action_html;
                },
                "width":"150px",
                className: "text-center align-middle",
                "orderable": false,
            }
        ],
        'order': [[2, 'desc']] // sort order by id
    }); 
    
    $.fn.dataTable.ext.errMode = 'none';

    table.on('error.dt', function (e, settings, techNote, message) 
    {
        errorHandling(e, settings, techNote, message);

        return true;
    });
}