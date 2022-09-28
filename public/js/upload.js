var $modal = $('#modal');
var $modalDetail = $('#modalDetail');
var imageCrop = document.getElementById('image');
var cropper;

$(document).ready(function($) 
{
    //$('#exceltable').hide();
    //$('#btnSubmit').css('display','none');

    $(document).on('click','#filebutton',function(){
        $('#file').click();
    });

    // $(document).on('click','#filebutton',function(){
    //     $('#gambar').click();
    // });

    $(document).on('click', '.upload-btn', function()
    {
        var target = $(this).data('target');
        if(typeof target === 'undefined' || target == '' || target == null) target = '.gambar';
        $(this).closest('.card').find(target).click();
    })

    $(document).on('change', '.gambar', function()
    {
        var cropper = $(this).data('cropper');
        if(typeof cropper === 'undefined') cropper = 0;

        // console.log(cropper);
        bacaGambar(this, cropper);
    })

    $(document).on('change', '.image', function(e)
    {
        cekGambar = bacaGambar(this,1);
        console.log('cekGambar')
        console.log(cekGambar)
        if(cekGambar)
        {
            var files = e.target.files;
            var done = function (url) {
                imageCrop.src = url;
                $modal.modal('show');
            };
            var reader;
            var file;
            var url;
            input = this

            if (files && files.length > 0) 
            {
                file = files[0];

                if (URL) 
                {
                    done(URL.createObjectURL(file));
                } 
                else if (FileReader) 
                {
                    reader = new FileReader();
                    reader.onload = function (e) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        }
    })

    $modal.on('shown.bs.modal', function () 
    {
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 3,
            // preview: '.preview',
            minCropBoxWidth: 250,
            minCropBoxHeight: 250,
            movable: false,
            zoomable: false,
            rotatable: false,
            scalable: false
        });

    }).on('hidden.bs.modal', function () 
    {
        if(typeof cropper !== 'undefined' && cropper)
        {
            cropper.destroy();
            cropper = null;
        }
    });

    $("#croplist").click(function()
    {
        var imageType = $(this).data('image');
        var dataForm = $(this).data('form');
        var input = $(this).data('input');
        if(typeof imageType == 'undefined' || imageType == '' || imageType == null) imageType = 'default';

        switch(imageType)
        {
            case 'list':
                canvas = cropper.getCroppedCanvas({
                    width: 200,
                    height: 200,
                });
                var preview = $(input).data('preview');
                var meta = 'image';
                // $('#modalLabel').text('Crop Image Detail');
                break;
            // case 'detail':
            //     canvas = cropper.getCroppedCanvas({
            //         width: 400,
            //         height: 400,
            //     });
            //     var preview = $(input).data('previewDetail');
            //     var meta = 'detail_image';
            //     $(this).data('image','list');
            //     $('#modalLabel').text('Crop Image List');
            //     break;
        }

        canvas.toBlob(function(blob) 
        {
            // console.log(blob);
            const formData = new FormData();
            var fileName = $('.editable-click').text() + '-'+imageType+'.' + blob.type.split('/')[1];
            formData.append('images[]', blob, fileName );
            // for (var pair of formData.entries()) {
            //     console.log(pair[0]+ ', ' + pair[1]); 
            // }
            // return
            url = URL.createObjectURL(blob);
            var reader = new FileReader();
            // console.log(input);

            reader.readAsDataURL(blob); 
            reader.onloadend = function(e) {
                var base64data = reader.result;	
                $(preview).attr('src', base64data);

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: base_url + '/upload-assets',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('.spinner').removeClass('d-none');
                    },
                    success: function(data)
                    {
                        $(dataForm).append('<input type="hidden" name="meta['+meta+']" class="meta-image" value=\''+ JSON.stringify(data.images[0]) +'\'>');
                        // $modal.modal('hide');
                        // alert("success upload image");
                    }
                });
            }
        });

        cropper.destroy();
        cropper = null;

        if(imageType == 'detail')
        {
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 3,
                // preview: '.preview',
                minCropBoxWidth: 400,
                minCropBoxHeight: 400,
                movable: false,
                zoomable: false,
                rotatable: false,
                scalable: false
            });
        }
        else
        {
            $(this).data('image','detail');
            $('#modalLabel').text('Crop Image');
            $modal.modal('hide');
        }

    //     $modal.modal('hide');
    //     $modalDetail.modal('show');
    //     console.log(image);
    //     $modalDetail.on('shown.bs.modal', function () {
    //         cropper = new Cropper(image, {
    //             // aspectRatio: 16 / 9,
    //             viewMode: 1,
    //             // preview: '.preview',
    //             minCropBoxWidth: 400,
    //             minCropBoxHeight: 400,

    //         });
    //     });
    })

    $(document).on('change','#file',function()
    {

        // $('#exceltable').show();

        // if(ExportToTable())
        // {
        //     $('#exceltable').show();
            
        //     //$('#btnSubmit').css('display','block');

        // }

        var fileInput = document.getElementById('file');   
        var filename = fileInput.files[0].name;

        console.log(fileInput);

        if(typeof filename !== 'undefined' && filename != '') 
        {
            $('#nameFile').html(filename).removeClass('d-none');
            $('.current_file').addClass('d-none');
            $('#btnSubmit').removeClass('d-none');
        }
        else
        {
            $('#nameFile').addClass('d-none');
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
        if($('.overlay').length)
            $('.overlay').removeClass('d-none');
    });

    // $(document).on('change', '#gambar', function(){
    //     bacaGambar(this);
    //  });
});

function bacaGambar(input, cropper = 0) 
{
    if (input.files && input.files[0]) 
    {
       var reader = new FileReader();

       var min_width = $(input).data('min-width');
       // console.log(min_width);
        var min_height = $(input).data('min-height');
        // console.log(min_height);
        var max_width = $(input).data('max-width');
       // console.log(max_width);
        var max_height = $(input).data('max-height');
       // console.log(max_height);
        var max_size = parseInt($(input).data('max-size'));
        var wid = $(input).data('wid')
        var preview = $(input).data('preview');

       reader.onload = function (e) 
       {
            if(typeof min_width !== 'undefined' || typeof min_height !== 'undefined' || typeof max_width !== 'undefined' || typeof max_height !== 'undefined')
            {
                //Initiate the JavaScript Image object.
                var image = new Image();

                //Set the Base64 string return from FileReader as source.
                image.src = e.target.result;

                //Validate the File Height and Width.
                image.onload = function () 
                {
                    var height = this.height;
                    var width = this.width;
                    // console.log(width)
                    // console.log(max_width*(Math.floor(height/max_height)))
                    if(width < min_width)
                    {
                        alert("The file width below min width: " + min_width +'px.');
                        $(input).val('');
                        return false;
                    }
                    else if(height < min_height)
                    {
                        alert("The file height below min height: " + min_height +'px.');
                        $(input).val('');
                        return false;
                    }
                    else if(width > max_width)
                    {
                        alert("The file width exceeded max width: " + max_width +'px.');
                        $(input).val('');
                        return false;
                    }
                    else if(height > max_height)
                    {
                        alert("The file height exceeded max height: " + max_height +'px.');
                        $(input).val('');
                        return false;
                    }
                    else if(((typeof min_width !== 'undefined' && typeof min_height !== 'undefined' && min_width == min_height) || (typeof max_width !== 'undefined' && typeof max_height !== 'undefined' && max_width == max_height)) && width != height)
                    {
                        alert("The file width x height must be 1:1 (square).");
                        $(input).val('');
                        return false;
                    }
                    else if(typeof max_size !== 'undefined' && input.files[0].size > max_size)
                    {
                        alert('file '+ input.files[0].name +' exceeded max size : ' + (max_size / Math.pow(1048576,1)) + ' Mb')
                        $(input).val('');
                        return false;
                    }
                    else
                    {  
                        // if(typeof max_size !== 'undefined' && input.files[0].size > max_size)
                        // {
                        //     alert('file '+ input.files[0].name +' exceeded max size : ' + (max_size / Math.pow(1024,1)) + ' Kb')
                        // }
                        // else
                        // {
                            if(cropper)
                            {
                                imageCrop.src = e.target.result;
                                $modal.modal('show');

                                // var files = e.target.files;
                                // var done = function (url) {
                                //     image.src = url;
                                //     $modal.modal('show');
                                // };
                                // var reader;
                                // var file;
                                // var url;
                                // input = this

                                // if (files && files.length > 0) 
                                // {
                                //     file = files[0];

                                //     if (URL) 
                                //     {
                                //         done(URL.createObjectURL(file));
                                //     } 
                                //     else if (FileReader) 
                                //     {
                                //         reader = new FileReader();
                                //         reader.onload = function (e) {
                                //             done(reader.result);
                                //         };
                                //         reader.readAsDataURL(file);
                                //     }
                                // }
                                return false;
                            }

                            // console.log(cropper);
                            $(preview).attr('src', e.target.result);
                            if(typeof wid !== 'undefined')
                                $(preview).attr('width', wid+'px');
                            else
                                $(preview).attr('width', '225px');
                        // }
                    }
                }
            }
            else if(typeof max_size !== 'undefined' && input.files[0].size > max_size)
            {
                alert('file '+ input.files[0].name +' exceeded max size : ' + (max_size / Math.pow(1024,1)) + ' Kb')
                return false;
            }
            else
            {
                if(cropper)
                {
                    imageCrop.src = e.target.result;
                    $modal.modal('show');

                    // var files = e.target.files;
                    // var done = function (url) {
                    //     image.src = url;
                    //     $modal.modal('show');
                    // };
                    // var reader;
                    // var file;
                    // var url;
                    // input = this

                    // if (files && files.length > 0) 
                    // {
                    //     file = files[0];

                    //     if (URL) 
                    //     {
                    //         done(URL.createObjectURL(file));
                    //     } 
                    //     else if (FileReader) 
                    //     {
                    //         reader = new FileReader();
                    //         reader.onload = function (e) {
                    //             done(reader.result);
                    //         };
                    //         reader.readAsDataURL(file);
                    //     }
                    // }
                    return false;
                }

                $(preview).attr('src', e.target.result);
                if(typeof wid !== 'undefined')
                    $(preview).attr('width', wid+'px');
                else
                    $(preview).attr('width', '225px');
            }
       }
  
       reader.readAsDataURL(input.files[0]);
    }
 }