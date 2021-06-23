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
                @if (Session::has('posCardCart'))
                    @php
                        $subtotal = 0;
                        $tax = 0;
                    @endphp
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
                                        {{ $cartItem['name'] }}
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
