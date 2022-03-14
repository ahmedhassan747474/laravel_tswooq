@extends('admin.layout')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> {{ trans('labels.ViewOrder') }} <small> {{ trans('labels.ViewOrder') }}...</small> </h1>
    <ol class="breadcrumb">
      <li><a href="{{ URL::to('admin/dashboard/this_month')}}"><i class="fa fa-dashboard"></i> {{ trans('labels.breadcrumb_dashboard') }}</a></li>
      <li><a href="{{ URL::to('admin/orders_likecard/display')}}"><i class="fa fa-dashboard"></i>  {{ trans('labels.ListingAllOrders') }}</a></li>
      <li class="active"> {{ trans('labels.ViewOrder') }}</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="invoice" style="margin: 15px;">
      <!-- title row -->
      @if(session()->has('message'))
       <div class="col-xs-12">
       <div class="row">
      	<div class="alert alert-success alert-dismissible">
           <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
           <h4><i class="icon fa fa-check"></i> {{ trans('labels.Successlabel') }}</h4>
            {{ session()->get('message') }}
        </div>
        </div>
        </div>
        @endif
        @if(session()->has('error'))
        <div class="col-xs-12">
      	<div class="row">
        	<div class="alert alert-warning alert-dismissible">
               <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
               <h4><i class="icon fa fa-warning"></i> {{ trans('labels.WarningLabel') }}</h4>
                {{ session()->get('error') }}
            </div>
          </div>
         </div>
        @endif
      <div class="row">
        <div class="col-xs-12">
          <a href="{{ URL::to('admin/orders_likecard/invoiceprint/'.$order->id)}}" target="_blank" class="btn btn-default pull-right"><i class="fa fa-print"></i> {{ trans('labels.Print') }}</a>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <img src="{{ asset('/images/admin_logo/logo_print.jpeg') }}" height="100" width="150" class="float-right">
      <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
          {{ trans('labels.CustomerInfo') }}:
         
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          {{ trans('labels.ShippingInfo') }}
         
        </div>
        <!-- /.col -->
        <!--<div class="col-sm-4 invoice-col">-->
        <!-- {{ trans('labels.BillingInfo') }}-->
 
        <!--</div>-->
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-striped">
            <thead>
            <tr>
              <th>id</th>
              <th>{{ trans('labels.Image') }}</th>
              <th>{{ trans('labels.ProductName') }}</th>
              <th>serialCode</th>
              <th>serialNumber</th>
              <th>validTo</th>
            </tr>
            </thead>
            <tbody>
           @if($order_details!=null)
            @foreach($order_details->serials??[]  as $product)

            <tr>
                <td>{{  $product->productId }}</td>
                <td >
                   <img src="{{ $product->productImage }}" width="60px"> <br>
                </td>
                 <td  width="30%">
                    {{  $product->productName }}<br>
                </td>
                <td>{{$product->serialCode}}</td>
                <td>{{$product->serialNumber}}</td>
                <td>{{$product->validTo}}</td>
           

           
             </tr>
            @endforeach
            @endif

            </tbody>
          </table>
        </div>
        <!-- /.col -->

      </div>
      <!-- /.row -->

      <div class="row">
        <!-- accepted payments column -->
        
        <!-- /.col -->
        <div class="col-xs-4">
          <!--<p class="lead"></p>-->

          <div class="table-responsive ">
            <table class="table order-table">
              <tr>
                <td>
                  {{ $data['orderNumber'] }}
                </td>
              </tr>
  
            </table>
          </div>

        </div>

        <div class="col-xs-4">
          <!--<p class="lead"></p>-->

          <div class="table-responsive ">
            <table class="table order-table">
              
              <tr>
                <td>
                  {{ $data['serialNumber'] }}
                </td>
              </tr>
            </table>
          </div>

        </div>

        <div class="col-xs-4">
          <!--<p class="lead"></p>-->

          <div class="table-responsive ">
            <table class="table order-table">
              
              <tr>
                <td>
                  {{ $data['serialCode'] }}
                </td>
              </tr>
            </table>
          </div>

        </div>
   

    

      </div>
      <!-- /.row -->

      <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-7">
          <p class="lead" style="margin-bottom:10px">{{ trans('labels.PaymentMethods') }}:</p>
          <p class="text-muted well well-sm no-shadow" style="text-transform:capitalize">
              {{optional($order->orderCard)->payment_method}}
          </p>
        


        
        </div>
        <!-- /.col -->
        <div class="col-xs-5">
          <!--<p class="lead"></p>-->

          <div class="table-responsive ">
            <table class="table order-table">
              <tr>
                <th style="width:50%">orderCreateDate:</th>
                <td>
                      @if($order_details!=null)

                     {{$order_details->orderCreateDate??'01-01-1970'}}
                     @endif
                  </td>
              </tr>
              <!--<tr>-->
              <!--  <th>{{ trans('labels.Tax') }}:</th>-->
              <!--  <td>-->
                 
              <!--    </td>-->
              <!--</tr>-->

              <tr>
                <th>{{ trans('labels.Total') }}:</th>
                <td>
                    @if($order_details!=null)

                     {{$order_details->orderFinalTotal??0}}
                     @endif

                  </td>
              </tr>
            </table>
          </div>

        </div>
   

    

      </div>
      <!-- /.row -->


    </section>
  <!-- /.content -->
</div>
@if($result['commonContent']['setting']['is_enable_location']==1 and $result['commonContent']['setting']['google_map_api'] != '')
<script src="https://www.gstatic.com/firebasejs/5.3.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.3.0/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.3.0/firebase-database.js"></script>
    <script>
/**
 * Firebase config block.
 */
var config = {
    apiKey: "{{$result['commonContent']['setting']['google_map_api']}}",
    authDomain: "{{$result['commonContent']['setting']['auth_domain']}}",
    databaseURL: "{{$result['commonContent']['setting']['database_URL']}}",
    projectId: "{{$result['commonContent']['setting']['projectId']}}",
    storageBucket: "{{$result['commonContent']['setting']['storage_bucket']}}",
    messagingSenderId: "{{$result['commonContent']['setting']['messaging_senderid']}}",
    appId: "{{$result['commonContent']['setting']['firebase_appid']}}"
};
  
  firebase.initializeApp(config);

/**
 * Data object to be written to Firebase.
 */
var data = {sender: 456456, timestamp: null, lat: null, lng: null};

function makeInfoBox(controlDiv, map) {
  // Set CSS for the control border.
  var controlUI = document.createElement('div');
  controlUI.style.boxShadow = 'rgba(0, 0, 0, 0.298039) 0px 1px 4px -1px';
  controlUI.style.backgroundColor = '#fff';
  controlUI.style.border = '2px solid #fff';
  controlUI.style.borderRadius = '2px';
  controlUI.style.marginBottom = '22px';
  controlUI.style.marginTop = '10px';
  controlUI.style.textAlign = 'center';
  controlDiv.appendChild(controlUI);

  // Set CSS for the control interior.
  var controlText = document.createElement('div');
  controlText.style.color = 'rgb(25,25,25)';
  controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
  controlText.style.fontSize = '100%';
  controlText.style.padding = '6px';
  controlText.textContent =
      'The map shows all clicks made in the last 10 minutes.';
  controlUI.appendChild(controlText);
}

      /**
      * Starting point for running the program. Authenticates the user.
      * @param {function()} onAuthSuccess - Called when authentication succeeds.
      */
      function initAuthentication(onAuthSuccess) {
        firebase.auth().signInAnonymously().catch(function(error) {
          console.log(error.code + ', ' + error.message);
        }, {remember: 'sessionOnly'});

        firebase.auth().onAuthStateChanged(function(user) {
          if (user) {
            data.sender = user.uid;
            onAuthSuccess();
          } else {
            // User is signed out.
          }
        });
      }

      /**
       * Creates a map object with a click listener and a heatmap.
       */
      function initMap() {
        var map = new google.maps.Map(document.getElementById('googleMap'), {
          center: {lat: 0, lng: 0},
          zoom: 3,
          styles: [{
            featureType: 'poi',
            stylers: [{ visibility: 'off' }]  // Turn off POI.
          },
          {
            featureType: 'transit.station',
            stylers: [{ visibility: 'off' }]  // Turn off bus, train stations etc.
          }],
          disableDoubleClickZoom: true,
          streetViewControl: false,
        });

        // Create the DIV to hold the control and call the makeInfoBox() constructor
        // passing in this DIV.
        var infoBoxDiv = document.createElement('div');
        makeInfoBox(infoBoxDiv, map);
        map.controls[google.maps.ControlPosition.TOP_CENTER].push(infoBoxDiv);

        // Listen for clicks and add the location of the click to firebase.
        map.addListener('click', function(e) {
          data.lat = e.latLng.lat();
          data.lng = e.latLng.lng();
          addToFirebase(data);
        });

        // Create a heatmap.
        var heatmap = new google.maps.visualization.HeatmapLayer({
          data: [],
          map: map,
          radius: 16
        });

        initAuthentication(initFirebase.bind(undefined, heatmap));
      }

      /**
       * Set up a Firebase with deletion on clicks older than expiryMs
       * @param {!google.maps.visualization.HeatmapLayer} heatmap The heatmap to
       */
      function initFirebase(heatmap) {

        // 10 minutes before current time.
        var startTime = new Date().getTime() - (60 * 10 * 1000);

        // Reference to the clicks in Firebase.
        var clicks = firebase.database().ref('clicks');

        // Listen for clicks and add them to the heatmap.
        clicks.orderByChild('timestamp').startAt(startTime).on('child_added',
          function(snapshot) {
            // Get that click from firebase.
            var newPosition = snapshot.val();
            var point = new google.maps.LatLng(newPosition.lat, newPosition.lng);
            var elapsedMs = Date.now() - newPosition.timestamp;

            // Add the point to the heatmap.
            heatmap.getData().push(point);

            // Request entries older than expiry time (10 minutes).
            var expiryMs = Math.max(60 * 10 * 1000 - elapsedMs, 0);

            // Set client timeout to remove the point after a certain time.
            window.setTimeout(function() {
              // Delete the old point from the database.
              snapshot.ref.remove();
            }, expiryMs);
          }
        );

        // Remove old data from the heatmap when a point is removed from firebase.
        clicks.on('child_removed', function(snapshot, prevChildKey) {
          var heatmapData = heatmap.getData();
          var i = 0;
          while (snapshot.val().lat != heatmapData.getAt(i).lat()
            || snapshot.val().lng != heatmapData.getAt(i).lng()) {
            i++;
          }
          heatmapData.removeAt(i);
        });
      }

      /**
       * Updates the last_message/ path with the current timestamp.
       * @param {function(Date)} addClick After the last message timestamp has been updated,
       *     this function is called with the current timestamp to add the
       *     click to the firebase.
       */
      function getTimestamp(addClick) {
        // Reference to location for saving the last click time.
        var ref = firebase.database().ref('last_message/' + data.sender);

        ref.onDisconnect().remove();  // Delete reference from firebase on disconnect.

        // Set value to timestamp.
        ref.set(firebase.database.ServerValue.TIMESTAMP, function(err) {
          if (err) {  // Write to last message was unsuccessful.
            console.log(err);
          } else {  // Write to last message was successful.
            ref.once('value', function(snap) {
              addClick(snap.val());  // Add click with same timestamp.
            }, function(err) {
              console.warn(err);
            });
          }
        });
      }

      /**
       * Adds a click to firebase.
       * @param {Object} data The data to be added to firebase.
       *     It contains the lat, lng, sender and timestamp.
       */
      function addToFirebase(data) {
        getTimestamp(function(timestamp) {
          // Add the new timestamp to the record data.
          data.timestamp = timestamp;
          var ref = firebase.database().ref('clicks').push(data, function(err) {
            if (err) {  // Data was not written to firebase.
              console.warn(err);
            }
          });
        });
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?=$result['commonContent']['setting']['google_map_api']?>&libraries=visualization&callback=initMap">
    </script>
    @endif

@endsection
