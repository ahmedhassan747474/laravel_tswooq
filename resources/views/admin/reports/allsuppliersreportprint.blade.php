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

                            <td>{{ $report->supplier_main_id }}</td>

                            @if($report->reference_code)
                            <td>{{ $report->reference_code }}</td>
                            @else
                            <td>---</td>
                            @endif

                            @if($report->supplier_name)
                            <td>{{ $report->supplier_name }}</td>
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
        </div>
        <!-- /.col -->

      </div>
      <!-- /.row -->

      <!-- /.row -->


    </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
