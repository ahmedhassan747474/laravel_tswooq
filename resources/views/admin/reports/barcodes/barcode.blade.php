@extends('admin.layout')

@push('styles')
{{-- <style>
    svg{
        width: 5cm !important;
        height: 2.5cm !important;
        
    }
</style> --}}
    
@endpush
@section('content')
<div class="content-wrapper">

    <section class="content-header">

        <h1>{{ trans('labels.barcode') }}</h1>

        {{-- <ol class="breadcrumb">
            <li> <a href="{{route('dashboard.welcome')}}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
            <li class="active"><i class="fa fa-category"></i> @lang('site.barcodes')</li>
        </ol> --}}
    </section>

    <section class="content">

        <div class="box box-primary">

            <div class="box-header with-border">
                <h1 class="box-title"> {{ trans('labels.barcode') }} <small>{{$barcodes->count()}}</small></h1>

                <form action="{{ URL::to('admin/products-barcode')}}" method="get">

                    <div class="row">

                        <div class="col-md-4">
                            <input type="text" name="search" value="{{request()->search}}" class="form-control" placeholder="@lang('site.search')">
                        </div>

                        <div class="col-md-4">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> @lang('site.search')</button>
                        </div>
                        <!--<button class="btn  btn-primary print-btn col-md-2"> <i class="fa fa-print"> {{ trans('labels.Print') }} </i> </button>-->
                        <button class="btn  btn-primary  col-md-2" id="downloa" onclick="downloadall(this,{{count($barcodes)}})"> <i class="fa fa-print"> {{ trans('labels.Print') }} </i> </button>

                    </div>
                </form>
            </div> {{--end of box header--}} 

            <div class="box-body row" id="print-area">

                @if($barcodes->count() > 0)

                            @foreach($barcodes as $index=>$barcode)
                                    <div class="col-md-12 col-xs-12" id="d{{$index}}" onclick="downloadth(this)" style="cursor:pointer;margin-bottom: 20px;margin-top: 0px;" >
                                        {!! QrCode::size(70)->generate($barcode->products_id); !!}
                                        {{-- <img src="data:image/png;base64, {!! base64_encode(QrCode::size(70)->format('png')->generate($barcode->products_id)) !!} "> --}}


                                            {{-- // echo '<img style="padding-top: 0px"  src="data:image/png;base64,' . base64_encode($generator->getBarcode($barcode->barcode, $generator::TYPE_CODE_128)) . '"> '; --}}
                                            <span style="display: inline-block;position: absolute;width: 39px;font-size: 12px;text-align: right;" class="column">
                                            {{-- Nada ---  --}}
                                            {{str_limit($barcode->products_name, $limit = 10, $end = '...') }} <br>
                                            السعر: {{$barcode->products_price}} 
                                            </span>
                                            
                                    </div>
                                     <!--{{-- @if(($index+1) % 4 == 0) <div class="col-md-12"><hr></div>  @endif --}}-->
                            @endforeach

                @else
                    <tr>
                        <h4>@lang('site.no_records')</h4>
                    </tr>
                @endif

            </div> {{--end of box body--}}

        </div> {{--  end of box--}}

    </section><!-- end of content -->

</div><!-- end of content wrapper -->

@endsection

@push('scripts')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

<script src="https://superal.github.io/canvas2image/canvas2image.js"></script>

<script>
     $(document).on('click', '.print-btn', function (e) {
e.preventDefault();
// $(this).prev().children('.company_date').removeClass('hide');
$('#print-area').printThis();
// $(this).prev().children('.company_date').addClass('hide');


// $('#print-area').append('<h3 id="c_name"> <span >أحمد على</span></h3>');
//    setTimeout(function () { $('#c_name').remove();} , 1000);

}); //end of print function

$(document).on('click', '#printThisBarcode', function (e) {
e.preventDefault();
var myBarcode = $(this).html();
for (let i = 0; i < 10; i++) {
    $(this).append(myBarcode)
}
$(this).printThis();
}); //end of print function



function downloadth(th){
    var id =th.id;
    var element = $("#"+id); // global variable     
			 var getCanvas; //global variable 
			 html2canvas(element, {  
               
               allowTaint: false,
                            taintTest: true,
               onrendered: function (canvas) { 
               getCanvas = canvas;
               
                                              return Canvas2Image.saveAsPNG(getCanvas);

               var imgageData = getCanvas.toDataURL("image/png", 1);
			 		//Now browser starts downloading it instead of just showing it                
			 		var newData = imgageData.replace(/^data:image\/png/, "data:application/octet-stream");
			 		console.log(newData);
			 		$("#"+id).attr("download", "Check.png").attr("href", newData); 
               }      
             }); 
             
			 //function sa() {     
    //                         //  return Canvas2Image.saveAsPNG(getCanvas);

			 //		var imgageData = getCanvas.toDataURL("image/png", 1);
			 //		//Now browser starts downloading it instead of just showing it                
			 //		var newData = imgageData.replace(/^data:image\/png/, "data:application/octet-stream");
			 //		console.log(newData);
			 //		$("#"+id).attr("download", "Check.png").attr("href", newData);         
			 //}   
			 //sa();
}

function downloadall(th,len){
    // var id =th.id;
    // alert(len);
    
    for(let i=0 ; i<len ; i++){
        var id = "d"+i;
        var element = $("#"+id); // global variable     
			 var getCanvas; //global variable 
			 html2canvas(element, {  
               
               allowTaint: false,
                            taintTest: true,
               onrendered: function (canvas) { 
               getCanvas = canvas;
               
                return Canvas2Image.saveAsPNG(getCanvas);

               var imgageData = getCanvas.toDataURL("image/png", 1);
			 		//Now browser starts downloading it instead of just showing it                
			 		var newData = imgageData.replace(/^data:image\/png/, "data:application/octet-stream");
			 		console.log(newData);
			 		$(th.id).attr("download", "Check.png").attr("href", newData); 
               }      
             });  
    }
   
           
}
$(document).ready(function () {  
			 var element = $("#print-area"); // global variable     
			 var getCanvas; //global variable 
			 html2canvas(element, {  
               encodingOptions :1,
               windowWidth:600,
               windowWidth:400,
               allowTaint: false,
                            taintTest: true,
               onrendered: function (canvas) { 
               getCanvas = canvas;                
               }      
             }); 
			 $("#download").on('click', function () {     
                             return Canvas2Image.saveAsPNG(getCanvas);

			 		var imgageData = getCanvas.toDataURL("image/png", 1);
               console.log(imgageData);
			 		//Now browser starts downloading it instead of just showing it                
			 		var newData = imgageData.replace(/^data:image\/png/, "data:application/octet-stream");
			 		$("#download").attr("download", "Check.png").attr("href", newData);         
			 });    
		});
            
</script>


    
@endpush