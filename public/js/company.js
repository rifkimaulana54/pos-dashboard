var loadAjax, companyAjax;

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if($('#form-struktur').length)
    {
        $('#form-struktur').on('submit', function(e, params)
        {
            var localParams = params || {};
            var cur = $(this);
            // var status = $(this).data('value');

            // $('#status').val(status);

            if (!localParams.send && $('#form-struktur')[0].checkValidity()) 
            {
                e.preventDefault();
                swalConfirmSubmit(cur, e.type);
                $('button[type=submit]').prop('disabled', false);
            }
            // if(confirm('Apakah anda yakin?'))
            //     return true;
            // else
            // {
            //     $('button[type=submit]').prop('disabled', false);
            //     return false;
            // }
        })
    }

    if($('.dd').length)
    {
        if(!companyAjax)
        {
            companyAjax = jQuery.ajax({
                type: "get",
                dataType: "json",
                url: base_url + '/companies/getCompanyStrukturList',    
                success: function(data)
                {
                    console.log(data);
                    if(data.data.length != 0)
                    {
                        var output = '';
                        $.each(data.data, function(i, menu)
                        {
                            console.log(menu.menu_parent)
                            output += buildItem(menu);
                        })

                        $('#rootList').html(output);

                        $('.dd').nestable();

                        var serialized = $('.dd').nestable('serialize');
                            // document.getElementById("json_nestable").innerHTML = JSON.stringify(serialized);
                        var dataJson = JSON.stringify(serialized); 
                        $("#company_json").val(dataJson);

                        companyAjax = null;              
                    }        
                }
            });
        }

        $(document).on('change', '.dd', function(e) 
        {
            data = $('.dd').nestable('serialize');
            // document.getElementById("json_nestable").innerHTML = JSON.stringify(data);
            var dataJson = JSON.stringify(data); 
            $("#company_json").val(dataJson);
            console.log(data)

        });
    }

    if($('#companyList').length)
	{
		get_company_list();

	    $(document).on('click','.deleteCompany', function(e) 
	    {
	    	e.preventDefault();
	        var id = $(this).data('id');
	        var cur     = $(this);
	        var target	= $(this).closest('tr');
            
            swalArchivedTable(
                base_url + '/companies/'+id, 
                target, 
                '.deleteCompany', 
                8, 
                'Company berhasil di archived', 
                '#companyList'
            )
	    }) 

        $(document).on('click','.restoreCompany', function(e) 
        {
            e.preventDefault();

            var id = $(this).data('id');
            var cur     = $(this);
            var target  = $(this).closest('tr');

            swalRestoreTable(
                base_url + '/companies/'+id+'/restore/', 
                target, 
                '.restoreCompany', 
                8, 
                'Company berhasil di restore', 
                '#companyList', 
                'Company'
            )
        });  
    }

    if($('.btnSubmit').length)
    {
        $(document).on('click', '.btnSubmit', function(e, params)
        {
            var localParams = params || {};
            var cur = $(this);

            if (!localParams.send && $('.form-company')[0].checkValidity()) 
            {
                e.preventDefault();
                swalConfirmSubmit(cur, e.type);
            }
            // return false;
        })
    }
})

function buildItem(item) {

    var html = '<li class="dd-item" id="row-'+item.id+'" data-id="'+ item.id +'">'
    
    html += "<div class='dd-handle'>" + item.content + "</div>";

    if (item.children) {

        html += "<ol class='dd-list'>";
        $.each(item.children, function (index, sub) {
            html += buildItem(sub);
        });
        html += "</ol>";

    }

    html += "</li>";

    return html;
}

function get_company_list()
{
    before_load_data();

    // startDate = moment().subtract(29, 'days').utcOffset('+0700');
    // endDate = moment().utcOffset('+0700');

    // if(!$('input[name="daterange"]').data('datepicker'))
    // {
    //     $('input[name="daterange"]').daterangepicker({
    //         "startDate": startDate,
    //         "endDate": endDate,
    //         ranges: {
    //              'Today': [moment(), moment()],
    //              'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
    //              'Last 7 Days': [moment().subtract(6, 'days'), moment()],
    //              'Last 30 Days': [moment().subtract(29, 'days'), moment()],
    //              'This Month': [moment().startOf('month'), moment().endOf('month')],
    //              'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    //         },
    //         alwaysShowCalendars:true
    //     }, 
    //     function(start, end, label) {
    //         start = start.utcOffset('+0700');
    //         end = end.utcOffset('+0700');
    //         startDate = start;
    //         endDate = end;
    //         $('#reportingList').DataTable().ajax.reload();
    //     });
    // }

    var table = jQuery('#companyList').DataTable({
        iDisplayLength: 25,
        bServerSide: true,
        sAjaxSource: base_url + '/companies/getCompanyList',
        // fnServerParams: function ( aoData ) {
        //   aoData.push( 
        //     { "name": "start_date", "value": startDate.format('YYYY-MM-DD HH:mm:ss') },
        //     { "name": "end_date", "value": endDate.format('YYYY-MM-DD HH:mm:ss') },
        //     { "name": "type", "value": jQuery('select[name="type"]').val() },
        //     { "name": "status", "value": jQuery('select[name="status"]').val() },
        //   );
        // },
        sAjaxDataProp: 'data',
        sServerMethod: 'POST',
        responsive: true,
        processing:true,
        language: {
            "emptyTable": "Company List tidak tersedia.",
            processing: image_loading + 'Memuat data...',
        },
        // fixedColumns: true,
        "fnDrawCallback": function( oSettings ) 
        { 
            $('[data-toggle="tooltip"]').tooltip();
        },
        "initComplete":function( settings, data)
        {
            // $('#rejectionList').wrap('<div class="table-responsive"></div>');
        },
        // dom: 'Bfrtip',
        // buttons: [ 'excel', 'csv', 'pdf', 'print' ],
        columns: [
            {
                data:'id',
            },
            {
                data:'display_name',
            },
            {
                data:'created_html',
                defaultContent: "<i>-</i>",
                // width:"200px"
            },
            {
                data:'updated_html',
                defaultContent: "<i>-</i>",
                // width:"150px"
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
                    action_html += '<a data-toggle="tooltip" class="btn btn-info btn-xs" href="' +base_url+ '/companies/' + data.id + '" role="button" title="Detail Company"><i class="fa fa-eye"></i> </a> ';
                    if(typeof data.update !== 'undefined' && data.update)
                        action_html += '<a data-toggle="tooltip" class="btn btn-success btn-xs" href="' +base_url+ '/companies/' + data.id + '/edit/" role="button" title="Edit Company"><i class="fa fa-fw fa-edit"></i></a> ';
                    if(typeof data.delete !== 'undefined' && data.delete)
                    {
                        if(data.status)
                            action_html += '<a data-toggle="tooltip" class="btn btn-danger btn-xs deleteCompany href="javascript:void(0)" role="button" title="Archive Company" data-id ="'+data.id+'" data-name="'+ data.company_name +'" style="color:white"><i class="fa fa-fw fa-trash"></i></a> ';
                        else
                            action_html += '<a data-toggle="tooltip" class="btn btn-primary btn-xs restoreCompany href="javascript:void(0)" role="button" title="Restore Company" data-id ="'+data.id+'" data-name="'+ data.company_name +'" style="color:white"><i class="fa fa-fw fa-undo"></i></a> ';
                    }  

                    return action_html;
                },
                "width":"150px",
                "orderable": false,
                "className": 'text-center'
            }
        ],
        'order': [[1, 'asc']] // sort order by modified date
       
    });

    $.fn.dataTable.ext.errMode = 'none';

    table.on('error.dt', function (e, settings, techNote, message) 
    {
        errorHandling(e, settings, techNote, message);

        return true;
    });
}