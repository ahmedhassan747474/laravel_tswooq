@extends('web.layout')
@section('content')
<div class="container">
  <h2 class="h6 mt-3">يتم الدفع من خلال : </h2>
  <div class="row">
    <div class="col-12 mt-3 mb-4">
        <img style="width:200px" class="img-fluid" src="{{asset('web/images/miscellaneous/tap.png')}}">
    </div>
	<div class="col-md-12">
		@if (session('message'))
		<div class="alert alert-success" style="text-align: center;" role="alert">
			{{ session('message') }}
		</div>	
		@endif	
	</div>	
  </div>
		<form id="payment-form" method="post" action="{{route('packge.charge')}}">
			@csrf
			<input type="hidden" name="package_id" value="{{$package->id}}">
			<input type="hidden" name="month" value="{{$month}}">
			<!-- Tap element will be here -->
			<div id="element-container"></div>
			<div id="error-handler" role="alert"></div>
			<div id="success" style=" display: none;;position: relative;float: left;">
            Payment Success! Your Amount is <span class="alert alert-success">{{$package->price-$package->discount}}</span>
			</div>
			<!-- Tap pay button -->
			<button id="tap-btn" >Submit</button>
		</form>
    </div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.3.4/bluebird.min.js"></script>
	<script src="https://secure.gosell.io/js/sdk/tap.min.js"></script>
	<Script>
		//pass your public key from tap's dashboard
		var tap = Tapjsli('pk_test_WhawgZ7epdJyfAiqLktbK12o');

		var elements = tap.elements({});
		var style = {
		base: {
			color: '#535353',
			lineHeight: '18px',
			fontFamily: 'sans-serif',
			fontSmoothing: 'antialiased',
			fontSize: '16px',
			'::placeholder': {
			color: 'rgba(0, 0, 0, 0.26)',
			fontSize:'15px'
			}
		},
		invalid: {
			color: 'red'
		}
		};
		// input labels/placeholders
		var labels = {
			cardNumber:"Card Number",
			expirationDate:"MM/YY",
			cvv:"CVV",
			cardHolder:"Card Holder Name"
		};
		//payment options
		var paymentOptions = {
		currencyCode:["KWD","USD","SAR"],
		labels : labels,
		TextDirection:'ltr'
		}
		//create element, pass style and payment options
		var card = elements.create('card', {style: style},paymentOptions);
		//mount element
		card.mount('#element-container');
		//card change event listener
		card.addEventListener('change', function(event) {
		if(event.loaded){
			console.log("UI loaded :"+event.loaded);
			console.log("current currency is :"+card.getCurrency())
		}
		var displayError = document.getElementById('error-handler');
		if (event.error) {
			displayError.textContent = event.error.message;
		} else {
			displayError.textContent = '';
		}
		});

		// Handle form submission
	// 	var form = document.getElementById('payment-form');
	// 	form.addEventListener('submit', function(event) {
	// 	event.preventDefault();

	// 	tap.createToken(card).then(function(result) {
	// 		console.log(result);
	// 		if (result.error) {
	// 		// Inform the user if there was an error
	// 		var errorElement = document.getElementById('error-handler');
	// 		errorElement.textContent = result.error.message;
	// 		} else {
	// 		// Send the token to your server
	// 		var errorElement = document.getElementById('success');
	// 		errorElement.style.display = "block";
	// 		var tokenElement = document.getElementById('token');
	// 		tokenElement.textContent = result.id;
	// 		$(this).unbind('submit').submit();
	// 		tapTokenHandler(token)

	// 		}
	// 	});
	// });

	function tapTokenHandler(token) {
		// Insert the token ID into the form so it gets submitted to the server
		var form = document.getElementById('payment-form');
		var hiddenInput = document.createElement('input');
		hiddenInput.setAttribute('type', 'hidden');
		hiddenInput.setAttribute('name', 'tapToken');
		hiddenInput.setAttribute('value', token.id);
		form.appendChild(hiddenInput);


		// Submit the form
		form.submit();
		}
	</script>
@endsection