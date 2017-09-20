/**
 * Version: 1.0, 29.07.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Libraries plugin (once.libraries)
 *
*/

once.libraries = {
	loaded: false,
	keypress: false,
	initialized: function(){
		this.loaded=true;
	},
	
	// View function
	itemDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var path=$(obj).data('path');
		// We need to confirm to delete
		var r = confirm("Delete "+path+"?");
		if(r){
			// Call to item_delete with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=libraries&o=item_delete&path="+path,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						// Intermediatly delete from dom then refresh items list
						$(obj).parent().parent().remove();
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
	itemNew: function(obj){//ok
		// Call to item_new for new item
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=libraries&o=item_new&path="+$("#libraries-data").data("path"),
			success: function(data) { 
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Call for refresh
					$.get(once.path+"/view.php?c=libraries&o="+$("#libraries-data").data("o")+"&path="+$("#libraries-data").data("path"), function(data) {
						$("#content-body").html(data);
					})
					.error(function() { alert("couldnt load selected page"); });
					
					// Open edit dialog
					setTimeout(function(){
						$("#item_"+data.item.name+" .item-edit").click();
					},500);
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_new"); });
	},
	itemOpen: function(obj){//ok
		// Get varibles defined in rendering data-*
		var path=$(obj).data('path');
		// Call for refresh
		$.get(once.path+"/view.php?c=libraries&o="+$("#libraries-data").data("o")+"&path="+path, function(data) {
			$("#content-body").html(data);
		})
		.error(function() { console.log("Request Error: "+$("#libraries-data").data("o")); });
	},
	itemEditSave: function(obj){//ok
		// Save selected source or just item edit
		if($("#image-data").data("tab")!==undefined){
			//once.libraries.itemEditSaveSource();
		}else{
			$("#editForm").submit();
		}
	},
}

once.libraries.actions = {
	mainInit: function(obj){//ok
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');

		// Initialize itemEdit dialog
		once.libraries.dialogs.itemEdit(".item-edit");

		// Delete item
		$(".item-open").click(function () {
			once.libraries.itemOpen($(this));
		});
		
		// Delete item
		$(".item-delete").click(function () {
			once.libraries.itemDelete($(this));
		});

		// Initialize searchForm
		once.libraries.forms.searchForm($(this));

		// Initialize uploadFiles
		once.libraries.forms.uploadFiles($(this));
		
		// Initialize / sandbox
		once.libraries.initialized();
	},
	editInit: function(obj){//ok
		// Save item
		$("#image-data .item-save").click(function () {
			once.libraries.itemEditSave($(this));
		});

		// Initialize editForm
		once.libraries.forms.editForm($(this));
	},
}

once.libraries.dialogs = {
	itemEdit: function(obj){//ok
		// Append at end of the body
		$("body").append("<div id=\"item-edit\"></div>");

		// Read and open edit dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-edit").load(once.path+"/dialog.php?c=libraries&o=edit&path="+encodeURI($(this).data('path')), function() {
				$('#item-edit .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: edit"); });
		});
	},
}

once.libraries.forms = {
	editForm: function(obj){//ok
		$("#editForm").attr("action",once.path+"/ajax.php?c=libraries&o=item_edit&path="+$("#image-data").data("path"));
		var options = {
			dataType:  "json",
			success: function(data){
				// Update name & version on items list
				if(data.status=='ok'){
					if($("#libraries-data").data('redirect')==undefined){
						// Call for refresh
						$.get(once.path+"/view.php?c=libraries&o="+$("#libraries-data").data("o")+"&path="+$("#libraries-data").data("path"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { alert("couldnt load selected page"); });

						// Get new path
						$("#editForm").attr("action",once.path+"/ajax.php?c=libraries&o=item_edit&path="+data.item.path);
					}
					console.log("Item updated!");
				}else{
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
		},1000);
	},
	searchForm: function(obj){//ok
		$("#searchForm").attr("action",once.path+"/view.php?c=libraries&o="+$("#libraries-data").data("o"));
		var options = {
			complete: function(data){
				$("#content-body").html(data.responseText);
			},
		};
		$("#searchForm").ajaxForm(options);
	},
	uploadFiles: function(obj){//ok
		if(!$("#uploadFiles").length){
			var str='';
			str+='<form id="uploadFiles" method="post" enctype="multipart/form-data" class="hidden">';
				str+='<input type="file" size="60" name="myFiles[]" id="myFiles" multiple>';
				str+='<input type="submit" value="Ajax File Upload">';
			str+='</form>';
			// Append at end of the body
			$("body").append(str);
			
			// Onclick event
			$("#uploadFiles input[type='file']").change(function(e) {
				$("#uploadFiles input[type='submit']").click();
			});
		}

		$("#uploadFiles").attr("action",once.path+"/ajax.php?c=libraries&o=upload_files&path="+$("#libraries-data").data("path"));
		var options = {
			dataType:  "json",
			success: function(data){
				// If response ok refresh thumbnail
				if(data.status=='ok'){
					// Call for refresh
					$.get(once.path+"/view.php?c=libraries&o="+$("#libraries-data").data("o")+"&path="+$("#libraries-data").data("path"), function(data) {
						$("#content-body").html(data);
					})
					.error(function() { alert("couldnt load selected page"); });
				}
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: uploadFiles");
			}
		};
		$("#uploadFiles").ajaxForm(options);
	},
}

$(document).ready(function () {
	
	// Initialize onclick action
	$(".item-new").click(function () {
		once.libraries.itemNew($(this));
    });
			
	// Change thumbnail
	$(".item-upload").click(function () {
		$("#uploadFiles input[type='file']").click();
	});

	// Load libraries & modes
	if($("#image-data").data("ajax") || $("#libraries-data").data("ajax")){
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
		//once.loadJSfile('//oss.maxcdn.com/jquery.form/3.50/jquery.form.min.js');
	}
	
	// Initialize libraries actions
	if($("#libraries-data").length>0){
		// Initialize searchForm if its ajax only
		if($("#libraries-data").data("ajax")){
			once.libraries.forms.searchForm($(this));
		}
	}
	
	// Initialize image actions
	if($("#image-data").length){
		once.libraries.actions.editInit();
	}

	// Initialize / sandbox
	once.libraries.initialized();
});