/**
 * Version: 1.0, 04.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Account plugin (once.account)
 *
*/
$(document).ready(function () {
	once.loadCSSfile('http://css-spinners.com/css/spinner/spinner.css');
	once.loadCSSfile('css/ionicons.min.css');
	
	// Load code mirror library & modes
	once.loadJSfile('/once/libs/jquery-form/jquery.form.js');
	//once.loadJSfile('//oss.maxcdn.com/jquery.form/3.50/jquery.form.min.js');
	once.loadJSfile('/once/libs/jquery-validation/dist/jquery.validate.js');
	//once.loadJSfile('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js');

	if($("#accountPlugin").length>0){
		// Open page via click
		$("#accountPlugin .dropdown-menu a").click(function () {
			once.account.loadPage($(this));
		});
		
		// Change logo
		$("#accountPluginContent .item-photo").click(function () {
			once.account.itemEditPhoto($(this));
		});
		
		// Initialize editForm & uploadImage
		once.account.form.editForm($(this));
		once.account.form.uploadImage($(this));
	}
});


once.account = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
	loadPage: function(obj){
		// Remove all hightlights
		$("#accountPlugin a").removeClass("current");
		// Highlight selected object
		obj.addClass("current");
		// Call for items list with selected category
		$('#accountPluginContent').css("visibility","hidden");
		$('#accountPluginLoader').show();
		$("#accountPluginContent").load(once.path+"/view.php?c=account&o="+obj.data('page'), function() {
			$('#accountPluginLoader').hide();
			$('#accountPluginContent').css("visibility","visible");
		})
		.error(function() { console.log("Views Error: account-"+obj.data('page')); });
	},
	itemTerminate: function(){
		$('#accountPluginContent .step').hide();
		$('#accountPluginContent .step2').show();
	},
	itemEditPhoto: function(obj){
		$("#uploadImage input[type='file']").click();
	},
}

once.account.form = {
	editForm: function(obj){
		// First validate
		var container = $('#editForm .message-error');
		
		// validate the form when it is submitted
		var	validator = $("#editForm").validate({
			errorContainer: container,
			errorLabelContainer: $("ol", container),
			wrapper: 'li',
			rules: {
				username: {
					required: true,
					minlength: 3
				},
				firstname: {
					required: true,
					minlength: 3
				},
				lastname: {
					required: true,
					minlength: 3
				}
			}
		});
		
		$("#editForm").attr("action",once.path+"/ajax.php?c=account&o=save_profile");
		var options = {
		dataType:  "json",
			success: function(data){
				// If response ok 
				if(data.status=='ok'){
					$("#editForm .message-success").show();
					$("#editForm .message-error").hide();
					
					setTimeout(function(){
						$("#editForm .message-success").hide();
					},700);
					
					console.log("account profile updated");
				}else{
					$("#editForm .message-sent").hide();
					$("#editForm .message-error").show();
						
					var str='';
					var length=data.errors.length;

					if(length){
						for(var i=0; i<length; i++){
							str+='<li>'+data.errors[i]+'</li>';
						}
					}
						
					$("#editForm .message-error").find("ol").show();
					$("#editForm .message-error").find("ol").html(str);

					console.log("Action Error: "+data.error);
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: editForm");
			}
		}; 
		$("#editForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#editForm input[name=\"name\"]").focus();
		},700);
	},
	uploadImage: function(obj){
		$(document).ready(function () {
			$("#uploadImage input[type='file']").change(function(e) {
				$("#uploadImage input[type='submit']").click();
			});
		});
		
		var options = { 
			dataType:  "json",
			success: function(data){
				// If response ok refresh photo
				if(data.status=='ok'){
					$("#item-photo").attr("src","/once/users/"+$("#accountPlugin").data('id')+"/thumbnail.png?"+Math.random());
					
					console.log("Photo refreshed");
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: uploadImage");
			}
		}; 
		$("#uploadImage").ajaxForm(options);
	},
	informationForm: function(obj){
		// First validate
		var container = $('#informationForm .message-error');
		
		// validate the form when it is submitted
		var	validator = $("#informationForm").validate({
			errorContainer: container,
			errorLabelContainer: $("ol", container),
			wrapper: 'li',
			rules: {
				firstname: {
					minlength: 3
				},
				lastname: {
					minlength: 3
				}
			}
		});
		
		$("#informationForm").attr("action",once.path+"/ajax.php?c=account&o=save_information");
		var options = {
		dataType:  "json",
			success: function(data){
				// If response ok 
				if(data.status=='ok'){
					$("#informationForm .message-success").show();
					$("#informationForm .message-error").hide();
					
					setTimeout(function(){
						$("#informationForm .message-success").hide();
					},700);
					
					console.log("account profile updated");
				}else{
					$("#informationForm .message-sent").hide();
					$("#informationForm .message-error").show();
						
					var str='';
					var length=data.errors.length;

					if(length){
						for(var i=0; i<length; i++){
							str+='<li>'+data.errors[i]+'</li>';
						}
					}

						
					$("#informationForm .message-error").find("ol").show();
					$("#informationForm .message-error").find("ol").html(str);

					console.log("Action Error: "+data.error);
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: informationForm");
			}
		}; 
		$("#informationForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#informationForm input[name=\"name\"]").focus();
		},700);
	},
	socialForm: function(obj){
		// First validate
		var container = $('#socialForm .message-error');
		
		// validate the form when it is submitted
		var	validator = $("#socialForm").validate({
			errorContainer: container,
			errorLabelContainer: $("ol", container),
			wrapper: 'li',
			rules: {
				firstname: {
					minlength: 3
				},
				lastname: {
					minlength: 3
				}
			}
		});
		
		$("#socialForm").attr("action",once.path+"/ajax.php?c=account&o=save_social");
		var options = {
		dataType:  "json",
			success: function(data){
				// If response ok 
				if(data.status=='ok'){
					$("#socialForm .message-success").show();
					$("#socialForm .message-error").hide();
					
					setTimeout(function(){
						$("#socialForm .message-success").hide();
					},700);
					
					console.log("account profile updated");
				}else{
					$("#socialForm .message-sent").hide();
					$("#socialForm .message-error").show();
						
					var str='';
					var length=data.errors.length;

					if(length){
						for(var i=0; i<length; i++){
							str+='<li>'+data.errors[i]+'</li>';
						}
					}
						
					$("#socialForm .message-error").find("ol").show();
					$("#socialForm .message-error").find("ol").html(str);

					console.log("Action Error: "+data.error);
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: socialForm");
			}
		}; 
		$("#socialForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#socialForm input[name=\"name\"]").focus();
		},700);
	},
	changepasswordForm: function(obj){
		// First validate
		var container = $('#changepasswordForm .message-error');
		
		// validate the form when it is submitted
		var	validator = $("#changepasswordForm").validate({
			errorContainer: container,
			errorLabelContainer: $("ol", container),
			wrapper: 'li',
			rules: {
				currentpassword: {
					required: true,
					minlength: 3
				},
				password: {
					required: true,
					minlength: 3
				},
				confirmpassword: {
					required: true,
					minlength: 3
				}
			}
		});
		
		$("#changepasswordForm").attr("action",once.path+"/ajax.php?c=account&o=change_password");
		var options = {
		dataType:  "json",
			success: function(data){
				// If response ok 
				if(data.status=='ok'){
					$("#changepasswordForm .message-success").show();
					$("#changepasswordForm .message-error").hide();
					
					$("#changepasswordForm input").val('');
					
					setTimeout(function(){
						$("#changepasswordForm .message-success").hide();
					},700);
					
					console.log("password updated");
				}else{
					$("#changepasswordForm .message-success").hide();
					$("#changepasswordForm .message-error").show();
						
					var str='';
					var length=data.errors.length;

					if(length){
						for(var i=0; i<length; i++){
							str+='<li>'+data.errors[i]+'</li>';
						}
					}
						
					$("#changepasswordForm .message-error").find("ol").show();
					$("#changepasswordForm .message-error").find("ol").html(str);

					console.log("Action Error: "+data.error);
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: changepasswordForm");
			}
		}; 
		$("#changepasswordForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#changepasswordForm input[name=\"name\"]").focus();
		},700);
	},
	terminateForm: function(obj){
		// First validate
		var container = $('#terminateForm .message-error');
		
		// validate the form when it is submitted
		var	validator = $("#terminateForm").validate({
			errorContainer: container,
			errorLabelContainer: $("ol", container),
			wrapper: 'li',
			rules: {
				currentpassword: {
					required: true,
					minlength: 3
				},
				password: {
					required: true,
					minlength: 3
				},
				confirmpassword: {
					required: true,
					minlength: 3
				}
			}
		});
		
		$("#terminateForm").attr("action",once.path+"/ajax.php?c=account&o=account_terminiate");
		var options = {
		dataType:  "json",
			success: function(data){
				// If response ok 
				if(data.status=='ok'){
					$('#accountPluginContent .step').hide();
					$('#accountPluginContent .step3').show();
		
					console.log("account profile updated");
				}else{
					$("#terminateForm .message-sent").hide();
					$("#terminateForm .message-error").show();
						
					var str='';
					var length=data.errors.length;

					if(length){
						for(var i=0; i<length; i++){
							str+='<li>'+data.errors[i]+'</li>';
						}
					}
						
					$("#terminateForm .message-error").find("ol").show();
					$("#terminateForm .message-error").find("ol").html(str);

					console.log("Action Error: "+data.error);
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: terminateForm");
			}
		}; 
		$("#terminateForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#terminateForm input[name=\"name\"]").focus();
		},700);
	},
}