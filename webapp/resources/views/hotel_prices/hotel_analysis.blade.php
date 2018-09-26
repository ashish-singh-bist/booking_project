@extends('adminlte::page')
@section('title')
    Hotel Prices
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
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
                        <div class="box-body">
                            <div class="row">
                                <div class="col-xs-12">  
                                    <div class="form-group col-sm-2 filter-outer-box">
                                        <label>Days</label>
                                        <li><input type="checkbox" name="days[]" value="1"/> 1 day</li>
                                        <li><input type="checkbox" name="days[]" value="2"/> 2 day</li>
                                        <li><input type="checkbox" name="days[]" value="3"/> 3 day</li>
                                        <li><input type="checkbox" name="days[]" value="5"/> 5 day</li>
                                        <li><input type="checkbox" name="days[]" value="7"/> 7 day</li>
                                    </div>

                                    <div class="form-group col-sm-2 filter-outer-box">
                                        <label>Max Person</label>
                                        <li><input type="checkbox" name="max_persons[]" value="1"/> 1 person</li>
                                        <li><input type="checkbox" name="max_persons[]" value="2"/> 2 person</li>
                                        <li><input type="checkbox" name="max_persons[]" value="3"/> 3 person</li>
                                        <li><input type="checkbox" name="max_persons[]" value="4"/> 4 person</li>
                                        <li><input type="checkbox" name="max_persons[]" value="5"/> 5 person</li>
                                    </div>   

                                    <div class="form-group col-sm-6">
                                        <label>CheckIn Date</label>
                                        <div class="form-inline">
                                            <div class="form-group">
                                                <label for="checkin_date_from">From</label>
                                                <p class="input-group date" data-provide="datepicker">
                                                    <input type="text" class="form-control filter_class" id="checkin_date_from" readonly="readonly">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-th"></span>
                                                    </span>
                                                </p>
                                                <label for="checkin_date_to">To</label>
                                                <p class="input-group date" data-provide="datepicker">
                                                    <input type="text" class="form-control filter_class" id="checkin_date_to" readonly="readonly">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-th"></span>
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-2">
                                        <label>Price Range</label>
                                        <div id="slider-range"></div>
                                        <input class="slider-range-data" type="text" id="amount" readonly>
                                        <input class="slider-range-data" type="hidden" id="min_price" readonly>
                                        <input class="slider-range-data" type="hidden" id="max_price" readonly>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label>Parse Date</label>
                                        <div class="form-inline">
                                            <div class="form-group">
                                                <label for="created_at_from">From</label>
                                                <p class="input-group date" data-provide="datepicker">
                                                    <input type="text" class="form-control filter_class" id="created_at_from" readonly="readonly">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-th"></span>
                                                    </span>
                                                </p>
                                                <label for="created_at_to">To</label>
                                                <p class="input-group date" data-provide="datepicker">
                                                    <input type="text" class="form-control filter_class" id="created_at_to" readonly="readonly">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-th"></span>
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-2">
                                        <div class="input-group">
                                            <label>Meal Plan &nbsp;&nbsp;</label>
                                            <select class="form-control filter_class" id="meal_plan">
                                                <option value="">Any</option>
                                                <option value="empty">Empty</option>
                                                <option value="not-empty">Not Empty</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-3">
                                    <div class="input-group">
                                        <label>Hotel Type &nbsp;</label>
                                        <select class="form-control filter_class" id="hotel_types" multiple="multiple">
                                            @foreach($hotel_type as $hotel)
                                                <option value="{{$hotel[0]}}">{{$hotel[0]}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-3">
                                    <div class="input-group">
                                        <label>Room Type &nbsp;</label>
                                        <select class="form-control filter_class" id="room_types" multiple="multiple">
                                            @foreach($room_type_list as $room_type)
                                                <option value="{{$room_type[0]}}">{{$room_type[0]}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-sm-3">
                                    <div class="input-group">
                                        <label>Cancellation Type &nbsp;</label>
                                        <select class="form-control filter_class" id="cancellation_type" multiple="multiple">
                                            @foreach($cancel_type_list as $cancel_type)
                                                <option value="{{$cancel_type[0]}}">{{$cancel_type[0]}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-sm-3">
                                    <div class="input-group">
                                        <label>Other Desc &nbsp;</label>
                                        <select class="form-control filter_class" id="others_desc" multiple="multiple">
                                            @foreach($other_desc_list as $other_desc)
                                                @if ($other_desc[0])
                                                    <option value="{{$other_desc[0]}}">{{$other_desc[0]}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group col-sm-2 filter-outer-box">
                                    <label>Star</label>
                                    <li><input type="checkbox" name="stars[]" value="1"/> 1 stars</li>
                                    <li><input type="checkbox" name="stars[]" value="2"/> 2 stars</li>
                                    <li><input type="checkbox" name="stars[]" value="3"/> 3 stars</li>
                                    <li><input type="checkbox" name="stars[]" value="4"/> 4 stars</li>
                                    <li><input type="checkbox" name="stars[]" value="5"/> 5 stars</li>
                                    <li><input type="checkbox" name="stars[]" value="6"/> 6 stars</li>
                                    <li><input type="checkbox" name="stars[]" value="7"/> 7 stars</li>
                                </div>
                                <div class="form-group col-sm-2 filter-outer-box">
                                    <label>Rating</label>
                                    <li><input type="checkbox" name="ratings[]" value="0"/> 0 - 1</li>
                                    <li><input type="checkbox" name="ratings[]" value="1"/> 1 - 2</li>
                                    <li><input type="checkbox" name="ratings[]" value="2"/> 2 - 3</li>
                                    <li><input type="checkbox" name="ratings[]" value="3"/> 3 - 4</li>
                                    <li><input type="checkbox" name="ratings[]" value="4"/> 4 - 5</li>
                                    <li><input type="checkbox" name="ratings[]" value="5"/> 5 - 6</li>
                                    <li><input type="checkbox" name="ratings[]" value="6"/> 6 - 7</li>
                                    <li><input type="checkbox" name="ratings[]" value="7"/> 7 - 8</li>
                                    <li><input type="checkbox" name="ratings[]" value="8"/> 8 - 9</li>
                                    <li><input type="checkbox" name="ratings[]" value="9"/> 9 - 10</li>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label>Country</label>
                                    <select class="form-control filter_class select2-country" id="countries" multiple="multiple">
                                    </select>
                                </div>

                                <div class="form-group col-sm-4">
                                    <label>City</label>
                                    <select class="form-control filter_class select2-city" id="cities" multiple="multiple">
                                    </select>
                                </div>

                                <div class="form-group col-sm-4">
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
            <div class="row">
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $('#datepicker').datepicker();
        });
    </script>
    <script type="text/javascript">
        $(function() {
            $.fn.dataTable.ext.errMode = 'none';
            var oTable = $('#hotel_prices').DataTable({
                "aLengthMenu": [5, 10, 25, 50, 100, 500, 1000],
                "iDisplayLength": 100,
                "sPaginationType" : "full_numbers",
                processing: true,
                serverSide: true,
                searching: false,
                select: {
                    style: 'multi'
                },
                dom: "<'row'<'col-sm-2'l><'col-sm-4'B><'col-sm-6'<'#statistics.text-right'>>>",
                buttons: [
                    'csvHtml5'
                ],
                ajax: {
                    @if(isset($id))
                        url: "{!! route('hotel_analysis.getChartData') !!}?id={{$id}}",
                    @else
                        url: "{!! route('hotel_analysis.getChartData') !!}",
                    @endif
                    data: function (d) {
                        d.room_types = $("#room_types").val();
                        d.created_at_to = $('#created_at_to').val();
                        d.created_at_from = $('#created_at_from').val();
                        d.min_price = $('#min_price').val();
                        d.max_price = $('#max_price').val();
                        d.checkin_date_to = $('#checkin_date_to').val();
                        d.checkin_date_from = $('#checkin_date_from').val();
                        d.meal_plan = $('#meal_plan').val();
                        d.cancellation_type = $('#cancellation_type').val();
                        d.others_desc = $('#others_desc').val();
                        var stars = $('input[name="stars[]"]:checked')
                        .map(function() {
                            return $(this).val();
                        }).get();
                        var ratings = $('input[name="ratings[]"]:checked')
                        .map(function() {
                            return $(this).val();
                        }).get();
                        d.stars = stars;
                        d.ratings = ratings;
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
                        d.countries = $("#countries").val();
                        d.hotel_type = $("#hotel_types").val();
                    },
                    dataFilter: function(response) {
                        var statistics = JSON.parse(response)['statistics'];
                        var date_array = JSON.parse(response)['date_array'];
                        var price_array = JSON.parse(response)['price_array'];
                        
                        console.log(statistics);
                        $("#statistics").html('<div><span class="p_badge"><b>Max Price : </b>&euro;'+statistics['max_price'].toFixed(2)+'</span>|<span class="p_badge"><b>Min Price : </b>&euro;'+statistics['min_price'].toFixed(2)+'</span>|<span class="p_badge"><b>Avg. Price : </b>&euro;'+statistics['avg_price'].toFixed(2)+'</span></div>');
                        return response;
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

            $( "#slider-range" ).slider({
                range: true,
                min: 0,
                max: 5000,
                values: [ 0, 5000 ],
                change: function( event, ui ) { },
                slide: function( event, ui ) {
                    $( "#min_price" ).val( ui.values[ 0 ] );
                    $( "#max_price" ).val( ui.values[ 1 ] );
                    $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
                }
            });
            setTimeout(function(){ $( "#amount" ).val("$0" + " - $5000"); }, 1000);

            $('#clear_filter').on('click',function(){
                $("#checkin_date_to").val('');
                $("#checkin_date_from").val('');
                $("#room_types").val('').trigger('change');
                $('input[name="max_persons[]"]:checked')
                .map(function() {
                    $(this).prop( "checked", false );
                });
                $('input[name="days[]"]:checked')
                .map(function() {
                    $(this).prop( "checked", false );
                });
                $("#created_at_to").val('');
                $("#created_at_from").val('');
                $("#meal_plan").val('').trigger('change');
                $("#cancellation_type").val('').trigger('change');
                $("#others_desc").val('').trigger('change');
                $(".select2-country").val('').trigger('change');
                $(".select2-city").val('').trigger('change');
                $("#hotel_types").val('').trigger('change');
                $('input[name="stars[]"]:checked')
                .map(function() {
                    $(this).prop( "checked", false );
                });
                $('input[name="ratings[]"]:checked')
                .map(function() {
                    $(this).prop( "checked", false );
                });
                resetSlider();
                oTable.draw();
            });

            function resetSlider() {
                var $slider = $("#slider-range");
                var min_value = 0;
                var max_value = 5000;
                $slider.slider("values", 0, min_value);
                $slider.slider("values", 1, max_value);
                $( "#min_price" ).val(0);
                $( "#max_price" ).val(5000);
                $( "#amount" ).val( "$" + min_value + " - $" + max_value );
            }

            $('#room_types').select2({
                placeholder: 'Select room type',
                allowClear: true,
            });

            $('#cancellation_type').select2({
                placeholder: 'Select cancellation type',
                allowClear: true,
            });

            $('#others_desc').select2({
                placeholder: 'Select other desc',
                allowClear: true,
            });

            $('#hotel_types').select2({
                placeholder: 'select hotel',
                allowClear: true,
            });

            $('#countries').select2({
                placeholder: 'Select a country',
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: '{{route("get_filter_list")}}',
                    dataType: 'json',
                    data: function (params) {
                      var query = {
                        search: params.term,
                        type: 'Country'
                      }
                      return query;
                    },
                    processResults: function (data) {
                        console.log(data)
                        return {
                          results: data
                        };
                    },
                    cache: true
                }
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
                        console.log(data)
                        return {
                          results: data
                        };
                    },
                    cache: true
                }
            });
        });

        var date_value = JSON.parse('{!! $date_array !!}');
        console.log(date_value);
        var price_value = JSON.parse('{!! $price_array !!}');
        new Chart(document.getElementById("line-chart"), {
            type: 'line',
            data: {
                labels: date_value,
                datasets: [{
                    data: price_value,
                    borderColor: "#3e95cd",
                    label: "price",
                }]
              },
              options: {
                title: {
                    display: true,
                    text: 'Hotel Price Rate Per Day'
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true,
                            callback: function(value, index, values) {
                            return '$'+value.toString();
                            }
                        },
                    }],
                }
            }
        });
    </script> 
@endsection