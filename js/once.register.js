/**
 * Version: 1.0, 01.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Register plugin (once.register)
 *
*/
$(document).ready(function () {
	// Load code mirror library & modes
	once.loadJSfile('/once/libs/jquery-form/jquery.form.js');
	//once.loadJSfile('//oss.maxcdn.com/jquery.form/3.50/jquery.form.min.js');
	once.loadJSfile('/once/libs/jquery-validation/dist/jquery.validate.js');
	//once.loadJSfile('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js');
	
	if($("#registerPlugin").length>0){
		// First validate
		var container = $('.message-error');

		// validate the form when it is submitted
		var	validator = $("#registerForm").validate({
			errorContainer: container,
			errorLabelContainer: $("ol", container),
			wrapper: 'li',
			rules: {
				email: {
					required: true,
					minlength: 3
				},
				password: {
					required: true,
					minlength: 3
				}
			}
		});
		// Toggle password
		$("#password-toggle").click(function(){
			var input=$("#registerPlugin input[name=\"password\"]");
			
			if(input.prop("type")=='text'){
				input.prop("type","password");
			}else{
				input.prop("type","text");
			}
			$(this).find("i").toggleClass("glyphicon-eye-open");
			$(this).find("i").toggleClass("glyphicon-eye-close");
		});

		// Initialize registerForm
		once.register.forms.registerForm($(this));
	}
});

once.register = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
}

once.register.forms = {
	registerForm: function(obj){
		$("#registerForm").attr("action",once.path+"/ajax.php?c=register&o=item_register");
		var options = {
			dataType:  "json",
			success: function(data){
				// If response ok refresh logo
				if(data.status=='ok'){
					$("#registerPlugin .message-error").hide();
					$("#registerPlugin .message-registred").show();

					$("#registerForm").hide();
					
					console.log("Activation sent!");
				}else{
					$("#registerPlugin .message-sent").hide();
					$("#registerPlugin .message-error").show();
					
					var str='';
					var length=data.errors.length;
					if(length>0){
						for(var i=0; i<length; i++){
							str+='<li>'+data.errors[i]+'</li>';
						}
					}
					
					$("#registerPlugin .message-error").find("ol").show();
					$("#registerPlugin .message-error").find("ol").html(str);

					console.log("Action Error: "+data.error);
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: registerForm");
			}
		};
		$("#registerForm").ajaxForm(options);
	},
}