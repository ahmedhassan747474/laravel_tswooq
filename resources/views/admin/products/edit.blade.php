@extends('admin.layout')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> {{ trans('labels.EditProduct') }} <small>{{ trans('labels.EditProduct') }}...</small> </h1>
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('admin/dashboard/this_month') }}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
            <li><a href="{{ URL::to('admin/products/display')}}"><i class="fa fa-database"></i> {{ trans('labels.ListingAllProducts') }}</a></li>
            <li class="active">{{ trans('labels.EditProduct') }}</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->

        <!-- /.row -->

        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">{{ trans('labels.EditProduct') }} </h3>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box box-info">
                                    <!-- /.box-header -->
                                    <!-- form start -->
                                    <div class="box-body">
                                        @if( count($errors) > 0)
                                        @foreach($errors->all() as $error)
                                        <div class="alert alert-danger" role="alert">
                                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                            <span class="sr-only">Error:</span>
                                            {{ $error }}
                                        </div>
                                        @endforeach
                                        @endif

                                        {!! Form::open(array('url' =>'admin/products/update', 'method'=>'post', 'class' => 'form-horizontal form-validate', 'enctype'=>'multipart/form-data')) !!}
                                        {!! Form::hidden('id', $result['product'][0]->products_id, array('class'=>'form-control', 'id'=>'id')) !!}
                                        <div class="row">
                                            
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Manufacturers') }} </label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <select class="form-control" name="manufacturers_id">
                                                            <option value="">{{ trans('labels.Choose Manufacturer') }}</option>
                                                            @foreach ($result['manufacturer'] as $manufacturer)
                                                            <option @if($result['product'][0]->manufacturers_id == $manufacturer->id )
                                                                selected
                                                                @endif
                                                                value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.ChooseManufacturerText') }}.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-2 control-label">{{ trans('labels.Category') }}</label>
                                                    <div class="col-sm-10 col-md-9">
                                                    <?php print_r($result['categories']); ?>
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.ChooseCatgoryText') }}.</span>
                                                        <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <!--<div class="col-xs-12 col-md-6">-->
                                            <!--    <div class="form-group">-->
                                            <!--        <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.IsFeature') }} </label>-->
                                            <!--        <div class="col-sm-10 col-md-8">-->
                                            <!--            <select class="form-control" name="is_feature">-->
                                            <!--                <option value="0" @if($result['product'][0]->is_feature==0) selected @endif>{{ trans('labels.No') }}</option>-->
                                            <!--                <option value="1" @if($result['product'][0]->is_feature==1) selected @endif>{{ trans('labels.Yes') }}</option>-->
                                            <!--            </select>-->
                                            <!--            <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">-->
                                            <!--                {{ trans('labels.IsFeatureProuctsText') }}</span>-->
                                            <!--        </div>-->
                                            <!--    </div>-->
                                            <!--</div>-->
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Status') }} </label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <select class="form-control" name="products_status">
                                                            <option value="1" @if($result['product'][0]->products_status==1) selected @endif >{{ trans('labels.Active') }}</option>
                                                            <option value="0" @if($result['product'][0]->products_status==0) selected @endif>{{ trans('labels.Inactive') }}</option>
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
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.ProductsPrice') }}</label>
                                                    <div class="col-sm-10 col-md-8">
                                                        {!! Form::text('products_price', $result['product'][0]->products_price, array('class'=>'form-control number-validate', 'id'=>'products_price')) !!}
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.ProductPriceText') }}
                                                        </span>
                                                        <span class="help-block hidden">{{ trans('labels.ProductPriceText') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.PriceQuantty') }}</label>
                                                    <div class="col-sm-10 col-md-8">
                                                        {!! Form::text('products_quantity', $result['product'][0]->products_quantity, array('class'=>'form-control number-validate', 'id'=>'products_quantity')) !!}
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.products_quantity') }}
                                                        </span>
                                                        <span class="help-block hidden">{{ trans('labels.products_quantity') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!--<div class="col-xs-12 col-md-6">-->
                                            <!--    <div class="form-group" id="tax-class">-->
                                            <!--        <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.TaxClass') }} </label>-->
                                            <!--        <div class="col-sm-10 col-md-8">-->
                                            <!--            <select class="form-control field-validate" name="tax_class_id">-->
                                            <!--                <option selected> {{ trans('labels.SelectTaxClass') }}</option>-->
                                            <!--                @foreach ($result['taxClass'] as $taxClass)-->
                                            <!--                <option @if($result['product'][0]->products_tax_class_id == $taxClass->tax_class_id )-->
                                            <!--                    selected-->
                                            <!--                    @endif-->
                                            <!--                    value="{{ $taxClass->tax_class_id }}">{{ $taxClass->tax_class_title }}</option>-->
                                            <!--                @endforeach-->
                                            <!--            </select>-->
                                            <!--            <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">-->
                                            <!--                {{ trans('labels.ChooseTaxClassForProductText') }}-->
                                            <!--            </span>-->
                                            <!--            <span class="help-block hidden">{{ trans('labels.SelectProductTaxClass') }}</span>-->
                                            <!--        </div>-->
                                            <!--    </div>-->
                                            <!--</div>-->

                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.PriceBuy') }}</label>
                                                    <div class="col-sm-10 col-md-8">
                                                        {!! Form::text('price_buy', $result['product'][0]->price_buy, array('class'=>'form-control number-validate', 'id'=>'price_buy')) !!}
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.PriceBuyText') }}
                                                        </span>
                                                        <span class="help-block hidden">{{ trans('labels.PriceBuyText') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Show Product') }}<span style="color:red;">*</span></label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-2">
                                                                <fieldset>
                                                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                                                        <input type="checkbox" name="is_show_web" {{$result['product'][0]->is_show_web == '1' ? 'checked' : ''}}>
                                                                        <span class="vs-checkbox">
                                                                            <span class="vs-checkbox--check">
                                                                                <i class="vs-icon feather icon-check"></i>
                                                                            </span>
                                                                        </span>
                                                                        <span class="">{{trans('labels.is_show_web')}}</span>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2">
                                                                <fieldset>
                                                                    <div class="vs-checkbox-con vs-checkbox-success">
                                                                        <input type="checkbox" name="is_show_app" {{$result['product'][0]->is_show_app == '1' ? 'checked' : ''}}>
                                                                        <span class="vs-checkbox">
                                                                            <span class="vs-checkbox--check">
                                                                                <i class="vs-icon feather icon-check"></i>
                                                                            </span>
                                                                        </span>
                                                                        <span class="">{{trans('labels.is_show_app')}}</span>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2">
                                                                <fieldset>
                                                                    <div class="vs-checkbox-con vs-checkbox-danger">
                                                                        <input type="checkbox" name="is_show_admin" {{$result['product'][0]->is_show_admin == '1' ? 'checked' : ''}}>
                                                                        <span class="vs-checkbox">
                                                                            <span class="vs-checkbox--check">
                                                                                <i class="vs-icon feather icon-check"></i>
                                                                            </span>
                                                                        </span>
                                                                        <span class="">{{trans('labels.is_show_admin')}}</span>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            
                                        </div>
                                        <div class="row">
                                            
                                        </div>

                                        <div class="row">

                                           

                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.slug') }} </label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <input type="hidden" name="old_slug" value="{{$result['product'][0]->products_slug}}">
                                                        <input type="text" name="slug" class="form-control field-validate" value="{{$result['product'][0]->products_slug}}">
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;margin-top: 0;">{{ trans('labels.slugText') }}</span>
                                                        <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                    </div>
                                                </div>
                                            </div>


                                           
                                        </div>
                                        <div class="row">
                                            @if (auth()->user()->role_id == 1)
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Shop') }}</label>
                                                    <div class="col-sm-10 col-md-4">
                                                        <select class="form-control field-validate" name="admin_id">
                                                            <option value="">{{ trans('labels.SelectShop') }}</option>
                                                            @foreach($result['shops'] as $shop)
                                                            <option value="{{ $shop->id }}" {{$result['product'][0]->admin_id == $shop->id ? 'selected' : ''}}>{{ $shop->name }}</option>
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

                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Image') }} </label>
                                                    <div class="col-sm-10 col-md-4">

                                                        <!-- Modal -->
                                                        <div class="modal fade" id="Modalmanufactured" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" id="closemodal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                                        <h3 class="modal-title text-primary" id="myModalLabel">Choose Image </h3>
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
                                                            {!! Form::button(trans('labels.Add Image'), array('id'=>'newImage','class'=>"btn btn-primary ", 'data-toggle'=>"modal", 'data-target'=>"#Modalmanufactured" )) !!}
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


                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label"></label>
                                                    <div class="col-sm-10 col-md-4">
                                                        {!! Form::hidden('oldImage', $result['product'][0]->products_image , array('id'=>'oldImage', 'class'=>'field-validate ')) !!}
                                                        <img src="{{asset($result['product'][0]->path)}}" alt="" width=" 100px">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <hr>
                                                        <hr>

                                        @if (session()->has('error'))
                                        <div class="alert alert-danger alert-dismissible show" role="alert">
                                            <strong>{{session()->get('error')}}</strong>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        @endif

                                        <div class="row">
                                          
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.BarCode') }}<span style="color:red;">*</span></label>
                                                    <div class="col-sm-10 col-md-8">
                                                        <input class="form-control field-validate" name="barcode" value="{{$result['product'][0]->barcode}}">
                                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                        {{ trans('labels.AddBarCodeText') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        
                                        <hr>
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0 h6">{{trans('Product Variation')}}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <div class="col-lg-3">
                                                        <input type="text" class="form-control" value="{{trans('Colors')}}" disabled>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <select class="form-control aiz-selectpicker" data-live-search="true" data-selected-text-format="count" name="colors[]" id="colors" multiple>
                                                            @foreach (\App\Color::orderBy('name', 'asc')->get() as $key => $color)
                                                            <option
                                                                value="{{ $color->code }}"
                                                                data-content="<span><span class='size-15px d-inline-block mr-2 rounded border' style='background:{{ $color->code }}'></span><span>{{ $color->name }}</span></span>"
                                                                <?php if (in_array($color->code, json_decode($result['product'][0]->colors))) echo 'selected' ?>
                                                                ></option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-1">
                                                        <label class="aiz-switch aiz-switch-success mb-0">
                                                            <input value="1" type="checkbox" name="colors_active" <?php if (count(json_decode($result['product'][0]->colors)) > 0) echo "checked"; ?> >
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </div>
                        
                                                <div class="form-group row">
                                                    <div class="col-lg-3">
                                                        <input type="text" class="form-control" value="{{trans('Attributes')}}" disabled>
                                                    </div>
                                                    <div class="col-lg-8">
                                                        <select name="choice_attributes[]" id="choice_attributes" data-selected-text-format="count" data-live-search="true" class="form-control aiz-selectpicker" multiple data-placeholder="{{ trans('Choose Attributes') }}">
                                                            @foreach (DB::table('products_options_descriptions')->where('language_id',request()->session()->get('back_locale') == 'en' ? 1 : 2)->get() as $key => $attribute)
                                                            <option value="{{ $attribute->products_options_id }}">{{ $attribute->options_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                        
                                                <div class="">
                                                    <p>{{ trans('Choose the attributes of this product and then input values of each attribute') }}</p>
                                                    <br>
                                                </div>
                        
                                                <div class="customer_choice_options" id="customer_choice_options">
                                                    @foreach (json_decode($result['product'][0]->choice_options) as $key => $choice_option)
                                                    <div class="form-group row">
                                                        <div class="col-lg-3">
                                                            <input type="hidden" name="choice_no[]" value="{{ $choice_option->attribute_id }}">
                                                            <input type="text" class="form-control" name="choice[]" value="{{ \App\Attribute::find($choice_option->attribute_id)->getTranslation('name') }}" placeholder="{{ trans('Choice Title') }}" disabled>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="text" class="form-control aiz-tag-input" name="choice_options_{{ $choice_option->attribute_id }}[]" placeholder="{{ trans('Enter choice values') }}" value="{{ implode(',', $choice_option->values) }}" data-on-change="update_sku">
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="mb-0 h6">{{trans('Product price + stock')}}</h5>
                                            </div>
                                            <div class="card-body">
                                                
                        
                                                <br>
                                                <div class="sku_combination" id="sku_combination">
                        
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="tabbable tabs-left">
                                                    <ul class="nav nav-tabs">
                                                        @php
                                                        $i = 0;
                                                        @endphp
                                                        @foreach($result['languages'] as $key=>$languages)
                                                        <li class="@if($i==0) active @endif"><a href="#product_<?=$languages->languages_id?>" data-toggle="tab"><?=$languages->name?></a></li>
                                                        @php
                                                        $i++;
                                                        @endphp
                                                        @endforeach
                                                    </ul>
                                                    <div class="tab-content">
                                                        @php
                                                        $j = 0;
                                                        @endphp
                                                        @foreach($result['description'] as $key=>$description_data)
                                                        <div style="margin-top: 15px;" class="tab-pane @if($j==0) active @endif" id="product_<?=$description_data['languages_id']?>">
                                                            @php
                                                            $j++;
                                                            @endphp
                                                            <div class="form-group">
                                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.ProductName') }} ({{ $description_data['language_name'] }})</label>
                                                                <div class="col-sm-10 col-md-4">
                                                                    <input type="text" name="products_name_<?=$description_data['languages_id']?>" class="form-control field-validate" value='{{$description_data['products_name']}}'>
                                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                        {{ trans('labels.EnterProductNameIn') }} {{ $description_data['language_name'] }} </span>
                                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>

                                                                </div>
                                                            </div>

                                                            <div class="form-group external_link" style="display: none">
                                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.External URL') }} ({{ $description_data['language_name'] }})</label>
                                                                <div class="col-sm-10 col-md-4">
                                                                    <input type="text" name="products_url_<?=$description_data['languages_id']?>" class="form-control products_url" value='{{$description_data['products_url']}}'>
                                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                        {{ trans('labels.External URL Text') }} ({{ $description_data['language_name'] }}) </span>
                                                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Description') }} ({{ $description_data['language_name'] }})</label>
                                                                <div class="col-sm-10 col-md-8">
                                                                    <textarea id="editor<?=$description_data['languages_id']?>" name="products_description_<?=$description_data['languages_id']?>" class="form-control"
                                                                      rows="5">{{stripslashes($description_data['products_description'])}}</textarea>

                                                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                        {{ trans('labels.EnterProductDetailIn') }} {{ $description_data['language_name'] }}</span> </div>
                                                            </div>

                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.box-body -->
                                        <div class="box-footer text-center">
                                            <button type="submit" class="btn btn-primary pull-right" id="normal-btn">{{ trans('labels.Save_And_Continue') }} <i class="fa fa-angle-right 2x"></i></button>
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

@section('script')

<script type="text/javascript">
    $(document).ready(function (){
        show_hide_shipping_div();
    });

    $("[name=shipping_type]").on("change", function (){
        show_hide_shipping_div();
    });

    function show_hide_shipping_div() {
        var shipping_val = $("[name=shipping_type]:checked").val();

        $(".product_wise_shipping_div").hide();
        $(".flat_rate_shipping_div").hide();
        if(shipping_val == 'product_wise'){
            $(".product_wise_shipping_div").show();
        }
        if(shipping_val == 'flat_rate'){
            $(".flat_rate_shipping_div").show();
        }
    }

    function add_more_customer_choice_option(i, name){
        $('#customer_choice_options').append('<div class="form-group row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="'+i+'"><input type="text" class="form-control" name="choice[]" value="'+name+'" placeholder="{{ trans('Choice Title') }}" readonly></div><div class="col-md-8"><input type="text" class="form-control aiz-tag-input" name="choice_options_'+i+'[]" placeholder="{{ trans('Enter choice values') }}" data-on-change="update_sku"></div></div>');

    	AIZ.plugins.tagify();
    }

    AIZ.plugins.bootstrapSelect('refresh');

    $('input[name="colors_active"]').on('change', function() {
        if(!$('input[name="colors_active"]').is(':checked')){
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

    function delete_row(em){
        $(em).closest('.form-group').remove();
        update_sku();
    }

    function delete_variant(em){
        $(em).closest('.variant').remove();
    }

    function update_sku(){
        $.ajax({
           type:"POST",
           url:'{{ route('products.sku_combination_edit') }}',
           data:$('#choice_form').serialize(),
           success: function(data){
               console.log(data);
                $('#sku_combination').html(data);
                AIZ.uploader.previewGenerate();
                AIZ.plugins.fooTable();
                if (data.length > 1) {
                    $('#quantity').hide();
                }
                else {
                    $('#quantity').show();
                }
           }
        });
    }

    AIZ.plugins.tagify();

	$(document).ready(function(){
		update_sku();

		$('.remove-files').on('click', function(){
            $(this).parents(".col-md-4").remove();
        });
	});

	$('#choice_attributes').on('change', function() {
		$.each($("#choice_attributes option:selected"), function(j, attribute){
			flag = false;
			$('input[name="choice_no[]"]').each(function(i, choice_no) {
				if($(attribute).val() == $(choice_no).val()){
					flag = true;
				}
			});
            if(!flag){
				add_more_customer_choice_option($(attribute).val(), $(attribute).text());
			}
        });

		var str = @php echo $result['product'][0]->attributes @endphp;

		$.each(str, function(index, value){
			flag = false;
			$.each($("#choice_attributes option:selected"), function(j, attribute){
				if(value == $(attribute).val()){
					flag = true;
				}
			});
            if(!flag){
				$('input[name="choice_no[]"][value="'+value+'"]').parent().parent().remove();
			}
		});

		update_sku();
	});

</script>

@endsection
