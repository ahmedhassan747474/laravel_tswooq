<div class="modal-header bord-btm">
    {{-- <h4 class="modal-title h6">{{ translate('Select variation') }} - {{ $stocks->first()->product->getTranslation('name') }}</h4> --}}
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="row gutters-5">
        @foreach ($stocks as $key => $stock)
        @php
        $img='';
        // if($stock->image){
        //     $img = \App\Upload::findOrFail($stock->image)->file_name??'';
        // }
        @endphp
            <div class="col-lg-3 col-sm-6">
                <label class="aiz-megabox d-block">
                    <input type="checkbox" class="variant" name="variant" value="{{ $stock->variant }}" @if ($stock->pos_qty <= 0)
                        disabled
                    @endif>
                    <span class="d-flex p-2 pad-all aiz-megabox-elem">
                        <span class="aiz-rounded-check flex-shrink-0 @if ($stock->pos_qty > 0)
                            mt-1
                        @endif"></span>
                        <span class="flex-grow-1 pad-lft pl-2">
                            <span class="d-block strong-600">{{  str_replace('_', ' ', $stock->variant)  }}</span>
                            <span class="d-block">Price: {{ $stock->pos_price }}</span>
                            <span class="badge badge-inline @if ($stock->pos_qty <= 0)
                                badge-secondary
                            @else
                                badge-success
                            @endif">Stock: {{ $stock->pos_qty }}</span>
                            @if ($img)
                            <img style="width: 119px;height: 65px;" src="{{ asset('public/'.$img) }}">
                            @endif

                        </span>
                    </span>
                </label>
            </div>
        @endforeach
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal">Close</button>
    <button type="button" onclick="addVariantProductToCart({{ $stocks->first()->product_id }})" class="btn btn-primary btn-styled btn-base-1">Add Product</button>
</div>
