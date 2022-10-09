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

    if($('.product-wrapper').length)
    {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: base_url + '/kasir/getProductList/',    
            dataType: "json",
            beforeSend: function()
            {
                $('.spinner').removeClass('d-none');
            },
            success: function(data)
            {
                // console.log(data);
                if(data.data.length != 0)
                {
                    var output = '';
                    $.each(data.data, function(i, product)
                    {
                        output += buildItem(product);
                    })

                    $('.product-wrapper').html(output);
                }
            }
        });
    }

    $(document).on('change', '#category_id', function()
    {
        $('#search').val('');
        $('.icon').html('<i class="fas fa-fw fa-search"></i>')

        var $value = $(this).val();

        $.ajax({
            type: "POST",
            dataType: "json",
            url: base_url + '/kasir/getProductList/',    
            data:{'category_id':$value},
            // processData: false,
            // contentType: false,
            dataType: "json",
            beforeSend: function()
            {
                $('.spinner').removeClass('d-none');
            },
            success: function(data)
            {
                // console.log(data);
                if(data.data.length != 0)
                {
                    var output = '';
                    $.each(data.data, function(i, product)
                    {
                        output += buildItem(product);
                    })

                    $('.product-wrapper').html(output);
                }
            }
        });

        // $(document).on('change', '.dd', function(e) 
        // {
        //     data = $('.dd').nestable('serialize');
        //     // document.getElementById("json_nestable").innerHTML = JSON.stringify(data);
        //     var dataJson = JSON.stringify(data); 
        //     $("#company_json").val(dataJson);
        //     console.log(data)

        // });
    });

    $('#search').on('keyup',function()
    {
        $value = $(this).val();
        
        if($value)
            $('.icon').html('<a href="#" id="icon-remove" class="text-dark"><i class="fas fa-fw fa-times"></i></a>')
        
        $(document).on('click','#icon-remove',function(e)
        {
            $('#search').val('').focus();
            $('.icon').html('<i class="fas fa-fw fa-search"></i>')
        });

        $.ajax({
            type: "POST",
            dataType: "json",
            url: base_url + '/kasir/getProductList/',    
            data:{'search': $value},
            // processData: false,
            // contentType: false,
            dataType: "json",
            beforeSend: function()
            {
                $('.spinner').removeClass('d-none');
            },
            success: function(data)
            {
                if(data.data.length != 0)
                {
                    var output = '';
                    $.each(data.data, function(i, product)
                    {
                        output += buildItem(product);
                    })

                    $('.product-wrapper').html(output);
                } 
                else
                {
                    $('.product-wrapper').html('<p class="m-auto">Produk <b>#'+$value+'</b> tidak di temukan</p>');
                }
            }
        });
    });

    $(document).on('click','.btn-update-qty',function(e)
    {
        e.preventDefault();

        var target  = $(this).closest('tr');
        var operator = $(this).data('operator');
        var claim_qty = $(target).find('#claim_qty').val();
        var default_price = $(target).find('.default_price').val();

        switch(operator)
        {
            case '+':
                claim_qty++;
                break
            case '-':
                claim_qty--;
                if(claim_qty<1){
                    claim_qty = 1;
                    confirm('Yakin ingin di hapus?');
                }
                break;
        }
        if(claim_qty > 1)
            $('.btn-update-qty').find('.button-minus').prop('disabled',false);
        else
            $('.btn-update-qty').find('.button-minus').prop('disabled',true);
        $(target).find('#claim_qty').val(claim_qty);
        $(target).find('#price').html('Rp. ' + DecimalAsString(claim_qty*default_price));
        $(target).find('.sub_price').val(claim_qty*default_price);

        // console.log($(this).closest('.cart-item').find('.claim_qty').val());
    });
});

function buildItem(item) {

    var html = '<div class="col-md-2.5 ml-3">';
        html += '<a href="#" class="product-show" title="'+item.product_display_name+'">';
            html += '<div class="card card-kasir" style="width: 133px">';

                if (item.metas) 
                {
                    $.each(item.metas, function (index, meta) {
                        html += '<img class="card-img-top" src="'+JSON.parse(meta.meta_value).media_path+'" height="100" alt="'+item.product_display_name+'">';
                    });
                }

                html += '<p class="text-center mb-0 text-light text-price"><b>Rp. '+DecimalAsString(item.product_price)+'</b></p>';
                html += '<p class="card-text text-center text-title"><b>'+item.product_display_name.substr(0, 28)+'</b></p>';
            html += "</div>";
        html += "</a>";
    html += "</div>";

    return html;
}