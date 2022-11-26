@extends('admin.layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1> {{ trans('labels.AddProduct') }} <small>{{ trans('labels.AddProduct') }}...</small></h1>
            <ol class="breadcrumb">
                <li><a href="{{ URL::to('admin/dashboard/this_month') }}"><i
                                class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
                <li><a href="{{ URL::to('admin/products/display')}}"><i
                                class="fa fa-database"></i> {{ trans('labels.ListingAllProducts') }}</a></li>
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
                                                        <span class="glyphicon glyphicon-exclamation-sign"
                                                              aria-hidden="true"></span>
                                                        <span class="sr-only">{{ trans('labels.Error') }}:</span>
                                                        {{ $error }}
                                                    </div>
                                                @endforeach
                                            @endif

                                            {!! Form::open(array('url' =>'admin/products/add', 'method'=>'post', 'class' => 'form-horizontal form-validate', 'id'=>'choice_form' , 'enctype'=>'multipart/form-data')) !!}


                                            <div class="row">

                                                @foreach($result['languages'] as $key=>$languages)
                                                    <div class="col-md-6">

                                                        <div style="margin-top: 15px;"
                                                             class="tab-pane @if($key==0) active @endif"
                                                             id="product_<?=$languages->languages_id?>">
                                                            <div class="">
                                                                <div class="form-group">
                                                                    <label for="name"
                                                                           class="col-sm-2 col-md-3 control-label">{{ trans('labels.ProductName') }}
                                                                        <span style="color:red;">*</span>
                                                                        ({{ $languages->name }})</label>
                                                                    <div class="col-sm-10 col-md-8">
                                                                        <input type="text"
                                                                               name="products_name_<?=$languages->languages_id?>"
                                                                               class="form-control field-validate">
                                                                        <span class="help-block"
                                                                              style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                            {{ trans('labels.EnterProductNameIn') }} {{ $languages->name }} </span>
                                                                        <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group external_link"
                                                                     style="display: none">
                                                                    <label for="name"
                                                                           class="col-sm-2 col-md-3 control-label">{{ trans('labels.External URL') }}
                                                                        ({{ $languages->name }})</label>
                                                                    <div class="col-sm-10 col-md-8">
                                                                        <input type="text"
                                                                               name="products_url_<?=$languages->languages_id?>"
                                                                               class="form-control products_url">
                                                                        <span class="help-block"
                                                                              style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                                            {{ trans('labels.External URL Text') }} {{ $languages->name }} </span>
                                                                        <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="name"
                                                                           class="col-sm-2 col-md-3 control-label">{{ trans('labels.Description') }}
                                                                        <span style="color:red;">*</span>
                                                                        ({{ $languages->name }})</label>
                                                                    <div class="col-sm-10 col-md-8">
                                                                        <textarea
                                                                                id="edito<?=$languages->languages_id?>"
                                                                                name="products_description_<?=$languages->languages_id?>"
                                                                                class="form-control"
                                                                                rows="5"></textarea>
                                                                        <span class="help-block"
                                                                              style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
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
                                                        <label for="name"
                                                               class="col-sm-2 col-md-2 control-label">{{ trans('labels.Category') }}
                                                            <span style="color:red;">*</span></label>
                                                        <div class="col-sm-10 col-md-9">
                                                            <?php print_r($result['categories']); ?>
                                                            <input id="categories_0" type="hidden"
                                                                   class=" required_one categories sub_categories"
                                                                   name="categories[]" value="0">
                                                            <span class="help-block"
                                                                  style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.ChooseCatgoryText') }}.</span>
                                                            <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">

                                                <div class="col-xs-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="name"
                                                               class="col-sm-2 col-md-3 control-label">{{ trans('labels.Status') }} </label>
                                                        <div class="col-sm-10 col-md-8">
                                                            <select class="form-control" name="products_status">
                                                                <option value="1">{{ trans('labels.Active') }}</option>
                                                                <option value="0">{{ trans('labels.Inactive') }}</option>
                                                            </select>
                                                            <span class="help-block"
                                                                  style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.SelectStatus') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xs-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="name"
                                                               class="col-sm-2 col-md-3 control-label">{{ trans('labels.PriceBuy') }}
                                                            <span style="color:red;">*</span></label>
                                                        <div class="col-sm-10 col-md-8">
                                                            {!! Form::text('price_buy', '', array('class'=>'form-control number-validate', 'id'=>'price_buy')) !!}
                                                            <span class="help-block"
                                                                  style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.PriceBuyText') }}
                                                        </span>
                                                            <span class="help-block hidden">{{ trans('labels.PriceBuyText') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="name"
                                                               class="col-sm-2 col-md-3 control-label">{{ trans('labels.Tax') }}
                                                            %<span style="color:red;">*</span></label>
                                                        <div class="col-sm-10 col-md-8">
                                                            {!! Form::text('tax', '', array('class'=>'form-control number-validate', 'id'=>'tax')) !!}
                                                            <span class="help-block"
                                                                  style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.Tax') }}%
                                                        </span>
                                                            <span class="help-block hidden">{{ trans('labels.Tax') }}%</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="name"
                                                               class="col-sm-2 col-md-3 control-label">{{ trans('labels.ProductsPrice') }}
                                                            <span style="color:red;">*</span></label>
                                                        <div class="col-sm-10 col-md-8">
                                                            {!! Form::text('products_price', '', array('class'=>'form-control number-validate', 'id'=>'products_price')) !!}
                                                            <span class="help-block"
                                                                  style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                            {{ trans('labels.ProductPriceText') }}
                                                        </span>
                                                            <span class="help-block hidden">{{ trans('labels.ProductPriceText') }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="name"
                                                               class="col-sm-2 col-md-3 control-label">{{ trans('labels.Show Product') }}
                                                            <span style="color:red;">*</span></label>
                                                        <div class="col-sm-10 col-md-8">
                                                            <ul class="list-unstyled mb-0">
                                                                @if ($result['commonContent']['roles']!= null and $result['commonContent']['roles']->is_show_web == 1)
                                                                    <li class="d-inline-block mr-2">
                                                                        <fieldset>
                                                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                <input type="checkbox"
                                                                                       name="is_show_web">
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
                                                                                <input type="checkbox"
                                                                                       name="is_show_app">
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
                                                                                <input type="checkbox"
                                                                                       name="is_show_admin">
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
                                            <div class="row">
                                                @if (auth()->user()->role_id == 1)
                                                    <div class="col-xs-12 col-md-6">
                                                        <div class="form-group">
                                                            <label for="name"
                                                                   class="col-sm-2 col-md-3 control-label">{{ trans('labels.Shop') }}</label>
                                                            <div class="col-sm-10 col-md-4">
                                                                <select class="form-control field-validate"
                                                                        name="admin_id">
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
                                                    <input type="hidden" name="admin_id"
                                                           value="{{auth()->user()->parent_admin_id}}">
                                                @endif
                                                <div class="col-xs-12 col-md-6">
                                                    <label for="file"
                                                           class="col-sm-2 col-md-3 control-label">{{ trans('labels.Image') }}</label>
                                                    <input type="file" name="file" class="form-control"
                                                           onchange="readURL(this);"/>
                                                    <img id="blah" src="#" alt="your image"/>
                                                </div>
                                            </div>
                                            <hr>
                                            <hr>
                                            <div class="row">

                                                <div class="col-xs-12 col-md-6">
                                                    <div class="form-group">
                                                        <label for="name"
                                                               class="col-sm-2 col-md-3 control-label">{{ trans('labels.BarCode') }}
                                                            <span style="color:red;">*</span></label>
                                                        <div class="col-sm-10 col-md-8">
                                                            <input class="form-control field-validate" name="barcode">
                                                            <span class="help-block"
                                                                  style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                                        {{ trans('labels.AddBarCodeText') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="mb-0 h6">{{__('labels.Product Variation')}}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group row">
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control"
                                                                   value="{{__('labels.Colors')}}" disabled>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select class="form-control aiz-selectpicker"
                                                                    data-live-search="true"
                                                                    data-selected-text-format="count" name="colors[]"
                                                                    id="colors" multiple disabled>
                                                                @foreach (\App\Color::orderBy('name', 'asc')->get() as $key => $color)
                                                                    <option value="{{ $color->code }}"
                                                                            data-content="<span><span class='size-15px d-inline-block mr-2 rounded border' style='background:{{ $color->code }}'></span><span>{{ $color->name }}</span></span>"></option>
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
                                                            <input type="text" class="form-control"
                                                                   value="{{__('labels.attributes')}}" disabled>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select name="choice_attributes[]" id="choice_attributes"
                                                                    class="form-control aiz-selectpicker"
                                                                    data-selected-text-format="count"
                                                                    data-live-search="true" multiple
                                                                    data-placeholder="{{ __('labels.Choose Attributes') }}">
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

                                                    <div class="form-group row" id="quantity">
                                                        <label class="col-md-3 col-from-label">{{trans('labels.Quantity')}}
                                                            <span class="text-danger">*</span></label>
                                                        <div class="col-md-6">
                                                            <input type="number" lang="en" min="0" value="0" step="1"
                                                                   placeholder="{{ trans('labels.Quantity') }}"
                                                                   name="quantity" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" id="pos_quantity">
                                                        <label class="col-md-3 col-from-label">{{trans('labels.POS_Quantity')}}
                                                            <span class="text-danger">*</span></label>
                                                        <div class="col-md-6">
                                                            <input type="number" lang="en" min="0" value="0" step="1"
                                                                   placeholder="{{ trans('labels.POS_Quantity') }}"
                                                                   name="pos_quantity" class="form-control" required>
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

            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>

    <script src="{!! asset('admin/plugins/jQuery/jQuery-2.2.0.min.js') !!}"></script>
    <script type="text/javascript">


        $(function () {

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
    <script src="{!! asset('admin/js/vendors.js') !!}"></script>
    <script src="{!! asset('admin/js/aiz-core.js') !!}"></script>
@endpush
@section('script')

    <script type="text/javascript">
        $("[name=shipping_type]").on("change", function () {
            $(".product_wise_shipping_div").hide();
            $(".flat_rate_shipping_div").hide();
            if ($(this).val() == 'product_wise') {
                $(".product_wise_shipping_div").show();
            }
            if ($(this).val() == 'flat_rate') {
                $(".flat_rate_shipping_div").show();
            }

        });

        function add_more_customer_choice_option(i, name) {
            $('#customer_choice_options').append('<div class="form-group row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + name + '" placeholder="{{ trans('Choice Title') }}" readonly></div><div class="col-md-8"><input type="text" class="form-control aiz-tag-input" name="choice_options_' + i + '[]" placeholder="{{ trans('labels.Enter choice values') }}" data-on-change="update_sku"></div></div>');

            AIZ.plugins.tagify();
        }

        AIZ.plugins.bootstrapSelect('refresh');
        $('input[name="colors_active"]').on('change', function () {
            if (!$('input[name="colors_active"]').is(':checked')) {
                $('#colors').prop('disabled', true);
                AIZ.plugins.bootstrapSelect('refresh');
            } else {
                $('#colors').prop('disabled', false);
                AIZ.plugins.bootstrapSelect('refresh');
            }
            update_sku();
        });

        $('#colors').on('change', function () {
            update_sku();
        });

        $('input[name="unit_price"]').on('keyup', function () {
            update_sku();
        });

        $('input[name="name"]').on('keyup', function () {
            update_sku();
        });

        function delete_row(em) {
            $(em).closest('.form-group row').remove();
            update_sku();
        }

        function delete_variant(em) {
            $(em).closest('.variant').remove();
        }

        function update_sku() {
            $.ajax({
                type: "POST",
                url: '{{ route('products.sku_combination') }}',
                data: $('#choice_form').serialize(),
                success: function (data) {
                    console.log(data);
                    $('#sku_combination').html(data);
                    AIZ.plugins.fooTable();
                    if (data.length > 1) {
                        $('#quantity').hide();
                        $('#pos_quantity').hide();
                    } else {
                        $('#pos_quantity').show();
                        $('#quantity').show();
                    }
                }
            });
        }

        $('#choice_attributes').on('change', function () {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function () {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
            update_sku();
        });


    </script>

@endsection

