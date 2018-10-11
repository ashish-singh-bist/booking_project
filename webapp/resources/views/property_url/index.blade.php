@extends('adminlte::page')
@section('title')
    Property Urls
@endsection

@section('content')
    <!-- content wrapper. contains page content -->
    <div class="content-panel">
        <!-- content header (page header) -->
        <section class="content-header">
            <h1>Property<small>Url's</small></h1>
        </section>
        <!-- end of content header (page header) -->

        <!-- main content-->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
{{--                         <div class="box-header with-border">
                            <h3 class="box-title">Upload CSV File</h3>
                        </div> --}}
                            
                            <div class="box-body">
                                {!! Form::open(array('url' => 'property_url', 'method' => 'POST', 'enctype' =>'multipart/form-data', 'class' => 'form-inline') ) !!}
                                <div class="form-group">
                                    {!! Form::label('property_url_file', 'Upload CSV File (city,url)') !!}
                                    {!! Form::file('property_url_file', array('required' => 'required', 'class' => 'form-control')) !!}
                                </div>
                                {!! Form::submit('Upload', array('class' => 'btn btn-primary')) !!}
                                {!! Form::close() !!}
                            </div>
                    </div>
                </div>
            </div>
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
                                <div class="col-md-4 col-sm-12">
                                    <div class="box box-primary box-solid filter-box">
                                        <div class="box-header">
                                            <h4 class="box-title">Country</h4>
                                        </div>
                                        <div class="box-body">
                                            <select class="form-control filter_class select2-country" id="countries" multiple="multiple"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="box box-primary box-solid filter-box">
                                        <div class="box-header">
                                            <h4 class="box-title">City</h4>
                                        </div>
                                        <div class="box-body">
                                            <select class="form-control filter_class select2-city" id="cities" multiple="multiple"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12 text-right">
                                    <a id='filter_apply' class="btn btn-success">Apply Filters</a>
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
                            <table class="table table-bordered" id="property-table">
                                <thead>
                                    <tr>
                                        <th>Hotel Title/Url</th>
                                        {{-- <th>Hotel Id</th> --}}
                                        <th>City</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Links</th>
                                        <th>Select Parsing Interval (in days)</th>
                                    </tr>
                                </thead>
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
            var oTable = $('#property-table').DataTable({
                "aLengthMenu": [5, 10, 25, 50, 100, 500, 1000],
                "iDisplayLength": 100,
                "sPaginationType" : "full_numbers",
                processing: true,
                serverSide: true,
                select: {
                    style: 'multi'
                },
                dom: "<'row'<'col-sm-2'li><'col-sm-10'f><'col-sm-10'p>>rt<'bottom'ip><'clear'>",
                ajax: {
                        url: "{!! route('property_url.index.getData') !!}",
                    data: function (d) {
                        d.countries = $("#countries").val();
                        d.cities = $("#cities").val();
                    },
                },
                columns: [
                    // { data: 'url', name: 'url', 'render' : function ( data, type, row) { return '<a target="_blank" href="' + data + '">' + data + '</a>'; } },
                    { data: 'hotel_name', name: 'hotel_name'},
                    // { data: 'hotel_id', name: 'hotel_id' },
                    { data: 'city', name: 'city' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'link', name: 'link' },
                    { data: 'action', name: 'action' },
                ],
                columnDefs: [
                    { "orderable": false, "targets": [4, 5] },
                    { "orderable": true, "targets": [0, 1, 2, 3] }
                ],
                "order": [[ 2, "desc" ]]
            });

            $('#filter_apply').on('click', function(e) {
                oTable.draw();
            });

            $('#clear_filter').on('click',function(e){
                $(".select2-country").val('').trigger('change');
                $(".select2-city").val('').trigger('change');
                oTable.draw();
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

             $('#property-table').on('change', '.update_status', function (e) { 
                e.preventDefault();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var data = {};
                data._id = $(this).attr('prop_id');
                var parse_interval = $(this).val();
                data.parse_interval = parse_interval;

                if(data.parse_interval != '' || data.parse_interval !=null){
                    $.ajax({
                        type: "POST",
                        url: "{!! URL::to('property_url/updatestatus') !!}",
                        dataType: "json",
                        data: data,
                        success:function(data){
                            oTable.draw();
                            console.log("this is success msg");
                        },
                        error: function(error) {
                            console.log("this is error msg");
                            console.log(error);
                        }
                    });
                } else{
                    alert("Please Add Message.");
                }
            });
            
        });
    </script>
@endsection