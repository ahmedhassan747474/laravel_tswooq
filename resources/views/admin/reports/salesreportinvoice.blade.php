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

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
        <table id="example1" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>{{ trans('labels.Date') }}</th>
                      <th>{{ trans('labels.No of Orders') }}</th>
                      <th>{{ trans('labels.No of Products') }}</th>
                      <th>{{ trans('labels.PriceBuy') }}</th>
                      <th>{{ trans('labels.OrderTotal') }}</th>
                      <th>{{ trans('labels.TotalWin') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                  @if(count($result['reports']['orders'])>0)
                    @foreach ($result['reports']['orders'] as $key=>$orderData)
                    <tr>
                        <td>{{ $orderData->date_purchased }}</td>
                        <td>{{ $orderData->total_orders }}</td>
                        <td>{{ $orderData->total_products }}</td>  
                        <td>{{ $orderData->total_price_buy }}</td>
                        <td>{{ $orderData->total_price }}</td>    
                        <td>{{ $orderData->total_price_win }}</td>    
                    </tr>
                    @endforeach
                  @else
                  	<tr>
                    	<td colspan="6"><strong>{{ trans('labels.NoRecordFound') }}</strong></td>
                    </tr>
                  @endif
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
