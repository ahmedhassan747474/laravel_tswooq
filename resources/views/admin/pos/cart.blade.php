<div class="panel-body card-body">
    <div class="aiz-pos-cart-list c-scrollbar c-scrollbar-light">
        <table class="table table-bordered mb-0 mar-no" cellspacing="0" width="100%">
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
                @if (Session::has('posCart'))
                    @php
                        $subtotal = 0;
                        $tax = 0;
                        $shipping = 0;
                    @endphp
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
                                <button type="button" class="btn btn-circle btn-icon btn-sm btn-danger" onclick="removeFromCart({{ $key }})"><i class="fa fa-trash"></i></button>
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
    <table class="table mar-no" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="text-center">Sub Total</th>
                <th class="text-center">Total Tax</th>
                <th class="text-center">Total Shipping</th>
                <th class="text-center">Discount</th>
                <th class="text-center">Total</th>
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