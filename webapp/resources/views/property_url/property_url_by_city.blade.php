@extends('adminlte::page')
@section('title')
    Search Property
@endsection

@section('content')
    <!-- content wrapper. contains page content -->
    <div class="content-panel">
        <!-- content header (page header) -->
        <section class="content-header">
            <h1>Search Property<small>By city</small></h1>
        </section>
        <!-- end of content header (page header) -->

        <!-- main content-->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        
                        <div class="box-body">
                            <form class="form-inline" id="search-city">
                                <label>Search City </label>
                                <input type="text" id="city" class="form-control" required="required" placeholder="City Name">
                                <input type="submit" class="btn btn-primary" value="search">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-body table-responsive">
                            <table class="table table-bordered property-city-table" id="property-city-table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" class="editor-active" id="select-all"></th>
                                        <th>Image</th>
                                        <th>Hotel Name</th>
                                        <th>Hotel Id</th>
                                        <th>Url</th>
                                        <th>Review</th>
                                        <th>Rating</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="update_msg">
            </div>
            <div>
                <input type="button" class="btn btn-primary" id="save_urls" value="Save Selected Urls">
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
            var oTable = $('#property-city-table').DataTable({
                "aLengthMenu": [5, 10, 25, 50, 100, 500, 1000],
                "iDisplayLength": 500,
                "sPaginationType" : "full_numbers",
                processing: true,
                serverSide: true,
                "paging":   false,
                "ordering": false,
                "info":     false,
                "searching": false,
                deferLoading: false,
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
                    { data: 'select', width: '50px', render: function ( data, type, row ) {
                            if ( type === 'display' ) {
                                return '<input type="checkbox" class="editor-active">';
                            }
                            return data;
                        },
                    },
                    { data: 'img_url', width: '50px', render: function ( data, type, row ) {
                            return '<img class="prop_img popoverButton" src="' + data + '" data-toggle="popover" data-content=\'<img src="' + data + '">\'>';
                        },
                    },
                    { data: 'name', name: 'name'},
                    { data: 'hotel_id', width: '50px', render: function ( data, type, row ) {
                            return data;
                        },
                    },                    
                    { data: 'url', name: 'url'},
                    { data: 'review_count', width: '50px', render: function ( data, type, row ) {
                            return data;
                        },
                    },
                    { data: 'rating', width: '50px', render: function ( data, type, row ) {
                            return data;
                        },
                    },
                ],
                columnDefs: [{
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0
                }],                
                select: {
                    style:    'os',
                    selector: 'td:first-child'
                }
            });
          

            $('#search-city').on('submit', function(e) {
                e.preventDefault();
                oTable.draw();
            });

            $('#save_urls').on('click',function(e){
                var urls = $.map(oTable.rows('.selected').data(), function (item) {
                    return item['url']
                });
                var city = $('#city').val();
                console.log(city);
                $.ajax({
                    type: "POST",
                    url: "{!! route('PropertyUrlByCity.insertPropertyUrls') !!}",
                    dataType: "json",
                    data: {'url': urls,'city':city},
                    success:function(data){
                        var skip_count = data.skip_count;
                        var insert_count = data.insert_count; 
                        var msg_html = '<div class="alert alert-success alert-important" role="alert"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+insert_count+' url inserted and '+skip_count+' url already exists!</div>';
                        $('.alert-msg-outer').html(msg_html);
                        $(window).scrollTop(0);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

           // Handle click on "Select all" control
           $('#select-all').on('click', function(){
              // Get all rows with search applied
              var rows = oTable.rows({ 'search': 'applied' }).nodes();
              // Check/uncheck checkboxes for all rows in the table
              $('input[type="checkbox"]', rows).prop('checked', this.checked);
           });            

            $('#property-city-table tbody').on( 'click', 'tr', function () {
                $(this).toggleClass('selected');
            });  

            $('#property-city-table').on('draw.dt', function () {
                $('[data-toggle="popover"]').popover({
                  trigger: 'hover',
                  html: true,
                  container: 'body'
                });
            });





        });
    </script>
@endsection