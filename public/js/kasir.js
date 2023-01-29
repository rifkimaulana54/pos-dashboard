var loadAjax, userAjax, timer, arr = [];

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if($('#form-order').length)
    {
        $('#form-order').on('submit', function()
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

            if (!localParams.send && $('#form-order')[0].checkValidity()) 
            {
                e.preventDefault();
                swalConfirmSubmit(cur, e.type, 'Apakah anda yakin?', true);
            }
            // return false;
        })
    }

    // if($('.product-wrapper').length)
    // {
    //     //set timeout overlay
    //     clearTimeout(timer)
    //     timer = setTimeout(function() {
    //         $.ajax({
    //             type: "POST",
    //             dataType: "json",
    //             url: base_url + '/kasir/getProductList/',    
    //             dataType: "json",
    //             // beforeSend: function()
    //             // {
    //             //     // $('.spinner').removeClass('d-none');
    //             // },
    //             success: function(data)
    //             {
    //                 // console.log(data);
    //                 if(data.data.length != 0)
    //                 {
    //                     var output = '';
    //                     $.each(data.data, function(i, product)
    //                     {
    //                         output += buildItem(product);
    //                     })

    //                     $('.product-wrapper').html(output);
    //                 }
    //             }
    //         });
    //     },200)
    // }

    $(document).on('change', '#category_id', function()
    {
        $('#search').val('');
        $('.icon').html('<i class="fas fa-fw fa-search"></i>')

        var $value = $(this).val();

        // clearTimeout(timer)
        // timer = setTimeout(function() {
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
                    $('.spinner').addClass('d-none');
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
        // }, 200)

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
                $('.spinner').addClass('d-none');
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
        var grandtotal = $('#grandtotal').val();

        switch(operator)
        {
            case '+':
                claim_qty++;
                grandtotal_qty = parseInt(grandtotal)+parseInt(default_price)
                break
            case '-':
                claim_qty--;
                grandtotal_qty = parseInt(grandtotal)-parseInt(default_price)
                if(claim_qty<1){
                    claim_qty = 1;
                    if(confirm('Yakin ingin di hapus?'))
                    {
                        $(target).html('')
                        if(!$('.clone_tr').find('td').length)
                        {
                            $('#customer-name').val('');
                            $("#order_id").val('').select2();
                            $('.not-product').removeClass('d-none')
                            $('#customer-name').prop('disabled', true);
                            $('.form-btn').css("opacity", "0.5");
                            $('.add-btn-simpan').removeClass('btn-simpan');
                            $('.add-btn-hapus').removeClass('btn-hapus');
                            $('.add-btn-bayar').removeClass('btn-bayar');
                            $('.grandtotal-bayar').html('<h4><b>Rp. 0</b></h4>')
                        }
                    }
                    else
                        return false;
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

        // console.log(grandtotal_qty);
        $('#grandtotal').val(grandtotal_qty);
        $('.grandtotal-bayar').html('<h4><b>Rp. '+DecimalAsString(grandtotal_qty)+'</b></h4>');
        $('#total_add_qty').val(grandtotal_qty);
    });

    if(!$('.clone_tr').find('td').length){
        $('#customer-name').prop('disabled', true);
        $('.form-btn').css("opacity", "0.5");
        $('.add-btn-simpan').removeClass('btn-simpan');
        $('.add-btn-hapus').removeClass('btn-hapus');
        $('.add-btn-bayar').removeClass('btn-bayar');
    }
    
    $(document).on('click','.btn-product-card',function(e)
    {
        id = $(this).data('id');
        name_product = $(this).data('name');
        price = $(this).data('price');
        table = $('#list_order');
        clone = $(table).find('.tr_clone_items');

        // console.log(clone);
        if($(table).length && $(clone).length)
        {
            e.preventDefault();
            $tr_id = ($('tr[id^="add_tr_"]:visible').length)+1;
            // $screen_number = parseInt($(this).parent().parent().find('.screen_number').text())-1;
            var $cloned_tr = $(clone).clone(true);

            $cloned_tr.attr({
                id: 'add_tr_' +  $tr_id
            }).removeAttr('style').removeAttr('class');

            if(!$('.tr-'+id).find('td').length)
            {
                $('.not-product').addClass('d-none')
                $cloned_tr.insertAfter(clone);
                $cloned_tr.find('#product_name').text(name_product);
                $cloned_tr.find('#price').text('Rp. '+DecimalAsString(price));
                $cloned_tr.find('.sub_price').val(price);
                $cloned_tr.find('.default_price').val(price);
                $cloned_tr.find('.product_id').val(id);
                $cloned_tr.addClass('clone_tr tr-'+id);
                $cloned_tr.find('[name*="[##n##]"').each(function(){
                    // Update the 'rules[0]' part of the name attribute to contain the latest count
                    $(this).attr('name',$(this).attr('name').replace('##n##',$tr_id-1));
                });
                if($('#order_id').val() == '')
                    $('#customer-name').prop('disabled', false);
                $('.form-btn').css("opacity", "1");
                $('.add-btn-simpan').addClass('btn-simpan');
                $('.add-btn-hapus').addClass('btn-hapus');
                $('.add-btn-bayar').addClass('btn-bayar');
                
                //result grandtotal
                if($('#total_add_qty').val() != 0)
                {
                    result = parseInt($('#total_add_qty').val())+price;
                    
                    $('.grandtotal-bayar').html('<h4><b>Rp. '+DecimalAsString(result)+'</b></h4>');
                    $('#total_add_qty').val(result);
                    $('#grandtotal').val(result);
                }
                else
                {
                    if($('#order_id').val() == '')
                    {
                        result = parseInt($('#total_add_qty').val())+price;
                        $('#total_add_qty').val(result);
                        $('#grandtotal').val(result);
                    }
                    else
                    {
                        grandtotal = $('#grandtotal').val();

                        arr.push(price);
                        total = arr.reduce(function (a, b) { return a + b })
                        result = parseInt(grandtotal) + total
                    }

                    $('.grandtotal-bayar').html('<h4><b>Rp. '+DecimalAsString(result)+'</b></h4>')
                    $('#grandtotal').val(result);
                }
            }
            else
                $('.tr-'+id).find('.button-plus').click();
        }
    });

    
    if($('#append_order').length)
    {
        if ($('#order_id').val() != '') {
            var id = $('#order_id').val();
            renderOrder(id);
        }
    }

    $(document).on('change', '#order_id', function()
    {
        var id = $(this).val();
        renderOrder(id);
    });

    $(document).on('click', '.btn-simpan', function (e) {
        $('.btnSubmit').click();

    });

    $(document).on('click', '.btn-bayar', function (e) {
        $('.btnSubmit').click();
        $('#form-order').append('<input type="hidden" value="1" name="btn_bayar">');
    });

    $(document).on('click', '.btn-hapus', function (e) {
        if(confirm('Yakin ingin di hapus?'))
        {
            $('#form-order').attr('action', base_url +'/kasir/store');
            $('#update-order').html('');
            $('.not-product').removeClass('d-none');
            $('#append_order').html('');
            $("#order_id").val('').select2();
            $('#customer-name').val('');
            $('#total_add_qty').val(0);
            $('#grandtotal').val(0);
            $('.grandtotal-bayar').html('<h4><b>Rp. 0</b></h4>');
            if($('.clone_tr').find('td').length)
                $('#list_order').find('.clone_tr').remove();

            $('#customer-name').prop('disabled', true);
            $('.form-btn').css("opacity", "0.5");
            $('.add-btn-simpan').removeClass('btn-simpan');
            $('.add-btn-hapus').removeClass('btn-hapus');
            $('.add-btn-bayar').removeClass('btn-bayar');
        }
    });

    $(document).on('click', '.btn-list-order', function (e) {
        // console.log('ok')
        window.location.href=base_url+'/kasir/order-list'
    })
});

function buildItem(item) {

    var html = '<div class="col-md-2.5 ml-3">';
        html += '<div class="product-show btn-product-card" data-name="'+item.product_display_name+'" data-price="'+item.product_price+'" data-id="'+item.id+'" title="'+item.product_display_name+'">';
            html += '<div class="card card-kasir" id="card-product" style="width: 133px">';

                if (item.metas.length) 
                {
                    $.each(item.metas, function (index, meta) {
                        if (meta.meta_value !== null)
                            html += '<img class="card-img-top" src="'+JSON.parse(meta.meta_value).media_path+'" height="100" alt="'+item.product_display_name+'">';
                        else
                            html += '<img class="card-img-top" src="' + base_url + '/assets/img/box.jpg" height="100" alt="' + item.product_display_name + '">';
                    });
                } 
                else
                    html += '<img class="card-img-top" src="'+base_url+'/assets/img/box.jpg" height="100" alt="'+item.product_display_name+'">';

                html += '<p class="text-center mb-0 text-light text-price"><b>Rp. '+DecimalAsString(item.product_price)+'</b></p>';
                html += '<p class="card-text text-center text-title"><b>'+item.product_display_name.substr(0, 28)+'</b></p>';
                // html += '<input type="hidden" value="1" class="selected-'+item.id+'">';
            html += "</div>";
        html += "</div>";
    html += "</div>";

    return html;
}

function renderOrder(id)
{
    $('#customer-name').val('');
    $('#customer-name').prop('disabled', false); 

    $('#total_add_qty').val(0);
    $('.not-product').removeClass('d-none');
    $('#append_order').html('');
    $('.grandtotal-bayar').html('<h4><b>Rp. 0</b></h4>')
    $('#grandtotal').val(0);
    $('.form-btn').css("opacity", "0.5");
    $('.add-btn-simpan').removeClass('btn-simpan');
    $('.add-btn-hapus').removeClass('btn-hapus');
    $('.add-btn-bayar').removeClass('btn-bayar');
    if($('tr[id^="add_tr_"]:visible').length)
        $('tr[id^="add_tr_"]:visible').remove();
    $('#form-order').attr('action', base_url+'/kasir/store');
    $('#update-order').html('');

    if(id != '')
    {
        clearTimeout(timer)
        timer = setTimeout(function() {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: base_url + '/kasir/'+id,    
                // data:{'order_id':id},
                // processData: false,
                // contentType: false,
                dataType: "json",
                beforeSend: function()
                {
                    // $('.spinner').removeClass('d-none');
                },
                success: function(data)
                {
                    if(data.customer_name != undefined)
                    {
                        $('#customer-name').val(data.customer_name);
                        $('#customer-name').prop('disabled', true);
                    }
                    if(data.mapping.length != 0)
                    {
                        var column = 0;
                        var html = '';
                        $.each(data.mapping, function(i, item)
                        {
                            if($(".tr-"+item["product_id"]).length)
                            {
                                $(".tr-"+item["product_id"]).html('');
                                $(".tr-"+item["product_id"]).removeAttr('id class');
                            }
                            html += '<tr id="add_tr_'+column+'" class="clone_tr tr-'+item["product_id"]+'">';
                                html += '<td>';
                                    html += '<div class="input-group input-spinner">';
                                        html += '<div class="input-group-prepend">';
                                            html += '<button class="btn btn-light rounded-left btn-update-qty button-minus layer-0" type="button" id="button-minus" data-operator="-"> <i class="fa fa-minus"></i> </button>';
                                        html += '</div>';
                                        html += '<input type="text" class="form-control claim_qty px-0 " value="'+item["order_qty"]+'" min="1" name="items['+column+'][column][claim_qty]" id="claim_qty" max="45" readonly>';
                                        html += '<div class="input-group-append">';
                                            html += '<button class="btn btn-light rounded-right btn-update-qty button-plus layer-0" type="button" id="button-plus" data-operator="+" data-max="10"> <i class="fa fa-plus"></i> </button>';
                                        html += '</div>';
                                    html += '</div>';
                                html += '</td>';
                                html += '<td width="210">';
                                    html += '<p class="mt-3">'+item["product"].product_display_name+'</p>';
                                html += '</td>';
                                html += '<td>';
                                    html += '<p class="mt-3" id="price">Rp. '+DecimalAsString(item['order_subtotal'])+'</p>';
                                    html += '<input type="hidden" name="items[' + column +'][column][default_price]" value="'+item['default_price']+'" class="default_price">';
                                    html += '<input type="hidden" value="'+item['order_subtotal']+'" name="items['+column+'][column][subtotal]" class="sub_price">';
                                    html += '<input type="hidden" value="'+item['product_id']+'" name="items['+column+'][column][product_id]" class="product_id" value="'+item["id"]+'">';
                                html += '</td>';
                            html += "</tr>";
                            column++;
                        })
                        $('#append_order').html(html);
                        $('.not-product').addClass('d-none');
                        $('#form-order').removeAttr('action');
                        $('#form-order').attr('action', base_url+'/kasir/'+id);
                        $('#update-order').html('<input type="hidden" name="_method" value="PUT">');

                        total = data.total_order;
                        $('.grandtotal-bayar').html('<h4><b>Rp. '+DecimalAsString(total)+'</b></h4>')
                        $('#grandtotal').val(total);
                        $('#total_add_qty').val(total);

                        $('.form-btn').css("opacity", "1");
                        $('.add-btn-simpan').addClass('btn-simpan');
                        $('.add-btn-hapus').addClass('btn-hapus');
                        $('.add-btn-bayar').addClass('btn-bayar');
                    }
                }
            });
        }, 200)
    }
}