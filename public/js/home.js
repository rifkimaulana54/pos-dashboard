var userAjax,itemAjax, wysiwyg,startDate,endDate, 
    timeAjax, timeChart, timeAvg,
    pickupAjax, pickupChart,
    frontlinerAjax, frontlinerChart;

$(document).ready(function() 
{
	$.ajaxSetup({
	  headers: {
	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	  }
	});

    var hash = window.location.hash;
    if($('ul.nav').length)
    {
        hash && $('ul.nav a[href="' + hash + '"]').tab('show');
        $('.tab').val(hash);

        $(document).on('click','.nav-tabs a', function (e)
        {
            $(this).tab('show');
            var scrollmem = $('body').scrollTop() || $('html').scrollTop();
            window.location.hash = this.hash;
            $('.tab').val(this.hash);
            $('html,body').scrollTop(scrollmem);
        });
    }   

    if($('#transaction_time_range').length && !$('#transaction_time_range').data('datepicker'))
    {
        var start_date = urlParams.get('start_date');
        var end_date = urlParams.get('end_date');

        startDate = (typeof start_date !== 'undefined' && start_date) ? moment(start_date) : moment().subtract(6, 'days');
        endDate = (typeof end_date !== 'undefined' && end_date) ? moment(end_date) : moment();

        // console.log(start_date);
        // console.log(end_date);
        if(!$('input[name="daterange"]').data('datepicker'))
        {
         
            $('#transaction_time_range').daterangepicker(
            {
                "startDate": startDate,
                "endDate": endDate,
                // autoUpdateInput: false,
                ranges: {
                     'Today': [moment(), moment()],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                    alwaysShowCalendars:true
                }, 
                function(start, end, label) {
                    start = start.utcOffset('+0700');
                    end = end.utcOffset('+0700');
                    startDate = start.format('YYYY-MM-DD');
                    endDate = end.format('YYYY-MM-DD');
                }
            );
        }

        $('#transaction_time_range').on('apply.daterangepicker', function(ev, picker) 
        {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
              // console.log($(this).val())

            // if($('#transactionTime').length)
            //     getTransactionTimeData();

            // if($('#listAction').length)
            //     getActionList();

            // if($('#successPickup').length)
            //     getPickupData();

            // if($('#frontlinerPerformance').length)
            // {
            //     loadFrontliner();
            //     getFrontlinerData();   
            // }
        });

        $('#transaction_time_range').on('cancel.daterangepicker', function(ev, picker) {
              $(this).val('');
        });
    }

    // if($('#transactionTime').length)
    //     loadTransactionTimeChart($('#transactionTime'));   

    // if($('#listAction').length)
    //     getActionList();

    // if($('#successPickup').length)
    //     loadSuccessPickupChart($('#successPickup'));   

    // if($('#frontlinerPerformance').length)
    // {
    //     loadFrontliner();
    //     loadFrontlinerChart($('#frontlinerPerformance'));   
    // }

    // if($('#filterFrontliner').length)
    // {
    //     $(document).on('change','#filterFrontliner',function(e)
    //     {            
    //         getFrontlinerData();
    //     });
    // }
    $('[data-toggle="tooltip"]').tooltip();
});