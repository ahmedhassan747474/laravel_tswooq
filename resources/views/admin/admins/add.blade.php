@extends('admin.layout')
<?php
$images = new App\Models\Core\Images;
$allimage = $images->getimages();
?>
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> {{ trans('labels.addadmins') }} <small>{{ trans('labels.addadmins') }}...</small> </h1>
    <ol class="breadcrumb">
      <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
      <li><a href="{{ URL::to('admin/admins')}}"><i class="fa fa-users"></i> {{ trans('labels.admins') }}</a></li>
      <li class="active">{{ trans('labels.addadmins') }}</li>
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
            <h3 class="box-title">{{ trans('labels.addadmins') }} </h3>
          </div>

          <!-- /.box-header -->
          <div class="box-body">
            <div class="row">
            
              <div class="col-xs-12">
              		<div class="box box-info">
                        <br>

                        @if(session()->has('message'))
                            <div class="alert alert-success" role="alert">
						  	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                {{ session()->get('message') }}
                            </div>
                        @endif

                        @if(session()->has('errorMessage'))
                            <div class="alert alert-danger" role="alert">
						  	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                {{ session()->get('errorMessage') }}
                            </div>
                        @endif

                        <!-- form start -->
                         <div class="box-body">
                            {!! Form::open(array('url' =>'admin/addnewadmin', 'method'=>'post', 'class' => 'form-horizontal form-validate', 'enctype'=>'multipart/form-data')) !!}
                            <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.AdminType') }} </label>
                                  <div class="col-sm-10 col-md-4">
                                    <select class="form-control field-validate changeAdminType" name="adminType">
                                    @foreach($result['adminTypes'] as $adminType)
                                          <option value="{{$adminType->user_types_id}}">{{$adminType->user_types_name}}</option>
                                    @endforeach
									                  </select>
                                  <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                  {{ trans('labels.AdminTypeText') }}</span>
                                  </div>
                            </div>
                            @if (auth()->user()->role_id == 1)
                              <div class="form-group show_hide_shop" style="display: none;">
                                <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Shop') }}</label>
                                <div class="col-sm-10 col-md-4">
                                  <select class="form-control" name="admin_id" id="select_shop">
                                      <option value="">{{ trans('labels.SelectShop') }}</option>
                                      @foreach($result['shops'] as $shop)
                                      <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                    @endforeach
                                  </select>
                                </div>
                              </div>
                            @else
                              <input type="hidden" name="admin_id" value="{{auth()->user()->id}}">
                            @endif

                            <hr class="show_hide_shop_name">
                            <h4 class="show_hide_shop_name">{{ trans('labels.Subscription Info') }} </h4>
                            <hr class="show_hide_shop_name">
                                    
                                <div class="form-group show_hide_shop_name">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.LikeCardLimit') }} </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('like_limit',  '', array('class'=>'form-control field-validate', 'id'=>'like_limit')) !!}
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.LikeCardLimitText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                  </div>
                                </div>
                                <div class="form-group show_hide_shop_name">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.SubscriptionFee') }} </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::number('subscription_fee',  '', array('class'=>'form-control', 'id'=>'subscription_fee')) !!}
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.SubscriptionFeeText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                  </div>
                                </div>
                                <div class="form-group show_hide_shop_name">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.StartDate') }} </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::date('start_date',  '', array('class'=>'form-control', 'id'=>'start_date')) !!}
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.StartDateText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                  </div>
                                </div>

                                <div class="form-group show_hide_shop_name">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.EndDate') }} </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::date('end_date',  '', array('class'=>'form-control', 'id'=>'end_date')) !!}
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.EndDateText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                  </div>
                                  
                                </div>
                                
                            <hr>

                            <h4>{{ trans('labels.Personal Info') }} </h4>
                            <hr>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.FirstName') }} </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('first_name',  '', array('class'=>'form-control field-validate', 'id'=>'first_name')) !!}
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.FirstNameText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.LastName') }} </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('last_name',  '', array('class'=>'form-control field-validate', 'id'=>'last_name')) !!}
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.lastNameText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Telephone') }}</label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('phone',  '', array('class'=>'form-control phone-validate', 'id'=>'phone')) !!}
                                   <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                   {{ trans('labels.TelephoneText') }}</span>
                                  </div>
                                </div>
                                <div class="form-group show_hide_shop_name">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.ShopName') }} </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('shop_name',  '', array('class'=>'form-control field-validate', 'id'=>'shop_name')) !!}
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.ShopNameText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                  </div>
                                </div>
                                <div class="form-group show_hide_shop_name">
                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.RecordNumber') }} </label>
                                    <div class="col-sm-10 col-md-4">
                                      {!! Form::text('record_number',  '', array('class'=>'form-control', 'id'=>'record_number')) !!}
                                      <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.RecordNumberText') }}</span>
                                      <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                    </div>
                                </div>
                                
                                <div class="form-group show_hide_shop_name" id="imageIcone">
                                    <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Image') }}</label>
                                    <div class="col-sm-10 col-md-4">
                                        <!-- Modal -->
                                        <div class="modal fade embed-images" id="ModalmanufacturedICone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" id="closemodal" aria-label="Close"><span aria-hidden="true">??</span></button>
                                                        <h3 class="modal-title text-primary" id="myModalLabel">{{ trans('labels.Choose Image') }} </h3>
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
                                                        <a href="{{url('admin/media/add')}}" target="_blank" class="btn btn-primary pull-left" >{{ trans('labels.Add Image') }}</a>
                                                        <button type="button" class="btn btn-default refresh-image"><i class="fa fa-refresh"></i></button>
                                                        <button type="button" class="btn btn-success" id="selectedICONE" data-dismiss="modal">{{ trans('labels.Done') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="imageselected">
                                          {!! Form::button(trans('labels.Add Image'), array('id'=>'newIcon','class'=>"btn btn-primary ", 'data-toggle'=>"modal", 'data-target'=>"#ModalmanufacturedICone" )) !!}
                                          <br>
                                          <div id="selectedthumbnailIcon" class="selectedthumbnail col-md-5"> </div>
                                          <div class="closimage">
                                              <button type="button" class="close pull-left image-close " id="image-Icone"
                                                style="display: none; position: absolute;left: 105px; top: 54px; background-color: black; color: white; opacity: 2.2; " aria-label="Close">
                                                  <span aria-hidden="true">&times;</span>
                                              </button>
                                          </div>
                                        </div>
                                        <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.ImageText') }}</span>

                                        <br>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 col-md-3 control-label">?????????? ???????? ??????</label>
                                    <div class="col-sm-10 col-md-4">
                                        <input class="form-control" type="file" name="shopImg">
                                    </div>
                                </div>
                               
                            <hr>
                            <h4>{{ trans('labels.AddressInfo') }}</h4>
                            <hr>

                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.StreetAddress') }}</label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('address',  '', array('class'=>'form-control', 'id'=>'address')) !!}
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.StreetAddressText') }}</span>
                                  </div>
                                </div>

                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Zip/Postal Code') }}</label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('zip',  '', array('class'=>'form-control', 'id'=>'zip')) !!}
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.Zip/Postal Code Text') }}</span>
                                  </div>
                                </div>

                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.City') }}</label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('city',  '', array('class'=>'form-control', 'id'=>'city')) !!}
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.CityText') }}</span>
                                  </div>
                                </div>

                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Country') }}</label>
                                  <div class="col-sm-10 col-md-4">
                                    <select class="form-control" name="country" id="entry_country_id">
                                        <option value="">{{ trans('labels.SelectCountry') }}</option>
                                        @foreach($result['countries'] as $countries_data)
                                        <option value="{{ $countries_data->countries_id }}">{{ $countries_data->countries_name }}</option>
                                   		@endforeach
                                    </select>
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.CountryText') }}</span>
                                  </div>
                                </div>

                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.State') }}</label>
                                  <div class="col-sm-10 col-md-4">
                                   <select class="form-control zoneContent" name="state">
                                      <option value="">{{ trans('labels.SelectState') }}</option>
                                   </select>
                                    <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">{{ trans('labels.StateText') }}</span>
                                  </div>
                                </div>
                                <hr>
                                <h4>{{ trans('labels.Login Info') }}</h4>
                                <hr>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.EmailAddress') }} </label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::text('email',  '', array('class'=>'form-control email-validate', 'id'=>'email')) !!}
                                     <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                     {{ trans('labels.EmailText') }}</span>
                                    <span class="help-block hidden"> {{ trans('labels.EmailError') }}</span>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Password') }}</label>
                                  <div class="col-sm-10 col-md-4">
                                    {!! Form::password('password', array('class'=>'form-control field-validate', 'id'=>'password')) !!}
                	                <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                   {{ trans('labels.PasswordText') }}</span>
                                    <span class="help-block hidden">{{ trans('labels.textRequiredFieldMessage') }}</span>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="name" class="col-sm-2 col-md-3 control-label">{{ trans('labels.Status') }} </label>
                                  <div class="col-sm-10 col-md-4">
                                    <select class="form-control" name="isActive">
                                          <option value="1">{{ trans('labels.Active') }}</option>
                                          <option value="0">{{ trans('labels.Inactive') }}</option>
									</select>
                                  <span class="help-block" style="font-weight: normal;font-size: 11px;margin-bottom: 0;">
                                  {{ trans('labels.StatusText') }}</span>
                                  </div>
                                </div>

                              <!-- /.box-body -->
                              <div class="box-footer text-center">
                                <button type="submit" class="btn btn-primary">{{ trans('labels.Submit') }}</button>
                                <a href="{{ URL::to('admin/admins')}}" type="button" class="btn btn-default">{{ trans('labels.back') }}</a>
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
@endsection

@section('script')
<script>
  $('.changeAdminType').on('change', function() {
    var type = $(this).val();
    if(type != 11) {
      $('.show_hide_shop').show();
      $('#select_shop').addClass('field-validate');
      $('.show_hide_shop_name').hide();
      $('#shop_name').removeClass('field-validate');
    } else {
      $('.show_hide_shop').hide();
      $('#select_shop').removeClass('field-validate');
      $('.show_hide_shop_name').show();
      $('#shop_name').addClass('field-validate');
    }
  })
</script>
@endsection