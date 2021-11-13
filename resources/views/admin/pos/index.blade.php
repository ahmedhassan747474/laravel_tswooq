@extends('admin.layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> {{ trans('labels.POS') }} </h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
            <li class="active">{{ trans('labels.POS') }}</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->

        <!-- /.row -->

        <div class="row">
            <div class="col-md-12">
                <div class="container-fluid">
                    {{-- <form class="" action="" method="POST" enctype="multipart/form-data">
                        @csrf --}}
                        <div class="row gutters-10">
                            <div class="col-lg-5">
                                <div class="card">
                                    <div class="card-header d-block">
                                        <div class="form-group">
                                            <input class="form-control form-control-sm" type="text" name="keyword" placeholder="{{ trans('labels.Search')}}" onkeyup="filterProducts()">
                                        </div>
                                        <div class="row gutters-5">
                                            <div class="col-md-6">
                                                <select name="poscategory" class="form-control form-control-sm aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                                                    <option value="">{{ trans('labels.All').' '.trans('labels.Categories') }}</option>
                                                    @foreach ($results['categories'] as $key => $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select name="brand"  class="form-control form-control-sm aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                                                    <option value="">{{ trans('labels.All').' '.trans('labels.Brands') }}</option>
                                                    @foreach ($results['brands'] as $key => $brand)
                                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="aiz-pos-product-list c-scrollbar-light">
                                            <div class="row gutters-5" id="product-list">

                                            </div>
                                            <div id="load-more">
                                                <p class="text-center fs-14 fw-600 p-2 bg-soft-primary c-pointer" onclick="loadMoreProduct()">Load More</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                    <div class="card mb-3">
                                        <div class="card-title">
                                            <label> {{ trans('labels.customerQr') }}</label>
                                        </div>
                                        <button type="button" onclick="location.reload();" class="btn btn-icon btn-soft-dark ml-3"  >
                                            <i class="fa fa-refresh"></i> {{ trans('labels.Refresh') }}
                                        </button>
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <table class="table aiz-table mb-0 mar-no" cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th width="50%">{{ trans('labels.CustomerName') }}</th>
                                                            <th width="15%">{{ trans('labels.Products') }}</th>
                                                            
                                                            <th class="text-right">{{ trans('labels.Add') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                           $customers = DB::table('pos_standby')->groupBy('customer_id')->get();
                                                        @endphp
                                            
                                                        @foreach($customers as $customer)
                                                            @php
                                                                $name = DB::table('users')->where('id',$customer->customer_id)->first();
                                                            
                                                                $products = DB::table('pos_standby')->where('customer_id',$customer->customer_id)->count();
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $name->first_name . ' '.$name->last_name }}</td>
                                                                <td>{{ $products }}</td>
                                                                <td class="text-right">
                                                                    <form method="POST" id="addNewForm" action="{{ route('pos.addToPOS') }}">
                                                                        @csrf                                    
                                                                        <button type="submit" class="btn btn-icon btn-soft-dark ml-3"  >
                                                                            <i class="fa fa-plus"></i>
                                                                        </button>
                                                                        <input type="hidden" name="customer_id_pos" value="{{ $customer->customer_id }}">
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                
                                                    </tbody>
                                                </table>
                                                    
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <form class="" action="" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <select name="user_id" class="form-control form-control-sm aiz-selectpicker pos-customer" data-live-search="true" onchange="getShippingAddress()">
                                                        <option value="" selected disabled>{{ trans('labels.walk') }}</option>
                                                        @foreach ($results['customers'] as $key => $customer)
                                                            @if ($customer->user)
                                                                <option value="{{ $customer->user->id }}" data-contact="{{ $customer->user->email }}">{{ $customer->user->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <button type="button" class="btn btn-icon btn-soft-dark ml-3" data-target="#new-customer" data-toggle="modal">
                                                    <i class="fa fa-truck"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <form method="POST" id="addNewForm" action="{{ route('pos.addToCartNew') }}">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                    @csrf
                                                    <div class="flex-grow-1">
                                                        <input type="text" required class="form-control" name="ProductName" placeholder="{{ trans('labels.productName')}}">
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input type="number" required class="form-control" name="ProductQuantity" placeholder="{{ trans('labels.Quantity')}}">
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input type="number" class="form-control" name="tax" placeholder="{{ trans('labels.Tax')}}">
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <input type="number" required class="form-control" name="ProductPrice" placeholder="{{ trans('labels.Price')}}">
                                                    </div>
                                                    <button type="submit"  class="btn btn-icon btn-soft-dark ml-3"  >
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="card mar-btm" id="cart-details">
                                    <div class="card-body">
                                        <div class="aiz-pos-cart-list c-scrollbar-light">
                                            <table class="table aiz-table mb-0 mar-no" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="50%">{{ trans('labels.productName')}}</th>
                                                        <th width="15%">{{ trans('labels.Qty')}}</th>
                                                        <th>{{ trans('labels.Price')}}</th>
                                                        <th>{{ trans('labels.Subtotal')}}</th>
                                                        <th class="text-right">{{ trans('labels.delete')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $subtotal = 0;
                                                        $tax = 0;
                                                        $shipping = 0;
                                                    @endphp
                                                    @if (Session::has('posCart'))
                                                        @forelse (Session::get('posCart') as $key => $cartItem)
                                                            @php
                                                                $subtotal += $cartItem['price']*$cartItem['quantity'];
                                                                $tax += $cartItem['tax']*$cartItem['quantity'];
                                                                $shipping += $cartItem['shipping']*$cartItem['quantity'];
                                                                if(Session::get('shipping', 0) == 0){
                                                                    $shipping = 0;
                                                                }
                                                                $products = DB::table('products')
                                                                    ->leftJoin('manufacturers', 'manufacturers.manufacturers_id', '=', 'products.manufacturers_id')
                                                                    ->leftJoin('manufacturers_info', 'manufacturers.manufacturers_id', '=', 'manufacturers_info.manufacturers_id')
                                                                    ->leftJoin('products_description', 'products_description.products_id', '=', 'products.products_id')
                                                                    ->LeftJoin('image_categories', 'products.products_image', '=', 'image_categories.image_id')
                                                                    ->where('products.products_id', $cartItem['id'])
                                                                    ->first();
                                                                // dd($products);
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    <span class="media">
                                                                        <div class="media-left">
                                                                            <img class="mr-3" height="60" src="{{asset(''). $products->path }}" >
                                                                        </div>
                                                                        <div class="media-body">
                                                                            {{ $products->products_name }} ({{ $cartItem['variant'] }})
                                                                        </div>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <div class="">
                                                                        <input type="number" class="form-control px-0 text-center" placeholder="1" id="qty-{{ $key }}" value="{{ $cartItem['quantity'] }}" onchange="updateQuantity({{ $key }})" min="1">
                                                                    </div>
                                                                </td>
                                                                <td>{{ $cartItem['price'] }}</td>
                                                                <td>{{ $cartItem['price']*$cartItem['quantity'] }}</td>
                                                                <td class="text-right">
                                                                    <button type="button" class="btn btn-circle btn-icon btn-sm btn-danger" onclick="removeFromCart({{ $key }})">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @empty


                                                            {{-- <tr>
                                                                <td colspan="5" class="text-center">
                                                                    <i class="las la-frown la-3x opacity-50"></i>
                                                                    <p>No Product Added</p>
                                                                </td>
                                                            </tr> --}}
                                                        @endforelse
                                                    @endif
                                                    @if (Session::has('posCartNew'))
                                                        @forelse (Session::get('posCartNew') as $key => $cartItem)
                                                        @php
                                                            $subtotal += $cartItem['price']*$cartItem['quantity'];
                                                            $tax += $cartItem['tax']*$cartItem['quantity'];
                                                            $shipping += $cartItem['shipping']*$cartItem['quantity'];
                                                            if(Session::get('shipping', 0) == 0){
                                                                $shipping = 0;
                                                            }

                                                            // dd($products);
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <span class="media">

                                                                    <div class="media-body">
                                                                        {{ $cartItem['name'] }}
                                                                    </div>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="">
                                                                    <input type="number" class="form-control px-0 text-center" placeholder="1" id="qtyNew-{{ $key }}" value="{{ $cartItem['quantity'] }}" onchange="updateQuantityNew({{ $key }})" min="1">
                                                                </div>
                                                            </td>
                                                            <td>{{ $cartItem['price'] }}</td>
                                                            <td>{{ $cartItem['price']*$cartItem['quantity'] }}</td>
                                                            <td class="text-right">
                                                                <button type="button" class="btn btn-circle btn-icon btn-sm btn-danger" onclick="removeFromCartNew({{ $key }})">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        {{-- <tr>
                                                            <td colspan="5" class="text-center">
                                                                <i class="las la-frown la-3x opacity-50"></i>
                                                                <p>No Product Added</p>
                                                            </td>
                                                        </tr> --}}
                                                        @endforelse

                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer bord-top">
                                        <table class="table mb-0 mar-no" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">{{ trans('labels.Subtotal')}}</th>
                                                    <th class="text-center">{{ trans('labels.Tax')}}</th>
                                                    <th class="text-center">{{ trans('labels.ShippingCost')}}</th>
                                                    <th class="text-center">{{ trans('labels.Discount')}}</th>
                                                    <th class="text-center">{{ trans('labels.Total')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">{{ $subtotal }}</td>
                                                    <td class="text-center">{{ $tax }}</td>
                                                    <td class="text-center">{{ $shipping }}</td>
                                                    <td class="text-center">{{ Session::get('pos_discount', 0) }}</td>
                                                    <td class="text-center">
                                                        {{ $subtotal+$tax+$shipping - Session::get('pos_discount', 0) }}
                                                        <input type="hidden" name="total_price" value="{{ $subtotal+$tax+$shipping - Session::get('pos_discount', 0) }}">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="pos-footer mar-btm">
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex">
                                            {{-- <div class="dropdown mr-3 dropup">
                                                <button class="btn btn-outline-dark btn-styled dropdown-toggle" type="button" data-toggle="dropdown">
                                                    Shipping
                                                </button>
                                                <div class="dropdown-menu p-3 dropdown-menu-lg">
                                                    <div class="radio radio-inline" style="margin-left: 10px;">
                                                        <input type="radio" name="shipping" id="radioExample_2a" value="0" checked onchange="setShipping()">
                                                        <label for="radioExample_2a">Without Shipping Charge</label>
                                                    </div>

                                                    <div class="radio radio-inline">
                                                        <input type="radio" name="shipping" id="radioExample_2b" value="1" onchange="setShipping()">
                                                        <label for="radioExample_2b">With Shipping Charge</label>
                                                    </div>
                                                </div>
                                            </div> --}}
                                            <div class="dropdown dropup">
                                                <button class="btn btn-outline-dark btn-styled dropdown-toggle" type="button" data-toggle="dropdown">
                                                    {{ trans('labels.Discount')}}
                                                </button>
                                                <div class="dropdown-menu p-3 dropdown-menu-lg">
                                                    <div class="input-group">
                                                        <input type="number" min="0" placeholder="Amount" name="discount" class="form-control" value="{{ Session::get('pos_discount', 0) }}" required onchange="setDiscount()">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">Flat</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="">
                                            <button type="button" class="btn btn-primary" data-target="#order-confirm" data-toggle="modal">{{ trans('labels.Pay with cashe')}}</button>
                                            <button type="button" class="btn btn-primary" data-target="#order-confirm-visa" data-toggle="modal">{{ trans('labels.Pay with visa')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{-- </form> --}}
                </div>
            </div>
            <!-- /.col -->
        </div>

        <!-- /.row -->


        <!-- Address Modal -->
        <div id="new-customer" class="modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
                <div class="modal-content">
                    <div class="modal-header bord-btm">
                        <h4 class="modal-title h6">Shipping Address</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body" id="shipping_address">


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal" id="close-button">Close</button>
                        <button type="button" class="btn btn-primary btn-styled btn-base-1" data-dismiss="modal">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- new address modal -->
        <div id="new-address-modal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
                <div class="modal-content">
                    <div class="modal-header bord-btm">
                        <h4 class="modal-title h6">Shipping Address</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="customer_id" id="set_customer_id" value="{{ Session::get('customerData')->id ?? '' }}">
                            <div class="form-group">
                                <div class=" row">
                                    <label class="col-sm-2 control-label" for="address">Address</label>
                                    <div class="col-sm-10">
                                        <textarea placeholder="Address" id="address" name="address" class="form-control" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class=" row">
                                    <label class="col-sm-2 control-label" for="email">Country</label>
                                    <div class="col-sm-10">
                                        <input type="hidden" name="country" value="">

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class=" row">
                                    <label class="col-sm-2 control-label" for="city">City</label>
                                    <div class="col-sm-10">
                                        <input type="text" placeholder="City" id="city" name="city" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class=" row">
                                    <label class="col-sm-2 control-label" for="postal_code">Postal code</label>
                                    <div class="col-sm-10">
                                        <input type="number" min="0" placeholder="Postal code" id="postal_code" name="postal_code" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class=" row">
                                    <label class="col-sm-2 control-label" for="phone">Phone</label>
                                    <div class="col-sm-10">
                                        <input type="number" min="0" value="{{ Session::get('customerData')->phone ?? '' }}" placeholder="Phone" id="phone" name="phone" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-styled btn-base-1">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="product-variation" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-lg">
                <div class="modal-content" id="variants">

                </div>
            </div>
        </div>

        <div id="order-confirm" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
                <div class="modal-content" id="variants">
                    <div class="modal-header bord-btm">
                        <h4 class="modal-title h6">Order Confirmation</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure to confirm this order?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal">Close</button>
                        <button type="button" onclick="submitOrder('cash')" class="btn btn-styled btn-base-1 btn-primary">Comfirm Order</button>
                    </div>
                </div>
            </div><!-- /.modal-dialog -->
        </div>

        <div id="order-confirm-visa" class="modal fade">
            <div class="modal-dialog modal-dialog-centered modal-dialog-zoom">
                <div class="modal-content" id="variants">
                    <div class="modal-header bord-btm">
                        <h4 class="modal-title h6">Order Confirmation</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure to confirm this order?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal">Close</button>
                        <button type="button" onclick="submitOrder('visa')" class="btn btn-styled btn-base-1 btn-primary">Comfirm Order</button>
                    </div>
                </div>
            </div><!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <!-- Main row -->

        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

@endsection

@section('script')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script type="text/javascript">

        var products = null;

        $(document).ready(function(){
            $('#container').removeClass('mainnav-lg').addClass('mainnav-sm');
            $('#product-list').on('click','.product-card',function(){
                var id = $(this).data('id');
                $.get('{{ route('variants') }}', {id:id}, function(data){
                    if (data == 0) {
                        addToCart(id, null, 1);
                    }
                    else {
                        $('#variants').html(data);
                        $('#product-variation').modal('show');
                    }
                });
            });
            filterProducts();
            getShippingAddress();

        });

        function filterProducts(){
            var keyword = $('input[name=keyword]').val();
            var category = $('select[name=poscategory]').val();
            var brand = $('select[name=brand]').val();
            $.get('{{ route('pos.search_product') }}',{keyword:keyword, category:category, brand:brand}, function(data){
                products = data;
                $('#product-list').html(null);
                // console.log(products.products.paginate);
                setProductList(data);
            });
        }

        function loadMoreProduct(){
            // console.log(products);
            if(products != null && products.products.paginate.next_page_url != null){
                $.get(products.products.paginate.next_page_url,{}, function(data){
                    products = data;
                    // console.log(products);
                    setProductList(data);
                });
            }
        }

        function setProductList(data){
            for (var i = 0; i < data.products.paginate.data.length; i++) {
                $('#product-list').append('<div class="col-md-4">' +
                    '<div class="card bg-light c-pointer mb-2 product-card" data-id="'+data.products.paginate.data[i].products_id+'" >'+
                        '<span class="absolute-top-left bg-dark text-white px-1">'+data.products.paginate.data[i].products_price +'</span>'+
                        '<img src="{{asset('')}}'+ data.products.paginate.data[i].image_path +'" class="card-img-top img-fit h-100px mw-100 mx-auto" >'+
                        '<div class="card-body p-2">'+
                            '<div class="text-truncate-2 small">'+ data.products.paginate.data[i].products_name +'</div>'+
                        '</div>'+
                    '</div>'+
                '</div>');
            }
            if (data.products.paginate.next_page_url != null) {
                $('#load-more').find('.text-center').html('Load More');
            }
            else {
                $('#load-more').find('.text-center').html('Nothing more found');
            }
            $('[data-toggle="tooltip"]').tooltip();
        }

        function removeFromCart(key){
            $.post('{{ route('pos.removeFromCart') }}', {_token:'{{ csrf_token() }}', key:key}, function(data){
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function removeFromCartNew(key){
            $.post('{{ route('pos.removeFromCartNew') }}', {_token:'{{ csrf_token() }}', key:key}, function(data){
                // location.reload();
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function addToCart(product_id, variant, quantity){
            $.post('{{ route('pos.addToCart') }}',{_token:'{{ csrf_token() }}', product_id:product_id, variant:variant, quantity, quantity}, function(data){
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function addVariantProductToCart(id){
            var variant = $('input[name=variant]:checked').val();
            addToCart(id, variant, 1);
        }

        function updateQuantity(key){
            $.post('{{ route('pos.updateQuantity') }}',{_token:'{{ csrf_token() }}', key:key, quantity: $('#qty-'+key).val()}, function(data){
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function updateQuantityNew(key){
            $.post('{{ route('pos.updateQuantityNew') }}',{_token:'{{ csrf_token() }}', key:key, quantity: $('#qtyNew-'+key).val()}, function(data){
                // location.reload();
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function setDiscount(){
            var discount = $('input[name=discount]').val();
            $.post('{{ route('pos.setDiscount') }}',{_token:'{{ csrf_token() }}', discount:discount}, function(data){
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function setShipping(){
            var shipping = $('input[name=shipping]:checked').val();
            $.post('{{ route('pos.setShipping') }}',{_token:'{{ csrf_token() }}', shipping:shipping}, function(data){
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function getShippingAddress(){

            $.post('{{ route('pos.getShippingAddress') }}',{_token:'{{ csrf_token() }}', id:$('select[name=user_id]').val()}, function(data){
                $('#shipping_address').html(data);
                $(".select3").select2();
            });
        }

        function add_new_address(){
             var customer_id = $('#customer_id').val();
            $('#set_customer_id').val(customer_id);
            $('#new-address-modal').modal('show');
            $("#close-button").click();
        }

        function submitOrder(payment_type){
            var user_id = $('select[name=user_id]').val();
            var first_name = $('input[name=first_name]').val();
            var last_name = $('input[name=last_name]').val();
            var email = $('input[name=email]').val();
            var shipping_address = $('textarea[name=address]').val();
            // var country = $('select[name=country]').val();
            var country = $('input[name=country]').val();
            var city = $('input[name=city]').val();
            var postal_code = $('input[name=postal_code]').val();
            var phone = $('input[name=phone]').val();
            var comment = $('textarea[name=comment]').val();
            var shipping = $('input[name=shipping]:checked').val();
            var discount = $('input[name=discount]').val();
            var address = $('input[name=address_id]:checked').val();
            var total_price = $('input[name=total_price]').val();
            var customer_id = $('input[name=customer_id]').val() != '' ? $('input[name=customer_id]').val() : $('select[name=customer]').val();

            $.post('{{ route('pos.order_place') }}',
            {
                _token:'{{ csrf_token() }}',
                user_id:user_id,
                first_name:first_name,
                last_name:last_name,
                email:email,
                address:address,
                country:country,
                city:city,
                postal_code:postal_code,
                phone:phone,
                shipping_address:shipping_address,
                payment_type:payment_type,
                shipping:shipping,
                discount:discount,
                total_price:total_price,
                comment: comment,
                customer_id: customer_id
            })
            .done(function(data) {
                if(data.data == 1){
                    // AIZ.plugins.notify('success', '{{ trans('labels.Order Completed Successfully.') }}');
                    swal("success!", "{{ trans('labels.Order Completed Successfully.') }}", "success");
                    // location.reload();
                    window.location.href = data.print_url;
                } else if(data.status == 2) {
                    swal("", data.message, "error");
                } else{
                    // AIZ.plugins.notify('danger', '{{ trans('labels.Something went wrong') }}');
                    swal("", "{{ trans('labels.Something went wrong') }}", "error");
                }
            })
            .fail(function() {
                swal("", "{{ trans('labels.Something went wrong') }}", "error");
            });
        }
    </script>
@endsection

@section('style')
<style>
    .d-flex {
        display: -ms-flexbox!important;
        display: flex!important;
    }

    .flex-grow-1 {
        -ms-flex-positive: 1!important;
        flex-grow: 1!important;
    }

    .d-block {
        display: block!important;
    }

    .justify-content-between {
        -ms-flex-pack: justify!important;
        justify-content: space-between!important;
    }

    .card {
        -webkit-box-shadow: 0 0 13px 0 rgb(82 63 105 / 5%);
        box-shadow: 0 0 13px 0 rgb(82 63 105 / 5%);
        margin-bottom: 20px;
        border-color: #ebedf2;
        position: relative;
        display: flex;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgba(0,0,0,.125);
        border-radius: .25rem;
    }

    .card .card-header {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
        position: relative;
        padding: 12px 25px;
        border-bottom: 1px solid #ebedf2;
        min-height: 50px;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        background-color: transparent;
    }

    .card-header:first-child {
        border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0;
        margin-bottom: 0;
    }

    .card .card-body {
        /* padding: 20px 25px; */
        padding: 10px 10px;
        border-radius: 4px;
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        /* min-height: 1px; */
        min-height: 6rem;
    }

    .aiz-pos-product-list {
        overflow-y: auto;
        max-height: calc(100vh - 365px);
        height: calc(100vh - 365px);
        overflow-x: hidden;
    }

    .gutters-5 {
        margin-right: -5px;
        margin-left: -5px;
    }

    .c-pointer {
        cursor: pointer !important;
    }

    .bg-dark {
        background-color: #111723 !important;
        color: #fff;
    }

    .absolute-top-left {
        position: absolute;
        top: 0;
        left: 0;
    }

    .img-fit {
        max-height: 100%;
        width: 100%;
        object-fit: cover;
        height: 10rem;
    }

    .text-truncate-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .btn {
        padding: 0.6rem 1.2rem;
        /* font-size: 0.875rem; */
        color: #2a3242;
        font-weight: inherit;
    }

    .btn-outline-dark {
        border-color: #111723;
        color: #111723;
    }

    .dropdown-toggle {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
    }

    a, button, input, textarea, .btn, .has-transition {
        -webkit-transition: all 0.3s ease;
        transition: all 0.3s ease;
    }

    .dropdown-toggle {
        white-space: nowrap;
    }

    .mr-3, .mx-3 {
        margin-right: 1rem!important;
    }

    .dropdown, .dropleft, .dropright, .dropup {
        position: relative;
    }

    .dropup .dropdown-toggle::after {
        display: inline-block;
        margin-left: .255em;
        vertical-align: .255em;
        content: "";
        border-top: 0;
        border-right: .3em solid transparent;
        border-bottom: .3em solid;
        border-left: .3em solid transparent;
    }

    /* .dropup .dropdown-toggle::after {
        border: 0;
        content: "\f106";
    } */

    .dropdown-toggle::after {
        border: 0;
        content: "\f107";
        font-family: "Line Awesome Free";
        font-weight: 900;
        font-size: 80%;
        margin-left: 0.3rem;
    }

    .dropdown-menu.dropdown-menu-lg {
        width: 320px;
        min-width: 320px;
    }

    .dropup .dropdown-menu {
        top: auto;
        bottom: 100%;
        margin-top: 0;
        margin-bottom: .125rem;
    }

    .dropdown-menu {
        border-color: #e2e5ec;
        margin: 0;
        border-radius: 0;
        min-width: 14rem;
        font-size: inherit;
        padding: 0;
        -webkit-box-shadow: 0 0 50px 0 rgb(82 63 105 / 15%);
        box-shadow: 0 0 50px 0 rgb(82 63 105 / 15%);
        padding: 0.5rem 0;
        border-radius: 4px;
        max-width: 100%;
    }

    .input-group {
        position: relative;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        /* flex-wrap: wrap; */
        -ms-flex-align: stretch;
        align-items: stretch;
        width: 100%;
    }

    .input-group-append {
        margin-left: -1px;
    }

    .input-group-append, .input-group-prepend {
        display: -ms-flexbox;
        display: flex;
    }

    .input-group>.input-group-append>.btn, .input-group>.input-group-append>.input-group-text, .input-group>.input-group-prepend:first-child>.btn:not(:first-child), .input-group>.input-group-prepend:first-child>.input-group-text:not(:first-child), .input-group>.input-group-prepend:not(:first-child)>.btn, .input-group>.input-group-prepend:not(:first-child)>.input-group-text {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .input-group-text {
        padding: 0.6rem 1rem;
        font-size: 0.875rem;
        line-height: 1.5;
        color: #74788d;
        background-color: #f7f8fa;
        border: 1px solid #e2e5ec;
        border-radius: 4px;
    }

    .input-group-text {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        padding: .375rem .75rem;
        margin-bottom: 0;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        text-align: center;
        white-space: nowrap;
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        border-radius: .25rem;
    }

    .bg-soft-primary {
        background-color: rgba(55, 125, 255, 0.15) !important;
    }

    /* .fs-14 {
        font-size: 0.875rem !important;
    } */
    .fw-600 {
        font-weight: 600 !important;
    }

    .bg-light {
        background-color: #f2f3f8 !important;
    }

</style>
@endsection
