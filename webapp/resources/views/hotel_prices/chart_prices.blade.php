@extends('adminlte::page')
@section('title')
    Chart Prices
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css">
{{--     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"> --}}
@endsection

@section('content')
    <!-- content wrapper. contains page content -->
    <div class="content-panel">
        <!-- content header (page header) -->
        <section class="content-header">
            <h1>Hotel<small>Prices</small></h1>
        </section>
        <!-- end of content header (page header) -->
        <!-- main content-->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-solid box-primary">                   
                        <div class="box-header with-border">
                            <h3 class="box-title">Filters</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                {{-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button> --}}
                                <a id='clear_filter' class="btn btn-danger">Clear Filters</a>
                            </div>                            
                        </div>
                        <div class="box-body hotel-price">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="col-sm-12">
                                        <div class="box box-primary box-solid filter-box">
                                            <div class="box-header">
                                                <h4 class="box-title">City</h4>
                                            </div>
                                            <div class="box-body">
                                                <select class="form-control filter_class select2-city" id="cities" multiple="multiple"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="box box-primary box-solid filter-box">
                                            <div class="box-header">
                                                <h4 class="box-title">Parse Date</h4>
                                            </div>
                                            <div class="box-body overflow-0">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        {{-- <label for="created_at">From</label> --}}
                                                        <p class="input-group created_at" >
                                                            <input type="text" class="form-control filter_class" id="created_at" readonly="readonly">
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="col-sm-6">
                                        <div class="box box-primary box-solid filter-box filter-box-fix-height">
                                            <div class="box-header">
                                                <h4 class="box-title">Booking Days</h4>
                                            </div>
                                            <div class="box-body">
                                                <ul>
                                                    <li><label><input class="flat-icheck" type="checkbox" name="days[]" value="1"/ checked="checked"> 1 day</li>
                                                    <li><label><input class="flat-icheck" type="checkbox" name="days[]" value="2"/> 2 day</li>
                                                    <li><label><input class="flat-icheck" type="checkbox" name="days[]" value="3"/> 3 day</li>
                                                    <li><label><input class="flat-icheck" type="checkbox" name="days[]" value="5"/> 5 day</li>
                                                    <li><label><input class="flat-icheck" type="checkbox" name="days[]" value="7"/> 7 day</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="box box-primary box-solid filter-box filter-box-fix-height">
                                            <div class="box-header">
                                                <h4 class="box-title">Max Person</h4>
                                            </div>
                                            <div class="box-body">
                                                <ul>
                                                    <li><label><input class="flat-icheck" type="checkbox" name="max_persons[]" value="1"/ checked="checked"> 1 person</li>
                                                    <li><label><input class="flat-icheck" type="checkbox" name="max_persons[]" value="2"/> 2 person</li>
                                                    <li><label><input class="flat-icheck" type="checkbox" name="max_persons[]" value="3"/> 3 person</li>
                                                    <li><label><input class="flat-icheck" type="checkbox" name="max_persons[]" value="4"/> 4 person</li>
                                                    <li><label><input class="flat-icheck" type="checkbox" name="max_persons[]" value="5"/> 5 person</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="box box-primary box-solid filter-box">
                                        <div class="box-header">
                                            <h4 class="box-title">CheckIn Date</h4>
                                        </div>
                                        <div class="box-body overflow-0">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <label for="checkin_date_from">From</label>
                                                    <p class="input-group checkin_date_from">
                                                        <input type="text" class="form-control filter_class" id="checkin_date_from" readonly="readonly">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                    </p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="checkin_date_to">To</label>
                                                    <p class="input-group checkin_date_to">
                                                        <input type="text" class="form-control filter_class" id="checkin_date_to" readonly="readonly">
                                                        <span class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                   
                                    <div class="col-sm-12 text-right">
                                        <a id='filter_apply' class="btn btn-success">Apply Filters</a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="card-title text-center">Statistics</h3>
                <canvas id="line-chart" width="800" height="450"></canvas>
            </div>
            <div class="row" style="display: none;">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered" id="hotel_prices">
                                <thead><tr>
                                    @foreach (config('app.hotel_prices_header_key') as $value) <th>{{$value}}</th> @endforeach
                                </tr></thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end of main content-->
    </div>
    <!-- end of content wrapper. contains page content -->
@endsection
@section('js')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
    {{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> --}}
    <script type="text/javascript">
        $(function() {
            var price_chart;
            $.fn.dataTable.ext.errMode = 'none';
            var oTable = $('#hotel_prices').DataTable({
                "aLengthMenu": [5, 10, 25, 50, 100, 500, 1000],
                "iDisplayLength": 100,
                "sPaginationType" : "full_numbers",
                processing: true,
                serverSide: true,
                searching: false,
                deferLoading: false,
                select: {
                    style: 'multi'
                },
                dom: "<'row'<'col-sm-2'l><'col-sm-4'B><'col-sm-6'<'#statistics.text-right'>>>",
                buttons: [
                    'csvHtml5'
                ],
                ajax: {
                    @if(isset($id))
                        url: "{!! route('chart_prices.getChartData') !!}?id={{$id}}",
                    @else
                        url: "{!! route('chart_prices.getChartData') !!}",
                    @endif
                    data: function (d) {
                       
                        d.created_at = $('#created_at').val();
                       
                        d.checkin_date_to = $('#checkin_date_to').val();
                        d.checkin_date_from = $('#checkin_date_from').val();
                       
                        var days = $('input[name="days[]"]:checked')
                        .map(function() {
                            return $(this).val();
                        }).get();
                        var max_persons = $('input[name="max_persons[]"]:checked')
                        .map(function() {
                            return $(this).val();
                        }).get();
                        d.days = days;
                        d.max_persons = max_persons;
                        d.cities = $("#cities").val();
                        d.hotel_type = $("#hotel_types").val();
                    },
                   dataFilter: function(response) {
                        var chart_data_array = JSON.parse(response)['chart_data_array'];
                        bindChartData(chart_data_array);
                    },
                },
                columns: [
                    @foreach (config('app.hotel_prices_header_key') as $key => $value) { data: '{{$key}}', name: '{{$key}}' }, 
                    @endforeach
                ]
            });

            $('#filter_apply').on('click', function(e) {
                oTable.draw();
            });

            $('#clear_filter').on('click',function(){
                $("#checkin_date_to").val('');
                $("#checkin_date_from").val('');
                $("#room_types").val('').trigger('change');
                $("#created_at").val('');
                $(".select2-city").val('').trigger('change');
                $("#hotel_types").val('').trigger('change');
                $('.flat-icheck').iCheck('uncheck');
                resetSlider();
                oTable.draw();
            });

            $('#hotel_types').select2({
                placeholder: 'select hotel',
                allowClear: true,
            });

            $('#cities').select2({
                placeholder: 'Select a city',
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: '{{route("get_filter_list")}}',
                    dataType: 'json',
                    data: function (params) {
                      var query = {
                        search: params.term,
                        type: 'City'
                      }
                      return query;
                    },
                    processResults: function (data) {
                        return {
                          results: data
                        };
                    },
                    cache: true
                }
            });

            function bindChartData(chart_data_array){
                if (price_chart) {
                    price_chart.destroy();
                }            
                date_array = chart_data_array['checkin_date'];
                var ctx  = document.getElementById("line-chart").getContext("2d");
                price_chart = new Chart(ctx, {
                  type: 'line',
                  connectNullData: true,
                  data: {
                    labels: date_array,
                    datasets: [
                    //{ 
                    //         data: price_array,
                    //         label: "Africa",
                    //         borderColor: "#3e95cd",
                    //         fill: false
                    //     },
                    //     { 
                    //         data: price_array,
                    //         label: "India",
                    //         borderColor: "#000000",
                    //         fill: false
                    //     }
                    ]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'Price chart'
                        },
                        responsive: true,
                        scales: {
                          yAxes: [{

                              stacked: true,
                               ticks: {
                                  min: 0,
                                  stepSize: 100,
                              }

                          }]
                        },
                        legend: {
                            display: false
                        },
                        tooltips: {
                            callbacks: {
                               label: function(tooltipItem, data) {
                                   var data_array = data.datasets[tooltipItem.datasetIndex].label.split("|");
                                   data_array[0] = "Room Type:- " + data_array[0];
                                   data_array[1] = "Number of booking days:- " + data_array[1];
                                   data_array[2] = "Sleeps:- " + data_array[2];
                                   data_array[3] = "Max Person:- " + data_array[3];
                                   data_array[4] = "Cancellation Type:- " + data_array[4];
                                   data_array[5] = "Meal Plan Included:- " + data_array[5];
                                   return data_array;
                               }
                            }
                        }
                    }
                });

                
                var i =0;
                for (var key in chart_data_array) {
                    var r = Math.floor((Math.random() * 255));
                    var g = Math.floor((Math.random() * 255));
                    var b = Math.floor((Math.random() * 255));
                    if(key != 'checkin_date'){
                        price_chart.data.datasets.push({
                            label: key,
                            backgroundColor: '#ff0000',
                            borderColor: "rgba(" + r + "," + g + ","+ b +",.5)",
                            data: chart_data_array[key],
                            fill: false,
                        });
                        price_chart.update();
                    }
                    i++;
                }
            }
            
            //$('#created_at').datepicker({setDate: new Date()});
            var current_date = new Date;
            $('.created_at, .checkin_date_from').datepicker("setDate", current_date);
            $('.created_at, .checkin_date_to').datepicker();

        });
    </script> 
@endsection