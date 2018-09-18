@extends('adminlte::page')
@section('title')
    Room Details
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
                            <div class="form-group col-sm-4">
                                <label>Room Type</label>
                                <select class="form-control" id="room_type">
                                    <option value="">Any Type</option>
                                    <option value="Doppel- oder Zweibettzimmer">Doppel- oder Zweibettzimmer</option>
                                    <option value="Einzelzimmer">Einzelzimmer</option>
                                    <option value="Einzelzimmer">Premium Doppel- oder Zweibettzimmer</option>
                                    <option value="Comfort Doppel-/Zweibettzimmer">Comfort Doppel-/Zweibettzimmer</option>
                                    <option value="Casa Special Room">Casa Special Room</option>
                                </select>
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
    <script type="text/javascript">
        $(function() {
            $.fn.dataTable.ext.errMode = 'none';
            var oTable = $('#room_details').DataTable({
                "aLengthMenu": [5, 10, 25, 50, 100, 500, 1000],
                "iDisplayLength": 100,
                "sPaginationType" : "full_numbers",
                processing: true,
                serverSide: true,
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
                    }
                },                
                columns: [
                    @foreach (config('app.room_details_header_key') as $key => $value) { data: '{{$key}}', name: '{{$key}}' }, @endforeach
                ]
            });

            $('#room_type').change( function(e) {
                oTable.draw();
                e.preventDefault();
            });            
        });
    </script>
@endsection