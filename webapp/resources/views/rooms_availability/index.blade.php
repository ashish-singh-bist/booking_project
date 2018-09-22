@extends('adminlte::page')
@section('title')
    Room Details
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css">
@endsection

@section('content')
    <!-- content wrapper. contains page content -->
    <div class="content-panel">
        <!-- content header (page header) -->
        <section class="content-header">
            <h1>Room<small>Details</small></h1>
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
                            <div class="row">
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

                                <div class="form-group col-sm-4">
                                    <label>Room Type</label>
                                    <select class="form-control filter_class" id="room_type">
                                        <option value="">Any Type</option>
                                        @foreach($room_type_list as $room_type)
                                            <option value="{{$room_type[0]}}">{{$room_type[0]}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
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
                                    <div class="clear_btn">
                                        <a id='clear_filter' class="btn btn-danger">Clear Filters</a>
                                    </div>
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
                            <table class="table table-bordered" id="room_details">
                                <thead><tr>
                                    @foreach (config('app.room_details_header_key') as $value) <th>{{$value}}</th> @endforeach
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
            var oTable = $('#room_details').DataTable({
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
                        url: "{!! route('room_details.index.getData') !!}?id={{$id}}",
                    @else
                        url: "{!! route('room_details.index.getData') !!}",
                    @endif                    
                    data: function (d) {
                        d.room_type = $('#room_type').val();
                        d.created_at_to = $('#created_at_to').val();
                        d.created_at_from = $('#created_at_from').val();
                        d.checkin_date_to = $('#checkin_date_to').val();
                        d.checkin_date_from = $('#checkin_date_from').val();
                    }
                },                
                columns: [
                    @foreach (config('app.room_details_header_key') as $key => $value) { data: '{{$key}}', name: '{{$key}}' }, @endforeach
                ]
            });

            $('.filter_class').on('change', function(e) {
                oTable.draw();
            });

            // $('#room_type').change( function(e) {
            //     oTable.draw();
            // });
            // $('#created_at').change( function(e) {
            //     oTable.draw();
            // });
            // $('#checkin_date').change( function(e) {
            //     oTable.draw();
            // });

            $('#clear_filter').on('click',function(){
                $("#room_type")[0].selectedIndex = 0;
                $("#checkin_date_to").val('');
                $("#checkin_date_from").val('');
                $("#created_at_to").val('');
                $("#created_at_from").val('');
                oTable.draw();
            });
        });
    </script>
@endsection