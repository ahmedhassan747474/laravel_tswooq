@extends('admin.layout')
<style>
.wrapper.wrapper2{
	display: block;
}
.wrapper{
	display: none;
}
</style>
<body onload="window.print();">
<div class="wrapper wrapper2">
  <!-- Main content -->
  <section class="invoice" style="margin: 15px;">
      <!-- title row -->
      
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header" style="padding-bottom: 25px">
          <div class="box-body no-padding">
              <form  name='registration' method="get" action="{{url('admin/customers-orders-report')}}">
              <input type="hidden" name="type" value="all">
              <div class="box-body">
              @if(app('request')->input('dateRange'))
                <div class="col-xs-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">{{ trans('labels.Date') }}</label>
                    <p>{{app('request')->input('dateRange')}}</p>
                  </div>
                </div>
                @endif
                @if( app('request')->input('products_id'))
                <div class="col-xs-6">
                  <div class="form-group">
                    <label for="exampleInputEmail1">{{ trans('labels.Products') }}</label>
                        @foreach($result['products'] as $product)
                        <p> @if( app('request')->input('products_id' ) == $product->products_id) {{ $product->products_name }} @endif </p>
                        @endforeach
                        
                  </div>
                </div>
                @endif

                
      
            </div>
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>{{ trans('labels.Date') }}</th>
                <th>{{ trans('labels.Product Name') }}</th>
                <th>{{ trans('labels.Price') }}</th>
                <th>{{ trans('labels.Stock') }}</th>
              </tr>
            </thead>
             <tbody>
              @foreach ($result['reports'] as  $key=>$report)
             
                  <tr>
                      <td>{{ date('m/d/Y', strtotime($report->created_at)) }}</td>

                      @if($report->products_name)
                      <td>{{ $report->products_name }}</td>
                      @else
                      <td>---</td>                            
                      @endif

                      @if($report->price)
                      <td>{{ $report->price }}</td>
                      @else
                      <td>---</td>                            
                      @endif


                      @if($report->stock)
                      <td>{{ $report->stock }}</td>
                      @else
                      <td>---</td>                            
                      @endif                           
                      
                  </tr>
              @endforeach
                <tr>
                  <td></td>
                  <td></td>
                  <td>{{ trans('labels.TotalPrice') }} : {{$result['total_price']}}</td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <td></td>
                  <td>{{ trans('labels.Remaining Price') }} : {{$result['total_price'] - $result['report_detail_total']}}</td>
                  <td></td>
                </tr>
            </tbody>
          </table>
        </div>
        <!-- /.col -->

      </div>
      <!-- /.row -->

      <!-- /.row -->


      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table id="example1" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>{{ trans('labels.Date') }}</th>
                <th>{{ trans('labels.Price') }}</th>
              </tr>
            </thead>
             <tbody>
              @foreach ($result['report_detail'] as  $key=>$report)
                  <tr>

                      <td>{{ date('m/d/Y', strtotime($report->created_at)) }}</td>

                      @if($report->price)
                      <td>{{ $report->price }}</td>
                      @else
                      <td>---</td>                            
                      @endif

                  </tr>
              @endforeach
                <tr>

                  <td></td>
                  <td>{{ trans('labels.TotalPrice') }} : {{$result['report_detail_total']}}</td>

                </tr>
            </tbody>
          </table>
        </div>
        <!-- /.col -->

      </div>
      <!-- /.row -->


    </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
