<!-- //footer style One -->
@push('styles')
<style>
  .hd{
    font-family: cairo;
  }
</style>
    
@endpush
<div class="row" style="padding-bottom: 9px;padding-top: 44px;padding-right: 34px; ">
  <div class="col-4 col-lg-4">
    <img style="width: 150px" src="{{ asset('images/1.jfif') }}">
    <div style="
    float: left;
    position: relative;
    left: 15%;
    top: 20%;
    font-family: 'Cairo';
    font-weight: bolder;">
      <h1 style="font-size: 35px;
      font-weight: bolder;">موقـــعنا
      </h1>
      <p> {{$result['commonContent']['setting'][4]->value}} {{$result['commonContent']['setting'][5]->value}} {{$result['commonContent']['setting'][6]->value}}, {{$result['commonContent']['setting'][7]->value}} {{$result['commonContent']['setting'][8]->value}} </p>
    </div>
  </div>
  <div class="col-4 col-lg-4">
    <img style="width: 150px" src="{{ asset('images/2.jfif') }}">
    <div style="
    float: left;
    position: relative;
    left: 35%;
    top: 20%;
    font-family: 'Cairo';
    font-weight: bolder;">
      <h1 style="font-size: 35px;
      font-weight: bolder;">خط الدعم
      </h1>
      <p dir="ltr"> {{$result['commonContent']['setting'][11]->value}}</p>
    </div>
  </div>
  <div class="col-4 col-lg-4">
    <img style="width: 150px" src="{{ asset('images/3.jfif') }}">
    <div style="
    float: left;
    position: relative;
    left: 25%;
    top: 20%;
    font-family: 'Cairo';
    font-weight: bolder;">
      <h1 style="font-size: 35px;
      font-weight: bolder;">دعم الطلبات
      </h1>
      <p> {{$result['commonContent']['setting'][3]->value}}</p>
    </div>
  </div>
</div>
{{-- <div class="col-12 col-lg-12">
  <img src="{{ asset('images/35ae5b11-db8e-4c09-b085-59e3f9408c2a.jpg') }}" style="width: 321%"  alt="">
</div> --}}
<footer id="footerOne"  class="footer-area footer-content footer-one footer-desktop d-none d-lg-block d-xl-block">
  <div class="container">

    <div class="row">
      
      <div class="col-2 col-lg-2">
        <div class="single-footer">
            <h1 class="hd" style="font-family: cairo;font-weight: 400;font-size: 35px;">                    
                @lang('website.aboutUs')
            </h1>
          </div>

      <ul class="links-list pl-0">
        @if(count($result['commonContent']['pages']))
          @foreach($result['commonContent']['pages'] as $page)
              <li> <a href="{{ URL::to('/page?name='.$page->slug)}}" data-toggle="tooltip" data-placement="left" title="{{$page->name}}">{{$page->name}}</a> </li>
          @endforeach
        @endif
        <li> <a href="{{ URL::to('/contact')}}" data-toggle="tooltip" data-placement="left" title="@lang('website.Contact Us')">@lang('website.Contact Us')</a> </li>

      </ul>
    </div>

    <div class="col-2 col-lg-2">
      <div class="single-footer">
          <h1 class="hd" style="font-family: cairo;font-weight: 400;font-size: 32px;">                    
              @lang('website.startsell')
          </h1>
        </div>

    <ul class="links-list pl-0">
      
      <li> <a href="{{ URL::to('/Become_merchant_with_us')}}" data-toggle="tooltip" data-placement="left" title="@lang('website.Become_merchant_with_us')">@lang('website.Become_merchant_with_us')</a> </li>

    </ul>
  </div>

  <div class="col-2 col-lg-2">
    <div class="single-footer">
        <h1 class="hd" style="font-family: cairo;font-weight: 400;font-size: 35px;">                    
            @lang('website.support')
        </h1>
      </div>

  <ul class="links-list pl-0">
    
    <li> <a href="{{ URL::to('/contact')}}" data-toggle="tooltip" data-placement="left" title="@lang('website.Contact Us')">@lang('website.Contact Us')</a> </li>

  </ul>
</div>

<div class="col-2 col-lg-2">
  <div class="single-footer">
      <h1 class="hd" style="font-family: cairo;font-weight: 400;font-size: 35px;">                    
          @lang('website.down')
      </h1>
    </div>

<ul class="links-list pl-0">
  <li> <a href="" data-toggle="tooltip" data-placement="left" title="الايفون">الايفون</a> </li>
  <li> <a href="" data-toggle="tooltip" data-placement="left" title="الاندرويد">الاندرويد</a> </li>

  <a href="#"><img style="margin-top: 5px;height: 43px;width: 159px;" class="img-fluid" src="{{ asset('') }}/web/images/miscellaneous/google-play-btn.png" alt="google-btn"></a>
  <br><a href="#"><img style="margin-top: 5px;height: 43px;width: 159px;" class="img-fluid" src="{{ asset('') }}/web/images/miscellaneous/app-store-btn.png" alt="appstore"></a>
                

</ul>
</div>

<div class="col-2 col-lg-2">
  <div class="single-footer">
      <h1 class="hd" style="font-family: cairo;font-weight: 400;font-size: 35px;">                    
          @lang('website.social')
      </h1>
    </div>

<ul class="links-list pl-0">
  <li>
    @if(!empty($result['commonContent']['setting'][50]->value))
        <a href="{{$result['commonContent']['setting'][50]->value}}"  data-toggle="tooltip" data-placement="bottom" title="@lang('website.facebook')">الفيس بوك</a>
    @else
    <a href="{{$result['commonContent']['setting'][50]->value}}"  data-toggle="tooltip" data-placement="bottom" title="@lang('website.facebook')">الفيس بوك</a>
    @endif
  </li> 
  <li>
    @if(!empty($result['commonContent']['setting'][53]->value))
        <a href="{{$result['commonContent']['setting'][53]->value}}"  data-toggle="tooltip" data-placement="bottom" title="@lang('website.facebook')">الانستقرام</a>
    @else
    <a href="{{$result['commonContent']['setting'][53]->value}}"  data-toggle="tooltip" data-placement="bottom" title="@lang('website.facebook')"> الانستقرام</a>
    @endif
  </li> 
  <li>
    @if(!empty($result['commonContent']['setting'][52]->value))
        <a href="{{$result['commonContent']['setting'][52]->value}}" data-toggle="tooltip" data-placement="bottom" title="@lang('website.twitter')">@lang('website.twitter')</a>
    @else
        <a href="#"  data-toggle="tooltip" data-placement="bottom" title="@lang('website.twitter')">@lang('website.twitter')</a>
    @endif
  </li>

  <li>
    @if(!empty($result['commonContent']['setting'][51]->value))
        <a href="{{$result['commonContent']['setting'][51]->value}}" data-toggle="tooltip" data-placement="bottom" title="@lang('website.google')">@lang('website.google')</a>
    @else
        <a href="#"><i  data-toggle="tooltip" data-placement="bottom" title="@lang('website.google')"></i>@lang('website.google')</a>
    @endif
  </li>

   

</ul>
</div>
       
<div class="col-2 col-lg-2">
  

<ul class="links-list pl-0">
  <a href="{{ url('/') }}"><img style="margin-top: 5px;height: 135px;width: 199px;" class="img-fluid" src="{{ asset('') }}/images/media/2021/10/PcH8R27307.png" alt="google-btn"></a>
  <br><a href="#"><img style="margin-top: 5px;height: 16px;width: 159px;" class="img-fluid" src="{{ asset('') }}/web/images/miscellaneous/payments.png" alt="appstore"></a>
                

</ul>
</div>
  </div>
  </div>
  {{-- <div class="container-fluid p-0">
    <div class="copyright-content">
        <div class="container">
          <div class="row align-items-center">
              <div class="col-12">
                <div class="footer-info">
                    ©&nbsp;@lang('website.Copy Rights'). <a href="{{url('/page?name=refund-policy')}}">@lang('website.Privacy')</a>
                    &nbsp;•&nbsp;
                    <a href="{{url('/page?name=term-services')}}">@lang('website.Terms')</a>
                </div>
                  
              </div>
          </div>
        </div>
      </div> --}}
  </div>
</footer>

<a href="https://wa.me/+966536990111" class="float" target="_blank">
{{-- <i class="fa fa-whatsapp my-float"></i> --}}
<svg xmlns="http://www.w3.org/2000/svg" width="38" height="35" style="margin-top: 10px;" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
  <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
</svg>
</a>

