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
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                {{-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button> --}}
                                <a id='clear_filter' class="btn btn-danger">Clear Filters</a>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group col-sm-2 filter-outer-box">
                                <label>Category</label>
                                @foreach($category_list as $category)
                                    <li><input type="checkbox" name="category[]" value="{{$category[0]}}"/> {{$category[0]}}</li>
                                @endforeach
{{--                                 <select class="form-control filter_class" id="category">
                                    <option value="">Any</option>
                                    @foreach($category_list as $category)
                                        <option value="{{$category[0]}}">{{$category[0]}}</option>
                                    @endforeach
                                </select> --}}
                            </div>

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

                            <div class="form-group col-sm-6">
                                <label>Country</label>
                                <select class="form-control filter_class select2-country" id="countries" multiple="multiple">
                                </select>
                            </div>

                            <div class="form-group col-sm-6">
                                <label>City</label>
                                <select class="form-control filter_class select2-city" id="cities" multiple="multiple">
                                </select>
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
                searching: false,
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
                        d.created_at_to = $('#created_at_to').val();
                        d.created_at_from = $('#created_at_from').val();
                        var stars = $('input[name="stars[]"]:checked')
                        .map(function() {
                            return $(this).val();
                        }).get();
                        var ratings = $('input[name="ratings[]"]:checked')
                        .map(function() {
                            return $(this).val();
                        }).get();
                        var categories = $('input[name="category[]"]:checked')
                        .map(function() {
                            return $(this).val();
                        }).get();
                        d.stars = stars;
                        d.ratings = ratings;
                        d.categories = categories;
                        d.cities = $("#cities").val();
                        d.countries = $("#countries").val();
                    }
                },                
                columns: [
                    @foreach (config('app.hotel_master_header_key') as $key => $value) { data: '{{$key}}', name: '{{$key}}' }, @endforeach
                ]
            });

            $('.filter_class, .filter-outer-box input').on('change', function(e) {
                oTable.draw();
            });

            // $('#star').change( function(e) {
            //     oTable.draw();
            // });
            // $('#rating').change( function(e) {
            //     oTable.draw();
            // });
            // $('#created_at').change( function(e) {
            //     oTable.draw();
            // });
            // $('#category').change( function(e) {
            //     oTable.draw();
            // });

            $('#clear_filter').on('click',function(){
                $('input[name="category[]"]:checked')
                .map(function() {
                    $(this).prop( "checked", false );
                });
                $('input[name="stars[]"]:checked')
                .map(function() {
                    $(this).prop( "checked", false );
                });
                $('input[name="ratings[]"]:checked')
                .map(function() {
                    $(this).prop( "checked", false );
                });
                $(".select2-country").val('').trigger('change');
                $(".select2-city").val('').trigger('change');
                $("#created_at_to").val('');
                $("#created_at_from").val('');
                oTable.draw();
            });

            $('#countries').select2({
                //data: ['s','y','z'],
                placeholder: 'Select a country',
                allowClear: true,
                //minimumResultsForSearch: 5,
                minimumInputLength: 3,
                ajax: {
                    url: '{{route("get_filter_list")}}',
                    dataType: 'json',
                    data: function (params) {
                      var query = {
                        search: params.term,
                        type: 'Country'
                      }

                      // Query parameters will be ?search=[term]&type=public
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
                //data: ['s','y','z'],
                placeholder: 'Select a city',
                allowClear: true,
                //minimumResultsForSearch: 5,
                minimumInputLength: 3,
                ajax: {
                    url: '{{route("get_filter_list")}}',
                    dataType: 'json',
                    data: function (params) {
                      var query = {
                        search: params.term,
                        type: 'City'
                      }

                      // Query parameters will be ?search=[term]&type=public
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
    </script>
@endsection