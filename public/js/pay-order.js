var loadAjax, userAjax, arr = [];

$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('click','#pay-pas',function(e)
    {
        e.preventDefault();
        let pay_pas = $(this).data('pay');
        $('#pay').html('Rp. '+DecimalAsString(pay_pas));
        $('#tunai').val(pay_pas);
        $('#bill').html('Rp. 0');
        $('#change').html('Rp. 0');
        $('.enter-pay').val(pay_pas);
        $('.btn-prosess').removeAttr('style');
        $('.btn-prosess').addClass('btn-bayar');
    });

    $(document).on('keyup', '.enter-pay', function(e)
    {
        const val = $(this).val();
        const total = $('#total').val();
        $('#change').html('Rp. 0');
        $('.btn-prosess').css("opacity", "0.5");
        $('#tunai').val(val);
        $('#bill').html('Rp. '+total);

        $('#pay').html('Rp. '+DecimalAsString(val));
        if(parseInt(total) >= parseInt(val)){
            const rest_bill = parseInt(total) - parseInt(val)
            $('#bill').html('Rp. ' + DecimalAsString(rest_bill));
            $('#change').html('Rp. 0');
        }else if (parseInt(val) >= parseInt(total)){
            const rest_bill = parseInt(val) - parseInt(total)
            $('#bill').html('Rp. 0');
            $('#change').html('Rp. ' + DecimalAsString(rest_bill));
            $('.btn-prosess').css("opacity", "1");
            $('.btn-prosess').addClass('btn-bayar');
        }
    })

    $(document).on('click', '.btn-print-bayar', function (e) {
        const id = $(this).data('id');
        window.location.href = base_url + '/kasir/print/'+id;
    })

    $(document).on('click', '.btn-bayar', function (e) {
        e.preventDefault()
        const id = $(this).data('id-order');
        const tunai = $('#tunai').val();

        if (tunai == 0) {
            swalConfirm('Mohon isi bayar terlebih dulu!', false, 'Ok', '', '', 'error');
        } else {
            swalWithBootstrapButtons.fire({
                title: 'Apakah kamu yakin ingin bayar?',
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes!',
                reverseButtons: true
            }).then((result) => 
            {
                if (result.isConfirmed) 
                {
                    $('.spinner').removeClass('d-none');
                    // const url = base_url + '/kasir/update_pay/'+id+'?pay='+tunai;
                    // window.open(url, '_blank');
                    window.location.href = base_url + '/kasir/update_pay/' + id + '?pay=' + tunai;
                }
            })

            return false;
        }
    })
})

