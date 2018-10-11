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
                        
                        <div class="box-body">
                            <label>Search City </label>
                            <input type="text" id="city">
                            <input type="button" class="btn btn-primary" id="search" value="search">
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
                                        <th>Action</th>
                                        <th>Url</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <input type="button" class="btn btn-primary" id="save_urls" value="Save">
            </div>
        </section>
        <!-- end of main content-->
    </div>
    <!-- end of content wrapper. contains page content -->
@endsection
@section('js')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function() {
            $.fn.dataTable.ext.errMode = 'none';
            var rows_selected = [];
            var oTable = $('#property-table').DataTable({
                "aLengthMenu": [5, 10, 25, 50, 100, 500, 1000],
                "iDisplayLength": 500,
                "sPaginationType" : "full_numbers",
                processing: true,
                serverSide: true,
                "paging":   false,
                "ordering": false,
                "info":     false,
                "searching": false,
                select: {
                    style: 'multi'
                },
                //dom: "<'row'<'col-sm-2'li><'col-sm-10'f><'col-sm-10'p>>rt<'bottom'ip><'clear'>",
                ajax: {
                    url: "{!! route('property_url_by_city.search_url_City') !!}",
                    data: function (d) {
                        d.city = $("#city").val();
                    },
                    dataFilter: function(response) {
                        var room_array = JSON.parse(response)['property_urls'];
                        return response;
                    },
                },
                columns: [
                    { data: 'action', width: '50px', render: function ( data, type, row ) {
                            if ( type === 'display' ) {
                                return '<input type="checkbox" class="editor-active">';
                            }
                            return data;
                        },
                    },
                    { data: 'url', name: 'url'},
                ],
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                }
            });

            $('#search').on('click', function(e) {
                console.log("click event");
                oTable.draw();
            });

            $('#save_urls').on('click',function(e){
                var urls = $.map(oTable.rows('.selected').data(), function (item) {
                    return item['url']
                });
                var city = $('input[name="city[]"]:checked')
                        .map(function() {
                            return $(this).val();
                        }).get();
                $.ajax({
                    type: "POST",
                    url: "{!! route('PropertyUrlByCity.insertPropertyUrls') !!}",
                    dataType: "json",
                    data: {'url': urls,'city':city},
                    success:function(data){
                        console.log("this is success msg");
                    },
                    error: function(error) {
                        console.log("this is error msg");
                        console.log(error);
                    }
                });
            });

            $('#property-table tbody').on( 'click', 'tr', function () {
                $(this).toggleClass('selected');
            });
        });
    </script>
@endsection