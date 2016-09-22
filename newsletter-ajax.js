jQuery(function($) {
	$('.tmcajax').on('submit',function(e) {
		currentForm = $(this)
		// Highjack the submit button, we will do it ourselves
		e.preventDefault();
		$('.tmcajax .fa-circle-o-notch').css({'visibility': 'visible'})

		// uncomment next line & check console to see if button works
		// console.log('submit button worked!');

		// store all the form data in a variable
		var formData = $(this).serialize();

		$.ajax({
			method: 'POST',
			url: tommy_plugin_path['template_url'] + 'mc-endpoint.php',
			data: formData,
			dataType: 'json'
		}).success(function(data){
			$('.tmcajaxresponse').remove()
			$('.tmcajax .fa-circle-o-notch').css({'visibility': 'hidden'})

			console.log(data);
      
      if(data.id){
      	if(currentForm.hasClass('pending')){
		        //successful adds will have an id attribute on the object
		        currentForm.append('<div class="tmcajaxresponse success">Bitte, überprüfe dein Postfach.<br/> Wir haben dir eine Bestätigungsmail gesendet... <i class="fa fa-check fa-3x fa-fw"></i></div>')
		      } else if(currentForm.hasClass('subscribed')) {
  		   		currentForm.append('<div class="tmcajaxresponse success">Willkommen auf der Liste. <br/>Wir melden uns bald bei dir! <i class="fa fa-check fa-3x fa-fw"></i></div>')
		      }

      } else if (data.title == 'Member Exists') {
        //MC wil send back an error object with "Member Exists" as the title
        currentForm.append('<div class="tmcajaxresponse success">Du bist schon auf der Liste registriert. Bis bald! <i class="fa fa-check fa-3x fa-fw"></i></div>')
      } else {
        //something went wrong with the API call
        currentForm.append('<div class="tmcajaxresponse times">Ooops, da gab es wohl ein Problem. Versuch es doch bitte später noch einmal.<i class="fa fa-error fa-3x fa-fw"></i></div>')
      }

    }).error(function(error){
    	console.log(error);

			$('.tmcajaxresponse').remove()
			$('.tmcajax .fa-circle-o-notch').css({'visibility': 'hidden'})

      //the AJAX function returned a non-200, probably a server problem
      currentForm.append('<div class="tmcajaxresponse times">Ooops, da gab es wohl ein Problem. Versuch es doch bitte später noch einmal.<i class="fa fa-error fa-3x fa-fw"></i></div>')
    });


	});
});
