/**
 * Version: 1.0, 30.06.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Comming plugin (once.newsletter)
 *
*/

$(document).ready(function () {
	// Load code mirror library & modes
	once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
	
	if($("#newsletterPlugin").length>0){
		// Initialize commingsoonForm
		once.newsletter.forms.commingsoonForm($(this));
	}
});

once.newsletter = {
}

once.newsletter.forms = {
	commingsoonForm: function(obj){
		$("#commingsoonForm").attr("action",once.path+"/ajax.php?c=newsletter&o=api_key_request");
		var options = {
			dataType:  "json",
			success: function(data){
				$('#commingsoon-modal .modal:first').modal({
					backdrop: false,
					show: 'false'
				});
				if(data.status=='ok'){
					$('#commingsoon-modal').removeClass('nosubscribed');
					$('#commingsoon-modal').addClass('subscribed');
					$('#commingsoon-modal .message').removeClass("hidden");
					$('#commingsoon-modal .error').addClass("hidden");
				}else{
					$('#commingsoon-modal').removeClass('subscribed');
					$('#commingsoon-modal').addClass('nosubscribed');
					$('#commingsoon-modal .message').addClass("hidden");
					$('#commingsoon-modal .error').removeClass("hidden");
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: importForm");
			}
		};
		$("#newsletterForm").ajaxForm(options);
	},
}