@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content')
    <!-- content wrapper. contains page content -->
    <div class="content-panel">
        <!-- content header (page header) -->
        <section class="content-header">
            <h1>Dashboard</h1>
        </section>
        <!-- end of content header (page header) -->
        <!-- main content-->
    
        <section class="content">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Users</span>
                            <span class="info-box-number">{{$user_count}}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Property</span>
                            <span class="info-box-number">Active:- {{$p_active_count}} <br>Total:- {{$p_total_count}}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12 text-right">
                    <a class="btn btn-success" href="{{ route('restart_parser') }}">Restart Scraper</a>
                    <a class="btn btn-success" href="{{ route('stop_parser') }}">Stop Scraper</a>
                </div>

            </div>

            <div class="row">
              <div class="col-sm-12">
                <div class="card stats_panel">
                  <div class="card-header">
                    <h3 class="card-title text-center">Parsing Stats</h3>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <table class="table table-bordered">
                      <tbody><tr>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Parsed</th>
                        <th>Success(%)</th>
                        <th>Pending</th>
                        <th>Price Parsed</th>
                        <th>Room Details Parsed</th>
                        <th>Availability Parsed</th>
                      </tr>


                      @foreach($stats as $stat)
                          <tr>
                            <td>{{ $stat->date->toDateTime()->format('Y M d') }}</td>
                            <td>{{ $stat->total_property }}</td>
                            <td><span class="custome_badge bg-success">{{ $stat->property_parsed }}</span></td>
                            <td>
                                <span class="custome_badge bg-success">{{ number_format(($stat->property_parsed*100)/$stat->total_property) }}%</span>
                            </td>
                            <td>
                              <span class="custome_badge bg-danger">{{ 100 - number_format(($stat->property_parsed*100)/$stat->total_property) }}%</span>
                            </td>
                            <td>{{ $stat->price_parsed }}</td>
                            <td>{{ $stat->room_details_parsed }}</td>
                            <td>{{ $stat->rooms_availability_parsed }}</td>
                          </tr>              
                      @endforeach
                    </tbody></table>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
            </div>
        </section>
    </div>

@stop