@extends('adminlte::page')
@section('title')
    Hotel Master
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css">
@endsection

@section('content')
    <!-- content wrapper. contains page content -->
    <div class="content-panel">
        <!-- content header (page header) -->
        <section class="content-header">
            <h1>Hotel<small>Master</small></h1>
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
                            <div class="form-group col-sm-3">
                                <label>Category</label>
                                <select class="form-control" id="category">
                                    <option value="">Any</option>
                                    <option value="Hotels">Hotels</option>
                                    <option value="Aparthotels">Aparthotels</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-2">
                                <label>Star</label>
                                <select class="form-control" id="star">
                                    <option value="">Any</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-2">
                                <label>Rating</label>
                                <select class="form-control" id="rating">
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
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered" id="hotel_master">
                                <thead><tr>
                                    @foreach (config('app.hotel_master_header_key') as $value) <th>{{$value}}</th> @endforeach
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
            var oTable = $('#hotel_master').DataTable({
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
                        url: "{!! route('hotel_master.getData') !!}?id={{$id}}",
                    @else
                        url: "{!! route('hotel_master.getData') !!}",
                    @endif
                    data: function (d) {
                        d.star = $('#star').val();
                        d.rating = $('#rating').val();
                        d.created_at = $('#created_at').val();
                        d.category = $('#category').val();
                    }
                },                
                columns: [
                    @foreach (config('app.hotel_master_header_key') as $key => $value) { data: '{{$key}}', name: '{{$key}}' }, @endforeach
                ]
            });

            $('#star').change( function(e) {
                oTable.draw();
            });
            $('#rating').change( function(e) {
                oTable.draw();
            });
            $('#created_at').change( function(e) {
                oTable.draw();
            });
            $('#category').change( function(e) {
                oTable.draw();
            });
        });
    </script>
@endsection