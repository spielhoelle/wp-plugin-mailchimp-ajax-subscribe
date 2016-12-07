jQuery(function($) {
	$('.tmcajax').on('submit',function(e) {
		currentForm = $(this)
		e.preventDefault();
		$('.tmcajax .fa-circle-o-notch').css({'display': 'inline-block'})
		$('.tmcajaxresponse').hide()

		var formData = $(this).serialize();

		$.ajax({
			method: 'POST',
			url: tommy_plugin_path['template_url'] + 'mc-endpoint.php',
			data: formData,
			dataType: 'json'
		}).success(function(data){
			$('.tmcajax .fa-circle-o-notch').css({'display': 'none'})

			console.log(data);
			if ( data.title == 'Member Exists') {
				currentForm.find('.tmcajaxresponse.welcome').show()
			}
			else if(data.id){
      	if(currentForm.hasClass('pending')){
	        currentForm.find('.tmcajaxresponse.opt-in').show()
	      } else if(currentForm.hasClass('subscribed')) {
		   		currentForm.find('.tmcajaxresponse.welcome').show()
	      }

      } else {
        currentForm.find('.tmcajaxresponse.error').show()
      }

    }).error(function(error){

			currentForm.find('.tmcajaxresponse').hide()
			$('.tmcajax .fa-circle-o-notch').css({'display': 'none'})

      currentForm.find('.tmcajaxresponse.error').show()
    });


	});
});
