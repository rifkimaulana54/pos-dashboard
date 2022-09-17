$(document).ready(function($) 
{
    //$('#exceltable').hide();
    //$('#btnSubmit').css('display','none');

    $(document).on('click','#filebutton',function(){
        var target = $(this).data('target');
        if(typeof target === 'undefined')
            target = '#file';
        $(target).click();
    });

    // $(document).on('click','#filebutton',function(){
    //     $('#gambar').click();
    // });

    $(document).on('click', '.upload-btn', function()
    {
        $(this).closest('.card').find('.gambar').click();
    })

    $(document).on('change', '.gambar', function()
    {
        bacaGambar(this);
    })

    $(document).on('change','#file',function()
    {

        // $('#exceltable').show();

        // if(ExportToTable())
        // {
        //     $('#exceltable').show();
            
        //     //$('#btnSubmit').css('display','block');

        // }
        // console.log('changed');
        var fileInput = document.getElementById('file');   
        var filename = fileInput.files[0].name;
        // console.log(filename);
        if(typeof filename !== 'undefined' && filename != '') 
        {
            $('#nameFile').html(filename).removeClass('d-none');
            $('.current_file').addClass('d-none');
            $('#btnSubmit').removeClass('d-none');
        }
        else
        {
            $('#nameFile').html('Tidak ada file yang dipilih').addClass('d-none');
            $('#btnSubmit').addClass('d-none');
        }
    });

    $(document).on('click','#btnCancel',function(e)
    {
        $('#cancelHidden').val(1);
        console.log('masuk');
    });

    $(document).on('click','#btnConfirm',function(e)
    {
        needToConfirm = false;
        if($('.overlay').length)
            $('.overlay').removeClass('d-none');
    });

    // $(document).on('change', '#gambar', function(){
    //     bacaGambar(this);
    //  });

    if($('.add-att').length)
    {
        $(document).on('click', '.add-att', function()
        {
            var clone_target = '.att-clone';
            if($(this).data('target-clone') !== 'undefined' && $(this).data('target-clone'))
            {
                var target_clone = $(this).data('target-clone');
                if($(target_clone).length)
                    clone_target = target_clone;
            }
            var cloned_tr_att = $(clone_target).clone(true);
            var name_file = $(this).data('name-file');
            var name_caption = $(this).data('name-caption');
            var col_size = typeof $(this).data('col-size') !== 'undefined' ? $(this).data('col-size') : 'col-sm-3';
            cloned_tr_att.attr({
                class: col_size+' container-img text-center py-3 mb-4'
            })
            if(typeof name_file !== 'undefined')
                $(cloned_tr_att).find('.file-att').attr('name', name_file);
            if(typeof name_caption !== 'undefined')
                $(cloned_tr_att).find('input[type=text]').attr('name', name_caption);
            $(cloned_tr_att).insertBefore($(this).closest('.att-add-container'));
        })
    }

    if($('.delete-attachment').length)
    {
        $(document).on('click', '.delete-attachment', function()
        {
            if(confirm('Are you sure?'))
            {
                $(this).closest('.container-img').remove();
            }
            else
                return false;
        })
    }

    if($('.btn-remove-file').length)
    {
        $(document).on('click', '.btn-remove-file', function()
        {
            if(!confirm('Are you sure?'))
                return false;

            $(this).closest('.container-img').find('.old_house_rules').val('');
            $(this).closest('.container-img').find('.file-name').addClass('d-none');
            $(this).closest('.container-img').find('.file-att').val('');
            $(this).addClass('d-none');
        })
    }

    if($('.btn-upload').length)
    {
        $(document).on('click', '.btn-upload', function(e)
        {
            $(this).closest('div').find('.file-att').click()
        })
    }

    if($('.img-preview').length)
    {
        $(document).on('click', '.img-preview', function()
        {
            var img_src = $(this).attr('src');
            if(!$(this).closest('.container-img').find('.attach-index').length)
            {
                $(this).closest('div').find('.file-att').click()
                return false;
            }
            $('#preview-attachment').find('.img-thumbnail').attr('src', img_src);

            $('#preview-attachment').modal('show')
        })
    }

    $(document).on('change','#xlxPreview',function(e)
    {
        var table = $(this).data('table');
        var val = $(this).val();

        if($(table).length)
        {
            $('.step-1, .step-2, .step3').addClass('d-none');
            $(table).html('').addClass('d-none');
        }

        var fileInput = document.getElementById('xlxPreview');   
        var filename = fileInput.files[0].name;

        if(typeof filename !== 'undefined' && filename != '') 
        {
            ExportToTable(this,table);
            $('#nameFile').html(filename).removeClass('d-none');
            $('.current_file').addClass('d-none');
            // $('#btnSubmit').removeClass('d-none');
        }
        else
        {
            $('#nameFile').addClass('d-none');
            // $('#btnSubmit').addClass('d-none');
        }
        // if(val)
        // {
        // }
    });
});

function ExportToTable(cur, table)
{
    var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xlsx|.xls)$/;  
    if (regex.test($(cur).val().toLowerCase())) 
    {
        var xlsxflag = false; /*Flag for checking whether excel is .xls format or .xlsx format*/  
        if ($(cur).val().toLowerCase().indexOf(".xlsx") > 0) {  
            xlsxflag = true;  
        }
        /*Checks whether the browser supports HTML5*/  
        if (typeof (FileReader) != "undefined") 
        {
            $('.step-header, .step-rows, .step-columns, .step-columns, .step-mapping, .column-list').addClass('d-none')
            if($('.overlay').length)
                $('.overlay').removeClass('d-none');
            $('#btnSubmit').addClass('d-none');
            var reader = new FileReader(); 
            reader.onload = function (e) 
            {
                var data = e.target.result;  
                var workbook = (xlsxflag) ? XLSX.read(data, { type: 'binary' }) : XLS.read(data, { type: 'binary' });
                /*Gets all the sheetnames of excel in to a variable*/  
                var sheet_name_list = workbook.SheetNames; 

                var cnt = 0;
                // console.log(sheet_name_list);
                sheet_name_list.forEach(function (y) 
                {
                    /*Convert the cell value to Json*/  
                    var excelHtml = (xlsxflag) ? XLSX.utils.sheet_to_html(workbook.Sheets[y]) : XLS.utils.sheet_to_html(workbook.Sheets[y]);
                    // console.log(excelHtml);
                    
                    // return;
                    var exceljson = (xlsxflag) ? XLSX.utils.sheet_to_json(workbook.Sheets[y],{header:1}) : XLS.utils.sheet_to_row_object_array(workbook.Sheets[y],{header:1});
                    // console.log(exceljson);
                // console.log(exceljson.length);
                    if (exceljson.length > 0 && cnt == 0) 
                    // if (excelHtml.length > 0 && cnt == 0) 
                    {  
                        // $(table).html(excelHtml);
                        // $(table).removeClass('d-none');
                        // if($('.step-1').length)
                        //     $('.step-1').removeClass('d-none');
                        BindTable(exceljson, table);  
                        cnt++;  
                    }
                });
            }  
            if (xlsxflag) {/*If excel file is .xlsx extension than creates a Array Buffer from excel*/  
                reader.readAsArrayBuffer($(cur)[0].files[0]);  
            }  
            else {  
                reader.readAsBinaryString($(cur)[0].files[0]);  
            }  
        }
        else
        {
            swalWithBootstrapButtons.fire('Sorry! Your browser does not support HTML5!','', 'error')
        }  
    } 
    else 
    {  
        swalWithBootstrapButtons.fire('Please upload a valid Excel file!','', 'error')
     // alert("Please upload a valid Excel file!");  
    } 
}

function BindTable(jsondata, tableid) 
{/*Function used to convert the JSON array to Html Table*/  
     var columns = BindTableHeader(jsondata, tableid); /*Gets all the column headings of Excel*/  
        // console.log(columns);
     // console.log(jsondata);
     // console.log(jsondata.length);
     for (var i = 0; i < 100; i++) 
     {
        var startCell = 0;
        var row$ = $('<tr class="tr-row" data-n="'+i+'"/>');  
        row$.append($('<td class="td-numbering"/>').html((i+1)));  
        for (var colIndex = 0; colIndex < columns.length; colIndex++) {
            var cellName = '';
            if(startCell < 26) 
                cellName += String.fromCharCode(97 + colIndex);
            // console.log(i);
            // console.log(colIndex);
            var cellValue = (typeof jsondata[i] !== 'undefined' && jsondata[i].length) ? jsondata[i][columns[colIndex]] : null;  
            if (typeof cellValue == null)  
                 cellValue = "";  
            row$.append($('<td class="td-'+ colIndex +'" data-n="'+ colIndex +'" data-cell="'+ cellName.toUpperCase() +'"/>').html(cellValue));  

            startCell++;
        }  
        $(tableid).append(row$);  
     }  

    if($(tableid).find('.tr-row').length)
    {
        $(tableid).removeClass('d-none');

        if($('.step-header').length)
        {
            $('.step-header #rowHeader').val('');
            $('.step-header #rowHeaderValue').html('');
            $('.step-header .fa-check, .cancel-step-header').addClass('d-none');
            $('.step-header').removeClass('d-none');
        }

        if($('.step-rows').length)
        {
            $('.step-rows #rowMapping').val('');
            $('.step-rows #rowValue').html('');
            $('.step-rows .fa-check, .cancel-step-rows').addClass('d-none');
            $('.step-rows').addClass('d-none');
        }

        if($('.step-columns').length)
        {
            $('.column-list').html('');
            $('.step-columns .fa-check, .cancel-step-columns').addClass('d-none');
            $('.step-columns,.column-list').addClass('d-none');
        }

        if($('.step-mapping').length)
        {
            $('.step-mapping .form-mapping').html('');
            $('.step-mapping .fa-check').addClass('d-none');
            $('.step-mapping').addClass('d-none');
        }

        if($('.overlay').length)
            $('.overlay').addClass('d-none');
    }
 }  

 function BindTableHeader(jsondata, tableid) {/*Function used to get all column names from JSON and bind the html table header*/  
     var columnSet = [];  
     var headerTr$ = $('<tr class="tr-header"/>');  
     for (var i = 0; i < jsondata.length; i++) {  
         var rowHash = jsondata[i];  
         // console.log(rowHash);
        for(key = 0; key < rowHash.length; key++)
         // for (var key in rowHash) 
        {  
            // console.log(key);
            // console.log(rowHash.hasOwnProperty(key));

            if ($.inArray(key, columnSet) == -1) {/*Adding each unique column names to a variable array*/  
             columnSet.push(key);  
             if (rowHash.hasOwnProperty(key)) 
                headerTr$.append($('<td/>').html(key));  
            else
                headerTr$.append($('<td/>'));  
            }   
         }  
     }  

     // console.log(columnSet);
     $(tableid).append(headerTr$);  
     return columnSet;  
 }

function bacaGambar(input) {
    if (input.files && input.files[0]) {
       var reader = new FileReader();

       var max_width = $(input).data('max-width');
        var max_height = $(input).data('max-height');
        var max_size = parseInt($(input).data('max-size'));
        var wid = $(input).data('wid')
        var preview = $(input).data('preview');
  
       reader.onload = function (e) {
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
                    // console.log(width)
                    // console.log(max_width*(Math.floor(height/max_height)))
                    if(width < max_width)
                    {
                        swalWithBootstrapButtons.fire("The file width x height min " + max_width + ' x ' + max_height +'px','', 'error')
                        // alert("The file width x height min " + max_width + ' x ' + max_height +'px');
                        $(input).val('');
                        return false;
                    }
                    else if(width != (max_width*(Math.floor(height/max_height))))
                    {
                        swalWithBootstrapButtons.fire("The file width x height min " + max_width + ' x ' + max_height +'px','', 'error')
                        // alert("The file width x height min " + max_width + ' x ' + max_height +'px');
                        $(input).val('');
                        return false;
                    }
                    else
                    {  
                        if(typeof max_size !== 'undefined' && input.files[0].size > max_size)
                        {
                            swalWithBootstrapButtons.fire('file '+ input.files[0].name +' exceeded max size : ' + (max_size / Math.pow(1024,1)) + ' Kb','', 'error')
                            // alert('file '+ input.files[0].name +' exceeded max size : ' + (max_size / Math.pow(1024,1)) + ' Kb')
                        }
                        else
                        {
                            $(preview).attr('src', e.target.result);
                            if(typeof wid !== 'undefined')
                                $(preview).attr('width', wid+'px');
                            else
                                $(preview).attr('width', '225px');
                        }
                    }
                }
            }
            else
            {
                if(typeof max_size !== 'undefined' && input.files[0].size > max_size)
                {
                    swalWithBootstrapButtons.fire('file '+ input.files[0].name +' exceeded max size : ' + (max_size / Math.pow(1024,1)) + ' Kb','', 'error')
                    // alert('file '+ input.files[0].name +' exceeded max size : ' + (max_size / Math.pow(1024,1)) + ' Kb')
                }
                else
                {
                    $(preview).attr('src', e.target.result);
                    if(typeof wid !== 'undefined')
                        $(preview).attr('width', wid+'px');
                    else
                        $(preview).attr('width', '225px');
                }
            }
       }
  
       reader.readAsDataURL(input.files[0]);
    }
 }