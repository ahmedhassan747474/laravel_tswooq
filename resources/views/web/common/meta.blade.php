<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
{{-- <meta content="width=device-width, initial-scale=1" name="viewport" /> --}}
<meta name="viewport" content="width=device-width, initial-scale=1.0">

@if(!empty($result['commonContent']['setting'][72]->value))
<title><?=stripslashes($result['commonContent']['setting'][72]->value)?></title>
@else
<title><?=stripslashes($result['commonContent']['setting'][18]->value)?></title>
@endif

@if(!empty($result['commonContent']['setting'][86]->value))
<link rel="icon" type="image/png" href="{{asset('').$result['commonContent']['setting'][86]->value}}">
@endif
<meta name="DC.title"  content="<?=stripslashes($result['commonContent']['setting'][73]->value)?>"/>
<meta name="description" content="<?=stripslashes($result['commonContent']['setting'][75]->value)?>"/>
<meta name="keywords" content="<?=stripslashes($result['commonContent']['setting'][74]->value)?>"/>

<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link href='https://fonts.googleapis.com/css?family=Cairo' rel='stylesheet'>

<!-- Core CSS Files -->
<link rel="stylesheet" type="text/css" href="{{asset('web/css').'/'.$result['commonContent']['setting'][81]->value}}.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="{!! asset('web/js/app.js') !!}"></script>
@if(Request::path() == 'checkout')
	<!--------- stripe js ------>
	<script src="https://js.stripe.com/v3/"></script>

	<link rel="stylesheet" type="text/css" href="{{asset('web/css/stripe.css') }}" data-rel-css="" />

	<!------- razorpay ---------->
	<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

@endif
 
<!---- onesignal ------>
<!--<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>-->
<!--<script>-->
<!--var OneSignal = window.OneSignal || [];-->
<!--OneSignal.push(function() {-->
<!--	OneSignal.init({-->
<!--	appId: "{{$result['commonContent']['setting'][55]->value}}",-->
<!--	notifyButton: {-->
<!--		enable: true,-->
<!--	},-->
<!--	allowLocalhostAsSecureOrigin: true,-->
<!--	});-->
<!--});-->

<!--</script>	-->

@if(!empty($result['commonContent']['settings']['before_head_tag']))
	<?=stripslashes($result['commonContent']['settings']['before_head_tag'])?>
@endif

<style>
	.form-validate .form-row .has-error .form-control {
		color: red !important;
		border-color: red !important;
	}
	.has-error .error-content{
		color: red !important;
		float: left;
		width: 100%;
	}

	.media-main .media h3{
		border-radius: 100%;
		padding: 4px 10px 2px;
		margin-top: 0px;
		margin-bottom: 1px;
		background-color: #dbdbdb;
		text-transform: uppercase;

	}
	.avatar h3 {
		border-radius: 100%;
		padding: 1px 5px 0px;
		margin-top: 0px;
		margin-bottom: 0px;
		background-color: #dbdbdb;
		text-transform: uppercase;
		color: #333;
		font-size: 16px;
	}
	.footer-one .mail li a {
		word-break: break-all;
	}

nav#menu-container {
    background:#586e75;
    position:relative;
    width:100%;
    height: 56px;
}
#btn-nav-previous {
    text-align: center;
    color: white;
    cursor: pointer;
    font-size: 24px;
    position: absolute;
    left: 0px;
    padding: 10px 20px;
    background: #8f9a9d;
}
#btn-nav-next {
    text-align: center;
    color: white;
    cursor: pointer;
    font-size: 24px;
    position: absolute;
    right: 0px;
    padding: 10px 20px;
    background: #8f9a9d;
}
.menu-inner-box
{ 
    width: 100%;
    white-space: nowrap;
    margin: 0 auto;
    /* overflow: hidden; */
    padding: 0px 54px;
    box-sizing: border-box;
}
.menu
{  
    padding:0;
    margin: 0;
    list-style-type: none;
    display:block;
    text-align: center;
}
.menu-item
{
    height:100%;
    padding: 0px 25px;
    color:#fff;
    display:inline;
    margin:0 auto;
    line-height:57px;
    text-decoration:none;
    text-align:center;
    white-space:no-wrap;
}
.menu-item:hover {
    /* text-decoration:underline; */
    /* display: block;
    margin-top: 0px;
    position: relative; */
}

.dropdown:hover .dropdown-menu {
	display: block;
	margin-top: 0px;
}

@media only screen and (max-width: 480px) {
  #btn-nav-previous {
    display:none;
  }
  #btn-nav-next {
    display:none;
  }
    .menu-inner-box
    { 
        width:100%;
        overflow-x:auto;
    }
}

.float{
	position:fixed;
	width:60px;
	height:60px;
	bottom:40px;
	right:40px;
	background-color:#25d366;
	color:#FFF;
	border-radius:50px;
	text-align:center;
  font-size:30px;
	box-shadow: 2px 2px 3px #999;
  z-index:100;
}

.my-float{
	margin-top:16px;
}


</style>

<style> 
  
  .carousel-inner img {
      width: 100%;
      height: 100%
  }
  
  #custCarousel .carousel-indicators {
      position: static;
      margin-top: 20px
  }
  
  #custCarousel .carousel-indicators>li {
      width: 100px
  }
  
  #custCarousel .carousel-indicators li img {
      display: block;
      opacity: 0.5
  }
  
  #custCarousel .carousel-indicators li.active img {
      opacity: 1
  }
  
  #custCarousel .carousel-indicators li:hover img {
      opacity: 0.75
  }
  
  .carousel-item img {
      width: 80%
  }
</style>

@if(isset($result['colors']))

@foreach ($result['colors'] as $item)
<style> 
  
  .carousel-inner img {
      width: 100%;
      height: 100%
  }
  
  #custCarousel{{$item->id}} .carousel-indicators {
      position: static;
      margin-top: 20px
  }
  
  #custCarousel{{$item->id}} .carousel-indicators>li {
      width: 100px
  }
  
  #custCarousel{{$item->id}} .carousel-indicators li img {
      display: block;
      opacity: 0.5
  }
  
  #custCarousel{{$item->id}} .carousel-indicators li.active img {
      opacity: 1
  }
  
  #custCarousel{{$item->id}} .carousel-indicators li:hover img {
      opacity: 0.75
  }
  
  .carousel-item img {
      width: 80%
  }
</style>
@endforeach


@endif

@stack('styles')
