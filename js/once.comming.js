/**
 * Version: 1.0, 30.06.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Comming plugin (once.comming)
 *
*/

$(document).ready(function () {
	// Load code mirror library & modes
	once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
	
	if($("#commingPlugin").length>0){
		// Initialize commingForm
		once.comming.forms.commingForm($(this));
	}
});

once.comming = {
}

once.comming.forms = {
	commingForm: function(obj){
		$("#commingForm").attr("action",once.path+"/ajax.php?c=comming&o=api_key_request");
		var options = {
			dataType:  "json",
			success: function(data){
				$('#comming-modal .modal:first').modal({
					backdrop: false,
					show: 'false'
				});
				if(data.status=='ok'){
					$('#comming-modal').removeClass('nosubscribed');
					$('#comming-modal').addClass('subscribed');
					$('#comming-modal .message').removeClass("hidden");
					$('#comming-modal .error').addClass("hidden");
				}else{
					$('#comming-modal').removeClass('subscribed');
					$('#comming-modal').addClass('nosubscribed');
					$('#comming-modal .message').addClass("hidden");
					$('#comming-modal .error').removeClass("hidden");
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: importForm");
			}
		};
		$("#commingForm").ajaxForm(options);
	},
}