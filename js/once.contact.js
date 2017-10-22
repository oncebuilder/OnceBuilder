/**
 * Version: 1.0, 01.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Contact plugin (once.contact)
 *
*/

$(document).ready(function () {
	// Load code mirror library & modes
	once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
	once.loadJSfile(once.path+'/libs/jquery-validation/dist/jquery.validate.js');

	if($("#contactForm").length>0){
		// First validate
		var container = $('.message-error');

		// validate the form when it is submitted
		var validator = $("#contactForm").validate({
			errorContainer: container,
			errorLabelContainer: $("ol", container),
			wrapper: 'li'
		});
		
		// Initialize contactForm
		once.contact.forms.contactForm($(this));
	}
});

once.contact = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
}

once.contact.forms = {
	contactForm: function(obj){
		$("#contactForm").attr("action",once.path+"/ajax.php?c=contact&o=send_message");
		var options = {
			dataType:  "json",
			success: function(data){
				if(data.status=='ok'){
					$("#contactForm .message-sent").show();
					$("#contactForm  .message-error").hide();
					
					$("textarea").attr("disabled",true);
					
					$("input").attr("disabled",true);
					
					console.log("Message sent!");
				}else{
					var str='';
					var length=data.errors.length;
					if(length>0){
						for(var i=0; i<length; i++){
							str+='<li>'+data.errors[i]+'</li>';
						}
					}
					
					$("#contactForm .message-error").find("ol").show();
					$("#contactForm .message-error").find("ol").html(str);

					$("#contactForm .message-sent").hide();
					$("#contactForm .message-error").show();
					
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: importForm");
			}
		};
		$("#contactForm").ajaxForm(options);
	},
}