@extends('adminlte::page')
@section('title')
    Config
@endsection

@section('content')
    <!-- content wrapper. contains page content -->
    <div class="content-panel">
        <!-- content header (page header) -->
        <section class="content-header">
            <h1>Edit Config<small></small></h1>
        </section>
        <!-- end of content header (page header) -->

        <!-- main content-->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                            {!! Form::open(array('url' => 'config', 'method' => 'POST') ) !!}
                            <div class="box-body">
                                @foreach($custom_config as $key => $value)
                                    @if($key != '_id' and $key != 'updated_at' )
                                        <div class="form-group row">
                                            <label class="col-sm-3">{{$key}}</label>
                                            <input class="col-sm-3" type="text" value="{{$value}}" name="{{$key}}">
                                        </div>
                                    @endif                             
                                @endforeach
                            </div>
                            <div class="box-footer">
                                {!! Form::submit('Upload', array('class' => 'btn btn-primary')) !!}
                            </div>
                            {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </section>
        <!-- end of main content-->
    </div>
    <!-- end of content wrapper. contains page content -->
@endsection