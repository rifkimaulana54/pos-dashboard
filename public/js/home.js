var userAjax,itemAjax, wysiwyg,startDate,endDate, 
    timeAjax, timeChart, timeTotal,
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

    if($('#order_time_range').length && !$('#order_time_range').data('datepicker'))
    {
        var start_date = urlParams.get('start_date');
        var end_date = urlParams.get('end_date');

        startDate = (typeof start_date !== 'undefined' && start_date) ? moment(start_date) : moment().subtract(6, 'days');
        endDate = (typeof end_date !== 'undefined' && end_date) ? moment(end_date) : moment();

        // console.log(start_date);
        // console.log(end_date);
        if(!$('input[name="daterange"]').data('datepicker'))
        {
         
            $('#order_time_range').daterangepicker(
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

        $('#order_time_range').on('apply.daterangepicker', function(ev, picker) 
        {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
              // console.log($(this).val())

            if($('#order_alls').length && $('#order_waiting_lists').length && $('#income').length)
                getOrderData();

            if ($('#orderTime').length)
                loadOrderTimeChart($('#orderTime')); 
        });

        $('#order_time_range').on('cancel.daterangepicker', function(ev, picker) {
              $(this).val('');
        });
    }

    if ($('#order_alls').length && $('#order_waiting_lists').length && $('#income').length)
        getOrderData();

    if($('#orderTime').length)
        loadOrderTimeChart($('#orderTime')); 

    $('[data-toggle="tooltip"]').tooltip();
});

function getOrderData()
{
    jQuery.ajax({
        type: "POST",
        url: base_url + '/home/getOrderReportSummary',
        data: { 
            order_time : jQuery('#order_time_range').val(),
        },
        beforeSend:function()
        {
            $('#order_alls').html('<i class="fa fa-fw fa-spinner fa-spin"></i>');
            $('#order_waiting_lists').html('<i class="fa fa-fw fa-spinner fa-spin"></i>');
            $('#income').html('<i class="fa fa-fw fa-spinner fa-spin"></i>');
        },
        success: function(data)
        {
            console.log(data);
            if(typeof data.orders !== 'undefined' && $('#order_alls').length)
                $('#order_alls').html(data.orders);
            if(typeof data.order_waiting_lists !== 'undefined' && $('#order_waiting_lists').length)
                $('#order_waiting_lists').html(data.order_waiting_lists);
            if(typeof data.income !== 'undefined' && $('#income').length)
                $('#income').html('Rp. ' + DecimalAsString(data.income));
        },
        error: function(data, ajaxOptions, thrownError)
        {
            console.log(data);
            console.log(ajaxOptions);
            console.log(thrownError);
            // if(!ajax_loading)
            //     $('.btn-submit, .nav-tabs a').prop('disabled',false);
            // $(cur).prop('disabled',false);
            // $(target).find('option.ajax-default').text('Gagal Memuat... Coba lagi');

            // processAjax = null;
        }
    });
} 

function loadOrderTimeChart(cur)
{
    before_load_data();

    let timeData =
    {
        datasets: [
            {
                label               : 'Total',
                // backgroundColor     : '#000000',
                borderColor         : '#A0A4B8',
                data                : []
                // pointRadius          : false,
                // pointColor          : '#3b8bba',
                // pointStrokeColor    : '#00a65a',
                // pointHighlightFill  : '#fff',
                // pointHighlightStroke: '#00a65a',
                // data_percent        : [],
            },
        ],
        labels: []
    }

    timeTotal = {
        type: 'line',
        borderColor: 'orange',
        borderDash: [6, 6],
        // borderDashOffset: 0,
        // borderWidth: 3,
        label: {
            enabled: true,
            content: (ctx) => 'Total: ' + total(ctx).toFixed(2),
            position: 'end'
        },
        scaleID: 'y',
        value: null
    };

    var chartExist = Chart.getChart("orderTime"); // <canvas> id
    if (chartExist != undefined)
        chartExist.destroy(); 
    
    //---------------------
    //- STACKED BAR CHART -
    //---------------------
    var lineChartTime = jQuery(cur).get(0).getContext('2d')

    timeChart = new Chart(lineChartTime, {
        type: 'line',
        data: timeData,
        options: {
            responsive: true,
            hover: {
                mode: 'nearest',
                intersect: true
            },
            elements: {
                line: {
                    fill: true,
                },
            },
            scales: {
              x: {
                title: {
                    display: true,
                    text: 'Date',
                    font: {
                        size: 15
                    }
                }
              },
              y: {
                title: {
                    display: true,
                    text: 'Total Order',
                    font: {
                        size: 15
                    }
                }
              }
            },
            xAxes: [{
                stacked: true,
                ticks: {
                    stepSize: 1
                },
                beginAtZero: true
            }],
            yAxes: [{
                stacked: true
            }],
            plugins: {                
                legend: {
                    display: false,
                },
                tooltip: {
                    displayColors: false,
                    mode: 'index',
                    intersect: false,
                    callbacks:
                    {
                        label: function(tooltipItem) 
                        {
                            var label = [];
                            // console.log(timeData.datasets)
                            // console.log(tooltipItem)
                            jQuery.each(timeData.datasets, function(i,set)
                            {
                                label.push(set.label + ': Rp. ' + DecimalAsString(set.data[tooltipItem.dataIndex]));
                            });
                            // console.log(data.datasets);
                            // console.log(tooltipItem);
                            // var label = data.datasets[tooltipItem.index].label;

                            // if (label)
                            //     label += ': ';

                            // // label += 'Rp. ' + DecimalAsString(tooltipItem.yLabel);
                            // label += tooltipItem.yLabel;
                            // console.log(label);

                            return label;
                        }
                    }
                },
                annotation: {
                    annotations: {
                        timeTotal
                    }
                }
            },
        }
    })
    // timeChart.update();

    getOrderTimeData();
}

function total(ctx) {
    const values = ctx.chart.data.datasets[0].data;
    return values.reduce((a, b) => a + b, 0) / values.length;
}

function getOrderTimeData(start,end)
{
    if(!timeAjax)
    {
        timeAjax = jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: base_url + '/home/getOrderReportSummary',
            data: { 
                order_time : jQuery('#order_time_range').val(),
            },
            beforeSend: function()
            {
                $('#loadingChart').prop('hidden',false);
                timeChart.data.labels = [];
                timeChart.options.scales.x.title.text = 'Date';
                for(s=0;s<timeChart.data.datasets.length;s++)
                {
                    timeChart.data.datasets[s].data = []
                }
                timeTotal.value = null;
                timeChart.update();
            // $(loadingGif).insertBefore('#renveeCalendar');
            // $('#renveeCalendar').prop('hidden',true);
            },
            success: function(data)
            {
                $('#loadingChart').prop('hidden',true);
                if(typeof data.data !== 'undefined' && data.data)
                {
                    jQuery.each(data.data, function(i,trx)
                    {
                        // console.log(trx)
                        // label
                        if(typeof trx.order_time !== 'undefined' && trx.order_time)
                        {
                            timeChart.data.labels.push(trx.order_time);
                            timeChart.options.scales.x.title.text = 'Hour';
                        }
                        else if(typeof trx.order_date !== 'undefined' && trx.order_date)
                            timeChart.data.labels.push(trx.order_date);

                        // // total queue
                        timeChart.data.datasets[0].data.push(trx.total_income_time);

                        // // total_subtransaction
                        // timeChart.data.datasets[1].data.push(trx.total_subtransaction);

                        // // total_subtransaction_item
                        // timeChart.data.datasets[2].data.push(trx.total_subtransaction_item);

                        // // longest time
                        // timeChart.data.datasets[3].data.push(trx.longest_time_min);

                        // // shortest time
                        // timeChart.data.datasets[4].data.push(trx.shortest_time_min);

                        // // total time
                        // timeChart.data.datasets[5].data.push(trx.total_time_min);
                    });

                    // console.log(timeChart.data.datasets);
                    if(typeof data.total_times !== 'undefined' && data.total_times)
                        timeTotal.value = data.total_times

                    // render
                    timeChart.update();
                }

                timeAjax = null;
            },
            error: function(data, ajaxOptions, thrownError)
            {
                console.log(data);
                console.log(ajaxOptions);
                console.log(thrownError);

                errorAjax(data, ajaxOptions, thrownError);
            }
        });
    }
}