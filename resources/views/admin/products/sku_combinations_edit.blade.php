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
				<input type="number" lang="en" name="pos_price_{{ $str }}" value="@php
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
				<input type="number" lang="en" name="pos_qty_{{ $str }}" value="@php
                    if(($stock = $product->stocks->where('variant', $str)->first()) != null){
                        echo $stock->pos_qty;
                    }
                    else{
                        echo '10';
                    }
                @endphp" min="0" step="1" class="form-control" required>
			</td>
			<td>
				{{-- <div class=" input-group " data-toggle="aizuploader" data-type="image">
					<div class="input-group-prepend">
						<div class="input-group-text bg-soft-secondary font-weight-medium">{{ trans('Browse') }}</div>
					</div>
					<div class="form-control file-amount text-truncate">{{ trans('Choose File') }}</div>
					<input type="hidden" name="img_{{ $str }}" class="selected-files" value="@php
	                    if(($stock = $product->stocks->where('variant', $str)->first()) != null){
	                        echo $stock->image;
	                    }
	                    else{
	                        echo null;
	                    }
	                @endphp">
				</div>
				<div class="file-preview"></div> --}}

				<div class="col-xs-12 col-md-6 ">
					<input type="file" name="img_{{ $str }}" class="form-control" onchange="readURL(this);" />
					<img id="img_{{ $str }}" src="#" alt="" />

					{{-- <div class="form-group">
						<label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Image') }}<span style="color:red;">*</span></label>
						<div class="col-sm-10 col-md-8">
							<!-- Modal -->
						
							<div class="modal fade" id="Modalmanufactured_{{ $str }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" id="closemodal" aria-label="Close"><span aria-hidden="true">×</span></button>
											<h3 class="modal-title text-primary" id="myModalLabel">{{ trans('labels.Choose Image') }}</h3>
										</div>
										<div class="modal-body manufacturer-image-embed">
											@if(isset($allimage))
										
											<select class="image-picker show-html " name="img_{{ $str }}" id="select_img">
												<option value=""></option>
												@foreach($allimage as $key=>$image)
												<option data-img-src="{{asset($image->path)}}" class="imagedetail" data-img-alt="{{$key}}" value="{{$image->id}}"> {{$image->id}} </option>
												@endforeach
											</select>
											@endif
										</div>
										<div class="modal-footer">

											<a href="{{url('admin/media/add')}}" target="_blank" class="btn btn-primary pull-left">{{ trans('labels.Add Image') }}</a>
											<button type="button" class="btn btn-default refresh-image"><i class="fa fa-refresh"></i></button>
											<button type="button" class="btn btn-primary" value="{{ $str }}" id="selected2" data-dismiss="modal">Done</button>
										</div>
									</div>
								</div>
							</div>


							<div id="imageselected{{ $str }}">
							  {!! Form::button( trans('labels.Add Image'), array('id'=>'newImage{{$str}}','class'=>"btn btn-primary", 'data-toggle'=>"modal", 'data-target'=>"#Modalmanufactured_$str" )) !!}
							  <br>
							  <div id="selectedthumbnail{{ $str }}" class="selectedthumbnail col-md-5"> </div>
							  <div class="closimage">
								  <button type="button" class="close pull-left image-close " id="image-close{{ $str }}"
									style="display: none; position: absolute;left: 105px; top: 54px; background-color: black; color: white; opacity: 2.2; " aria-label="Close">
									  <span aria-hidden="true">&times;</span>
								  </button>
							  </div>
							</div>
							<span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.UploadProductImageText') }}</span>

						</div>
					</div> --}}
				</div>
			</td>
			{{-- <td>
				<button style="height: 2.7em;width: 2.7em;" type="button" class="btn btn-icon btn-sm btn-danger" onclick="delete_variant(this)"><i class="las la-trash"></i></button>
			</td> --}}
		</tr>
	@endif
@endforeach

	</tbody>
</table>
@endif
