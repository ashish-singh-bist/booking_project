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
                        </div>
                        <div class="box-body">
                            <div class="form-group col-sm-2">
                                <label>CheckIn Date</label>
                                <div class="input-group date" data-provide="datepicker">
                                    <input type="text" class="form-control" id="checkin_date" readonly="readonly">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-sm-2">
                                <label>Days</label>
                                <select class="form-control" id="days">
                                    <option value="">Any</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="5">5</option>
                                    <option value="7">7</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-3">
                                <label>Room Type</label>
                                <select class="form-control" id="room_type">
                                    <option value="">Any Type</option>
                                    @foreach($room_type_list as $room_type)
                                        <option value="{{$room_type[0]}}">{{$room_type[0]}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-sm-2">
                                <label>Max Person</label>
                                <select class="form-control" id="max_persons">
                                    <option value="">Any</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>                            

                            <div class="form-group col-sm-2">
                                <label>Parse Date</label>
                                <div class="input-group date" data-provide="datepicker">
                                    <input type="text" class="form-control" id="created_at" readonly="readonly">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-sm-3">
                                <label>Price Range</label>
                                <div id="slider-range"></div>
                                <input class="slider-range-data" type="text" id="amount" readonly>
                                <input class="slider-range-data" type="hidden" id="min_price" readonly>
                                <input class="slider-range-data" type="hidden" id="max_price" readonly>
                            </div>

                            <div class="form-group col-sm-2">
                                <div class="clear_btn">
                                    <a id='clear_filter' class="btn btn-danger">Clear Filters</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
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
                ajax: {
                    @if(isset($id))
                        url: "{!! route('hotel_prices.index.getData') !!}?id={{$id}}",
                    @else
                        url: "{!! route('hotel_prices.index.getData') !!}",
                    @endif
                    data: function (d) {
                        d.room_type = $('#room_type').val();
                        d.days = $('#days').val();
                        d.created_at = $('#created_at').val();
                        d.min_price = $('#min_price').val();
                        d.max_price = $('#max_price').val();
                        d.max_persons = $('#max_persons').val();
                        d.checkin_date = $('#checkin_date').val();
                    }
                },                
                columns: [
                    @foreach (config('app.hotel_prices_header_key') as $key => $value) { data: '{{$key}}', name: '{{$key}}' }, 
                    @endforeach
                ]
            });

            $('#room_type').change( function(e) {
                oTable.draw();
            });
            $('#days').change( function(e) {
                oTable.draw();
            });
            $('#created_at').change( function(e) {
                oTable.draw();
            });
            $('#max_persons').change( function(e) {
                oTable.draw();
            });
            $('#checkin_date').change( function(e) {
                oTable.draw();
            });                    
            function onChangePrice() {
                oTable.draw();
            }
            $( "#slider-range" ).slider({
                range: true,
                min: 0,
                max: 5000,
                values: [ 0, 5000 ],
                change: function( event, ui ) { onChangePrice(); },
                slide: function( event, ui ) {
                    $( "#min_price" ).val( ui.values[ 0 ] );
                    $( "#max_price" ).val( ui.values[ 1 ] );
                    $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
                }
            });
            setTimeout(function(){ $( "#amount" ).val("$0" + " - $5000"); }, 1000);

            $('#clear_filter').on('click',function(){
                $("#checkin_date").val('');
                $("#days")[0].selectedIndex = 0;
                $("#room_type")[0].selectedIndex = 0;
                $("#max_persons")[0].selectedIndex = 0;
                $("#created_at").val('');
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
        });
    </script> 
@endsection