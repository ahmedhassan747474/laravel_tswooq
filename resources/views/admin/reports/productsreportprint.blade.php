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
                    <th>{{trans('labels.ID') }}</th>
                    <th>{{trans('labels.Category') }}</th>
                    <th>{{trans('labels.Name') }}</th>
                    <th>{{ trans('labels.Quantity') }}</th>
                    <th>{{ trans('labels.Additional info') }}</th>
                    <th>{{trans('labels.ModifiedDate') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @if(count($results['products'])>0)
                    @php  $resultsProduct = $results['products']->unique('products_id')->keyBy('products_id');  @endphp
                    @foreach ($resultsProduct as  $key=>$product)
                        <tr>
                            <td>{{ $product->products_id }}</td>
                            <td>
                                {{ $product->categories_name }}
                            </td>
                            <td>
                                {{ $product->products_name }} @if(!empty($product->products_model)) ( {{ $product->products_model }} ) @endif
                            </td>
                            <td>
                                {{ $product->products_quantity}}
                            </td>
                            <td>

                                @if(!empty($product->manufacturers_name))
                                    <strong>{{ trans('labels.Manufacturer') }}:</strong> {{ $product->manufacturers_name }}<br>
                                @endif
                                <strong>{{ trans('labels.BuyPrice') }}: </strong>
                                @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $product->price_buy }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif
                                <br>
                                <strong>{{ trans('labels.SalePrice') }}: </strong>
                                @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $product->products_price }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif
                                <br>
                                @if($product->admin_id != null)
                                @php
                                    $getName = DB::table('users')->where('id', $product->admin_id)->first();
                                @endphp
                                <strong>{{ trans('labels.ShopName') }}: </strong> {{$getName != null ? $getName->shop_name : 'Not Exist'}} <br>
                                @endif
                                <strong>{{ trans('labels.Weight') }}: </strong>  {{ $product->products_weight }}{{ $product->products_weight_unit }}<br>
                                <strong>{{ trans('labels.Viewed') }}: </strong>  {{ $product->products_viewed }}<br>
                                @if(!empty($product->specials_id))
                                    <strong class="badge bg-light-blue">{{ trans('labels.Special Product') }}</strong><br>
                                    <strong>{{ trans('labels.SpecialPrice') }}: </strong>  {{ $product->specials_products_price }}<br>

                                    @if(($product->specials_id) !== null)
                                        @php  $mytime = Carbon\Carbon::now()  @endphp
                                        <strong>{{ trans('labels.ExpiryDate') }}: </strong>
                                    @endif
                                @endif
                            </td>
                            <td>
                                {{ $product->productupdate }}
                            </td>

                            {{-- <td>
                              <a class="btn btn-primary" style="width: 100%; margin-bottom: 5px;" href="{{url('admin/products/edit')}}/{{ $product->products_id }}">{{ trans('labels.EditProduct') }}</a>
                              </br>

                              <a class="btn btn-warning" style="width: 100%;  margin-bottom: 5px;" href="{{url('admin/products/images/display/'. $product->products_id) }}">{{ trans('labels.ProductImages') }}</a>
                              </br>
                              <a class="btn btn-danger" style="width: 100%;  margin-bottom: 5px;" id="deleteProductId" products_id="{{ $product->products_id }}">{{ trans('labels.DeleteProduct') }}</a>
                              </td> --}}
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">{{ trans('labels.NoRecordFound') }}</td>
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
