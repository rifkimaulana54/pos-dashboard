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

    if($('select[name=role]').length)
    {
        $(document).on('change', 'select[name=role]', function()
        {
            var text = $(this).find('option:selected').text();
            $('input[name=role_name]').val(text);
        })
    }

    if($('#userList').length)
	{
		get_user_list();

        $(document).on('change', '.filter-table', function()
        {
            $('#userList').DataTable().ajax.reload();
        })

	    $(document).on('click','.deleteUser', function(e) 
        {
            e.preventDefault();
            var id = $(this).data('id');
            var cur     = $(this);
            var target	= $(this).closest('tr');

            swalArchivedTable(
                base_url + '/users/'+id, 
                target, 
                '.deleteUser', 
                8, 
                'User berhasil di archived', 
                '#userList'
            )
        }) 

        $(document).on('click','.restoreUser', function(e) 
        {
            e.preventDefault();

            var id = $(this).data('id');
            var cur     = $(this);
            var target  = $(this).closest('tr');

            swalRestoreTable(
                base_url + '/users/'+id+'/restore/', 
                target, 
                '.restoreUser', 
                8, 
                'User berhasil direstore', 
                '#userList', 
                'User'
            )
        });  
    }
    
    if($('.datetimepicker-tlahir').length || $('.datetimepicker-sim').length)
    {
        $('.datetimepicker-tlahir').datetimepicker({
            showClear: true,
            sideBySide: true,
            format: 'DD-MM-YYYY',
            useCurrent: false,
            ignoreReadonly: true,
            icons: {
                time: 'far fa-clock',
                date: 'far fa-calendar',
                up: 'fas fa-arrow-up',
                down: 'fas fa-arrow-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right',
                today: 'fas fa-calendar-check',
                clear: 'far fa-trash-alt',
                close: 'far fa-times-circle'
            }
        });

        $('.datetimepicker-sim').datetimepicker({
            showClear: true,
            sideBySide: true,
            format: 'DD-MM-YYYY',
            useCurrent: false,
            ignoreReadonly: true,
            icons: {
                time: 'far fa-clock',
                date: 'far fa-calendar',
                up: 'fas fa-arrow-up',
                down: 'fas fa-arrow-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right',
                today: 'fas fa-calendar-check',
                clear: 'far fa-trash-alt',
                close: 'far fa-times-circle'
            }
        });
    }
    
    if($('#permissionList').length)
	{
		get_permission_list();

	    $(document).on('click','.deletePermission', function(e) 
	    {
	    	e.preventDefault();
	        var id = $(this).data('id');
	        var cur     = $(this);
	        var target	= $(this).closest('tr');

            swalArchivedTable(
                base_url + '/users/acl/permissions/'+id, 
                target, 
                '.deletePermission', 
                8, 
                'Permission berhasil di archived', 
                '#permissionList'
            )
	    }) 

        $(document).on('click','.restorePermission', function(e) 
        {
            e.preventDefault();

            var id = $(this).data('id');
            var cur     = $(this);
            var target  = $(this).closest('tr');

            swalRestoreTable(
                base_url + '/users/acl/permissions/'+id+'/restore/', 
                target, 
                '.restorePermission', 
                8, 
                'Permission berhasil direstore', 
                '#permissionList', 
                'Permission'
            )
        });  
    }
    
    if($('#roleList').length)
    {
        get_role_list();   

        $(document).on('click','.deleteRole', function(e) 
        {
            e.preventDefault();

            var name = $(this).data('name');
            var id = $(this).data('id');
            var cur     = $(this);
            var target  = $(this).closest('tr');

            swalArchivedTable(
                base_url + '/users/acl/roles/'+id, 
                target, 
                '.deleteRole', 
                6, 
                'Role berhasil di archived', 
                '#roleList'
                // name
            )

        });  

        $(document).on('click','.restoreRole', function(e) 
        {
            e.preventDefault();

            var name = $(this).data('name');
            var id = $(this).data('id');
            var cur     = $(this);
            var target  = $(this).closest('tr');

            swalRestoreTable(
                base_url + '/users/acl/roles/'+id+'/restore/', 
                target, 
                '.restoreRole', 
                6, 
                'Role berhasil direstore', 
                '#roleList', 
                name
            )
        });  

        $(document).on('change','.filter-table',function(e)
        {
            // if($(this).val())
            //     $(this).addClass('bg-aqua');
            // else
            //     $(this).removeClass('bg-aqua');
            $('#roleList').DataTable().ajax.reload();
        });
    }

    if($('#driverList').length)
	{
		get_driver_list();

        $(document).on('change', '.filter-table', function()
        {
            $('#driverList').DataTable().ajax.reload();
        })

	    $(document).on('click','.deleteDriver', function(e) 
        {
            e.preventDefault();
            var id = $(this).data('id');
            var cur     = $(this);
            var target	= $(this).closest('tr');

            swalArchivedTable(
                base_url + '/drivers/'+id, 
                target, 
                '.deleteDriver', 
                9, 
                'Driver berhasil di archived', 
                '#driverList'
            )
        }) 

        $(document).on('click','.restoreDriver', function(e) 
        {
            e.preventDefault();

            var id = $(this).data('id');
            var cur     = $(this);
            var target  = $(this).closest('tr');

            swalRestoreTable(
                base_url + '/drivers/'+id+'/restore/', 
                target, 
                '.restoreDriver', 
                9, 
                'Driver berhasil direstore', 
                '#driverList', 
                'Driver'
            )
        });  
    }

    if($('.multi-select-permission').length)
    {
        $('.multi-select-permission').multiSelect({
            selectableOptgroup: true,
            selectableHeader: '<div class="custom-header-search"><input type="text" class="search-input input-sm form-control" autocomplete="off" placeholder="Available Permission..."></div>',
            selectionHeader: '<div class="custom-header-search"><input type="text" class="search-input input-sm form-control" autocomplete="off" placeholder="Selected Permission..."></div>',
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

        $(document).on('click', '#perm_select_all', function(e) {
            e.preventDefault();
            $('.multi-select-permission').multiSelect('select_all');
            return false;
        });
        $(document).on('click', '#perm_deselect_all', function(e) {
            e.preventDefault();
            $('.multi-select-permission').multiSelect('deselect_all');
            return false;
        });

        $(document).on('change','#role',function(e)
        {
            var selected_role = $(this).val();
            if(selected_role)
            {
                get_permission_list(selected_role);
            }
        });

        $('#role').trigger('change');
    }

    if($('.multi-select-action').length)
    {
        $('.multi-select-action').multiSelect({
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

        $(document).on('click', '#act_select_all', function(e) {
            e.preventDefault();
            $('.multi-select-action').multiSelect('select_all');
            return false;
        });
        $(document).on('click', '#act_deselect_all', function(e) {
            e.preventDefault();
            $('.multi-select-action').multiSelect('deselect_all');
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

    if($('.btn-copy').length)
    {
        $(document).on('click', '.btn-copy', function(e)
        {
            e.preventDefault();
            var target = $(this).data('target');
            // var container = $(this).closest('.bank-type')
            if($(target).length && $(target).val())
            {
                var text = $(target).val();
                var target_url = $(this).data('url');
                if(typeof target_url !== 'undefined' && target_url)
                    text = target_url + text;
                $(target).parent().append('<input class="copyText" value="'+ text +'" />');
                $(target).parent().find('.copyText').select();
                document.execCommand("copy");
                $(target).parent().find('.copyText').remove();

                /* Copy the text inside the text field */

                alert("Copied : " + text);

            }
        })
    }
});


function get_user_list()
{
	before_load_data();

	var table = jQuery('#userList').DataTable({
        iDisplayLength: 25,
        responsive: true,
        processing:true,
        bServerSide: true,
        sAjaxSource: base_url + '/users/getUserList',
        sAjaxDataProp: 'data',
        sServerMethod: 'POST',
        language: {
            "emptyTable": "User tidak tersedia.",
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
                defaultContent: "<i>-</i>",
            },
            {
                data:'fullname',
                defaultContent: "<i>-</i>",
            },
            {
                data:'email',
                defaultContent: "<i>-</i>",
            },
            {
                data:'role_name',
                defaultContent: "<i>-</i>",
            },
            {
                data:'created_html',
                defaultContent: "<i>-</i>",
            },
            {
                data:'status_html',
                defaultContent: "<i>-</i>",
            },
            {   
                data: null,
                render: function(data)
                {
                    var url = 'users';
                    action_html = '';
                    action_html += '<a data-toggle="tooltip" class="btn btn-info btn-xs" href="' +base_url+ '/'+ url +'/'+data.id+'" role="button" title="Detail User"><i class="fa fa-eye"></i> </a> ';
                    if(typeof data.update !== 'undefined' && data.update)
                        action_html += '<a data-toggle="tooltip" class="btn btn-success btn-xs" href="' +base_url+ '/'+ url +'/'+data.id+'/edit/" role="button" title="Edit User"><i class="fa fa-edit"></i> </a> ';
                    if(typeof data.delete !== 'undefined' && data.delete)
                    {
                        if(data.status == 1 || data.status == 0)
                            action_html += '<a data-toggle="tooltip" class="btn btn-danger btn-xs deleteUser" href="javascript:void(0);" data-id="' + data.id + '" role="button" title="Archive User" style="color:white;"><span class="fa fa-trash"></span></a> ';
                        else if(data.status == 2)
                            action_html += '<a data-toggle="tooltip" class="btn btn-primary btn-xs restoreUser" href="javascript:void(0)" role="button" data-id ="'+ data.id +'" title="Restore User" style="color:white;"><i class="fa fa-fw fa-undo"></i></a> ';
                    }       
                    return action_html;
                },
                className: "text-center",
                "orderable": false,
                width:"125px",
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


function get_permission_list()
{
	before_load_data();

	var table = jQuery('#permissionList').DataTable({
        iDisplayLength: 25,
        responsive: true,
        processing:true,
        bServerSide: true,
        sAjaxSource: base_url + '/users/acl/permissions/getPermissionList',
        sAjaxDataProp: 'data',
        sServerMethod: 'POST',
        language: {
            "emptyTable": "Permission tidak tersedia.",
            processing: image_loading + 'Memuat data...',
        },
        fixedColumns: true,
        fnServerParams: function ( aoData ) {
          aoData.push(
            // { "name": "cob", "value": jQuery('select[name="cob"]').val() },
            // { "name": "toc", "value": jQuery('select[name="toc"]').val() },
            // { "name": "status", "value": jQuery('select[name="status"]').val() },
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
                defaultContent: "<i>-</i>",
            },
            {
                data:'display_name',
                defaultContent: "<i>-</i>",
            },
            {
                data:'created_html',
                defaultContent: "<i>-</i>",
            },
            {
                data:'updated_html',
                defaultContent: "<i>-</i>",
            },
            {
                data:'status_html',
                defaultContent: "<i>-</i>",
            },
            {   
                data: null,
                render: function(data)
                {
                    action_html = '';
                    // action_html += '<a data-toggle="tooltip" class="btn btn-info btn-sm" href="' +base_url+ '/users/acl/roles/' + data.id + '" role="button" title="Detail Role"><i class="fa fa-fw fa-eye"></i></a> ';
                    action_html += '<a data-toggle="tooltip" class="btn btn-success btn-xs" href="' +base_url+ '/users/acl/permissions/' + data.id + '/edit/" role="button" title="Edit Permission"><i class="fa fa-fw fa-edit"></i></a> ';
                    if(data.protected !== 1 && typeof data.delete !== 'undefined' && data.delete)
                    {
                        if(data.status)
                            action_html += '<a data-toggle="tooltip" class="btn btn-danger btn-xs deletePermission" href="javascript:void(0)" role="button" title="Archive Permission" data-id ="'+data.id+'" data-name="'+ data.display_name +'" ><i class="fa fa-fw fa-trash"></i></a> ';
                        else
                            action_html += '<a data-toggle="tooltip" class="btn btn-primary btn-xs restorePermission" href="javascript:void(0)" role="button" title="Restore Permission" data-id ="'+data.id+'" data-name="'+ data.display_name +'" ><i class="fa fa-fw fa-undo"></i></a> ';
                    }
                    return action_html;
                },
                className: "text-center",
                "orderable": false,
                width:"125px",
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

function get_role_list()
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

    var table = jQuery('#roleList').DataTable({
        iDisplayLength: 25,
        bServerSide: true,
        sAjaxSource: base_url + '/users/acl/roles/getRoleList',
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
            "emptyTable": "Role List tidak tersedia.",
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
                    // action_html += '<a data-toggle="tooltip" class="btn btn-info btn-sm" href="' +base_url+ '/users/' + data.id + '" role="button" title="Detail User"><i class="fa fa-eye"></i> </a> ';
                    if(typeof data.update !== 'undefined' && data.update)
                        action_html += '<a data-toggle="tooltip" class="btn btn-success btn-xs" href="' +base_url+ '/users/acl/roles/' + data.id + '/edit/" role="button" title="Edit Role"><i class="fa fa-fw fa-edit"></i></a> ';
                    if(typeof data.delete !== 'undefined' && data.delete)
                    {
                        if(data.status)
                            action_html += '<a data-toggle="tooltip" class="btn btn-danger btn-xs deleteRole" href="javascript:void(0)" role="button" title="Archive Role" data-id ="'+data.id+'" data-name="'+ data.display_name +'" ><i class="fa fa-fw fa-trash"></i></a> ';
                        else
                            action_html += '<a data-toggle="tooltip" class="btn btn-primary btn-xs restoreRole" href="javascript:void(0)" role="button" title="Restore Role" data-id ="'+data.id+'" data-name="'+ data.display_name +'" ><i class="fa fa-fw fa-undo"></i></a> ';
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

function get_driver_list()
{
	before_load_data();
	var table = jQuery('#driverList').DataTable({
		"processing": true,
        bServerSide: true,
        sAjaxSource: base_url + '/drivers/getDriverList/',
        sAjaxDataProp: 'data',
        sServerMethod: 'POST',
		responsive: true,
        language: {
            "emptyTable": "Driver tidak tersedia. <a href='" + base_url + "drivers/create/'>Tambah Baru</a>",
            processing: image_loading + 'Memuat data...',
        },
        "initComplete":function( settings, data)
        {
        	$('[data-toggle="tooltip"]').tooltip();
        },
        fixedColumns: true,
        // dom: 'Bfrtip',
        // buttons: [ 'excel', 'csv', 'pdf', 'print' ],
        columns: [
        	{
                data:'meta_html',
                defaultContent: '<img src="\/assets/img/pr.jpg" class="img-circle" width="70px">',
            	"orderable": false,
            },
            {
                data:'fullname',
                defaultContent: "<i>-</i>",
            },
            {
                data:'company.company_display_name',
                defaultContent: "<i>-</i>",
            },
            {
                data:'phone',
                defaultContent: "<i>-</i>",
            }, 
            {
                data:null,
                render: function(data)
                {
                    return moment.unix(data.created_html).format('DD/MMMM/YYYY');
                },
                defaultContent: "<i>Not set</i>"
            },   
            {
                data:'status_html',
                aultContent: "<i>Not set</i>"
            },
            {
                data: null,
            	render: function(data)
            	{
                    var url = 'drivers';
                    action_html = '';
                    if(typeof data.update !== 'undefined' && data.update)
                        action_html += '<a data-toggle="tooltip" class="btn btn-success btn-xs" href="' +base_url+ '/'+ url +'/'+data.id+'/edit/" role="button" title="Edit Driver"><i class="fa fa-edit"></i> </a> ';
                    
                    if(typeof data.delete !== 'undefined' && data.delete)
                    {
                        if(data.status == 1 || data.status == 0)
                            action_html += '<a data-toggle="tooltip" class="btn btn-danger btn-xs deleteDriver" href="javascript:void(0);" data-id="' + data.id + '" role="button" title="Archive Driver" style="color:white;"><span class="fa fa-trash"></span></a> ';
                        else if(data.status == 2)
                            action_html += '<a data-toggle="tooltip" class="btn btn-primary btn-xs restoreDriver" href="javascript:void(0)" role="button" data-id ="'+ data.id +'" title="Restore Driver" style="color:white;"><i class="fa fa-fw fa-undo"></i></a> ';
                    }       
                    return action_html;
                },
            	"orderable": false,
            	// "className": "row"
            }
        ],
        'order': [[5, 'desc']] // sort order by id
    });

    
    $.fn.dataTable.ext.errMode = 'none';

    table.on('error.dt', function (e, settings, techNote, message) 
    {
        errorHandling(e, settings, techNote, message);

        return true;
    });
}