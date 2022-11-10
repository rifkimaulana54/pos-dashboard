var image_loading = "<center class='image-loading'><img src='" + base_url + "/assets/img/loading.gif' style='width: 64px;' /></center>";
var wysiwyg;
var tmp_files = new Array();
var deleted_files = new Array();
var useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
var loadAjax, ajaxHeartbeat, ajaxHeartbeatInterval, loadAjax2;
var btn_upload_active;
const confirmBtnColor = '#3085d6';
const cancelBtnColor = '#d33';
const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: 'btn btn-success ml-2',
      cancelButton: 'btn btn-danger'
    },
    buttonsStyling: false
})

var url = new URL(window.location);
var queryString = window.location.search;
var urlParams = new URLSearchParams(queryString);

$(document).ready(function($) 
{
    if($('.no-need-confirm').length)
    {
        $(document).on('click', '.no-need-confirm', function()
        {
            needToConfirm = false
        })
    }

    if($('.numbering-comma').length)
    {
      $('.numbering-comma').number( true, 2 );
    }

    if($('.qty,.amount').length)
    {
        $(document).on('focus', '.qty,.amount', function()
        {
            $(this).select();
        })
    }

    if($('.add-att').length)
    {
        $(document).on('click', '.add-att', function()
        {
            var cloned_tr_att = $('.att-clone').clone(true);

            cloned_tr_att.attr({
                class: 'col-sm-3 text-center py-3 mb-4 img-container'
            })
            cloned_tr_att.insertBefore($('.att-add-container'));
        })
    }

    if($('.btn-upload').length)
    {
        $(document).on('click', '.btn-upload', function(e)
        {
            var choose = $(this).data('choose');
            var url = $(this).closest('.img-container').find('.ext-link-att').val();
            console.log(url);
            if(typeof choose !== 'undefined')
            {
                var check = $('.choose-attachment').find('.upload_att:checked');
                $(check).closest('.list-group').find('.link').val('');
                console.log(url !== '');
                if(url !== '')
                    $(check).closest('.list-group').find('.link').val(url);
                $('.choose-attachment').modal('show');
                btn_upload_active = $(this);
                return false;
            }
            $(this).closest('div').find('.file-att').click()
        })
    }

    if($('.btn-choose').length)
    {
        $(document).on('click', '.btn-choose', function()
        {
            var check = $('.choose-attachment').find('.upload_att:checked');
            var type = $(check).val();
            var var_link = $(check).closest('.input-group').find('.choose-att-radio').val();
            var icon_link = $(this).data('icon-link');
            var container = $(btn_upload_active).closest('.col-sm-3');
            $(container).find('.ext-link-att').prop('disabled', true);
            console.log(type);
            if(typeof type !== 'undefined')
            {
                switch (type) 
                {
                    case 'link':
                        $(container).find('.ext-link-att').prop('disabled', false);
                        $(container).find('.ext-link-att').val(var_link);
                        $(container).find('.preview-reset').addClass('d-none')
                        $(container).find('.img-preview').attr('src', icon_link).removeClass('d-none');
                        $(container).find('.file-name').html(var_link);
                        $(container).find('.file-att').val('');
                        $(this).closest('.choose-attachment').modal('hide');
                        break;
                
                    default:
                        $(this).closest('.choose-attachment').modal('hide');
                        $(btn_upload_active).closest('.col-sm-3').find('.file-att').click();
                        break;
                }
            }
            else
                swalWithBootstrapButtons.fire('Please Choose upload type','', 'error');
                // alert('Choose upload type')
        })
    }

    if($('.delete-cart').length)
    {
        $(document).on('click', '.delete-cart', function()
        {
            if(confirm('Apakah anda yakin?'))
            {
                $(this).closest('.img-container').remove();
            }
            else
                return false;
        })
    }

    if($('.img-preview').length)
    {
        $(document).on('click', '.img-preview', function()
        {
            var img_src = $(this).attr('src');

            $('#preview-attachment').find('.img-thumbnail').attr('src', img_src);

            $('#preview-attachment').modal('show')
        })
    }

    if($('.file-att').length)
    {
        $(document).on('change', '.file-att', function()
        {
            var isImage = ['image/jpeg', 'image/png', 'image/jpg'];
            var max_size = typeof $(this).data('size') !== 'undefined' ? parseInt($(this).data('size')) : undefined;

            if(typeof max_size  !== 'undefined' && typeof this.files[0].size !== 'undefined' && max_size < this.files[0].size)
            {
                swalWithBootstrapButtons.fire('Max Size '+ Math.floor(max_size/(1024*1024)) + 'Mb','', 'error');
                // alert('Max Size '+ Math.floor(max_size/(1024*1024)) + 'Mb')
                return false;
            }

            var media_type = this.files[0].type;
            $(this).closest('.img-container').find('.preview-reset').addClass('d-none')
            if($(this).closest('.img-container').find('.attach-index').length)
                $(this).closest('.img-container').find('.attach-index').remove()
            if($(this).closest('.img-container').find('.ext-link-att').length)
                $(this).closest('.img-container').find('.ext-link-att').val('').prop('disabled', true);
            if($('.choose-attachment').length && $('.choose-attachment').hasClass('show'))
                $('.choose-attachment').modal('hide');
            if(isImage.includes(this.files[0].type))
            {
                readImage(this)
            }
            else if(media_type.indexOf("sheet") > 0)
                $(this).closest('.img-container').find('.file-preview').attr('src', base_url+'/assets/img/xls.jpg').removeClass('d-none');
            else
                $(this).closest('.img-container').find('.file-preview').attr('src', base_url+'/assets/img/file.jpg').removeClass('d-none');
            $(this).closest('.img-container').find('.btn-remove-file').removeClass('d-none');
            $(this).closest('.img-container').find('.file-name').html(this.files[0].name).removeClass('d-none')
        })
    }

    if($('.file-preview').length)
    {
        $(document).on('click', '.file-preview', function()
        {
            $(this).closest('.img-container').find('.file-name').click();
        })
    }

    if($('.choose-att-radio').length)
    {
        $(document).on('click', '.choose-att-radio', function()
        {
            $(this).closest('.input-group').find('.upload_att').click();
            if($(this).hasClass('upload-file'))
                $(btn_upload_active).closest('div').find('.file-att').click();
        })
    }

    if($('.form-flow').length)
    {
        $(document).on('change', '.form-flow', function()
        {
            if(!$('input[name=_method').length)
            {
                var lanjut = $(this).data('lanjut');
                $(lanjut).val('').prop('disabled', true).trigger('change');
                // console.log(lanjut);
                if($(this).hasClass('first'))
                {
                    $('.form-flow.select2').not(this).val('').select2('destroy').select2({theme: 'bootstrap4'})
                    $('.form-flow').not(this).val('').prop('disabled', true);
                }
                
                if($(lanjut).hasClass('select2'))
                    $(lanjut+'.select2').val('').select2({theme: 'bootstrap4'}).trigger('change');
                if(typeof lanjut !== 'undefined' && $(this).val() !== '' && $(this).val() !== null)
                {
                    $(lanjut).prop('disabled', false);
                }
            }
        })
    }

    if($('.overlay').length)
    {
        window.ajax_loading = false;
        $.hasAjaxRunning = function() {
            return window.ajax_loading;
        };

        $(document).ajaxStart(function() 
        {
            window.ajax_loading = true;
            $('.overlay').removeClass('d-none');
            var cekAjaxLoading = setInterval(function()
            { 
                // console.log(ajax_loading)
                if(!ajax_loading)
                {
                    // $('.btn-submit, .nav-tabs a').prop('disabled',false);
                    $('.overlay').addClass('d-none');
                    clearInterval(cekAjaxLoading);
                }
            }, 1000);
        });
        $(document).ajaxStop(function() {
            window.ajax_loading = false;
        });
    }
    
	if($('.select2').length)
	{
	    $(".select2:visible").each(function(e)
	    {
            // console.log('visible');
	        var readonly = $(this).attr('readonly');
	        if(typeof readonly === 'undefined')
	            $(this).select2({theme: 'bootstrap4'});
	    });
    }

    $(document).on('select2:open', (e) => 
    {
        setTimeout(function()
        {
         document.querySelector('.select2-container--open .select2-search__field').focus();
        }, 500)
    });

    if($('form:not(#logout-form,.export, #ajaxLogin, #form_reason)').length)
    {
        needToConfirm = false;
        window.onbeforeunload = askConfirm;
        $("select,input,textarea").change(function() {
            needToConfirm = true;
        });

        $('form').not('.export').on('submit', function()
        {
            // needToConfirm = false;
            // $(this).find('button[type=submit]').prop('disabled', true);
            // if($('.overlay').length)
            //     $('.overlay').removeClass('d-none');
        })
    }

    $(document).on('click', '.logout', function(e)
    {
        e.preventDefault()
        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "You will be logged out from Renvee Dashboard",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, log me out!',
            reverseButtons: true
        }).then((result) => 
        {
            if (result.isConfirmed) 
            {
                $('form#logout-form').submit();
            }
        })

        return false;
    })

    if($('#company').length)
    {
        $(document).on('change', '#company', function()
        {
            var company = $(this).find('option:selected').data('company');
            if(typeof company !== 'undefined')
            {
                jQuery.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
              
                jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                    company: JSON.stringify(company) 
                    },
                    url: base_url+'/users/setCompany',
                    success: function(data)
                    {
                        window.location.reload(true);
                    }
                });
            }
        })
    }
    
    if($('#importList').length)
    {
        if ( $.fn.dataTable.isDataTable( '#importList' ) ) {
            table = $('#importList').DataTable({
                paging: false,
                "scrollX":true,
                columnDefs: [
                    {
                        orderable: false,
                        targets: [1]
                    }
                ]
            });
        }
        else {
            table = $('#importList').DataTable( {
                paging: false,
                "scrollX":true,
                columnDefs: [
                    {
                        orderable: false,
                        targets: [1]
                    }
                ]
            } );
        }   
    }

    if($('#importList tr.table-danger').length)
    {
        var jml_error = $('#importList tr.table-danger').length;
        $('#error_info').removeClass('d-none').append('. <strong>Jumlah Error ('+ jml_error +')</strong>');
    }

    if($('.ktp').length)
        $('.ktp').inputmask({"mask":"9999999999999999"});

    if($('.npwp').length)
        $('.npwp').inputmask({"mask": "99.999.999.9-999.999"});

    // if($('#actived').length)
    // { 
    //     $('#actived').bootstrapToggle({
    //       on:  'Active',
    //       off: 'Inactive'
    //     });
    // }

    if($('.wysiwyg').length)
    {
        $('.wysiwyg').wysihtml5({
            toolbar: 
            {
                "font-styles": false, //Font styling, e.g. h1, h2, etc. Default true
                "emphasis": true, //Italics, bold, etc. Default true
                "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                "html": true, //Button which allows you to edit the generated HTML. Default false
                "link": true, //Button to insert a link. Default true
                "image": false, //Button to insert an image. Default true,
                "color": false, //Button to change color of font  
                "blockquote": false, //Blockquote  
                "size": "xs" //default: none, other options are xs, sm, lg
            },
            events:
            {
                "load": function(e) { 
                    if($('.wysiwyg').prop('readonly') == true || $('.wysiwyg').prop('disabled') == true)
                    {
                        wysiwyg.disable();
                        wysiwyg.toolbar.commandsDisabled = true;
                    }
                }
            }
        });

        wysiwyg = $('.wysiwyg').data("wysihtml5").editor;
    }

    if($('.summernote:visible').length)
    {
        $('.summernote').summernote({
            placeholder: 'Masukan Content',
            tabsize: 2,
            height: 350,
            callbacks: {
                onImageUpload: function(files, editor, welEditable) {
                    console.log(editor);
                    sendFile(files[0], this);
                }
            },
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['style']],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video', 'hr']],
            ],
            popover: {
                image: [
                    ['custom', ['imageAttributes']],
                    ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
                    ['float', ['floatLeft', 'floatRight', 'floatNone']],
                    ['remove', ['removeMedia']]
                ],
                imageAttributes:{
                    icon:'<i class="note-icon-pencil"/>',
                    removeEmpty:false, // true = remove attributes | false = leave empty if present
                    disableUpload: false // true = don't display Upload Options | Display Upload Options
                }
            },
        });
    }

    if($('.summernote-min').length)
    {
        $('.summernote-min').each(function(e)
        {
            $(this).summernote({
                placeholder: $(this).attr('placeholder'),
                tabsize: 2,
                height: 350,
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['style']],
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    // ['font', ['strikethrough']],
                    // ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    // ['insert', ['link', 'picture', 'video', 'hr']],
                  ]
            });
        });
    }

    if($('[data-toggle="tooltip"').length)
    {
        $('[data-toggle="tooltip"]').tooltip({
            placement: 'bottom'
        });
    }

    if($('.editable').length)
    {
        $.fn.editable.defaults.mode = 'inline';
        $('.editable').editable({
            title: 'Masukan Slug',
            emptytext: '',
            success: function(response, newValue) {
                console.log(newValue);
                checkSlug(newValue, $('.editable').data('url'), $('#id').val())
            },
        });
    }
    
	if($('.icheck').length)
	{
		$('.icheck').iCheck({
			checkboxClass: 'icheckbox_square-blue',
		});
    }

    if($(".numbering").length)
        $('.numbering').number( true, 0 );

    if($(".timepicker").length)
        $('.timepicker').timepicker({
          showInputs: false,
          showMeridian: false
        })

    if($('.checkbox-toggle').length)
    {
        var active = $('.checkbox-toggle').data('on');
        var inactive = $('.checkbox-toggle').data('off');
        $('.checkbox-toggle').bootstrapToggle({
            on: active,
            off: inactive,
            offstyle: "danger",
            onstyle: "success"
        });
    }

    if($('.number').length)
    {
        $(document).on('keydown', '.number', function(e)
        {
            $(this).on('change', function()
            {
                const val = $(this).val();
                if(val < 0){
                    $(this).val(0)
                }
            })
            var k = e.which;
            var ok = k >= 48 && k <= 57 || // 0-9
                     k === 8 ||
                     k === 37 ||
                     k === 39 ||
                     k === 46;
    
            if (!ok){
                e.preventDefault();
            }
    
            if(e.which === 32)
                return false;
        })
    }

    if($('.number-comma').length)
    {
        $(document).on('keydown', '.number-comma', function(e)
        {
            $(this).on('change', function()
            {
                const val = $(this).val();
                if(val < 0){
                    $(this).val(0)
                }
                $(this).val((Math.round($(this).val() * 100) / 100).toFixed(2));
            })
            var k = e.which;
            var ok = k >= 48 && k <= 57 || // 0-9
                     k === 8 ||
                     k === 188 ||
                     k === 190 ||
                     k === 37 ||
                     k === 39 ||
                     k === 46;
    
            if (!ok){
                e.preventDefault();
            }
    
            if(e.which === 32)
                return false;
        })
    }

    if($('.number-minus').length)
    {
        $(document).on('keydown', '.number-minus', function(e)
        {
            var k = e.which;
            console.log(e.which);
            var ok = k >= 48 && k <= 57 || // 0-9
                     k === 8 || k === 173 ||
                     k === 37 ||
                     k === 39 ||
                     k === 46;
    
            if (!ok){
                e.preventDefault();
            }
    
            if(e.which === 32)
                return false;
        })
    }

    if($('.number-minus-comma').length)
    {
        $(document).on('keydown', '.number-minus-comma', function(e)
        {
            var k = e.which;
            console.log(e.which);
            var ok = k >= 48 && k <= 57 || // 0-9
                     k === 8 ||
                     k === 188 ||
                     k === 190 ||
                     k === 173 ||
                     k === 189 ||
                     k === 37 ||
                     k === 39 ||
                     k === 46;
    
            if (!ok){
                e.preventDefault();
            }
    
            if(e.which === 32)
                return false;
        })
    }

    if($('.datetimepicker').length)
    {
        $('.datetimepicker').datetimepicker({
            showClear: true,
            sideBySide: true,
            ignoreReadonly: true,
            // autoClose: true,
            format: 'YYYY-MM-DD HH:mm:ss',
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

        $(document).on('keydown', '.datetimepicker', function(e)
        {
            e.preventDefault()
        })
    }

    if($('.datekeydown').length)
    {
        $(document).on('keydown', '.datekeydown', function(e)
        {
            e.preventDefault()
        })
    }

    if($('.datepicker').length)
    {
        var start_date = $('.datepicker').data('start-date');
        var max_date = $('.datepicker').data('max-date');
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose:true,
            todayHighlight:true
        }); //.prop('readonly', true).attr('autocomplete', 'off');

        if(typeof start_date !== 'undefined' && start_date != '' && start_date != null)
        {
            switch(start_date)
            {
                case 'today':
                    start_date = new Date();
                    break;
                default:break;
            }
            $('.datepicker').datepicker('setStartDate',start_date);
        }

        if(typeof max_date !== 'undefined' && max_date != '' && max_date != null)
        {
            $('.datepicker').datepicker('setMaxDate',max_date);
        }

        $(document).on('keydown', '.datepicker', function(e)
        {
            e.preventDefault()
        })
    }

    if($('.filebutton').length)
    {
        $(document).on('click','.filebutton',function(e)
        {
            var target = $(this).data('target');
            if(typeof target === 'undefined')
                target = '.file';

            if($(target).length)
                $(target).trigger('click');
        });
    }


    if($('.file').length)
    {
        $(document).on('change','.file',function()
        {

            var fileInput = $(this)[0];   
            var file = fileInput.files[0];
            var max_img_size = 1000000;
            var max_width = $(this).data('max-width');
            var max_height = $(this).data('max-height');
            var cur = $(this);

            if (file.size > max_img_size)
            {
                swalWithBootstrapButtons.fire('The file must be less than 1 MB','', 'error');
               // alert("The file must be less than 1 MB");
               $(cur).value="";
               return false;
            }
            var icon = '';
            var preview = $(this).data('preview');
            // console.log(preview);
            if(typeof preview === 'undefined')
                preview = '.file-preview';

            if($(preview).length)
            {
                $(preview).html('').addClass('hidden');
                switch(file.type)
                {
                    case 'image/png':
                    case 'image/jpg':
                    case 'image/jpeg':
                        var reader = new FileReader();
                        reader.onload = function (e) 
                        {
                            if(typeof max_width !== 'undefined' || typeof max_height !== 'undefined')
                            {
                                //Initiate the JavaScript Image object.
                                var image = new Image();

                                //Set the Base64 string return from FileReader as source.
                                image.src = e.target.result;

                                //Validate the File Height and Width.
                                image.onload = function () {
                                    var height = this.height;
                                    var width = this.width;
                                    if(height > max_height || width > max_width)
                                    {
                                        swalWithBootstrapButtons.fire("The file width x height must be " + max_height + ' x ' + max_width +'px','', 'error');
                                       // alert("The file width x height must be " + max_height + ' x ' + max_width +'px');
                                       $(cur).val('');
                                       return false;
                                    }
                                    else
                                    {
                                        if(height != width)
                                        {                  
                                            swalWithBootstrapButtons.fire('The file width x height must be same.','', 'error');                          
                                           // alert("The file width x height must be same. ");
                                           $(cur).val('');
                                           return false;
                                        }

                                        icon = '<img src="'+e.target.result+'" class="img-responsive img-fluid" alt="'+file.name+'">';
                                        $(preview).html(icon + ' <div class="caption text-center">' + file.name + '</div>').removeClass('hidden');
                                    }
                                }
                            }
                            else
                            {
                                icon = '<img src="'+e.target.result+'" class="img-responsive img-fluid" alt="'+file.name+'">';
                                $(preview).html(icon + ' <div class="caption text-center">' + file.name + '</div>').removeClass('hidden');
                            }
                        };

                        // read the image file as a data URL.
                        reader.readAsDataURL(file);
                        break;
                    case 'application/pdf':
                        icon = '<i class="fa fa-fw fa-file-pdf"></i>'; 
                        $(preview).html(icon + ' ' + file.name).removeClass('hidden');
                        break;
                    default:
                        icon = '<i class="fa fa-fw fa-file"></i>'; 
                        $(preview).html(icon + ' ' + file.name).removeClass('hidden');
                        break;
                }
                // $(preview).html(icon + ' ' + file.name).removeClass('hidden');
            }

            var target = $(this).data('target');
            if($(target).length)
                $(target).removeClass('has-error');
        });
    }

    if($('#ajaxLogin').length)
    {
        $('#ajaxLogin').ajaxForm(
        {
            success: function(data)
            {
                // console.log(data);
                // console.log(data.token);
                if(typeof data.token !== 'undefined' && data.token)
                {
                    $('input[name="_token"]').val(data.token);
                    $.ajaxSetup({
                        headers: {
                          'X-CSRF-TOKEN': data.token
                        }
                    });

                }
                $('#sessionLogin').modal('hide');
                $('#ajaxLogin').resetForm();
                $('#ajaxLogin .btn').prop('disabled',false);
                needToConfirm = false;
                heartBeat();
            }
        });
    }

    heartBeat();
});

function heartBeat()
{
    if(window.location.pathname != '/' && window.location.pathname.indexOf('/home') < 0 && window.location.pathname.indexOf('/login') < 0)
    {
        ajaxHeartbeatInterval = setInterval(function()
        {
            if(!ajaxHeartbeat)
            {
                ajaxHeartbeat = jQuery.ajax({
                    type: "GET",
                    dataType: "json",
                    url: base_url + '/heartbeat',
                    success: function(data)
                    {
                        // $('.restoreCategory').prop('disabled',false);


                        ajaxHeartbeat = null;
                    },
                    error: function(data, ajaxOptions, thrownError)
                    {
                        errorAjax(data, ajaxOptions, thrownError);
                        ajaxHeartbeat = null;
                    }
                });
            }
        }, 300000)
        // }, 5000)
    }
}

function readImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        var preview = $(input).closest('.img-container').find('.img-preview');
    
        reader.onload = function (e) {
            $(preview).attr('src', e.target.result).removeClass('d-none');
        }
    
        reader.readAsDataURL(input.files[0]);
    }
 }

function checkSlug(slug, url, id)
{
    if(!loadAjax)
    {
        loadAjax = jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: url,
            data: {slug: slug, id: id},
            beforeSend:function()
            {
                // $('.restoreCategory').prop('disabled',true);
            },
            success: function(data)
            {
                // $('.restoreCategory').prop('disabled',false);

                if(typeof data.available_slug !== 'undefined' && data.available_slug !== null && data.available_slug)
                {
                    $('#slug').val(data.available_slug)
                    $('.editable').editable('setValue', data.available_slug)
                }

                loadAjax = null;
            }
        });
    }
}

function before_load_data()
{
    if($(".rowlink").length)
    {
        var colspan = $(".rowlink").parent().find('th').length;
        $(".rowlink").html('<tr><td colspan="'+colspan+'">' +image_loading+ '</td></tr>');
    }
}
            
function askConfirm() {
    if (needToConfirm) {
        // Put your custom message here 
        return "Anda yakin akan meninggalkan halaman ini? Data yang anda masukkan tidak akan tersimpan."; 
    }
}

function errorHandling(e, settings, techNote, message)
{
    var code = settings.jqXHR.status;
    var error_msg = JSON.parse(settings.jqXHR.responseText).error;
    console.log(code);
    console.log(settings.jqXHR);
    console.log(techNote);
    console.log(message);
    if(typeof error_msg === 'undefined' || error_msg == '')
        error_msg = 'Terjadi kesalahan, silahkan refresh halaman ini';

    switch(code)
    {
        case 401:
            // jQuery('#sessionLogin').modal('show');
            // logoutUser();
            // return false;
            // break;
        case 419:
            jQuery('#sessionLogin').modal('show');
            if(typeof ajaxHeartbeatInterval !== 'undefined' && ajaxHeartbeatInterval)
                clearInterval(ajaxHeartbeatInterval);
            needToConfirm = true;
            break;
        default:
            break;                
    }

    // alert(error_msg);
}

function errorAjax(data, ajaxOptions, thrownError)
{
    var code = data.status;
    var error_msg = JSON.parse(data.responseText).error;
    console.log(code);
    console.log(ajaxOptions);
    console.log(thrownError);
    if(typeof error_msg === 'undefined' || error_msg == '')
        error_msg = 'Terjadi kesalahan, silahkan refresh halaman ini';

    if(ajaxOptions == 'parsererror')
        code = 419;
    
    switch(code)
    {
        case 401:
            // jQuery('#sessionLogin').modal('show');
            // logoutUser();
            // return false;
            // break;
        case 419:
            jQuery('#sessionLogin').modal('show');
            console.log(ajaxHeartbeatInterval);
            if(typeof ajaxHeartbeatInterval !== 'undefined' && ajaxHeartbeatInterval)
                clearInterval(ajaxHeartbeatInterval);
            needToConfirm = true;
            // console.log('loggout')
            // window.location.href =  base_url + '/login';
            break;
        default:
            break;                
    }

    // alert(error_msg);
}

function logoutUser()
{
    jQuery.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: base_url+'/logout',
        success: function(data)
        {
            window.location.href =  base_url + '/login';
        }
    });
}



function DecimalAsString(value)
{
    var i = parseFloat(value);
    if(isNaN(i)) { i = 0.00; }
    var minus = '';
    //if(i < 0) { minus = '-'; }
    s = new String(i);  
    if(s.indexOf('.') < 0) { s += ''; }
    s = minus + s;  
    return FormatNumberBy3(s);// + ',-';

}

function FormatNumberBy3(num, decpoint, sep) {
  // check for missing parameters and use defaults if so
  if (arguments.length == 2) {
    sep = ",";
  }
  if (arguments.length == 1) {
    sep = ",";
    decpoint = ",";
  }
  // need a string for operations
  num = num.toString();
  // separate the whole number and the fraction if possible
  a = num.split(decpoint);
  x = a[0]; // decimal
  y = a[1]; // fraction
  z = "";


  if (typeof(x) != "undefined") {
    // reverse the digits. regexp works from left to right.
    for (i=x.length-1;i>=0;i--)
      z += x.charAt(i);
    // add seperators. but undo the trailing one, if there
    z = z.replace(/(\d{3})/g, "$1" + sep);
    if (z.slice(-sep.length) == sep)
      z = z.slice(0, -sep.length);
    x = "";
    // reverse again to get back the number
    for (i=z.length-1;i>=0;i--)
      x += z.charAt(i);
    // add the fraction back in, if it was there
    if (typeof(y) != "undefined" && y.length > 0) 
      x += "." + y;
  }
  return x;
}



function validate(evt) {
  var theEvent = evt || window.event;

  // Handle paste
  if (theEvent.type === 'paste') {
      key = event.clipboardData.getData('text/plain');
  } else {
  // Handle key press
      var key = theEvent.keyCode || theEvent.which;
      key = String.fromCharCode(key);
  }
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}

function readURL(input) 
{
  
  if (input.files && input.files[0]) 
  {
    var i = tmp_files.length;
    var rules_width = $(input).data('rules-width')
    var rules_height = $(input).data('rules-height')
    console.log(input.files);
    $(input.files).each(function () 
    {
      totalImg = $('.img-uploaded-container').length;
      var imgHtml = '';
      var file = $(this);
      var reader = new FileReader();
      console.log(file)
    //   tmp_files.push(file);
      console.log(tmp_files);
      reader.onload = function (e) 
      {
        var image = new Image();

        //Set the Base64 string return from FileReader as source.
        image.src = e.target.result;
        image.onload = function () {
            var height = this.height;
            var width = this.width;
            if(width < rules_width)
            {
                alert("The file width x height min " + rules_width + ' x ' + rules_height +'px');
                return false;
            }
            else if(width != rules_width*(height/rules_height))
            {
                alert("The file width x height min " + rules_width + ' x ' + rules_height +'px');
                return false;
            }
            else
            {
                var clone = $('#clone_image').clone(true);
                clone.removeAttr('id').attr('name','images_kosong[]') //.prop('files',file);
                imgHtml += '<div class="img-uploaded-container data-img-'+totalImg+' data-new data-new-'+i+'" data-id="'+totalImg+'" data-src="'+ e.target.result +'" data-name="'+ file[0].name +'" style="background:url('+e.target.result+') no-repeat;background-size: contain;background-position: center;">';
                // imgHtml += '<div class="thumbnail" style="max-height:150px;overflow:hidden;position:relative;">';
                    // imgHtml += '<input type="hidden" class="image_hdn" name="images[]" />';
                //  imgHtml += '<img src="'+e.target.result+'" class="img-fluid card-img-top preview"/>';
                // imgHtml += '</div>';
                // imgHtml += '<span class="hoverContainer" style="position:absolute;top:50%;left:100px;">';
                //  imgHtml += '<a href="javascript:void(0);" class="btn btn-danger deleteImage btn-xs hover" hidden data-image-id="'+totalImg+'" data-fleet-id="'+fleetID+'"><span>DELETE</span></a>';
                // imgHtml += '</span>';
                imgHtml += '<button class="btn btn-danger deleteImage btn-delete-'+totalImg+'" data-target="'+ i +'" type="button" data-file-name="'+ file[0].name +'" data-image-id="" data-fleet-id="" display><i class="fa fa-trash"></i></button>';
                imgHtml += '</div>&nbsp;';
                jQuery(imgHtml).insertBefore('.upload-button');
                tmp_files.push(file);
                clone.insertBefore($('.btn-delete-'+totalImg));
                totalImg++;
                i++;
            }
        }
      }      
      console.log(tmp_files);     
      reader.readAsDataURL(this);
    });

    // totalFiles = input.files.length;
    // console.log(totalFiles);
    // var listFiles = new Array();
    // for(z=0;i<totalFiles;z++)
    // {
    //  thefiles = input.files[z];
    //  totalImg = $('.img-uploaded-container').length;
    //  var clone = $('#fleet_image').clone();
    //  clone.removeAttr('id').attr('class','images[]');
    //  var reader = new FileReader();
    //  console.log(z);
    //  console.log('.data-files-'+z);
    //  console.log($('.data-files-'+z).length);
    //  console.log('.data-img-'+totalImg);
    //  console.log($('.data-img-'+totalImg).length);
    //  if($('.data-img-'+totalImg).length == 0)
    //  {
    //    reader.onload = function (e) 
    //    {       
          
    //    }
    //    reader.readAsDataURL(thefiles);
    //  }
    // }

    return i;
  }
}

function checkSize(max_img_size,id)
{
   var input = document.getElementById(id);
   // check for browser support (may need to be modified)
   if(input.files && input.files.length == 1)
   {
       if (input.files[0].size > max_img_size)
       {
            swalWithBootstrapButtons.fire("The file must be less than " + (max_img_size/1000) + "KB", '', 'error');

           // alert("The file must be less than " + (max_img_size/1000) + "KB");
           input.value="";
           return false;
       }
   }
   return true;
}

function multi_select(cls, label, select_all, deselect_all)
{
    $(cls).multiSelect({
        selectableOptgroup: true,
        selectableHeader: '<div class="custom-header-search"><input type="text" class="search-input input-sm form-control" autocomplete="off" placeholder="Available '+ label +'..."></div>',
        selectionHeader: '<div class="custom-header-search"><input type="text" class="search-input input-sm form-control" autocomplete="off" placeholder="Selected '+ label +'..."></div>',
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
        afterSelect: function(values){
            this.qs1.cache();
            this.qs2.cache();
            console.log(values);
            $.each(values, function(i, val)
            {
                var parent = $(cls).find('option[value="'+val+'"]').data('parent');
                if($(cls).find('option[value='+parent+']').length)
                    $(cls).find('option[value='+parent+']').attr('selected', 'selected');
                if($(cls).find('option[data-parent='+val+']').length)
                {
                    var child_ids = [];
                    $(cls).find('option[data-parent='+val+']').each(function()
                    {
                        child_ids.push($(this).val());
                    })
                    console.log(child_ids);
    
                    if(child_ids.length)
                        $(cls).multiSelect('select', child_ids)
                    // $(cls).find('option[data-parent='+val+']').attr('selected', 'selected');
    
                }
            })
        },
        afterDeselect: function(values){
            this.qs1.cache();
            this.qs2.cache();
            $.each(values, function(i, val)
            {
                var parent = $(cls).find('option[value="'+val+'"]').data('parent');
                if($(cls).find('option[data-parent='+val+']').length)
                {
                    var child_ids = [];
                    $(cls).find('option[data-parent='+val+']').each(function()
                    {
                        child_ids.push($(this).val());
                    })
                    console.log(child_ids);
    
                    if(child_ids.length)
                        $(cls).multiSelect('deselect', child_ids)
                    // $(cls).find('option[data-parent='+val+']').attr('selected', 'selected');
    
                }
            })
        }
    }); 

    $(document).on('click', select_all, function(e) {
        e.preventDefault();
        $(cls).multiSelect('select_all');
        return false;
    });
    $(document).on('click', deselect_all, function(e) {
        e.preventDefault();
        $(cls).multiSelect('deselect_all');
        return false;
    });
}

function addLang(lang, label)
{
    var tabs_clone = $('.tabs_clone').clone(true);
    var tab_pane_clone = $('.tab_pane_clone').clone(true);

    tabs_clone.attr({
        class: 'nav-item tabs_new'
    });
    tab_pane_clone.attr({
        class: 'tab-pane fade tab_pane_new'
    });

    var language = lang.lang_code + '-' + lang.lang_name;

    var id = $(tabs_clone).find('.nav-link').attr('id')
    var href = $(tabs_clone).find('.nav-link').attr('href')
    var aria_controls = $(tabs_clone).find('.nav-link').attr('aria-controls')

    var new_id = id.replace('##a##', language)
    $(tabs_clone).find('.nav-link').attr('id', new_id)
    var new_href = href.replace('##a##', language)
    $(tabs_clone).find('.nav-link').attr('href', '#'+new_href)
    var new_aria_controls = aria_controls.replace('##a##', language)
    $(tabs_clone).find('.nav-link').attr('aria-controls', new_aria_controls)

    $(lang.lang_name).insertBefore($(tabs_clone).find('.deleteLang'))
    $(tabs_clone).find('.deleteLang').before(lang.lang_name)

    var pane_id = $(tab_pane_clone).attr('id')
    var pane_aria_labelledby = $(tab_pane_clone).attr('aria-labelledby')

    var new_pane_id = pane_id.replace('##a##', language)
    $(tab_pane_clone).attr('id', new_pane_id)
    var new_pane_aria = pane_aria_labelledby.replace('##a##', language)
    $(tab_pane_clone).attr('aria-labelledby', new_pane_aria)

    $(tab_pane_clone).find('input[type=text]').attr('placeholder', 'Masukan '+label[0]+' ('+ lang.lang_code.toUpperCase() +')')
    $(tab_pane_clone).find('input[type=text]').prop('required', true)
    $(tab_pane_clone).find('textarea').attr('placeholder', 'Masukan '+label[1]+' ('+ lang.lang_code.toUpperCase() +')')
    $(tab_pane_clone).find('textarea.summernote-clone').addClass('summernote')
    $(tabs_clone).find('.deleteLang').attr('data-target', language)

    $(tab_pane_clone).append('<input type="hidden" name="details[lang_id][]" class="exist-lang-id" value="'+ lang.id +'">')

    $(tabs_clone).insertBefore($('#lang'))
    $('#custom-tabs-one-tabContent').append(tab_pane_clone)
}

function slug(text)
{
    // console.log(text);
    if(typeof text !== 'undefined')
    {
        return text.toString().toLowerCase().replace(/\s+/g, '-') // Ganti spasi dengan -
            .replace(/[^\w\-]+/g, '') // Hapus semua karakter non-word
            .replace(/\-\-+/g, '-') // Ganti multiple - atau single -
            .replace(/^-+/, ' ') 
            .replace(/-+$/, ' ');
    }
}

function clone(table_id, trClass = 'tr_new', id = null, prop, hidden, uom_name)
{
    var target = table_id
    var a = $(target).find(trClass).length+1;
    // console.log(a);
    var cloned_tr = $(target).find('.tr_clone').clone(true);
    // console.log(cloned_tr)
    cloned_tr.attr({
        class: trClass
    });

    var dimension = cloned_tr.find('.dimension').attr(prop, '')
    cloned_tr.find('.tr_delete_btn').attr(hidden, '')
    cloned_tr.find('.uom_name').html(uom_name)
    cloned_tr.find('.dimension_id').html(id)

    var option = $(dimension).find('option[value="'+ id +'"]:not(.default)').attr('selected', '')

    console.log(option)

    cloned_tr.find('.field-name').each(function(e)
    {
        var name = $(this).attr('name');
        var new_name = name.replace("##a##",a);
        $(this).attr('name',new_name);

        var id = $(this).attr('id');
        if(typeof id !== 'undefined'){
            var new_id = id.replace("##a##",a);
            $(this).attr('id',new_id);
            $(this).addClass('supplier-name');
        }
    });

    $(table_id + '>tbody').append(cloned_tr);
    cloned_tr.find('select:visible').select2({theme: 'bootstrap4'});
}

function renderProductWarehouse(id, target)
{
    jQuery.ajax({
        type: 'GET',
        dataType: 'json',
        url: base_url+'/products/getProductWarehouse/'+id,
        success: function(data)
        {
            console.log(data)
            if($(target).length)
            {
                var tr = ''
                $.each(data.products, function(i, product)
                {
                    tr += '<tr>';
                    tr += '<td class="align-middle">'+product.item_name+'</td>'
                    tr += '<td class="align-middle">'+product.warehouse+'</td>'
                    tr += '<td class="align-middle">'+product.item_qty+'</td>'
                    tr += '<td class="align-middle">'+product.avail_qty+'</td>'
                    tr += '</tr>';
                })

                if(tr !== '')
                {
                    $(target).find('tbody').html(tr)
                    if ( $.fn.dataTable.isDataTable( target ) ) {
                        table = $(target).DataTable();
                    }
                    else {
                        table = $(target).DataTable();
                    }   
                }
            }
        }
    });
}

function roundFixed(value, fixed=0)
{
    return parseFloat((Math.round(value * 100) / 100).toFixed(fixed))
}

function sendFile(file, el) 
{
    data = new FormData();
    data.append("file", file);
    $.ajax({
        data: data,
        type: "POST",
        url: base_url+'/posts/asset',
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            
            if(data.data.media !== null && data.data.media.media_path !== null)
            {
                var url = data.data.media.media_path;
                $(el).summernote('insertImage', url)
            }
        }
    });
}

function format(data, sales_order=false)
{
    console.log(data)
    var renderList = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;width:100%">'+
                '<thead class="thead-dark">'+
                    '<th>Posting ID</th>'+
                    '<th>Jurnal ID</th>'+
                    '<th>Jurnal Code</th>'
    if(sales_order)
        renderList += '<th>Grand Total</th>'
                    // '<th>Grand Total</th>'+
    renderList += '<th>Param Sent</th>'+
            '<th>Response</th>'+
            '<th>Created At</th>'+
        '</thead>'+
        '<tbody>';
    if(typeof data.transaction !== 'undefined' && data.transaction.length)
    {
        $.each(data.transaction, function(i, trx)
        {
            renderList +=  '<tr>'+
                                renderTd(data.jurnal_posting_id)+
                                renderTd(trx.target_id)+
                                renderTd(trx.target_code)
            if(sales_order)
                renderList += renderTd(trx.target_amount);
            renderList += renderTd(trx.params_limit, true, trx.params_sent, 'Params Sent')+
                            renderTd(trx.responses_limit, true, trx.responses, 'Response')+
                            renderTd(trx.created_at)+
                        '</tr>'
        })
    }
    else
        renderList += '<tr><td colspan="6" class="text-center">No Posting Data Yet</td></tr>'

    renderList += '</tbody>'+
                '</table>';
    
    return renderList;
}

function renderTd(text, hover=false, data=null, label=null)
{
    if(hover)
    {
        var value = '';
        if(data !== null)
            value = data;
        return "<td class='parent'>"+text+" <br><a href='javascript:void(0)' class='read-more hide' data-value='"+value+"' data-title-label='"+label+"'>View Detail</a></td>"
    }
    else if(typeof text !== 'undefined' && text !== null)
        return '<td>'+text+'</td>';
    else
        return '<td>-</td>';
}

function check_item_maintain_stock(item, class_tr, class_rack='.rack', bin_rack='.bin')
{
    if((typeof item.product !== 'undefined' && item.product !== null && item.product.item_maintain_stock == 0) || 
        (typeof item.item_maintain_stock !== 'undefined' && item.item_maintain_stock == 0)
    )
    {
        $(class_tr).find(class_rack).addClass('d-none')
        $(class_tr).find(bin_rack).addClass('d-none')
    }
    else
    {
        $(class_tr).find(class_rack).select2({theme: 'bootstrap4'}).prop('required', true)
        $(class_tr).find(bin_rack).select2({theme: 'bootstrap4'}).prop('required', true)
    }
}

function get_product_detail(item_id, wh_id, that)
{
    var avail_qty = 0;
    if(typeof item_id !== 'undefined' && typeof wh_id !== 'undefined' && wh_id !== null)
    {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: base_url + '/sales-order/getProduct/' + item_id +'/useID?wh_id='+wh_id,
            beforeSend:function()
            {
                // $(target).val('').trigger('change')
                $(that).prop('disabled',true);
                $('.warehouse-action').prop('disabled',true);
            },
            success: function(data)
            {
                // salesOrderAjax = null;
                if(typeof data.product.avail_qty !== 'undefined' && data.avail_qty !== null)
                {
                    avail_qty = data.product.avail_qty;
                }
                $(that).prop('disabled',false);
                $('.warehouse-action').prop('disabled',false);
                $(that).closest('tr').find('.check-avail-qty').val(avail_qty);
                $(that).closest('tr').find('.avail-qty').html('Avail Qty : '+ avail_qty);
            }
        })
    }
}

function swalConfirm(title='Apakah anda yakin?',showCancelButton=true, confirmBtnText="Ya!",cancelBtnText="Tidak", text="", icon='warning')
{
    return swalWithBootstrapButtons.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: showCancelButton,
        confirmButtonText: confirmBtnText,
        cancelButtonText: cancelBtnText,
        reverseButtons: true
    })
}

function swalConfirmSubmit(cur, e_type, text="Apakah anda yakin?", spinner="false")
{
    $('.spinner').addClass('d-none');
    swalConfirm(text).then((result) => {
        if(result.isConfirmed)
        {
            $(cur).trigger(e_type, {'send': true});
            if (spinner == true)
                $('.spinner').removeClass('d-none');
        }
    // else // if(result.isDismiss)
    //   return false;
    })
}

function swalArchivedTable(url,targetTr,deleteBtn, tdColspan, tdText, table)
{
    swalConfirm().then((result) => {
        if(!loadAjax2 && result.isConfirmed)
        {
            loadAjax2 = jQuery.ajax({
                type: "DELETE",
                dataType: "json",
                url: url,
                beforeSend: function()
                {
                    $(deleteBtn).prop('disabled',true);
                },
                success: function(data)
                {
                    $(deleteBtn).prop('disabled',false);

                    if(typeof data.success !== 'undefined' && data.success !== null && data.success)
                    {
                        $(targetTr).html('<td colspan="'+tdColspan+'"><p class="text-center">'+tdText+'</p></td>');
                        // swalWithBootstrapButtons.fire(tdText, "", 'success')
                        $(targetTr).addClass('table-danger');
                        setTimeout(function(){ 
                            $(table).DataTable().ajax.reload();
                        },2000);
                    }
                    else
                    {
                        if(typeof data.error !== 'undefined' && data.error !== null)
                            swalWithBootstrapButtons.fire(data.error, "", 'error');
                    }

                    loadAjax2 = null;
                }
            });
        }
        // needToConfirm = true;
    })
}

function swalRestoreTable(url,targetTr,restoreBtn, tdColspan, tdText, table, label)
{
    swalConfirm('Apakah anda yakin akan me-restore '+label+' ?').then((result) => {
        if(!loadAjax2 && result.isConfirmed)
        {
            loadAjax2 = jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: url,
                beforeSend:function()
                {
                    $(restoreBtn).prop('disabled',true);
                },
                success: function(data)
                {
                    $(restoreBtn).prop('disabled',false);

                    if(typeof data.success !== 'undefined' && data.success !== null && data.success)
                    {
                        $(targetTr).html('<td colspan="'+tdColspan+'"><p class="text-center">'+tdText+'</p></td>');
                        $(targetTr).addClass('table-success');
                        setTimeout(function(){ 
                            $(table).DataTable().ajax.reload();
                        },2000);
                    }
                    else
                    {
                        if(typeof data.error !== 'undefined' && data.error !== null)
                            swalWithBootstrapButtons.fire(data.error, "", 'error');
                    }

                    loadAjax2 = null;
                }
            });
        }
        // needToConfirm = true;
    })
}

function checkExist(params)
{
   var term = $.trim(params.term);
   if(term === "") { return null; }

   var optionsMatch = false;

   this.$element.find("option").each(function() {
      if(this.text.toLowerCase() == term.toLowerCase()) {
         optionsMatch = true;
      }
   });

  if(optionsMatch) {
    return null;
  }

   var return_text = term.toLowerCase().replace(/\b[a-z]/g, function(letter) {
      return letter.toUpperCase();
   });
  return {id: return_text, text: return_text};
}