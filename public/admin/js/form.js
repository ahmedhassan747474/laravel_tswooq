(function ($) {
	"use strict";
  //basicform submit
	$("#productform").on('submit', function(e){
		e.preventDefault();
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type: 'POST',
			url: this.action,
			data: new FormData(this),
			dataType: 'json',
			contentType: false,
			cache: false,
			processData:false,
			beforeSend: function() {
       			
       			$('.basicbtn').attr('disabled','')

    		},
			
			success: function(response){ 
				$('.basicbtn').removeAttr('disabled')
				Sweet('success',response)
				
				success(response)
			},
			error: function(xhr, status, error) 
			{
				$('.basicbtn').removeAttr('disabled')
				$('.errorarea').show();
				$.each(xhr.responseJSON.errors, function (key, item) 
				{
					Sweet('error',item)
					$("#errors").html("<li class='text-danger'>"+item+"</li>")
				});
				errosresponse(xhr, status, error);
			}
		})


	});


	$("#basicform").on('submit', function(e){
		

    //   myVar = setTimeout(loaderHide, 3000);
		e.preventDefault();
		
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type: 'POST',
			url: this.action,
			data: new FormData(this),
			dataType: 'json',
			contentType: false,
			cache: false,
			processData:false,
			beforeSend: function() {
				$("#loader").show();
       			$('.basicbtn').attr('disabled','')
				//    document.getElementById("loader").style.display = "";
      			// 	document.getElementById("submit").disabled = true;

    		},
			
			success: function(response){ 
				Swal.fire(
					response.toString(),
					'You clicked the button!',
					'success'
				  );
				document.getElementById("loader").style.display = "none";
      			document.getElementById("submit").disabled = false;
				  
				$('.basicbtn').removeAttr('disabled')
				Sweet('success',response)
				location.reload();
				
				success(response)

			},
			error: function(xhr, status, error) 
			{
				$('.basicbtn').removeAttr('disabled')
				$('.errorarea').show();
				$.each(xhr.responseJSON.errors, function (key, item) 
				{
					Sweet('error',item)
					$("#errors").html("<li class='text-danger'>"+item+"</li>")
				});
				errosresponse(xhr, status, error);
			}
		})


	});

	$(".basicform").on('submit', function(e){
		e.preventDefault();
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		var basicbtnhtml=$('.basicbtn').html();
		$.ajax({
			type: 'POST',
			url: this.action,
			data: new FormData(this),
			dataType: 'json',
			contentType: false,
			cache: false,
			processData:false,
			beforeSend: function() {
       			
       			$('.basicbtn').html("Please Wait....");
       			$('.basicbtn').attr('disabled','')

    		},
			
			success: function(response){ 
				$('.basicbtn').removeAttr('disabled')
				Sweet('success',response);
				$('.basicbtn').html(basicbtnhtml);
				success(response);
			},
			error: function(xhr, status, error) 
			{
				$('.basicbtn').html(basicbtnhtml);
				$('.basicbtn').removeAttr('disabled')
				$('.errorarea').show();
				$.each(xhr.responseJSON.errors, function (key, item) 
				{
					Sweet('error',item)
					$("#errors").html("<li class='text-danger'>"+item+"</li>")
				});
				errosresponse(xhr, status, error);
			}
		})


	});

	//id basicform1 when submit 
	$("#basicform1").on('submit', function(e){
		e.preventDefault();

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$.ajax({
			type: 'POST',
			url: this.action,
			data: new FormData(this),
			dataType: 'json',
			contentType: false,
			cache: false,
			processData:false,
			success: function(response){ 
				success(response)
			},
			error: function(xhr, status, error) 
			{
				$('.errorarea').show();

				$.each(xhr.responseJSON.errors, function (key, item) 
				{
					Sweet('error',item)
					$("#errors").html("<li class='text-danger'>"+item+"</li>")
				});
				errosresponse(xhr, status, error);
			}
		})
	});	
	
	$(".checkAll").on('click',function(){
		$('input:checkbox').not(this).prop('checked', this.checked);
	});
})(jQuery);	
