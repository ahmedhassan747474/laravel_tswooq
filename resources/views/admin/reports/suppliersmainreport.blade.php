@extends('admin.layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> {{ trans('labels.Supplier Report') }} <small>{{ trans('labels.Supplier Report') }}...</small> </h1>
    <ol class="breadcrumb">
      <li><a href="{{ URL::to('admin/dashboard/this_month') }}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
      <li class="active">{{ trans('labels.Supplier Report') }}</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Info boxes -->

    <!-- /.row -->
    <div class="row">
        <!-- Left col -->
        <div class="col-md-12">
          <!-- MAP & BOX PANE -->
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">{{ trans('labels.Filter') }}</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>

            <div class="box-body no-padding">
              <form  name='registration' method="get" action="{{url('admin/suppliersmainreport')}}">
              <input type="hidden" name="type" value="all">
              <div class="box-body">
                <div class="col-xs-3">
                  <div class="form-group">
                    <label for="exampleInputEmail1">{{ trans('labels.Choose start and end date') }}</label>
                    <input class="form-control reservation dateRange" placeholder="{{ trans('labels.Choose start and end date') }}" readonly value="{{app('request')->input('dateRange')}}" name="dateRange" aria-label="Text input with multiple buttons ">
                  </div>
                </div>

                <div class="col-xs-3">
                  <div class="form-group">
                    <label for="exampleInputEmail1">{{ trans('labels.Choose Supplier') }}</label>
                    <select type="button" required class="btn btn-default select2 form-control" data-toggle="dropdown" name="user_supplier_id" id="products_id"  >
                        <option value="">{{trans('labels.Choose Supplier')}}</option>
                        @foreach($result['suppliers'] as $supplier)
                        <option value="{{$supplier->id}}"  @if( app('request')->input('user_supplier_id')) @if  (app('request')->input('user_supplier_id') == $supplier->id) {{ 'selected' }} @endif @endif>{{$supplier->name}}</option>
                        @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-xs-2" style="padding-top: 25px">
                  <div class="form-group">
                    <button class="btn btn-primary" id="submit" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                    @if(app('request')->input('type') and app('request')->input('type') == 'all')  <a class="btn btn-danger " href="{{url('admin/suppliersmainreport')}}"><i class="fa fa-ban" aria-hidden="true"></i> </a>@endif
                  </div>
                </div>
            </div>
              <!-- /.box-body -->

            </form>
          </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">{{ trans('labels.Supplier Report') }} </h3>

            {{-- <div class="box-tools pull-right">
              <form action="{{ URL::to('admin/suppliersreportprint')}}" target="_blank">
                <input type="hidden" name="page" value="invioce">
                <input type="hidden" name="user_supplier_id" value="{{app('request')->input('user_supplier_id')}}">
                <input type="hidden" name="dateRange" value="{{app('request')->input('dateRange')}}">
                <button type='submit' class="btn btn-default pull-right"><i class="fa fa-print"></i> {{ trans('labels.Print') }}</button>
              </form>
            </div> --}}
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div class="row">
              <div class="col-xs-12">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>{{ trans('labels.ID') }}</th>
                      <th>{{ trans('labels.Reference Code') }}
                      <th>{{ trans('labels.Supplier Name') }}</th>
                      <th>{{ trans('labels.Price') }}</th>
                      <th>{{ trans('labels.Paied Price') }}</th>
                      <th>{{ trans('labels.Remaining Price') }}</th>
                      <th>{{ trans('labels.Date') }}</th>
                    </tr>
                  </thead>
                   <tbody>
                    @foreach ($result['reports'] as  $key=>$report)

                    <?php
                    $result['total_price'] = $thisReport->suppliersreportTotalPrice($request, $report->supplier_main_id);

                    $result['report_detail'] = $thisReport->suppliersreportDetail($request, $report->supplier_main_id);

                    $result['report_detail_total'] = $thisReport->suppliersreportDetailTotal($request, $report->supplier_main_id);

                    ?>
                        <tr>

                            <td><a href="{{ URL::to('admin/suppliersreport') . '/' . $report->supplier_main_id}}">{{ $report->supplier_main_id }}</a></td>

                            @if($report->reference_code)
                            <td>{{ $report->reference_code }}</td>
                            @else
                            <td>---</td>
                            @endif

                            @if($report->supplier_name)
                            <td><a href="{{ URL::to('admin/suppliersreport') . '/' . $report->supplier_main_id}}">{{ $report->supplier_name }}</a></td>
                            @else
                            <td>---</td>
                            @endif

                            @if($report->price)
                            <td>{{ $report->price }}</td>
                            @else
                            <td>---</td>
                            @endif

                            <td>{{$result['report_detail_total']}}</td>
                            <td>{{$result['total_price'] - $result['report_detail_total']}}</td>

                            <td>{{ date('m/d/Y', strtotime($report->created_at)) }}</td>

                        </tr>
                    @endforeach
                  </tbody>
                </table>
                <div class="col-xs-12" style="background: #eee;">


                  @php
                    if($result['reports']->total()>0){
                      $fromrecord = ($result['reports']->currentpage()-1)*$result['reports']->perpage()+1;
                    }else{
                      $fromrecord = 0;
                    }
                    if($result['reports']->total() < $result['reports']->currentpage()*$result['reports']->perpage()){
                      $torecord = $result['reports']->total();
                    }else{
                      $torecord = $result['reports']->currentpage()*$result['reports']->perpage();
                    }

                  @endphp
                  <div class="col-xs-12 col-md-6" style="padding:30px 15px; border-radius:5px;">
                    <div>Showing {{$fromrecord}} to {{$torecord}}
                        of  {{$result['reports']->total()}} entries
                    </div>
                  </div>
                <div class="col-xs-12 col-md-6 text-right">
                    {{ $result['reports']->appends(\Request::except('type'))->render() }}
                </div>
              </div>

            </div>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Main row -->

    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
@endsection
