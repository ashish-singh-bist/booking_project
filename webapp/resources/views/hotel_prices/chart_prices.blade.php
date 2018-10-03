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
                                <a id='clear_filter' class="btn btn-danger">Reset Filters</a>
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
                                                    <li><label><input class="flat-icheck" type="checkbox" id="default_day" name="days[]" value="1"/ checked="checked"> 1 day</li>
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
                                                    <li><label><input class="flat-icheck" type="checkbox" id="default_person" name="max_persons[]" value="1"/ checked="checked"> 1 person</li>
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
                                    <div class="box box-primary box-solid filter-box">
                                        <div class="box-header">
                                            <h4 class="box-title">Hotel Name</h4>
                                        </div>
                                        <div class="box-body overflow-0">
                                            <select class="form-control filter_class" id="hotel_type" multiple="multiple">
                                                @foreach($hotel_type as $hoteltype)
                                                    <option value="{{$hoteltype[0]}}">{{$hoteltype[0]}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="box box-primary box-solid filter-box">
                                            <div class="box-header">
                                                <h4 class="box-title">Room Type</h4>
                                            </div>
                                            <div class="box-body">
                                                <select class="form-control filter_class" id="room_types" multiple="multiple">
                                                    @foreach($room_type_list as $room_type)
                                                        <option value="{{$room_type[0]}}">{{$room_type[0]}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="box box-primary box-solid filter-box">
                                            <div class="box-header">
                                                <h4 class="box-title">Cancellation Type</h4>
                                            </div>
                                            <div class="box-body">
                                                <select class="form-control filter_class" id="cancellation_type" multiple="multiple">
                                                    @foreach($cancel_type_list as $cancel_type)
                                                        <option value="{{$cancel_type[0]}}">{{$cancel_type[0]}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <div class="box box-primary box-solid filter-box">
                                            <div class="box-header">
                                                <h4 class="box-title">Meal Plan</h4>
                                            </div>
                                            <div class="box-body">
                                                <select class="form-control filter_class" id="meal_type" multiple="multiple">
                                                    @foreach($meal_type_list as $meal_type)
                                                        <option value="{{$meal_type[0]}}">{{$meal_type[0]}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    <a id='filter_apply' class="btn btn-success">Apply Filters</a>
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var price_chart;
            function searchFilterForChart(){
                var data = {};
                data.created_at = $('#created_at').val();
                data.room_types = $("#room_types").val();
                data.checkin_date_to = $('#checkin_date_to').val();
                data.checkin_date_from = $('#checkin_date_from').val();
                data.meal_plan = $('#meal_type').val();
                data.cancellation_type = $('#cancellation_type').val();
                var days = $('input[name="days[]"]:checked')
                .map(function() {
                    return $(this).val();
                }).get();
                var max_persons = $('input[name="max_persons[]"]:checked')
                .map(function() {
                    return $(this).val();
                }).get();
                data.days = days;
                data.max_persons = max_persons;
                data.cities = $("#cities").val();
                data.hotel_type = $("#hotel_types").val();

                if(data.created_at != '' && data.checkin_date_to != '' && data.checkin_date_from != '' && days.length>0 && max_persons.length>0){
                    $.ajax({
                        type: "POST",
                        url: "{!! route('chart_prices.getChartData') !!}",
                        dataType: "json",
                        async:false,
                        data: JSON.stringify(data),
                        contentType: "application/json; charset=utf-8",
                        success: function (data) {
                            var chart_data = data.chart_data;
                            bindChartData(chart_data);
                        },
                        error: function (textStatus, errorThrown) {
                            console.log(errorThrown);
                        }
                    });
                }else{
                    alert("Required:- Parse date, Check-in date, Booking days, Max Person");
                }
            }

            $('#filter_apply').on('click', function(e) {
                searchFilterForChart();
            });

            $('#clear_filter').on('click',function(){
                $("#checkin_date_to").val('');
                $("#checkin_date_from").val('');
                $("#room_types").val('').trigger('change');
                $("#created_at").val('');
                $(".select2-city").val('').trigger('change');
                $("#hotel_types").val('').trigger('change');
                $('.flat-icheck').iCheck('uncheck');
                $("#room_types").val('').trigger('change');
                $("#meal_type").val('').trigger('change');
                $("#cancellation_type").val('').trigger('change');
                $("#hotel_type").val('').trigger('change');
                var current_date = new Date;
                $('.created_at, .checkin_date_from').datepicker("setDate", current_date);
                $('.created_at, .checkin_date_to').datepicker();
                $('#default_day').iCheck('check');
                $('#default_person').iCheck('check');
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

            $('#room_types').select2({
                placeholder: 'Select room type',
                allowClear: true,
            });

            $('#cancellation_type').select2({
                placeholder: 'Select cancellation type',
                allowClear: true,
            });

            $('#meal_type').select2({
                placeholder: 'Select meal type',
                allowClear: true,
            });

            $('#hotel_type').select2({
                placeholder: 'Select hotel',
                allowClear: true,
            });

            function bindChartData(chart_data){
                if (price_chart) {
                    price_chart.destroy();
                }            
                date_array = chart_data['checkin_date'];
                var ctx  = document.getElementById("line-chart").getContext("2d");
                price_chart = new Chart(ctx, {
                  type: 'line',
                  connectNullData: true,
                  data: {
                    labels: date_array,
                    datasets: [
                   
                    ]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'Price chart'
                        },
                        responsive: true,
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
                for (var key in chart_data) {
                    var r = Math.floor((Math.random() * 255));
                    var g = Math.floor((Math.random() * 255));
                    var b = Math.floor((Math.random() * 255));
                    if(key != 'checkin_date'){
                        price_chart.data.datasets.push({
                            label: key,
                            backgroundColor: '#ff0000',
                            spanGaps: false,
                            borderColor: "rgba(" + r + "," + g + ","+ b +",.5)",
                            data: chart_data[key],
                            fill: false,
                        });
                        price_chart.update();
                    }
                    i++;
                }
            }
            
            var current_date = new Date;
            $('.created_at, .checkin_date_from').datepicker("setDate", current_date);
            $('.checkin_date_to').datepicker("setDate",new Date(current_date.getFullYear(), current_date.getMonth() + 1, current_date.getDate()));
        });
    </script> 
@endsection