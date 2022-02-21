@extends('admin.layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> {{ trans('labels.AddProduct') }} <small>{{ trans('labels.AddProduct') }}...</small> </h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('admin/dashboard/this_month') }}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
            <li><a href="{{ URL::to('admin/products/display')}}"><i class="fa fa-database"></i> {{ trans('labels.ListingAllProducts') }}</a></li>
            <li class="active">{{ trans('labels.AddProduct') }}</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">{{ trans('labels.AddNewProduct') }} </h3>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box box-info">
                                    <!-- form start -->
                                    <div class="box-body">
                                        @if( count($errors) > 0)
                                        @foreach($errors->all() as $error)
                                        <div class="alert alert-danger" role="alert">
                                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                            <span class="sr-only">{{ trans('labels.Error') }}:</span>
                                            {{ $error }}
                                        </div>
                                        @endforeach
                                        @endif

                                        {!! Form::open(array('url' =>'admin/products/add', 'method'=>'post', 'class' => 'form-horizontal form-validate', 'id'=>'choice_form' , 'enctype'=>'multipart/form-data')) !!}

                                            {{-- <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Manufacturers') }} </label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <select class="form-control" name="manufacturers_id">
                                                            <option value="">{{ trans('labels.ChooseManufacturers') }}</option>
                                                            @foreach ($result['manufacturer'] as $manufacturer)
                                                            <option value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
                                                            @endforeach
                                                        </select><span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.ChooseManufacturerText') }}.</span>
                                                    </div>
                                                </div>
                                            </div> --}}
                                        
                                        <div class="row">

                                                @foreach($result['languages'] as $key=>$languages)
                                                    <div class="col-md-6">

                                                        <div style="margin-top: 15px;" class="tab-pane @if($key==0) active @endif" id="product_<?=$languages->languages_id?>">
                                                            <div class="">
                                                                <div class="form-group">
                                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.ProductName') }}<span style="color:red;">*</span> ({{ $languages->name }})</label>
                                                                    <div class="col-sm-10 col-md-8">
                                                                        <input type="text" name="products_name_<?=$languages->languages_id?>" class="form-control field-validate">
                                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                            {{ trans('labels.EnterProductNameIn') }} {{ $languages->name }} </span>
                                                                        <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group external_link" style="display: none">
                                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.External URL') }} ({{ $languages->name }})</label>
                                                                    <div class="col-sm-10 col-md-8">
                                                                        <input type="text" name="products_url_<?=$languages->languages_id?>" class="form-control products_url">
                                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                            {{ trans('labels.External URL Text') }} {{ $languages->name }} </span>
                                                                        <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Description') }}<span style="color:red;">*</span> ({{ $languages->name }})</label>
                                                                    <div class="col-sm-10 col-md-8">
                                                                        <textarea id="edito<?=$languages->languages_id?>" name="products_description_<?=$languages->languages_id?>" class="form-control" rows="5"></textarea>
                                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                            {{ trans('labels.EnterProductDetailIn') }} {{ $languages->name }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                @endforeach
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-2 control-label">{{ trans('labels.Category') }}<span style="color:red;">*</span></label>
                                                    <div class="col-sm-10 col-md-9">
                                                        <?php print_r($result['categories']); ?>
                                                        <input id="categories_0" type="hidden" class=" required_one categories sub_categories" name="categories[]" value="0">
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.ChooseCatgoryText') }}.</span>
                                                        <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            {{-- <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.IsFeature') }} </label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <select class="form-control" name="is_feature">
                                                            <option value="0">{{ trans('labels.No') }}</option>
                                                            <option value="1">{{ trans('labels.Yes') }}</option>
                                                        </select>
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.IsFeatureProuctsText') }}</span>
                                                    </div>
                                                </div>
                                            </div> --}}
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Status') }} </label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <select class="form-control" name="products_status">
                                                            <option value="1">{{ trans('labels.Active') }}</option>
                                                            <option value="0">{{ trans('labels.Inactive') }}</option>
                                                        </select>
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.SelectStatus') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.PriceBuy') }}<span style="color:red;">*</span></label>
                                                    <div class="col-sm-10 col-md-8">
                                                        {!! Form::text('price_buy', '', array('class'=>'form-control number-validate', 'id'=>'price_buy')) !!}
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.PriceBuyText') }}
                                                        </span>
                                                        <span class="help-block hidden">{{ trans('labels.PriceBuyText') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Tax') }}%<span style="color:red;">*</span></label>
                                                    <div class="col-sm-10 col-md-8">
                                                        {!! Form::text('tax', '', array('class'=>'form-control number-validate', 'id'=>'tax')) !!}
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.Tax') }}%
                                                        </span>
                                                        <span class="help-block hidden">{{ trans('labels.Tax') }}%</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Quantity') }}<span style="color:red;">*</span></label>
                                                    <div class="col-sm-10 col-md-8">
                                                        {!! Form::text('quantity', '', array('class'=>'form-control number-validate', 'id'=>'quantity')) !!}
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.Quantity') }}
                                                        </span>
                                                        <span class="help-block hidden">{{ trans('labels.Quantity') }}</span>
                                                    </div>
                                                </div>
                                            </div> --}}

                                            {{-- <div class="col-xs-12 col-md-6">
                                                <div class="form-group" id="tax-class">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.TaxClass') }} </label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <select class="form-control field-validate" name="tax_class_id">
                                                            <option selected>{{ trans('labels.SelectTaxClass') }}</option>
                                                            @foreach ($result['taxClass'] as $taxClass)
                                                            <option value="{{ $taxClass->tax_class_id }}">{{ $taxClass->tax_class_title }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.ChooseTaxClassForProductText') }}
                                                        </span>
                                                        <span class="help-block hidden">{{ trans('labels.SelectProductTaxClass') }}</span>
                                                    </div>
                                                </div>
                                            </div> --}}

                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.ProductsPrice') }}<span style="color:red;">*</span></label>
                                                    <div class="col-sm-10 col-md-8">
                                                        {!! Form::text('products_price', '', array('class'=>'form-control number-validate', 'id'=>'products_price')) !!}
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.ProductPriceText') }}
                                                        </span>
                                                        <span class="help-block hidden">{{ trans('labels.ProductPriceText') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Show Product') }}<span style="color:red;">*</span></label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <ul class="list-unstyled mb-0">
                                                            @if ($result['commonContent']['roles']!= null and $result['commonContent']['roles']->is_show_web == 1)
                                                                <li class="d-inline-block mr-2">
                                                                    <fieldset>
                                                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                                                            <input type="checkbox" name="is_show_web">
                                                                            <span class="vs-checkbox">
                                                                                <span class="vs-checkbox--check">
                                                                                    <i class="vs-icon feather icon-check"></i>
                                                                                </span>
                                                                            </span>
                                                                            <span class="">{{trans('labels.is_show_web')}}</span>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            @endif
                                                            @if ($result['commonContent']['roles']!= null and $result['commonContent']['roles']->is_show_app == 1)
                                                                <li class="d-inline-block mr-2">
                                                                    <fieldset>
                                                                        <div class="vs-checkbox-con vs-checkbox-success">
                                                                            <input type="checkbox" name="is_show_app">
                                                                            <span class="vs-checkbox">
                                                                                <span class="vs-checkbox--check">
                                                                                    <i class="vs-icon feather icon-check"></i>
                                                                                </span>
                                                                            </span>
                                                                            <span class="">{{trans('labels.is_show_app')}}</span>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            @endif
                                                            @if ($result['commonContent']['roles']!= null and $result['commonContent']['roles']->is_show_admin == 1)
                                                                <li class="d-inline-block mr-2">
                                                                    <fieldset>
                                                                        <div class="vs-checkbox-con vs-checkbox-danger">
                                                                            <input type="checkbox" name="is_show_admin">
                                                                            <span class="vs-checkbox">
                                                                                <span class="vs-checkbox--check">
                                                                                    <i class="vs-icon feather icon-check"></i>
                                                                                </span>
                                                                            </span>
                                                                            <span class="">{{trans('labels.is_show_admin')}}</span>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="row">
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Min Order Limit') }}</label>
                                                    <div class="col-sm-10 col-md-8">
                                                        {!! Form::text('products_min_order', '1', array('class'=>'form-control field-validate number-validate', 'id'=>'products_min_order')) !!}
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.Min Order Limit Text') }}
                                                        </span>
                                                        <span class="help-block hidden">{{ trans('labels.Min Order Limit Text') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Max Order Limit') }}</label>
                                                    <div class="col-sm-10 col-md-8">
                                                        {!! Form::text('products_max_stock', '', array('class'=>'form-control ', 'id'=>'products_max_stock')) !!}
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.Max Order Limit Text') }}
                                                        </span>
                                                        <span class="help-block hidden">{{ trans('labels.Max Order Limit Text') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                        {{-- <div class="row">
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.ProductsWeight') }}</label>
                                                    <div class="col-sm-10 col-md-4">
                                                        {!! Form::text('products_weight', '', array('class'=>'form-control', 'id'=>'products_weight')) !!}
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.RequiredTextForWeight') }}
                                                        </span>
                                                    </div>
                                                    <div class="col-sm-10 col-md-4" style="padding-left: 0;">
                                                        <select class="form-control" name="products_weight_unit">
                                                            @if($result['units'] !== null)
                                                            @foreach($result['units'] as $unit)
                                                            <option value="{{$unit->units_name}}">{{$unit->units_name}}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.ProductsModel') }}</label>
                                                    <div class="col-sm-10 col-md-8">
                                                        {!! Form::text('products_model', '', array('class'=>'form-control', 'id'=>'products_model')) !!}
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.ProductsModelText') }}
                                                        </span>
                                                        <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}

                                        <div class="row">
                                            @if (auth()->user()->role_id == 1)
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Shop') }}</label>
                                                    <div class="col-sm-10 col-md-4">
                                                        <select class="form-control field-validate" name="admin_id">
                                                            <option value="">{{ trans('labels.SelectShop') }}</option>
                                                            @foreach($result['shops'] as $shop)
                                                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            @elseif(auth()->user()->role_id == 11)
                                            <input type="hidden" name="admin_id" value="{{auth()->user()->id}}">
                                            @else
                                            <input type="hidden" name="admin_id" value="{{auth()->user()->parent_admin_id}}">
                                            @endif

                                            {{-- <div class="col-xs-12 col-md-6 ">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Image') }}<span style="color:red;">*</span></label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <!-- Modal -->
                                                        <div class="modal fade" id="Modalmanufactured" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" id="closemodal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                                        <h3 class="modal-title text-primary" id="myModalLabel">{{ trans('labels.Choose Image') }}</h3>
                                                                    </div>
                                                                    <div class="modal-body manufacturer-image-embed">
                                                                        @if(isset($allimage))
                                                                        <select class="image-picker show-html " name="image_id" id="select_img">
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
                                                                        <button type="button" class="btn btn-primary" id="selected" data-dismiss="modal">Done</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div id="imageselected">
                                                          {!! Form::button( trans('labels.Add Image'), array('id'=>'newImage','class'=>"btn btn-primary ", 'data-toggle'=>"modal", 'data-target'=>"#Modalmanufactured" )) !!}
                                                          <br>
                                                          <div id="selectedthumbnail" class="selectedthumbnail col-md-5"> </div>
                                                          <div class="closimage">
                                                              <button type="button" class="close pull-left image-close " id="image-close"
                                                                style="display: none; position: absolute;left: 105px; top: 54px; background-color: black; color: white; opacity: 2.2; " aria-label="Close">
                                                                  <span aria-hidden="true">&times;</span>
                                                              </button>
                                                          </div>
                                                        </div>
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.UploadProductImageText') }}</span>

                                                    </div>
                                                </div>
                                            </div> --}}

                                            <div class="col-xs-12 col-md-6">
                                                <label for="file" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Image') }}</label>
                                                <input type="file" name="file" class="form-control" onchange="readURL(this);" />
                                                <img id="blah" src="#" alt="your image" />
                                            </div>

                                            {{-- <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.VideoEmbedCodeLink') }}</label>
                                                    <div class="col-sm-10 col-md-8">
                                                        {!! Form::textarea('products_video_link', '', array('class'=>'form-control', 'id'=>'products_video_link', 'rows'=>4)) !!}
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.VideoEmbedCodeLinkText') }}
                                                        </span>
                                                        <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                    </div>
                                                </div>
                                            </div> --}}
                                        </div>

                                        <hr>


                                        {{-- <div class="row">
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group flash-sale-link">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.FlashSale') }}</label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <select class="form-control" onChange="showFlash();" name="isFlash" id="isFlash">
                                                            <option value="no">{{ trans('labels.No') }}</option>
                                                            <option value="yes">{{ trans('labels.Yes') }}</option>
                                                        </select>
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.FlashSaleText') }}</span>
                                                    </div>
                                                </div>

                                                <div class="flash-container" style="display: none;">
                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.FlashSalePrice') }}<span style="color:red;">*</span></label>
                                                        <div class="col-sm-10 col-md-8">
                                                            <input class="form-control" type="text" name="flash_sale_products_price" id="flash_sale_products_price" value="">
                                                            <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                {{ trans('labels.FlashSalePriceText') }}</span>
                                                            <span class="help-block hidden">{{ trans('labels.FlashSalePriceText') }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.FlashSaleDate') }}<span style="color:red;">*</span></label>
                                                        <div class="col-sm-10 col-md-4">
                                                            <input class="form-control datepicker" readonly type="text" name="flash_start_date" id="flash_start_date" readonly value="">
                                                            <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                {{ trans('labels.FlashSaleDateText') }}</span>
                                                            <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                        </div>
                                                        <div class="col-sm-10 col-md-4 bootstrap-timepicker">
                                                            <input type="text" class="form-control timepicker" name="flash_start_time" readonly id="flash_start_time" value="">
                                                            <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.FlashExpireDate') }}<span style="color:red;">*</span></label>
                                                        <div class="col-sm-10 col-md-4">
                                                            <input class="form-control datepicker" readonly type="text" readonly name="flash_expires_date" id="flash_expires_date" value="">
                                                            <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                {{ trans('labels.FlashExpireDateText') }}</span>
                                                            <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                        </div>
                                                        <div class="col-sm-10 col-md-4 bootstrap-timepicker">
                                                            <input type="text" class="form-control timepicker" readonly name="flash_end_time" id="flash_end_time" value="">
                                                            <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Status') }}</label>
                                                        <div class="col-sm-10 col-md-8">
                                                            <select class="form-control" name="flash_status">
                                                                <option value="1">{{ trans('labels.Active') }}</option>
                                                                <option value="0">{{ trans('labels.Inactive') }}</option>
                                                            </select>
                                                            <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                {{ trans('labels.ActiveFlashSaleProductText') }}</span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group special-link">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Special') }}</label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <select class="form-control" onChange="showSpecial();" name="isSpecial" id="isSpecial">
                                                            <option value="no">{{ trans('labels.No') }}</option>
                                                            <option value="yes">{{ trans('labels.Yes') }}</option>
                                                        </select>
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.SpecialProductText') }}.</span>
                                                    </div>
                                                </div>

                                                <div class="special-container" style="display: none;">
                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.SpecialPrice') }}<span style="color:red;">*</span></label>
                                                        <div class="col-sm-10 col-md-8">
                                                            <input class="form-control" type="text" name="specials_new_products_price" id="special-price" value="">
                                                            <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                {{ trans('labels.SpecialPriceTxt') }}.</span>
                                                            <span class="help-block hidden">{{ trans('labels.SpecialPriceNote') }}.</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.ExpiryDate') }}<span style="color:red;">*</span></label>
                                                        <div class="col-sm-10 col-md-8">
                                                            <input class="form-control datepicker" readonly readonly type="text" name="expires_date" id="expiry-date" value="">
                                                            <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                {{ trans('labels.SpecialExpiryDateTxt') }}
                                                            </span>
                                                            <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Status') }}<span style="color:red;">*</span></label>
                                                        <div class="col-sm-10 col-md-8">
                                                            <select class="form-control" name="status">
                                                                <option value="1">{{ trans('labels.Active') }}</option>
                                                                <option value="0">{{ trans('labels.Inactive') }}</option>
                                                            </select>
                                                            <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                {{ trans('labels.ActiveSpecialProductText') }}.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}

                                        <hr>

                                        {{-- @if (session()->has('error'))
                                        <div class="alert alert-danger alert-dismissible show" role="alert">
                                            <strong>{{session()->get('error')}}</strong>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        @endif --}}

                                        <div class="row">
                                            {{-- @foreach($result['attributes'] as $attribute)
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">Select {{$attribute->options_name}}</label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <select class="form-control" name="attributes[]">
                                                            <option value="">Select {{$attribute->options_name}}</option>
                                                            @foreach($attribute->option_value as $item)
                                                            <option value="{{ $item->products_options_values_id }}">{{ $item->options_values_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach --}}
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.BarCode') }}<span style="color:red;">*</span></label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <input class="form-control field-validate" name="barcode">
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                        {{ trans('labels.AddBarCodeText') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.SelectParallel') }}<span style="color:red;">*</span></label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <select class="form-control" name="product_parent_id">
                                                            <option value="">{{ trans('labels.SelectParallel') }}</option>
                                                            @foreach($result['products'] as $product)
                                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div> --}}
                                        </div>
                                        
                                        <hr>
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0 h6">{{__('labels.Product Variation')}}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" value="{{__('labels.Colors')}}" disabled>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select class="form-control aiz-selectpicker" data-live-search="true" data-selected-text-format="count" name="colors[]" id="colors" multiple disabled>
                                                            @foreach (\App\Color::orderBy('name', 'asc')->get() as $key => $color)
                                                            <option  value="{{ $color->code }}" data-content="<span><span class='size-15px d-inline-block mr-2 rounded border' style='background:{{ $color->code }}'></span><span>{{ $color->name }}</span></span>"></option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="aiz-switch aiz-switch-success mb-0">
                                                            <input value="1" type="checkbox" name="colors_active">
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                        
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control" value="{{__('labels.attributes')}}" disabled>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select name="choice_attributes[]" id="choice_attributes" class="form-control aiz-selectpicker" data-selected-text-format="count" data-live-search="true" multiple data-placeholder="{{ __('labels.Choose Attributes') }}">
                                                            {{-- @foreach (\App\Attribute::all() as $key => $attribute)
                                                            <option value="{{ $attribute->id }}">{{ $attribute->getTranslation('name') }}</option>
                                                            @endforeach --}}

                                                            @foreach (DB::table('products_options_descriptions')->where('language_id',request()->session()->get('back_locale') == 'en' ? 1 : 2)->get() as $key => $attribute)
                                                            <option value="{{ $attribute->products_options_id }}">{{ $attribute->options_name }}</option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p>{{ __('labels.Choose the attributes of this product and then input values of each attribute') }}</p>
                                                    <br>
                                                </div>
                        
                                                <div class="customer_choice_options" id="customer_choice_options">
                        
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0 h6">{{trans('labels.Product price + stock')}}</h5>
                                            </div>
                                            <div class="card-body">
                                                {{-- <div class="form-group row">
                                                    <label class="col-md-3 col-from-label">{{trans('Unit price')}} <span class="text-danger">*</span></label>
                                                    <div class="col-md-6">
                                                        <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ trans('Unit price') }}" name="unit_price" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-from-label">{{trans('Purchase price')}} <span class="text-danger">*</span></label>
                                                    <div class="col-md-6">
                                                        <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ trans('Purchase price') }}" name="purchase_price" class="form-control" required>
                                                    </div>
                                                </div> --}}
                                                {{--
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-from-label">{{trans('Tax')}} <span class="text-danger">*</span></label>
                                                    <div class="col-md-6">
                                                        <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ trans('Tax') }}" name="tax" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-control aiz-selectpicker" name="tax_type">
                                                            <option value="amount">{{trans('Flat')}}</option>
                                                            <option value="percent">{{trans('Percent')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                --}}
                                                {{-- <div class="form-group row">
                                                    <label class="col-md-3 col-from-label">{{trans('Discount')}} <span class="text-danger">*</span></label>
                                                    <div class="col-md-6">
                                                        <input type="number" lang="en" min="0" value="0" step="0.01" placeholder="{{ trans('Discount') }}" name="discount" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-control aiz-selectpicker" name="discount_type">
                                                            <option value="amount">{{trans('Flat')}}</option>
                                                            <option value="percent">{{trans('Percent')}}</option>
                                                        </select>
                                                    </div>
                                                </div> --}}
                                                <div class="form-group row" id="quantity">
                                                    <label class="col-md-3 col-from-label">{{trans('labels.Quantity')}} <span class="text-danger">*</span></label>
                                                    <div class="col-md-6">
                                                        <input type="number" lang="en" min="0" value="0" step="1" placeholder="{{ trans('labels.Quantity') }}" name="quantity" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row" id="pos_quantity">
                                                    <label class="col-md-3 col-from-label">{{trans('labels.POS_Quantity')}} <span class="text-danger">*</span></label>
                                                    <div class="col-md-6">
                                                        <input type="number" lang="en" min="0" value="0" step="1" placeholder="{{ trans('labels.POS_Quantity') }}" name="pos_quantity" class="form-control" required>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="sku_combination" id="sku_combination">
                        
                                                </div>
                                            </div>
                                        </div>


                                        <!-- /.box-body -->
                                        <div class="box-footer text-center">
                                            <button type="submit" class="btn btn-primary pull-right">
                                                <span>{{ trans('labels.Save_And_Continue') }}</span>
                                                <i class="fa fa-angle-right 2x"></i>
                                            </button>
                                        </div>

                                        <!-- /.box-footer -->
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->

        <!-- Main row -->

        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<script src="{!! asset('admin/plugins/jQuery/jQuery-2.2.0.min.js') !!}"></script>
<script type="text/javascript">
        // function readURL(input) {
        //     if (input.files && input.files[0]) {
        //         var reader = new FileReader();

        //         reader.onload = function (e) {
        //             $('#'+$(input).next().attr("id"))
        //                 .attr('src', e.target.result)
        //                 .width(150)
        //                 .height(200);
        //         };

        //         reader.readAsDataURL(input.files[0]);
        //     }
        // }

    $(function() {

        //for multiple languages
        @foreach($result['languages'] as $languages)
        // Replace the <textarea id="editor1"> with a CKEditor
        // instance, using default configuration.
        CKEDITOR.replace('editor{{$languages->languages_id}}');

        @endforeach

        //bootstrap WYSIHTML5 - text editor
        $(".textarea").wysihtml5();

    });
</script>
@endsection

@push('styles_aiz')
    <link rel="stylesheet" href="{{ asset('admin/css/vendors.css') }}">

	<link rel="stylesheet" href="{{ asset('admin/css/aiz-core.css') }}">
@endpush

@push('scripts_aiz')
<script src="{!! asset('admin/js/vendors.js') !!}" ></script>
<script src="{!! asset('admin/js/aiz-core.js') !!}" ></script>
@endpush
@section('script')



<script type="text/javascript">
    $("[name=shipping_type]").on("change", function (){
        $(".product_wise_shipping_div").hide();
        $(".flat_rate_shipping_div").hide();
        if($(this).val() == 'product_wise'){
            $(".product_wise_shipping_div").show();
        }
        if($(this).val() == 'flat_rate'){
            $(".flat_rate_shipping_div").show();
        }

    });

    function add_more_customer_choice_option(i, name){
        $('#customer_choice_options').append('<div class="form-group row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="'+i+'"><input type="text" class="form-control" name="choice[]" value="'+name+'" placeholder="{{ trans('Choice Title') }}" readonly></div><div class="col-md-8"><input type="text" class="form-control aiz-tag-input" name="choice_options_'+i+'[]" placeholder="{{ trans('labels.Enter choice values') }}" data-on-change="update_sku"></div></div>');

    	AIZ.plugins.tagify();
    }

    AIZ.plugins.bootstrapSelect('refresh');
	$('input[name="colors_active"]').on('change', function() {
	    if(!$('input[name="colors_active"]').is(':checked')) {
		$('#colors').prop('disabled', true);
                AIZ.plugins.bootstrapSelect('refresh');
            }
            else{
                $('#colors').prop('disabled', false);
                AIZ.plugins.bootstrapSelect('refresh');
            }
            update_sku();
	});

	$('#colors').on('change', function() {
	    update_sku();
	});

	$('input[name="unit_price"]').on('keyup', function() {
	    update_sku();
	});

	$('input[name="name"]').on('keyup', function() {
	    update_sku();
	});

	function delete_row(em){
		$(em).closest('.form-group row').remove();
		update_sku();
	}

    function delete_variant(em){
		$(em).closest('.variant').remove();
	}

	function update_sku(){
		$.ajax({
		   type:"POST",
		   url:'{{ route('products.sku_combination') }}',
		   data:$('#choice_form').serialize(),
		   success: function(data){
               console.log(data);
			   $('#sku_combination').html(data);
    			AIZ.plugins.fooTable();
			   if (data.length > 1) {
				   $('#quantity').hide();
				   $('#pos_quantity').hide();
			   }
			   else {
					$('#pos_quantity').show();
					$('#quantity').show();
			   }
		   }
	   });
	}

	$('#choice_attributes').on('change', function() {
		$('#customer_choice_options').html(null);
		$.each($("#choice_attributes option:selected"), function(){
            add_more_customer_choice_option($(this).val(), $(this).text());
        });
		update_sku();
	});


</script>

@endsection

