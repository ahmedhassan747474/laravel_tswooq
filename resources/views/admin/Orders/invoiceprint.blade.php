{{-- @extends('admin.layout') --}}
@include('admin.common.meta')

<style>
/*@page { width: 79mm }  */
/*@page {*/
/*  size: 79mm;*/
/*  margin: 0;*/
   /*transform: scale(.7) !important;*/
/*}*/
/*@media print{ */
	/* All styles for print should goes here */ 
/*	.invoice{ */
/*		width: 79mm; */
/*		height: auto; */
/*		margin: 50px auto; */
/*	} */
/*} */

/*@media print {*/
/*  @page {*/
/*    width: 9.9cm;*/
    /*height: 297mm;*/
/*  }*/
/*   ... the rest of the rules ... */
/*}*/
.wrapper.wrapper2{
	display: block;
	display: flex;
    justify-content: center;
}
.wrapper{
	display: none;
}

* {
    font-size: 10px;
    font-family: 'Times New Roman';
}

td,
th,
tr,
table {
    border-top: 1px solid black;
    border-collapse: collapse;
}

td.description,
th.description {
    width: 75px;
    max-width: 75px;
}

td.quantity,
th.quantity {
    width: 40px;
    max-width: 40px;
    word-break: break-all;
}

td.price,
th.price {
    width: 40px;
    max-width: 40px;
    word-break: break-all;
}

.centered {
    text-align: center;
    align-content: center;
}

.ticket {
    width: 79mm;
    max-width: 79mm;
}

img {
    max-width: inherit;
    width: inherit;
}

@media print {
    .hidden-print,
    .hidden-print * {
        display: none !important;
    }
    .ticket{
        height:auto;
    }
}

</style>
<body onload="window.print();">
<div class="wrapper wrapper2">
  <!-- Main content -->
  <section class="ticket" style="margin: 15px;">
      <!-- title row -->
      <div class="col-xs-12">
      <div class="row">
       @if(session()->has('message'))
      	<div class="alert alert-success alert-dismissible">
           <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
           <h4><i class="icon fa fa-check"></i> {{ trans('labels.Successlabel') }}</h4>
            {{ session()->get('message') }}
        </div>
        @endif
        @if(session()->has('error'))
        	<div class="alert alert-warning alert-dismissible">
               <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
               <h4><i class="icon fa fa-warning"></i> {{ trans('labels.WarningLabel') }}</h4>
                {{ session()->get('error') }}
            </div>
        @endif


       </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header" style="padding-bottom: 25px">
            <i class="fa fa-globe"></i> {{ trans('labels.OrderID') }}# {{ $data['orders_data'][0]->orders_id }}
            <small class="pull-right">{{ trans('labels.OrderedDate') }}: {{ date('m/d/Y', strtotime($data['orders_data'][0]->date_purchased)) }}</small>
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      @php
        if($data['orders_data'][0]->ordered_source == 3 && $data['orders_data'][0]->admin_id !=1){
          $user=\App\Models\Core\User::find($data['orders_data'][0]->admin_id );
          $name = $user->shop_name;
          $src=asset('').'/'.$user->avatari->image_category->path??asset('/images/admin_logo/logo_print.jpeg');
        }
        else{
          $src=asset('/images/admin_logo/logo_print.jpeg');
        }
      @endphp
      <img src="{{ $src }}" height="60" width="50" style="width: 120px;" class="float-right">
      <h4 style="position: relative;left: 33px;top: 0px;">{{ $name??'' }}</h4>
      <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
          {{ trans('labels.CustomerInfo') }}:
          <address>

            <strong>{{ $data['orders_data'][0]->customers_name }}</strong><br>
            {{ $data['orders_data'][0]->customers_street_address }} <br>
            {{ $data['orders_data'][0]->customers_city }}, {{ $data['orders_data'][0]->customers_state }} {{ $data['orders_data'][0]->customers_postcode }}, {{ $data['orders_data'][0]->customers_country }}<br>
            {{ trans('labels.Phone') }}: {{ $data['orders_data'][0]->customers_telephone }}<br>
            {{ trans('labels.Email') }}: {{ $data['orders_data'][0]->email }}
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          {{ trans('labels.ShippingInfo') }}
          <address>
            <strong>{{ $data['orders_data'][0]->delivery_name }}</strong><br>
            {{ trans('labels.Phone') }}: {{ $data['orders_data'][0]->delivery_phone }}<br>
            {{ $data['orders_data'][0]->delivery_street_address }} <br>
            {{ $data['orders_data'][0]->delivery_city }}, {{ $data['orders_data'][0]->delivery_state }} {{ $data['orders_data'][0]->delivery_postcode }}, {{ $data['orders_data'][0]->delivery_country }}<br>
           <strong> {{ trans('labels.ShippingMethod') }}:</strong> {{ $data['orders_data'][0]->shipping_method }} <br>
           <strong> {{ trans('labels.ShippingCost') }}:</strong> @if(!empty($data['orders_data'][0]->shipping_cost))

           @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->shipping_cost }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif</td>
            @else --- @endif <br>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
         {{ trans('labels.BillingInfo') }}
          <address>
            <strong>{{ $data['orders_data'][0]->billing_name }}</strong><br>
            {{ trans('labels.Phone') }}: {{ $data['orders_data'][0]->billing_phone }}<br>
            {{ $data['orders_data'][0]->billing_street_address }} <br>
            {{ $data['orders_data'][0]->billing_city }}, {{ $data['orders_data'][0]->billing_state }} {{ $data['orders_data'][0]->billing_postcode }}, {{ $data['orders_data'][0]->billing_country }}<br>
          </address>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-striped">
            <thead>
            <tr>
              <th class="quantity">{{ trans('labels.Qty') }}</th>
              <th class="description">{{ trans('labels.ProductName') }}</th>
              <!--<th>{{ trans('labels.ProductModal') }}</th>-->
              <!--<th>{{ trans('labels.Options') }}</th>-->
              <th class="price">{{ trans('labels.Price') }}</th>
            </tr>
            </thead>
            <tbody>
            <?php $total=0 ; $tax=0; ?>
            @foreach($data['orders_data'][0]->data as $products)
            <tr>
                <td class="quantity">{{  $products->products_quantity }}</td>
                <td class="description" >
                    {{  $products->products_name }}<br>
                </td class="price">
                <!--<td>-->
                <!--    {{  $products->products_model }}-->
                <!--</td>-->
                <!--<td>-->
                <!--    @foreach($products->attribute as $attributes)-->
                <!--        <b>{{ $attributes->products_options }} :</b> {{ $attributes->products_options_values }} <br>-->
                <!--        {{-- <b>{{ trans('labels.Value') }}:</b> {{ $attributes->products_options_values }}<br> --}}-->
                <!--        {{-- <b>{{ trans('labels.Price') }}:</b> @if(!empty($result['commonContent']['currency']->symbol_left)) {{ $attributes->options_values_price }} @endif {{ $data['orders_data'][0]->shipping_cost }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif<br> --}}-->

                <!--    @endforeach-->
                <!--</td>-->

                <td>@if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $products->products_price * $products->products_quantity }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif</td>
                <?php $total = $total + $products->products_price * $products->products_quantity; $tax += $products->products_tax * $products->products_quantity?>
            </tr>
            @endforeach

            </tbody>
          </table>
        </div>
        <!-- /.col -->

      </div>
      <!-- /.row -->

      <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-7">
          <p class="lead" style="margin-bottom:10px">{{ trans('labels.PaymentMethods') }}:</p>
          <p class="text-muted well well-sm no-shadow" style="text-transform:capitalize">
           	{{ str_replace('_',' ', $data['orders_data'][0]->payment_method) }}
          </p>
          @if(!empty($data['orders_data'][0]->coupon_code))
              <p class="lead" style="margin-bottom:10px">{{ trans('labels.Coupons') }}:</p>
                <table class="text-muted well well-sm no-shadow stripe-border table table-striped" style="text-align: center; ">
                	<tr>
                        <th style="text-align: center; ">{{ trans('labels.Code') }}</th>
                        <th style="text-align: center; ">{{ trans('labels.Amount') }}</th>
                    </tr>
                	@foreach( json_decode($data['orders_data'][0]->coupon_code) as $couponData)
                    	<tr>
                        	<td>{{ $couponData->code}}</td>
                            <td>{{ $couponData->amount}}

                                @if($couponData->discount_type=='percent_product')
                                    ({{ trans('labels.Percent') }})
                                @elseif($couponData->discount_type=='percent')
                                    ({{ trans('labels.Percent') }})
                                @elseif($couponData->discount_type=='fixed_cart')
                                    ({{ trans('labels.Fixed') }})
                                @elseif($couponData->discount_type=='fixed_product')
                                    ({{ trans('labels.Fixed') }})
                                @endif
                            </td>
                        </tr>
                    @endforeach
				</table>
          @endif

          </p>

          @if($data['orders_data'][0]->payment_method == 'Bank Account')
          <p class="lead" style="margin-bottom:10px">{{ trans('website.Bank Account') }}:</p>
          <p class="text-muted well well-sm no-shadow" style="text-transform:capitalize">
            {{$data['orders_data'][0]->bank_account_iban}}
          </p>

          <p class="lead" style="margin-bottom:10px">{{ trans('website.Bank Account Image') }}:</p>
          <img src="{{asset('images/bank_account/')}}/{{$data['orders_data'][0]->bank_account_image}}" width="400px">
          @endif
        </div>
        <!-- /.col -->
        <div class="col-xs-5">
          <!--<p class="lead"></p>-->
          <div class="table-responsive ">
            <table class="table order-table">
              <tr>
                <th style="width:50%">{{ trans('labels.Subtotal') }}:</th>
                <td>
                  @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $total }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif
                  </td>
              </tr>
              <tr>
                <th>{{ trans('labels.Tax') }}:</th>
                <td>
                  @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $tax }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif
                  {{-- @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->total_tax }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif --}}
                  </td>
              </tr>
              <tr>
                <th>{{ trans('labels.ShippingCost') }}:</th>
                <td>
                  @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->shipping_cost }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}}@endif
                  </td>
              </tr>
              @if(!empty($data['orders_data'][0]->coupon_code))
              <tr>
                <th>{{ trans('labels.DicountCoupon') }}:</th>
                <td>
                  @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->coupon_amount }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif</td>
              </tr>
              @endif
              @if(!empty($data['orders_data'][0]->admin_discount))
              <tr>
                <th>{{ trans('labels.Discount') }}:</th>
                <td>
                  @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->admin_discount }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif</td>
              </tr>
              @endif

              <tr>
                <th>{{ trans('labels.Total') }}:</th>
                <td>
                    @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->order_price }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif
                </td>
              </tr>
            </table>
          </div>

        </div>
        <div class="col-xs-12">
        	<p class="lead" style="margin-bottom:10px">{{ trans('labels.Notes') }}:</p>
        	<p class="text-muted well well-sm no-shadow" style="text-transform:capitalize; word-break:break-all;">
                {{$data['orders_data'][0]->comments}}
            </p>
        </div>

        <div class="col-xs-12">
        	<p class="lead" style="margin-bottom:10px">{{ trans('labels.Orderinformation') }}:</p>
        	<p class="text-muted well well-sm no-shadow" style="text-transform:capitalize; word-break:break-all;">
            @if(trim($data['orders_data'][0]->order_information) != '[]' and !empty($data['orders_data'][0]->order_information))
           		{{ $data['orders_data'][0]->order_information }}
            @else
           		---
            @endif
            </p>
        </div>
        <div class="col-xs-12 text-center" id="printThisBarcode" style="cursor:pointer;" >
          {!! QrCode::size(120)->generate(URL::to('admin/orders/invoiceprint/'.$data['orders_data'][0]->orders_id)); !!}
        </div>

        <!-- /.col -->
      </div>
      <!-- /.row -->


    </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>

@include('admin.common.scripts')
