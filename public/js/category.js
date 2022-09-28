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

    if($('#categoryList').length)
	{
		get_category_list();

        $(document).on('change', '.filter-table', function()
        {
            $('#categoryList').DataTable().ajax.reload();
        })

	    $(document).on('click','.deleteCategory', function(e) 
        {
            e.preventDefault();
            var id = $(this).data('id');
            var cur     = $(this);
            var target	= $(this).closest('tr');

            swalArchivedTable(
                base_url + '/categories/'+id, 
                target, 
                '.deleteCategory', 
                6, 
                'Category berhasil di archived', 
                '#categoryList'
            )
        }) 

        $(document).on('click','.restoreCategory', function(e) 
        {
            e.preventDefault();

            var id = $(this).data('id');
            var cur     = $(this);
            var target  = $(this).closest('tr');

            swalRestoreTable(
                base_url + '/categories/'+id+'/restore/', 
                target, 
                '.restoreCategory', 
                6, 
                'Category berhasil direstore', 
                '#categoryList', 
                'Category'
            )
        });  
    }

    if($('.multi-select-store').length)
    {
        $('.multi-select-store').multiSelect({
            selectableOptgroup: true,
            selectableHeader: '<div class="custom-header-search"><input type="text" class="search-input input-sm form-control" autocomplete="off" placeholder="Available Action..."></div>',
            selectionHeader: '<div class="custom-header-search"><input type="text" class="search-input input-sm form-control" autocomplete="off" placeholder="Selected Action..."></div>',
            afterInit: function(ms){
                var that = this,
                $selectableSearch = that.$selectableUl.prev('div').children('input'),
                $selectionSearch = that.$selectionUl.prev('div').children('input'),
                selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';
                
                that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                .on('keydown', function(e){
                    if (e.which === 40){
                        that.$selectableUl.focus();
                        return false;
                    }
                });
                
                that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                .on('keydown', function(e){
                    if (e.which == 40){
                        that.$selectionUl.focus();
                        return false;
                    }
                });
            },
            afterSelect: function(){
                this.qs1.cache();
                this.qs2.cache();
            },
            afterDeselect: function(){
                this.qs1.cache();
                this.qs2.cache();
            }
        }); 

        $(document).on('click', '#str_select_all', function(e) {
            e.preventDefault();
            $('.multi-select-store').multiSelect('select_all');
            return false;
        });
        $(document).on('click', '#str_deselect_all', function(e) {
            e.preventDefault();
            $('.multi-select-store').multiSelect('deselect_all');
            return false;
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
            // return false;
        })
    }

    // if($('.btn-copy').length)
    // {
    //     $(document).on('click', '.btn-copy', function(e)
    //     {
    //         e.preventDefault();
    //         var target = $(this).data('target');
    //         // var container = $(this).closest('.bank-type')
    //         if($(target).length && $(target).val())
    //         {
    //             var text = $(target).val();
    //             var target_url = $(this).data('url');
    //             if(typeof target_url !== 'undefined' && target_url)
    //                 text = target_url + text;
    //             $(target).parent().append('<input class="copyText" value="'+ text +'" />');
    //             $(target).parent().find('.copyText').select();
    //             document.execCommand("copy");
    //             $(target).parent().find('.copyText').remove();

    //             /* Copy the text inside the text field */

    //             alert("Copied : " + text);

    //         }
    //     })
    // }
});


function get_category_list()
{
	before_load_data();

	var table = jQuery('#categoryList').DataTable({
        iDisplayLength: 25,
        responsive: true,
        processing:true,
        bServerSide: true,
        sAjaxSource: base_url + '/categories/getCategoryList',
        sAjaxDataProp: 'data',
        sServerMethod: 'POST',
        language: {
            "emptyTable": "Category tidak tersedia.",
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
                data:'id',
            },
            {
                data:'category_display_name',
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
                    action_html += '<a data-toggle="tooltip" class="btn btn-info btn-xs" href="' +base_url+ '/categories/' + data.id + '" role="button" title="Detail Category"><i class="fa fa-eye"></i> </a> ';
                    if(typeof data.update !== 'undefined' && data.update)
                        action_html += '<a data-toggle="tooltip" class="btn btn-success btn-xs" href="' +base_url+ '/categories/' + data.id + '/edit/" role="button" title="Edit Category"><i class="fa fa-fw fa-edit"></i></a> ';
                    if(typeof data.delete !== 'undefined' && data.delete)
                    {
                        if(data.status)
                            action_html += '<a data-toggle="tooltip" class="btn btn-danger btn-xs deleteCategory" href="javascript:void(0)" role="button" title="Archive Category" data-id ="'+data.id+'" data-name="'+ data.display_name +'" ><i class="fa fa-fw fa-trash"></i></a> ';
                        else
                            action_html += '<a data-toggle="tooltip" class="btn btn-primary btn-xs restoreCategory" href="javascript:void(0)" role="button" title="Restore Category" data-id ="'+data.id+'" data-name="'+ data.display_name +'" ><i class="fa fa-fw fa-undo"></i></a> ';
                    }  

                    return action_html;
                },
                "width":"150px",
                "orderable": false,
                "className": 'text-center'
            }
        ],
        'order': [[0, 'asc']] // sort order by id
    }); 
    
    $.fn.dataTable.ext.errMode = 'none';

    table.on('error.dt', function (e, settings, techNote, message) 
    {
        errorHandling(e, settings, techNote, message);

        return true;
    });
}