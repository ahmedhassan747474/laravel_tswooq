@extends('admin.layout')

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
                        <button class="btn  btn-primary print-btn col-md-2"> <i class="fa fa-print"> {{ trans('labels.Print') }} </i> </button>

                    </div>
                </form>
            </div> {{--end of box header--}} 

            <div class="box-body row" id="print-area">

                @if($barcodes->count() > 0)

                            @foreach($barcodes as $index=>$barcode)
                                    <div class="col-xs-3 text-center" id="printThisBarcode" style="cursor:pointer;" >
                                        {!! QrCode::size(200)->generate($barcode->products_id); !!}
                                            {{-- // echo '<img style="padding-top: 15px"  src="data:image/png;base64,' . base64_encode($generator->getBarcode($barcode->barcode, $generator::TYPE_CODE_128)) . '"> '; --}}
                                            <span style="display: block;" class="text-center">
                                            {{-- Nada ---  --}}
                                            {{$barcode->products_name}} ---
                                            {{ trans('labels.SalePrice') }}: {{$barcode->products_price}} 
                                            </span>
                                            
                                    </div>
                                     @if(($index+1) % 4 == 0) <div class="col-md-12"><hr></div>  @endif
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

</script>
    
@endpush