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
                        <div class="box-header with-border">
                            <h3 class="box-title">Upload CSV File</h3>
                        </div>
                            {!! Form::open(array('url' => 'property_url', 'method' => 'POST', 'enctype' =>'multipart/form-data') ) !!}
                            <div class="box-body">
                                <div class="form-group">
                                    {!! Form::label('name', 'Name') !!}
                                    {!! Form::file('property_url_file', array('required' => 'required')) !!}
                                </div>
                            </div>
                            <div class="box-footer">
                                {!! Form::submit('Upload', array('class' => 'btn btn-primary')) !!}
                            </div>
                            {!! Form::close() !!}
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
                                        <th>Url</th>
                                        <th>City</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
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
            $('#property-table').DataTable({
                "aLengthMenu": [5, 10, 25, 50, 100, 500, 1000],
                "iDisplayLength": 100,
                "sPaginationType" : "full_numbers",
                processing: true,
                serverSide: true,
                select: {
                    style: 'multi'
                },
                ajax: "{!! route('property_url.index.getData') !!}",
                columns: [
                    { data: 'url', name: 'url', 'render' : function ( data, type, row) { return '<a target="_blank" href="' + data + '">' + data + '</a>'; } },
                    { data: 'city', name: 'city' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action' },
                ]
            });
        });
    </script>
@endsection