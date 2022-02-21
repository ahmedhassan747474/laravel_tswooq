{{-- @extends('admin.layout') --}}
@include('admin.common.meta')

<style>

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

.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td {
    border-top: 1px solid #f4f4f4;
    padding: 5px !important;
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

@media print {    .hidden-print,
    .hidden-print * {
        display: none !important;
    }
    .ticket{
        height:auto;
    }
}

</style>
<body onload="self.print();" >
  @php
    setlocale(LC_ALL, "ar_SA.UTF-8");
        \Carbon\Carbon::setLocale(config('ar')); // sv
  @endphp
  {{-- <body onload="self.print();" onafterprint="window.location.href = '/admin/orders/cutPaper';"> --}}
<div class="wrapper wrapper2">
  <!-- Main content -->
  <section class="ticket" style="margin: 15px;">
      <!-- title row -->
      <div class="col-xs-12">
      <div class="row" style="position: relative;top: -25px;">
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
          <h5 class="page-header" style="padding-bottom: 25px">
            <i class="fa fa-globe"></i> 
            <small class="pull-right" style="text-align: right;">{{ trans('labels.OrderID') }}# {{ $data['orders_data'][0]->orders_id }}</small>
            <br><small class="pull-right" style="text-align: right;">{{ trans('labels.OrderedDate') }}: {{ date('m/d/Y', strtotime($data['orders_data'][0]->date_purchased)) }}</small>
          </h5>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      @php
        if($data['orders_data'][0]->ordered_source == 3 && $data['orders_data'][0]->admin_id !=1){
          $auth=\App\Models\Core\User::find($data['orders_data'][0]->admin_id );
          if($auth->role_id == 11){			
                  $user = App\Models\Core\User::find($auth->parent_admin_id);
                }else{
                  $user = App\Models\Core\User::find($auth->id);
                }
          // $user=\App\Models\Core\User::find($auth->parent_admin_id);
          $name = $user->shop_name;
          $src= isset($user->avatari->image_category) ? asset('').'/'.$user->avatari->image_category->path:asset('/images/admin_logo/logo_print.jpeg');
          // dd($user->shop_name);
        
        }
        else{
          $src=asset('/images/admin_logo/logo_print.jpeg');
        }
      @endphp
      <img src="{{ $src }}" height="60" width="50" style="width: 120px;position: relative;margin-top: -60px;" class="float-right">
      <h4 style="position: relative;left: 33px;top: 0px;">{{ $name??'' }}</h4>
      <div class="row invoice-info" style="direction: rtl;text-align: justify;line-height: normal;font-weight: 800;">
        
        <div class="col-md-12" style="direction: rtl">
        	<h4 style="display: flex;justify-content: center;">فاتورة ضريبة مبسطة</h4>
        </div>
        <div class="col-md-12" style="direction: rtl">
        	<h4 style="display: flex;justify-content: center;"> عقد اتفاق</h4>
        </div>
        <div class="col-md-8">
          <p>بعون الله تعالى تم الاتفاق بين كل من الطرفين</p>
          <p>الطرف الاول مطابخ شركة الخيال الراقى</p>
        </div>
        <div class="col-md-12">
          <p>الطرف الثانى: السيد/ {{ $data['orders_data'][0]->customers_name }}</p>
          <p>الجوال: {{ $data['orders_data'][0]->customers_telephone }}</p>
          <p>العنوان: {{ $data['orders_data'][0]->customers_street_address }}</p>
          <p>1- يقوم الطرف الأول بتصنيع وتركيب أعمال الألمونيوم المطلوبة من الطرف الثانى مده {{ $data['orders_data'][0]->duration }}</p>
          <p>2- يدفع الطرف الثانى حال توقيع العقد دفعة مقدمة لا تقل عن خمسين بالمئة  (50%) كعربون</p>
          <p>3- اذا اراد الطرف الثانى تغيير العمل ونوعية الالمنيوم او التصميم بعد تنفيذه فعلا فعليه ان يتحمل قيمة ماتم تنفيذه</p>
          <p>4- فى حالة تأخير العميل عن استلام المطبخ خلال فترة اقصاها شهرين لا يحق له المطالبة بالعربون والعمل</p>
          <p>المحل لا يتحمل تسرب المياه الناتجه عن تخريم المواسير داخل الجدران وغير مسئول عن اعمال السباكه والكهرباء واذا كانت الارضية مائلة او الجدران غير مستقيمة فالمحل لا يتحمل اي فراغات او ميول بين الدواليب او الرخام</p>
          <p></p>
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
              {{-- <th class="description">{{ trans('labels.ProductAttributes') }}</th> --}}
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
                {{-- <td class="description" >
                  {{  $products->products_attribute }}
                </td> --}}
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
                <?php $total = $total + $products->products_price * $products->products_quantity; $tax += $products->products_tax ?>
            </tr>
            @endforeach

            </tbody>
          </table>
        </div>
        <!-- /.col -->    
      </div>

      <div class="row invoice-info" style="direction: rtl;text-align: justify;line-height: normal;font-weight: 800;">
        
        <div class="col-md-12">
          <p>تضاف سعر الاجهزة والاكسسوارات كل حسب سعرها <br>يسدد باقى الحساب قبل التركيب والتحميل بخمسة ايام <br>تركيب الرخام بعد تشطيب المطبخ بخمس أيام</p>
        </div>
      
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row" style="direction: rtl">
        <!-- accepted payments column -->
        
        <!-- /.col -->
        <div class="col-xs-12">
          <!--<p class="lead"></p>-->
          <div class="table-responsive ">
            <table class="table order-table">

              <tr>
                <th style="text-align: right;">{{ trans('labels.PaymentMetods') }}:</th>
                <td>
                  {{ $data['orders_data'][0]->payment_method == 'visa' ?'فيزا':'كاش' }}
                  {{-- {{ str_replace('_',' ', $data['orders_data'][0]->payment_method) }} --}}
                </td>
              </tr>

              <tr>
                <th style="text-align: right;">{{ trans('labels.Delivered Date') }}:</th>
                <td>
                  {{ str_replace('_',' ', $data['orders_data'][0]->delivery_date) }}
                </td>
              </tr>

              <tr>
                <th  style="text-align: right;">{{ trans('labels.Subtotal') }}:</th>
                <td>
                  @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $total }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif
                  </td>
              </tr>
              <tr>
                <th style="text-align: right;">{{ trans('labels.Tax') }}:</th>
                <td>
                  @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->total_tax }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif
                  {{-- @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->total_tax }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif --}}
                  </td>
              </tr>
              {{-- <tr>
                <th style="text-align: right;">{{ trans('labels.ShippingCost') }}:</th>
                <td>
                  @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->shipping_cost }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}}@endif
                  </td>
              </tr> --}}
              {{-- @if(!empty($data['orders_data'][0]->coupon_code))
              <tr>
                <th style="text-align: right;">{{ trans('labels.DicountCoupon') }}:</th>
                <td>
                  @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->coupon_amount }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif</td>
              </tr>
              @endif --}}
              @if(!empty($data['orders_data'][0]->admin_discount))
              <tr>
                <th style="text-align: right;">{{ trans('labels.Discount') }}:</th>
                <td>
                  @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->admin_discount }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif</td>
              </tr>
              @endif

              <tr>
                <th style="text-align: right;">{{ trans('labels.Total') }}:</th>
                <td>
                    @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->order_price }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif
                </td>
              </tr>
              
              <tr>
                <th style="text-align: right;">{{ trans('labels.Paied Price') }}:</th>
                <td>
                    @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->paied }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif
                </td>
              </tr>

              <tr>
                <th style="text-align: right;">{{ trans('labels.Remaining Price') }}:</th>
                <td>
                    @if(!empty($result['commonContent']['currency']->symbol_left)) {{$result['commonContent']['currency']->symbol_left}} @endif {{ $data['orders_data'][0]->order_price - $data['orders_data'][0]->paied }} @if(!empty($result['commonContent']['currency']->symbol_right)) {{$result['commonContent']['currency']->symbol_right}} @endif
                </td>
              </tr>

              
            </table>
            <p>السعر شامل الضريبة</p>

          </div>

        </div>
        <div class="col-xs-12" style="direction: rtl">
        	<p class="lead" style="margin-bottom:10px;text-align: right;">{{ trans('labels.Notes') }}:</p>
        	<p class="" style="text-transform:capitalize;">
                {{$data['orders_data'][0]->comments}}
            </p>
        </div>
        <hr>
        <div class="row invoice-info" style="direction: rtl;text-align: justify;line-height: normal;font-weight: 800;">
          <div class="col-md-12" style="direction: rtl">
            <h4 style="display: flex;justify-content: center;"> </h4>
          </div>
          <div class="col-md-8">
            <p></p>
            <p></p>
          </div>
          <div class="col-md-12">
           
          </div>
          <!-- /.col -->
        </div>

        {{-- <div class="row">
          <div class="col-md-12" style="direction: rtl">
        	<h4 style="display: flex;justify-content: center;">والله ولى التوفيق</h4>
        </div>

        <table style="border: 1px solid #e3363600 !important;">
          <tr>
            <td style="padding-left: 150px;border-top: 1px solid #e3363600 !important;">توقيع الطرف الأول</td>
            
            <td style="    border-top: 1px solid #e3363600 !important;">توقيع الطرف الثانى</td>
          </tr>
          <tr>
            <td style="    border-top: 1px solid #e3363600 !important;">.....................</td>
            
            <td style="    border-top: 1px solid #e3363600 !important;">...................</td>
          </tr>
        </table> --}}

        {{-- <div class="col-md-6" style="direction: rtl">
        	<p style="display: flex;justify-content: center;">توقيع الطرف الأول<br>....................................<p>
        </div>

        <div class="col-md-6" style="direction: rtl">
        	<p style="">توقيع الطرف الثانى<br>...................................<p>
        </div> --}}
        </div>
        
        <br>
        {{-- <div class="col-xs-12">
        	<p class="lead" style="margin-bottom:10px" style="text-align: right;">{{ trans('labels.Orderinformation') }}:</p>
        	<p class="text-muted well well-sm no-shadow" style="text-transform:capitalize; word-break:break-all;">
            @if(trim($data['orders_data'][0]->order_information) != '[]' and !empty($data['orders_data'][0]->order_information))
           		{{ $data['orders_data'][0]->order_information }}
            @else
           		---
            @endif
            </p>
        </div> --}}
        @php
        $__QR=null;
                if(auth()->user()->role_id == 11){			
                  $shop = App\Models\Core\User::find(auth()->user()->parent_admin_id);
                }else{
                  $shop = App\Models\Core\User::find(auth()->user()->id);
                }
                $tax_number=$shop->tax_number??'';
                if(isset($shop->shop_name) && $shop->shop_name!=null){
                    $shop_name=$shop->shop_name??$shop->first_name.' '.$shop->last_name;
                }else if(isset($shop->first_name) && $shop->first_name!=null){
                    $shop_name=$shop->first_name.' '.$shop->last_name;
                }else {
                  $shop_name='tswooq';
                }              
              if(isset($tax_number) && $tax_number!='')
              {
                function __getLength($value) {
                    return strlen($value);
                }

                function __toHex($value) {
                    return pack("H*", sprintf("%02X", $value));
                }

                function __toString($__tag, $__value, $__length) {
                    $value = (string) $__value;
                    return __toHex($__tag) . __toHex($__length) . $value;
                }

                function __getTLV($dataToEncode) {
                    $__TLVS = '';
                    for ($i = 0; $i < count($dataToEncode); $i++) {
                        $__tag = $dataToEncode[$i][0];
                        $__value = $dataToEncode[$i][1];
                        $__length = __getLength($__value);
                        $__TLVS .= __toString($__tag, $__value, $__length);
                    }

                    return $__TLVS;
                }

                $dataToEncode = [
                    [1, $shop_name],
                    [2, $tax_number],
                    [3, date('Y-m-d\TH:i:s\Z', strtotime($data['orders_data'][0]->date_purchased))],
                    [4, $data['orders_data'][0]->order_price],
                    [5, $data['orders_data'][0]->total_tax]
                ];

                $__TLV = __getTLV($dataToEncode);
                $__QR = base64_encode($__TLV);
                }
          @endphp

          @if ($__QR)
          <br>
          <br>
            <div class="col-xs-12 text-center" id="printThisBarcode" style="cursor:pointer;margin-top: -13px;" >
              {{-- {!! QrCode::size(100)->generate(URL::to('admin/orders/invoiceprint/'.$data['orders_data'][0]->orders_id)); !!} --}}
              {!! QrCode::size(100)->generate($__QR); !!}
            </div>
          @endif

        <!-- /.col -->
      </div>
      <!-- /.row -->


    </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
<script></script>
</body>

@include('admin.common.scripts')
