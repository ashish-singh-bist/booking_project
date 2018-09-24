@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
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

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Property</span>
                    <span class="info-box-number">Active:- {{$p_active_count}} <br>Total:- {{$p_total_count}}</span>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Parsing Stats</h3>
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
                <th>Price</th>
                <th>Room Details</th>
                <th>Availability</th>
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


@stop