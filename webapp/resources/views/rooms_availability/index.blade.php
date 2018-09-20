@extends('adminlte::page')
@section('title')
    Room Availability
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css">
@endsection

@section('content')
    <!-- content wrapper. contains page content -->
    <div class="content-panel">
        <!-- content header (page header) -->
        <section class="content-header">
            <h1>Room<small>Availability</small></h1>
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

                            <div class="form-group col-sm-4">
                                <label>Room Type</label>
                                <select class="form-control" id="room_type">
                                    <option value="">Any Type</option>
                                    @foreach($room_type_list as $room_type)
                                        <option value="{{$room_type[0]}}">{{$room_type[0]}}</option>
                                    @endforeach
                                </select>
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

                            <div class="form-group col-sm-2">
                                <label>Available Only</label>
                                <select class="form-control" id="available_only">
                                    <option value="">Any</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
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
                            <table class="table table-bordered" id="rooms_availability">
                                <thead><tr>
                                    @foreach (config('app.rooms_availability_header_key') as $value) <th>{{$value}}</th> @endforeach
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
    <script type="text/javascript">
        $(function () {
            $('#datepicker').datepicker();
        });
    </script>
    <script type="text/javascript">
        $(function() {
            $.fn.dataTable.ext.errMode = 'none';
            var oTable = $('#rooms_availability').DataTable({
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
                        url: "{!! route('rooms_availability.index.getData') !!}?id={{$id}}",
                    @else
                        url: "{!! route('rooms_availability.index.getData') !!}",
                    @endif                    
                    data: function (d) {
                        d.room_type = $('#room_type').val();
                        d.days = $('#days').val();
                        d.available_only = $('#available_only').val();
                        d.created_at = $('#created_at').val();
                        d.checkin_date = $('#checkin_date').val();
                    }
                },
                columns: [
                    @foreach (config('app.rooms_availability_header_key') as $key => $value) { data: '{{$key}}', name: '{{$key}}' }, @endforeach
                ]
            });

            // $('#room_type').on('change', function(e) {
            $('#room_type').change( function(e) {
                oTable.draw();
            });
            $('#available_only').change( function(e) {
                oTable.draw();
            });
            $('#created_at').change( function(e) {
                oTable.draw();
            });
            $('#days').change( function(e) {
                oTable.draw();
            });
            $('#checkin_date').change( function(e) {
                oTable.draw();
            });

            $('#clear_filter').on('click',function(){
                $("#room_type")[0].selectedIndex = 0;
                $("#days")[0].selectedIndex = 0;
                $("#available_only")[0].selectedIndex = 0;
                $("#checkin_date").val('');
                $('#created_at').val('');
                oTable.draw();
            });
        });
    </script>
@endsection