<html>
  <head>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
  </head>
    <style>
      body {
        text-align: center;
        padding: 40px 0;
        background: #EBF0F5;
      }
        h1 {
          color: #88B04B;
          font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
          font-weight: 900;
          font-size: 40px;
          margin-bottom: 10px;
        }
        p {
          color: #404F5E;
          font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
          font-size:20px;
          margin: 0;
        }
      i {
        color: #9ABC66;
        font-size: 100px;
        line-height: 200px;
        margin-left:-15px;
      }
      .card {
        background: white;
        padding: 60px;
        border-radius: 4px;
        box-shadow: 0 2px 3px #C8D0D8;
        display: inline-block;
        margin: 0 auto;
      }
    </style>
    <body>
      <div class="card">
      <div style="margin:0 auto;">
        
      @if(empty($web_setting[15]->value))
        @if($web_setting[66]->value=='1' and $web_setting[67]->value=='0')
      		<img src="{{asset('images/admin_logo/logo-android-blue-v1.png')}}" class="ionic-hide">
        	<img src="{{asset('images/admin_logo/logo-ionic-blue-v1.png')}}" class="android-hide">
        @elseif($web_setting[66]->value=='1' and $web_setting[67]->value=='1' or $web_setting[66]->value=='0' and $web_setting[67]->value=='1')
   			<img src="{{asset('images/admin_logo/logo-laravel-blue-v1.png')}}" class="website-hide">
    	@endif
    @else
    	<img style="width: 60%" src="{{asset('').$web_setting[15]->value}}">
    @endif

      </div>
        @php($policy = \App\Models\Core\Pages::where('slug','Privacy-Policy')->leftJoin('pages_description', 'pages_description.page_id', '=', 'pages.page_id')->first())
        <p>{!!$policy["description"]!!}</p>
      </div>
    </body>
</html>
