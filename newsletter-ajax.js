jQuery(function($) {
	$('.tmcajax').on('submit',function(e) {

		// Highjack the submit button, we will do it ourselves
		e.preventDefault();
		$('.tmcajax .fa-circle-o-notch').css({'visibility': 'visible'})

		// uncomment next line & check console to see if button works
		// console.log('submit button worked!');

		// store all the form data in a variable
		var formData = $(this).serialize();

		// Let's make the call!
		// Replace the path to your own endpoint!
		$.post(tommy_plugin_path['template_url'] + 'mc-endpoint.php', formData, function(data) {			

			// opt in mail out
			if(data.status === 'pending' || data.status === 'subscribed') {
				//hide tooltip
				$('.tmcajaxresponse').remove()

				// Let us know!
		        $('.tmcajax .fa-circle-o-notch').css({'visibility': 'hidden'})
		        if(data.status == 'pending') {
		    		$('.tmcajax').append('<div class="tmcajaxresponse success">Bitte, überprüfe dein Postfach.<br/> Wir haben dir eine Bestätigungsmail gesendet...</div>')
		    	} else if (data.status == 'subscribed'){
		    		$('.tmcajax').append('<div class="tmcajaxresponse success">Willkommen auf der Liste. <br/>Wir melden uns bald bei dir!</div>')
		    	}


			} else {
				$('.tmcajaxresponse').remove()

				// Otherwise tell us why it didn't
		        $('.tmcajax .fa-circle-o-notch').css({'visibility': 'hidden'})
		        $('.tmcajax').append('<div class="tmcajaxresponse error">Ooops, da gab es wohl ein Problem. Versuch es doch bitte später noch einmal.</div>')
			}
		}, 'json');
	});
});
