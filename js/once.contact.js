$(document).ready(function () {
	// Load code mirror library & modes
	once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
	once.loadJSfile(once.path+'/libs/jquery-validation/dist/jquery.validate.js');
	
	// Load code mirror styles
	once.loadCSSfile('http://www.css-spinners.com/css/spinners.css');
	
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
				// If response ok refresh logo
				if(data.status!='ok'){
					$(".message-sent").show();
					$(".message-error").hide();
					
					$("textarea").attr("disabled",true);
					
					$("input").attr("disabled",true);
					
					console.log("message sent!");
				}else{
					$(".message-sent").hide();
					$(".message-error").show();
					
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