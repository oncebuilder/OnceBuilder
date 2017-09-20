/**
 * Version: 1.0, 15.01.2017
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Mailbox plugin (once.mailbox)
 *
*/

once.mailbox = {
	loaded: false,
	keypress: false,
	initialized: function(){
		this.loaded=true;
	},
	
	// List function
	bulkAction: function(obj){
		// Set action value
		$("#checkForm input[name='action']").val(obj.data("action"));
		// Set action o
		$("#checkForm").attr("action",once.path+"/ajax.php?c=mailbox&o=bulk_action");
		// Submit form
		$("#checkForm").submit();
	},
	displayLimit: function(obj){
		// Call for set limit
		$.getJSON(once.path+"/ajax.php?c=mailbox&o=set_limit&limit="+$(obj).val(), function(data) {
			$(".pagination a:first").click();
		})
		.error(function() { console.log("Request Error: set_limit"); });
	},
	openPage: function(obj){
		// Open selected page with params
		$.get(once.path+"/view.php?c=mailbox&o="+$("#mailboxs-data").data("o")+"&type_id="+$("#mailboxs-data").data("type_id")+"&category_id="+$("#mailboxs-data").data("category_id")+"&sort_by="+$("#mailboxs-data").data("sort_by")+"&page="+obj.html()+"&ids="+$("#mailboxs-data").data("ids")+"&query="+$("#mailboxs-data").data("query"), function(data) {
			$("#content-body").html(data);
		})
		.error(function() { console.log("Request Error: "+$("#mailboxs-data").data("o")); });
	},
	sortAction: function(obj){
		if($("#mailboxs-data").data("ajax")){
			$("#sort-label").html(obj.html());
			// Refresh items list by sort key
			$.get(once.path+"/view.php?c=mailbox&o="+$("#mailboxs-data").data("o")+"&type_id="+$("#mailboxs-data").data("type_id")+"&category_id="+$("#mailboxs-data").data("category_id")+"&sort_by="+obj.data("sort")+"&page="+$("#mailboxs-data").data("page")+"&ids="+$("#mailboxs-data").data("ids")+"&query="+$("#mailboxs-data").data("query"), function(data) {
				$("#content-body").html(data);
			})
			.error(function() { alert("Couldn\'t load sort"); });
		}else{
			// Refresh items list by sort key
			$("#sort-label").html(obj.html());
			document.location.href='/mailbox?category_name='+$("#mailboxs-data").data('category')+'&sort_by='+obj.data("sort")+'&p='+$("#mailboxs-data").data("page")+'&query='+$("#mailboxs-data").data('query');
		}
	},

	// View function
	itemEditDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var id=$("#mailbox-data").data('id');
		var name=$("#mailbox-data .modal-header h4").html();

		var l = name.indexOf('i>');
		var y = name.indexOf('<button');
		var name =name.slice(l+2,y);
		
		// We need to confirm to delete
		var r = confirm("Delete "+name+"?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=mailbox&o=item_delete&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						$("#mailbox-data .item-close").click();
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=mailbox&o="+$("#mailboxs-data").data("o")+"&type_id="+$("#mailboxs-data").data("type_id")+"&category_id="+$("#mailboxs-data").data("category_id")+"&sort_by="+$("#mailboxs-data").data("sort_by")+"&page="+$("#mailboxs-data").data("page")+"&ids="+$("#mailboxs-data").data("ids")+"&query="+$("#mailboxs-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#mailboxs-data").data("o")); });
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: item_delete"); });
		}
	},
	itemEditForward: function(obj){//ok
		$("#mailbox-data .modal-title button").toggleClass("hidden");
		$("#mailbox-data #mail").toggleClass("hidden");
		$("#mailbox-data #form").toggleClass("hidden");
		$("#mailbox-data .modal-footer button[type='submit']").toggleClass("hidden");

		var reciver=$("#mailbox-data .mail-reciver").text();
		var title=$("#mailbox-data .mail-title").text();
		var message=$("#mailbox-data .mail-message").text();

		// Format as reply
		message='\n\n>>>>> '+reciver+' wrote:\n'+message;
		
		$("#newForm input[name='email_to']").val('');
		$("#newForm input[name='title']").val('Re: '+title);
		$("#newForm textarea[name='message']").val(message);
	},
	itemEditReply: function(obj){//ok
		$("#mailbox-data .modal-title button").toggleClass("hidden");
		$("#mailbox-data #mail").toggleClass("hidden");
		$("#mailbox-data #form").toggleClass("hidden");
		$("#mailbox-data .modal-footer button[type='submit']").toggleClass("hidden");

		var reciver=$("#mailbox-data .mail-reciver").text();
		var title=$("#mailbox-data .mail-title").text();
		var message=$("#mailbox-data .mail-message").text();

		// Format as reply
		message='\n\n>>>>> '+reciver+' wrote:\n'+message;
		
		$("#newForm input[name='email_to']").val(reciver);
		$("#newForm input[name='title']").val('Re: '+title);
		$("#newForm textarea[name='message']").val(message);
	},
	itemEditStar: function(obj){//ok
		// Call for star item
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=mailbox&o=item_star&id="+$("#mailbox-data").data("id"),
			success: function(data) {
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Read and open open dialog with selected page
					$("#item-open .modal").load("dialog.php?c=mailbox&o=open&id="+$("#mailbox-data").data("id")+"&nomodal", function() {
						
					})
					.error(function() { console.log("Dialog Error: publish"); });
					
					// Check for fonts icos
					var obj=$("#item_"+$("#mailbox-data").data("id")+" td i");
					var glyph = obj.hasClass("glyphicon");
					var fa = obj.hasClass("fa");
					// Switch states
					if(glyph){
						obj.toggleClass("glyphicon-star");
						obj.toggleClass("glyphicon-star-empty");
					}else if(fa){
						obj.toggleClass("fa-star");
						obj.toggleClass("fa-star-o");
					}
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_star"); });
	},
	itemDelete: function(obj){
		// Get varibles defined in rendering data-*
		var id=$(obj).parent().parent().data('id');
		var name=$("#item_"+id+" td.item-name").html();
		// We need to confirm to delete
		var r = confirm("Delete "+name+"?");
		if(r){
			// Call to item_delete with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=mailbox&o=item_delete&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=mailbox&o="+$("#mailboxs-data").data("o")+"&type_id="+$("#mailboxs-data").data("type_id")+"&category_id="+$("#mailboxs-data").data("category_id")+"&sort_by="+$("#mailboxs-data").data("sort_by")+"&page="+$("#mailboxs-data").data("page")+"&ids="+$("#mailboxs-data").data("ids")+"&query="+$("#mailboxs-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#mailboxs-data").data("o")); });
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: item_delete"); });
		}
	},
	itemStar: function(obj){//ok
		var col=obj.parent();
		var row=col.parent();
		// Call for star item
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=mailbox&o=item_star&id="+row.data("id"),
			success: function(data) {
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Check for fonts icos
					var glyph = $(obj).hasClass("glyphicon");
					var fa = $(obj).hasClass("fa");
					// Switch states
					if(glyph){
						$(obj).toggleClass("glyphicon-star");
						$(obj).toggleClass("glyphicon-star-empty");
					}else if(fa){
						$(obj).toggleClass("fa-star");
						$(obj).toggleClass("fa-star-o");
					}
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_star"); });
	},
}

once.mailbox.actions = {
	mainInit: function(obj){
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');

		// Categories
		$("#categories-remote .list-group-item").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.categories.categoryLoad($(this));
		});
		
		// iCheck for checkbox and radio inputs
		if($('input[type="checkbox"]').length){
			$('input[type="checkbox"]').iCheck({
				checkboxClass: 'icheckbox_minimal-blue',
				radioClass: 'iradio_minimal-blue'
			});
			
			// When unchecking the checkbox
			$("#check-all").on('ifUnchecked', function(event) {
				//Uncheck all checkboxes
				$("input[type='checkbox']", ".table-mailbox").iCheck("uncheck");
			});
			
			//When checking the checkbox
			$("#check-all").on('ifChecked', function(event) {
				//Check all checkboxes
				$("input[type='checkbox']", ".table-mailbox").iCheck("check");
			});
			
			// Bulk actions
			$(".bulk-action").click(function () {
				once.mailbox.bulkAction($(this));
			});
		}
		
		// Sort actions
		$(".sort-action").click(function () {
			once.mailbox.sortAction($(this));
		});
		
		// Star item
		$(".item-star").click(function () {
			once.mailbox.itemStar($(this));
		});

		// Set display limit
		$(".display-limit").change(function () {
			once.mailbox.displayLimit($(this));
		});
		
		// Get selected page
		$(".pagination a").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.mailbox.openPage($(this));
		});
		
		// Initialize itemOpen dialog
		once.mailbox.dialogs.itemOpen(".item-open");
		
		// Initialize checkForm
		once.mailbox.forms.checkForm($(this));

		// Initialize searchForm
		once.mailbox.forms.searchForm($(this));

		// Initialize / sandbox
		once.mailbox.initialized();
	},
	editInit: function(obj){
		// Delete item
		$("#mailbox-data .item-delete").click(function () {
			once.mailbox.itemEditDelete($(this));
		});
		
		// Forward email
		$("#mailbox-data .item-forward").click(function () {
			once.mailbox.itemEditForward($(this));
		});
		
		// Reply email
		$("#mailbox-data .item-reply").click(function () {
			once.mailbox.itemEditReply($(this));
		});

		// Star email
		$("#mailbox-data .item-star").click(function () {
			once.mailbox.itemEditStar($(this));
		});
		
		// Unstar email
		$("#mailbox-data .item-stared").click(function () {
			once.mailbox.itemEditStar($(this));
		});
		
		// Initialize newForm
		once.mailbox.forms.newForm($(this));
		
		// Initialize uploadThumbnail
		once.mailbox.forms.uploadThumbnail($(this));
	},
}

once.mailbox.dialogs = {
	itemOpen: function(obj){//ok
		// Append at end of the body
		$("body").append("<div id=\"item-open\"></div>");

		// Read and open open dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-open").load(once.path+"/dialog.php?c=mailbox&o=open&id="+$(this).parent().data("id"), function() {
				$('#item-open .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: open"); });
		});
	},
	itemNew: function(obj){
		// Append at end of the body
		$("body").append("<div id=\"item-new\"></div>");

		// Read and open new dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-new").load(once.path+"/dialog.php?c=mailbox&o=new", function() {
				$('#item-new .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: new"); });
		});
	},
}


once.mailbox.forms = {
	checkForm: function(obj){
		var options = {
			dataType:  "json",
			success: function(data){
			
				console.log($("#checkForm").data("type"));
				console.log($("#checkForm").data("module"));
				
				// Call for refresh
				$.get(once.path+"/view.php?c=mailbox&o="+$("#mailboxs-data").data("o")+"&type_id="+$("#mailboxs-data").data("type_id")+"&category_id="+$("#mailboxs-data").data("category_id")+"&sort_by="+$("#mailboxs-data").data("sort_by")+"&page="+$("#mailboxs-data").data("page")+"&ids="+$("#mailboxs-data").data("ids")+"&query="+$("#mailboxs-data").data("query"), function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("Request Error: "+$("#mailboxs-data").data("o")); });
			},
			complete: function(){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: checkForm");
			}
		};
		$("#checkForm").ajaxForm(options);
	},
	newForm: function(obj){
		$("#newForm").attr("action",once.path+"/ajax.php?c=mailbox&o=item_new");
		var options = {
			dataType:  "json",
			success: function(data){
				// Update name & author on items list
				if(data.status=='ok'){
					$("#mailbox-data .modal-footer button[type='submit']").addClass("hidden");
					console.log("Mail sent!");
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: newForm");
			}
		};
		$("#newForm").ajaxForm(options);
	},
	searchForm: function(obj){
		$("#searchForm").attr("action",once.path+"/view.php?c=mailbox&o="+$("#mailboxs-data").data("o"));
		var options = {
			complete: function(data){
				$("#content-body").html(data.responseText);
			},
		};
		$("#searchForm").ajaxForm(options);
	},
	uploadThumbnail: function(obj){
		if(!$("#uploadThumbnail").length){
			var str='';
			str+='<form id="uploadThumbnail" method="post" enctype="multipart/form-data" class="hidden">';
				str+='<input type="file" size="60" name="myImage" id="myImage">';
				str+='<input type="submit" value="Ajax File Upload">';
			str+='</form>';
			// Append at end of the body
			$("body").append(str);
			
			// Onclick event
			$("#uploadThumbnail input[type='file']").change(function(e) {
				$("#uploadThumbnail input[type='submit']").click();
			});
		}

		$("#uploadThumbnail").attr("action",once.path+"/ajax.php?c=mailbox&o=upload_thumbnail&id="+$("#mailbox-data").data("id"));
		var options = { 
			dataType:  "json",
			success: function(data){
				// If response ok refresh thumbnail
				if(data.status=='ok'){
					$("#item-thumbnail").attr("src","/once/mailbox/"+data.item.id+"/thumbnail.png?"+Math.random());
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: uploadThumbnail");
			}
		}; 
		$("#uploadThumbnail").ajaxForm(options);
	},
}

$(document).ready(function () {
	// Initialize publish dialog
	once.mailbox.dialogs.itemNew(".item-new");

	// Initialize types & actions
	if($("#types-data").length){
		once.loadJSfile(once.path+'/js/once.types.js');
	}

	// Load libraries & modes
	if($("#mailbox-data").data("ajax") || $("#mailboxs-data").data("ajax")){
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
		//once.loadJSfile('//oss.maxcdn.com/jquery.form/3.50/jquery.form.min.js');
	}

	// Initialize mailbox actions
	if($("#mailboxs-data").length>0){
		// Sort actions
		$("#mailboxs-data .sort-action").click(function () {
			once.mailbox.sortAction($(this));
		});
		
		// Initialize searchForm if its ajax only
		if($("#mailboxs-data").data("ajax")){
			once.mailbox.forms.searchForm($(this));
		}
	}
	
	// Initialize mailbox actions
	if($("#mailbox-data").length){
		once.mailbox.actions.editInit();
	}

	// Initialize / sandbox
	once.mailbox.initialized();
});