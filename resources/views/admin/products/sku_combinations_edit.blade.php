@if(count($combinations[0]) > 0)
	<table class="table table-bordered aiz-table">
		<thead>
		<tr>
			<td class="text-center">
				{{trans('labels.Variant')}}
			</td>
			<td class="text-center">
				{{trans('labels.Variant Price')}}
			</td>
			<td class="text-center" data-breakpoints="lg">
				{{trans('labels.Barcode')}}
			</td>
			<td class="text-center" data-breakpoints="lg">
				{{trans('labels.Quantity')}}
			</td>
			<td class="text-center" data-breakpoints="lg">
				{{trans('labels.POS_Price')}}
			</td>
			<td class="text-center" data-breakpoints="lg">
				{{trans('labels.POS_Quantity')}}
			</td>
			<td class="text-center" data-breakpoints="lg">
				{{trans('labels.Purchase Price')}}
			</td>
			<td class="text-center" data-breakpoints="lg">
				{{ trans('labels.Image') }}
			</td>
			<td></td>
		</tr>
		</thead>
		<tbody>

		@foreach ($combinations as $key => $combination)
			@php
				$sku = '';
                foreach (explode(' ', $product_name) as $key => $value) {
                    $sku .= substr($value, 0, 1);
                }

                $str = '';
                foreach ($combination as $key => $item){
                    if($key > 0 ){
                        $str .= '-'.str_replace(' ', '', $item);
                        $sku .='-'.str_replace(' ', '', $item);
                    }
                    else{
                        if($colors_active == 1){
                            $color_name = \App\Color::where('code', $item)->first()->name;
                            $str .= $color_name;
                            $sku .='-'.$color_name;
                        }
                        else{
                            $str .= str_replace(' ', '', $item);
                            $sku .='-'.str_replace(' ', '', $item);
                        }
                    }
                }
			@endphp
			@if(strlen($str) > 0)
				<tr class="variant">
					<td>
						<label for="" class="control-label">{{ $str }}</label>
					</td>
					<td>
						<input type="number" lang="en" name="price_{{ $str }}" value="@php
                    if ($product->unit_price == $unit_price) {
						if(($stock = $product->stocks->where('variant', $str)->first()) != null){
	                        echo $stock->price;
	                    }
	                    else{
	                        echo $unit_price;
	                    }
                    }
					else{
						echo $unit_price;
					}
                @endphp" min="0" step="0.01" class="form-control" required>
					</td>
					<td>
						<input type="text" name="sku_{{ $str }}" value="@php
					if(($stock = $product->stocks->where('variant', $str)->first()) != null){
						echo $stock->sku;
					}
					else{
						echo $str;
					}
				@endphp" class="form-control">
					</td>
					<td>
						<input type="number" lang="en" name="qty_{{ $str }}" value="@php
                    if(($stock = $product->stocks->where('variant', $str)->first()) != null){
                        echo $stock->qty;
                    }
                    else{
                        echo '10';
                    }
                @endphp" min="0" step="1" class="form-control" required>
					</td>

					<td>
						<input type="number" lang="en" name="pos_price_{{ $str }}" value=
								"@php
                    if ($product->unit_price == $unit_price) {
						if(($stock = $product->stocks->where('variant', $str)->first()) != null){
	                        echo $stock->pos_price;
	                    }
	                    else{
	                        echo $unit_price;
	                    }
                    }
					else{
						echo $unit_price;
					}
                @endphp" min="0" step="0.01" class="form-control" required>
					</td>

					<td>
						<input type="number" lang="en" name="pos_qty_{{ $str }}" value="
						@php
                    if(($stock = $product->stocks->where('variant', $str)->first()) != null){
                        echo $stock->pos_qty;
                    }
                    else{
                        echo '10';
                    }
                @endphp" min="0" step="1" class="form-control" required>
					</td>
					<td>
						<input type="number" lang="en" name="Purchase_price_{{ $str }}" value="
						@php
                    if(($stock = $product->stocks->where('variant', $str)->first()) != null){
                        echo $stock->Purchase_price;
                    }
                    else{
                        echo '0';
                    }
                @endphp" min="0" step="1" class="form-control" required>
					</td>
					<td>

						<div class="col-xs-12 col-md-6 ">
							<input type="file" name="img_{{ $str }}" class="form-control" onchange="readURL(this);"/>
							<img id="img_{{ $str }}" src="#" alt=""/>


						</div>
					</td>
				</tr>
			@endif
		@endforeach

		</tbody>
	</table>
@endif
