@extends('admin.layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> {{ trans('labels.POS Like Card') }} </h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
            <li class="active">{{ trans('labels.POS Like Card') }}</li>
        </ol>
    </section>
    
    
    <section class="content-header">
        <span style="font-size: 22px;"> Limit Value </span> : <span style="color: red;font-size: 15px;font-weight: 600;">{{ auth()->user()->role_id != 1 ? DB::table('users')->where('id',auth()->user()->parent_admin_id)->first()->like_limit : 'Un limited'}}</span>
        
    </section>
    

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->

        <!-- /.row -->

        <div class="row">
            <div class="col-md-12">
                <div class="container-fluid">
                    <form class="" action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row gutters-10">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header d-block">
                                        {{-- <div class="form-group">
                                            <input class="form-control form-control-sm" type="text" name="keyword" placeholder="Search by Product Name" onkeyup="filterProducts()">
                                        </div> --}}
                                        <div class="row gutters-5">
                                            <div class="col-md-4">
                                                <select name="poscategory" id="poscategory" class="form-control form-control-sm aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                                                    <option value="">All Categories</option>
                                                    @if($results['categories']->response == 1)
                                                    @foreach ($results['categories']->data as $category)
                                                        <option value="{{ $category->id }}">{{ $category->categoryName }}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select name="possubcategory" id="possubcategory" class="form-control form-control-sm aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                                                    <option value="all">All Sub Categories</option>
                                                    @if($results['categories']->response == 1)
                                                    @foreach ($results['categories']->data as $category)
                                                    @if(count($category->childs))
                                                    @foreach($category->childs as $child)
                                                        <option value="{{ $child->id }}">{{ $child->categoryName }}</option>
                                                    @endforeach
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <select name="possubofsubcategory" id="possubofsubcategory" class="form-control form-control-sm aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                                                    <option value="all">All Sub of Sub Categories</option>
                                                    @if($results['categories']->response == 1)
                                                    @foreach ($results['categories']->data as $category)
                                                    @if(count($category->childs))
                                                    @foreach($category->childs as $child)
                                                    @if(count($child->childs))
                                                    @foreach($child->childs as $subchild)
                                                        <option value="{{ $subchild->id }}">{{ $subchild->categoryName }}</option>
                                                    @endforeach
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="aiz-pos-product-list c-scrollbar-light">
                                            <div class="row gutters-5" id="product-list">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <select name="user_id" class="form-control form-control-sm aiz-selectpicker pos-customer" data-live-search="true" onchange="getShippingAddress()">
                                                    <option value="" selected disabled>Walk In Customer</option>
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
                                    </div>
                                </div>
                                <div class="card mar-btm" id="cart-details">
                                    <div class="card-body">
                                        <div class="aiz-pos-cart-list c-scrollbar-light">
                                            <table class="table aiz-table mb-0 mar-no" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th width="50%">Product</th>
                                                        <th width="15%">QTY</th>
                                                        <th>Price</th>
                                                        <th>Subtotal</th>
                                                        <th class="text-right">Remove</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $subtotal = 0;
                                                        $tax = 0;
                                                    @endphp
                                                    @if (Session::has('posCardCart'))
                                                        @forelse (Session::get('posCardCart') as $key => $cartItem)
                                                            @php
                                                                $subtotal += $cartItem['price']*$cartItem['quantity'];
                                                                $tax += $cartItem['tax']*$cartItem['quantity'];
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    <span class="media">
                                                                        <div class="media-left">
                                                                            <img class="mr-3" height="60" src="{{ $cartItem['image'] }}" >
                                                                        </div>
                                                                        <div class="media-body">
                                                                            {{ $cartItem['name'] }})
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
                                                            <tr>
                                                                <td colspan="5" class="text-center">
                                                                    <i class="las la-frown la-3x opacity-50"></i>
                                                                    <p>No Product Added</p>
                                                                </td>
                                                            </tr>
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
                                                    <th class="text-center">Sub Total</th>
                                                    <th class="text-center">Total Tax</th>
                                                    <th class="text-center">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">{{ $subtotal }}</td>
                                                    <td class="text-center">{{ $tax }}</td>
                                                    <td class="text-center">
                                                        {{ $subtotal+$tax }}
                                                        <input type="hidden" name="total_price" value="{{ $subtotal+$tax }}">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="pos-footer mar-btm">
                                    <div class="d-flex justify-content-between">

                                        <div class="">
                                            <button type="button" class="btn btn-primary" data-target="#order-confirm" data-toggle="modal">Pay With Cash</button>
                                            <button type="button" class="btn btn-primary" data-target="#order-confirm-visa" data-toggle="modal">Pay With Visa</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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
                            <input type="hidden" name="customer_id" id="set_customer_id" value="">
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
                                        <input type="number" min="0" placeholder="Phone" id="phone" name="phone" class="form-control" required>
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
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">??</span></button>
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
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">??</span></button>
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
                var name = $(this).data('name');
                var price = $(this).data('price');
                var image = $(this).data('image');
                var currency = $(this).data('currency');
                $.get('{{ route('variants_card') }}', {id:id}, function(data){
                    if (data == 0) {
                        addToCart(id, name, price, image, currency, 1);
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
            var poscategory = $('select[name=poscategory]').val();
            var possubcategory = $('select[name=possubcategory]').val();
            var possubofsubcategory = $('select[name=possubofsubcategory]').val();
            // console.log(keyword);
            // console.log(poscategory);
            // console.log(possubcategory);
            $.get('{{ route('pos_card.search_product') }}',{keyword:keyword, poscategory:poscategory, possubcategory:possubcategory,possubofsubcategory:possubofsubcategory}, function(data){
                products = data;
                
                $('#product-list').html(null);
                console.log(products);
                $('#possubcategory').html(null);
                $('#possubofsubcategory').html(null);

                $('#possubcategory').append('<option value="all">All</option>');
                $('#possubofsubcategory').append('<option value="all">All</option>');

                for (var i = 0; i < data.subcategories.length; i++) {
                    $('#possubcategory').append('<option value="'+ data.subcategories[i].id +'">'+data.subcategories[i].categoryName+'</option>');
                }
                for (var i = 0; i < data.subsubcategories.length; i++) {
                    $('#possubofsubcategory').append('<option value="'+ data.subsubcategories[i].id +'">'+data.subsubcategories[i].categoryName+'</option>');
                }
                setProductList(data.products);
            });
        }

        // function loadMoreProduct(){
        //     // console.log(products);
        //     if(products != null && products.products.paginate.next_page_url != null){
        //         $.get(products.products.paginate.next_page_url,{}, function(data){
        //             products = data;
        //             // console.log(products);
        //             setProductList(data);
        //         });
        //     }
        // }

        function setProductList(result){
            // console.log(result);
            // console.log(result.data.length);
            for (var i = 0; i < result.data.length; i++) {
                $('#product-list').append('<div class="col-md-4">' +
                    '<div class="card bg-light c-pointer mb-2 product-card" data-id="'+result.data[i].productId+'" data-name="'+result.data[i].productName+'" data-price="'+result.data[i].sellPrice+'" data-image="'+result.data[i].productImage+'" data-currency="'+result.data[i].productCurrency+'">'+
                        '<span class="absolute-top-left bg-dark text-white px-1">'+result.data[i].sellPrice +'</span>'+
                        '<img src="'+ result.data[i].productImage +'" class="card-img-top img-fit h-100px mw-100 mx-auto" >'+
                        '<div class="card-body p-2">'+
                            '<div class="text-truncate-2 small">'+ result.data[i].productName +'</div>'+
                        '</div>'+
                    '</div>'+
                '</div>');
            }
            // if (data.products.paginate.next_page_url != null) {
            //     $('#load-more').find('.text-center').html('Load More');
            // }
            // else {
            //     $('#load-more').find('.text-center').html('Nothing more found');
            // }
            $('[data-toggle="tooltip"]').tooltip();
        }

        function removeFromCart(key){
            $.post('{{ route('pos_card.removeFromCart') }}', {_token:'{{ csrf_token() }}', key:key}, function(data){
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function addToCart(product_id, product_name, product_price, product_image, product_currency, quantity){
            $.post('{{ route('pos_card.addToCart') }}',{_token:'{{ csrf_token() }}', product_id:product_id, product_name:product_name, product_price:product_price, product_image:product_image, product_currency: product_currency, quantity:quantity}, function(data){
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function addVariantProductToCart(id){
            var variant = $('input[name=variant]:checked').val();
            addToCart(id, variant, 1);
        }

        function updateQuantity(key){
            $.post('{{ route('pos_card.updateQuantity') }}',{_token:'{{ csrf_token() }}', key:key, quantity: $('#qty-'+key).val()}, function(data){
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        // function setDiscount(){
        //     var discount = $('input[name=discount]').val();
        //     $.post('{{ route('pos.setDiscount') }}',{_token:'{{ csrf_token() }}', discount:discount}, function(data){
        //         $('#cart-details').html(data);
        //         $('#product-variation').modal('hide');
        //     });
        // }

        // function setShipping(){
        //     var shipping = $('input[name=shipping]:checked').val();
        //     $.post('{{ route('pos_card.setShipping') }}',{_token:'{{ csrf_token() }}', shipping:shipping}, function(data){
        //         $('#cart-details').html(data);
        //         $('#product-variation').modal('hide');
        //     });
        // }

        function getShippingAddress(){

            $.post('{{ route('pos_card.getShippingAddress') }}',{_token:'{{ csrf_token() }}', id:$('select[name=user_id]').val()}, function(data){
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
            var customer_id = $('select[name=customer]').val();

            $.post('{{ route('pos_card.order_place') }}',
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
                    window.open(data.print_url, '_blank');
                    location.reload();                    // window.location.href = data.print_url;
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
