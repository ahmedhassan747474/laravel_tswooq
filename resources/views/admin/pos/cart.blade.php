<div class="panel-body card-body">
    <div class="aiz-pos-cart-list c-scrollbar c-scrollbar-light">
        <table class="table table_class table-bordered mb-0 mar-no" cellspacing="0" width="100%">
            <thead class="thead_class">
                <tr>
                    <th >{{ trans('labels.productName')}}</th>
                    <th >{{ trans('labels.Qty')}}</th>
                    <th>{{ trans('labels.Price')}}</th>
                    <th>{{ trans('labels.Subtotal')}}</th>
                    <th class="text-right">{{ trans('labels.delete')}}</th>
                </tr>
            </thead>
            <tbody class="tbody_class">
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
                                ->where('products_description.language_id', 2)
                                ->first();
                            // dd($products);
                        @endphp
                        <tr>
                            <td>
                                <span class="media">
                                    <div class="media-left">
                                        <img class="mr-3" height="60" src="{{asset(''). $products->path??'' }}" >
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
    <table class="table mar-no" cellspacing="0" width="100%">
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
