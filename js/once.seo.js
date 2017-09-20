/**
 * Version: 1.0, 09.08.2015
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Settings plugin (once.settings)
 *
*/

once.settings = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
	// List function
	bulkAction: function(obj){
		// Set action value
		$("#checkForm input[name='action']").val(obj.data("action"));
		// Set action o
		$("#checkForm").attr("action",once.path+"/ajax.php?c=settings&o=bulk_action");
		// Submit form
		$("#checkForm").submit();
	},
	sortAction: function(obj){
		if($("#settings-data").data("ajax")){
			$("#sort-label").html(obj.html());
			// Refresh items list by sort key
			$.get(once.path+"/view.php?c=settings&o="+$("#settings-data").data("o")+"&type_id="+$("#settings-data").data("type_id")+"&type_id="+$("#settings-data").data("type_id")+"&sort_by="+obj.data("sort")+"&page="+$("#settings-data").data("page")+"&ids="+$("#settings-data").data("ids")+"&query="+$("#settings-data").data("query"), function(data) {
				$("#content-body").html(data);
			})
			.error(function() { alert("Couldn\'t load sort"); });
		}else{
			// Refresh items list by sort key
			$("#sort-label").html(obj.html());
			document.location.href='/settings?type_name='+$("#settings-data").data('type')+'&p='+$("#settings-data").data("page")+'&sort_by='+obj.data("sort")+"&query="+$("#settings-data").data('query');
		}
	},
	displayLimit: function(obj){
		// Call for set limit
		$.getJSON(once.path+"/ajax.php?c=settings&o=set_limit&limit="+$(obj).val(), function(data) {
			$(".pagination a:first").click();
		})
		.error(function() { console.log("Request Error: set_limit"); });
	},
	openPage: function(obj){
		// Open selected page with params
		$.get(once.path+"/view.php?c=settings&o="+$("#settings-data").data("o")+"&type_id="+$("#settings-data").data("type_id")+"&type_id="+$("#settings-data").data("type_id")+"&sort_by="+$("#settings-data").data("sort_by")+"&page="+obj.html()+"&ids="+$("#settings-data").data("ids")+"&query="+$("#settings-data").data("query"), function(data) {
			$("#content-body").html(data);
		})
		.error(function() { console.log("Request Error: "+$("#settings-data").data("o")); });
	},
	// View function
	itemNew: function(obj){
		// Call to del_layer with parm id
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=settings&o=item_new&type_id="+$("#settings-data").data('type_id')+"&type_id="+$("#settings-data").data('type_id'),
			success: function(data) { 
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Call for refresh
					$.get(once.path+"/view.php?c=settings&o="+$("#settings-data").data("o")+"&type_id="+$("#settings-data").data("type_id")+"&type_id="+$("#settings-data").data("type_id"), function(data) {
						$("#content-body").html(data);
					})
					.error(function() { alert("couldnt load selected page"); });
					// Open edit dialog
					setTimeout(function(){
						$("#item_"+data.item.id+" .item-edit").click();
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
	itemStar: function(obj){
		var col=obj.parent();
		var row=col.parent();
		// Call for star item
		$.getJSON(once.path+"/ajax.php?c=settings&o=item_star&id="+row.data("id"), function(data) {
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
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=settings&o=item_delete&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=settings&o="+$("#settings-data").data("o")+"&ids="+$("#settings-data").data("ids")+"&type_id="+$("#settings-data").data("type_id")+"&type_id="+$("#settings-data").data("type_id")+"&sort_by="+$("#settings-data").data("sort_by")+"&page="+$("#settings-data").data("page")+"&query="+$("#settings-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#settings-data").data("o")); });
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
	// After dialog open function
	// After dialog open function
	itemEditPublish: function(obj){
		// Get varibles defined in rendering data-*
		var id=$("#snippet-data").data('id');
		// We need to confirm to delete
		var r = confirm("Publish "+name+"?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=settings&o=item_publish&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						console.log(data);
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: item_publish"); });
		}
	},
	itemEditDelete: function(obj){
		// Get varibles defined in rendering data-*
		var id=$("#setting-data").data('id');
		var name=$("#item_"+id+" td.item-name").html();
		// We need to confirm to delete
		var r = confirm("Delete "+name+"?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=settings&o=item_delete&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						$("#setting-data .item-close").click();
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=settings&o="+$("#settings-data").data("o")+"&ids="+$("#settings-data").data("ids")+"&type_id="+$("#settings-data").data("type_id")+"&type_id="+$("#settings-data").data("type_id")+"&sort_by="+$("#settings-data").data("sort_by")+"&page="+$("#settings-data").data("page")+"&query="+$("#settings-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#settings-data").data("o")); });
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
	itemEditSave: function(obj){
		// Save selected source or just item edit
		if($("#setting-data").data("source")){
			once.settings.itemEditSource();
		}else if($("#setting-data").data("content")){
			once.settings.itemEditContent();
		}else{
			$("#editForm").submit();
		}
	},
	itemEditContent: function(obj){
		// Save selected source
		var source = rawurlencode($(".textarea").val());
		$.post(once.path+"/ajax.php?c=settings&o=save_content&id="+$("#setting-data").data("id"), { source: source }, function(data) {
			console.log("Source saved");
		})
		.error(function() { console.log("Request Error: save_content"); });
	},
	itemEditLogo: function(obj){
		$("#uploadImage input[type='file']").click();
	},
	itemEditPreviewRaw: function(obj){
		var id=$("#setting-data").data('id');
		window.open(once.path+'/settings/'+id+'/setting.html','_blank');
	},
	itemEditApprove : function(obj){
		// Get varibles defined in rendering data-*
		var id=$("#setting-data").data('id');
		var name=$("#item_"+id+" td.item-name").html();
		// We need to confirm to delete
		var r = confirm("Approve "+name+"?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=settings&o=item_approve&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						$("#setting-data .item-close").click();
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=settings&o="+$("#settings-data").data("o")+"&ids="+$("#settings-data").data("ids")+"&type_id="+$("#settings-data").data("type_id")+"&type_id="+$("#settings-data").data("type_id")+"&sort_by="+$("#settings-data").data("sort_by")+"&page="+$("#settings-data").data("page")+"&query="+$("#settings-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#settings-data").data("o")); });
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
	itemEditInsightsImage: function(obj){
		// Get varibles defined in rendering data-*
		var id=$("#setting-data").data('id');
		// Send ajax request to api
		$.getJSON(once.path+"/ajax.php?c=settings&o=item_insights_image&id="+id, function(data) {
			// Refresh items list if response ok
			if(data.status=='ok'){
				$("#item-logo").attr("src","/once/settings/"+data.item.id+"/thumbnail.png?"+Math.random());
				console.log(data);
			}else{
				console.log("Action Error: "+data.error);
			}
		})
		.error(function() { console.log("Request Error: item_star"); });
	},
	// Website functions
	itemUserPublish: function(obj){
		// Get varibles defined in rendering data-*
		var id=$("#setting-data").data('id');
		var r = confirm("Publish "+name+"?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=settings&o=item_user_publish&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						document.location.href="/setting/"+id;
						console.log(data);
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: item_user_publish"); });
		}
	},
	itemUserFork: function(obj){
		// Get varibles defined in rendering data-*
		var id=$("#setting-data").data('id');
		// Send ajax request to api
		$.getJSON(once.path+"/ajax.php?c=settings&o=item_user_fork&id="+id, function(data) {
			// Refresh items list if response ok
			if(data.status=='ok'){
				document.location.href='/setting/'+data.item.id;
				console.log(data);
			}else{
				console.log("Action Error: "+data.error);
			}
		})
		.error(function() { console.log("Request Error: item_star"); });
	},
	itemUserVote: function(obj){
		// Get varibles defined in rendering data-*
		var id=$("#setting-data").data('id');
		// Send ajax request to api
		$.getJSON(once.path+"/ajax.php?c=settings&o=item_user_vote&id="+id, function(data) {
			// Refresh items list if response ok
			if(data.status=='ok'){
				alert('Thanks. voted!');
				console.log(data);
			}else{
				if(data.errors[0]=='user not logged'){
					document.location.href="/login";
				}else{
					alert(data.errors[0]);
				}
				console.log("Action Error: "+data.error);
			}
		})
		.error(function() { console.log("Request Error: item_stared"); });
	},
	itemUserDownload: function(obj){
		// Get varibles defined in rendering data-*
		var id=$("#setting-data").data('id');
		// Open ajax request to api
		document.location.href="/ajax.php?c=settings&o=item_user_download&id="+id;
	},
	itemUserReport: function(obj){
		// Read and open report dialog
		$("#item-reported").load(once.path+"/dialog.php?c=settings&o=report&id="+obj.parent().parent().data('id'), function() {
			$('#item-reported .modal:first').modal({
				show: 'false'
			}); 
		})
		.error(function() { console.log("Dialog Error: report"); });
	},
}

once.settings.actions = {
	mainInit: function(obj){
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');

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
				once.settings.bulkAction($(this));
			});
		}
		
		// Sort actions
		$(".sort-action").click(function () {
			once.settings.sortAction($(this));
		});
		
		// Star item
		$(".item-star").click(function () {
			once.settings.itemStar($(this));
		});

		// Initialize itemEdit dialog
		once.settings.dialogs.itemEdit(".item-edit");
		
		// Delete item
		$(".item-delete").click(function () {
			once.settings.itemDelete($(this));
		});
		
		// Set display limit
		$(".display-limit").change(function () {
			once.settings.displayLimit($(this));
		});
		
		// Get selected page
		$(".pagination a").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.settings.openPage($(this));
		});
		
		// Initialize checkForm
		once.settings.forms.checkForm($(this));
				
		// Initialize searchForm
		once.settings.forms.searchForm($(this));

		// Initialize / sandbox
		once.settings.initialized();
	},
	editInit: function(obj){
		// After show selected tab
		$('#setting-data a[data-toggle="tab"]').on('show.bs.tab', function (e) {
			var tab = $(this); // current tab
			var previous = e.relatedTarget; // previous active tab
			var previous_id = $(e.relatedTarget).data('editor');
			
			// Load tab content
			if(tab.attr('href')=='#edit_settings'){
				// Setting data to disable keypress
				$("#setting-data").data("source",false);
				$("#setting-data").data("content",false);
			}else if(tab.attr('href')=='#edit_information'){
				// Setting data to disable keypress
				$("#setting-data").data("source",false);
				$("#setting-data").data("content",false);
			}else if(tab.attr('href')=='#edit_content'){
				// Setting data to make it work with keypress
				$("#setting-data").data("source",false);
				$("#setting-data").data("content",true);
			}else if(tab.attr('href')=='#edit_source'){
				// Setting data to make it work with keypress
				$("#setting-data").data("source",true);
				$("#setting-data").data("content",false);
				
				// Set previus last content
				$("#source_"+previous_id).html(once.editors[1].getValue());
				
				// Set other settings
				$("#setting-data").data("editor",tab.attr('data-editor'));
				$("#setting-data").data("path",tab.attr('data-path'));
				$("#setting-data").data("file",tab.attr('data-file'));
				$("#setting-data").data("title",tab.html());
				
				// Refresh codemirror
				setTimeout("once.editors[1].refresh()", 1000);
				
				// Get codemirror mode
				var name=tab.html();
				var mode='';
				switch(tab.html()){
					case 'HTML':
						mode='application/x-httpd-php';
					break;
					case 'CSS':
						mode='css';
					break;
					case 'JS':
						mode='javascript';
					break;
				}
				
				once.editors[1].focus();
				
				// Reset
				$("#ajax-playground").html('');

				// Get source from page
				var source=$("#source_"+tab.attr('data-editor')).html();
				source=source.replaceAll("&amp;","&");
				source=source.replaceAll("&quot;","\"");
				source=source.replaceAll("&lt;","<");
				source=source.replaceAll("&gt;",">");
				source=source.replaceAll("&#039;","'");

				// Fill editor
				once.editors[1].setValue(source);
						
				// Set editor mode
				once.editors[1].setOption('mode', mode);
			}
		});
		
		// Thumbnail hover on logo
		$("#setting-data .thumbnail").hover(
			function(){
				$(this).find(".caption").slideDown(250); //.fadeIn(250)
			},
			function(){
				$(this).find(".caption").slideUp(250,function(){$ (this).stop( true, true )}); //.fadeOut(205)
			}
		);
		
		// Active keypress 
		$(window).keypress(function(event) {
			// Ctr + S to save
			if(event.ctrlKey && event.which==115){
				event.preventDefault();
				once.settings.saveSource($(this));
			}
		});
		
		// Approve item
		$("#setting-data .item-insights-image").click(function () {
			once.settings.itemEditInsightsImage($(this));
		});
		
		// Publish item
		$("#setting-data .item-publish").click(function () {
			once.settings.itemEditPublish($(this));
		});
		
		// Delete item
		$("#setting-data .item-delete").click(function () {
			once.settings.itemEditDelete($(this));
		});
		
		// Approve item
		$("#setting-data .item-approve").click(function () {
			once.settings.itemEditApprove($(this));
		});
		
		// Save item
		$("#setting-data .item-save").click(function () {
			once.settings.itemEditSave($(this));
		});

		// Save item
		$("#setting-data .item-presave").click(function () {
			// item save & source and change location
			$("#editForm").submit();
		});
		
		// Change logo
		$("#setting-data .item-logo").click(function () {
			once.settings.itemEditLogo($(this));
		});
		
		// Preview raw file
		$("#setting-data .item-preview-raw").click(function () {
			once.settings.itemEditPreviewRaw($(this));
		});
		
		// Initialize report dialog
		once.settings.dialogs.itemReport("#setting-data .item-report");

		// Initialize editForm
		once.settings.forms.editForm($(this));
		
		// Initialize uploadImage
		once.settings.forms.uploadImage($(this));
		
		// Initialize bootstrap WYSIHTML5 - text editor
		$(".textarea").wysihtml5();
		
		// SOME USER FUNCTION FOR WEB PURPOSES
		// SOME USER FUNCTION FOR WEB PURPOSES
		// SOME USER FUNCTION FOR WEB PURPOSES
		
		// User save
		$("#setting-data .item-user-save").click(function () {
			$("#editForm").submit();
		});
		
		// User publish
		$("#setting-data .item-user-publish").click(function () {
			once.settings.itemUserPublish($(this));
		});
		
		// User vote
		$("#setting-data .item-user-vote").click(function () {
			once.settings.itemUserVote($(this));
		});
		
		// User download
		$("#setting-data .item-user-download").click(function () {
			once.settings.itemUserDownload($(this));
		});
		
		// User fork
		$("#setting-data .item-user-fork").click(function () {
			once.settings.itemUserFork($(this));
		});
	},
}

once.settings.dialogs = {
	itemReport: function(obj){
		// Append at end of the body
		$("body").append("<div id=\"item-report\"></div>");

		// Read and open report dialog
		$(obj).click(function () {
			$("#item-report").load(once.path+"/dialog.php?c=settings&o=report&id="+$("#setting-data").data("id"), function() {
				$('#item-report .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: report"); });
		});
	},
	itemPublish: function(obj){
		// Append at end of the body
		$("body").append("<div id=\"item-publish\"></div>");

		// Read and open publish dialog
		$(obj).click(function () {
			$("#item-publish").load(once.path+"/dialog.php?c=settings&o=publish", function() {
				$('#item-publish .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: publish"); });
		});
	},
	itemImport: function(obj){
		// Append at end of the body
		$("body").append("<div id=\"item-import\"></div>");

		// Read and open import dialog
		$(obj).click(function () {
			$("#item-import").load(once.path+"/dialog.php?c=settings&o=import", function() {
				$('#item-import .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: import"); });
		});
	},
	itemEdit: function(obj){
		// Append at end of the body
		$("body").append("<div id=\"item-edit\"></div>");

		// Read and open edit dialog
		$(obj).click(function () {
			$("#item-edit").load(once.path+"/dialog.php?c=settings&o=edit&id="+$(this).parent().parent().data("id"), function() {
				$('#item-edit .modal:first').modal({
					show: 'false'
				}); 
			})
			.error(function() { console.log("Dialog Error: edit"); });
		});
	},
}

once.settings.forms = {
	checkForm: function(obj){
		$("#checkForm").attr("action",once.path+"/view.php?c=settings");
		var options = {
			dataType:  "json",
			success: function(data){
			
				console.log($("#checkForm").data("type"));
				console.log($("#checkForm").data("module"));
				
				if($("#checkForm").data("type")=='stared' && $("#checkForm").data("module")=='unstar'){
					for(var i=0;i<data.settings.length;i++){
						console.log(data.settings[i]);
						$('tr[data-id="'+data.settings[i]+'"]').remove();
					}
				}else if($("#checkForm").data("module")=='star'){
					for(var i=0;i<data.settings.length;i++){
						var obj = $("tr[data-id='"+data.settings[i]+"'] .page-star");
						var glyph = $(obj).hasClass("glyphicon");
						var fa = $(obj).hasClass("fa");
						//Switch states
						if (glyph) {
							if($(obj).removeClass("glyphicon-star-empty")){
								$(obj).addClass("glyphicon-star");
							}
						}
						if (fa) {
							if($(obj).removeClass("fa-star-o")){
								$(obj).addClass("fa-star");
							}
						}
		
					}
				}else if($("#checkForm").data("module")=='unstar'){
					for(var i=0;i<data.settings.length;i++){
						var obj = $("tr[data-id='"+data.settings[i]+"'] .page-star");
						var glyph = $(obj).hasClass("glyphicon");
						var fa = $(obj).hasClass("fa");
						//Switch states
						if (glyph) {
							if($(obj).removeClass("glyphicon-star")){
								$(obj).addClass("glyphicon-star-empty");
							}
						}
						if (fa) {
							if($(obj).removeClass("fa-star")){
								$(obj).addClass("fa-star-o");
							}
						}
					}
				}else if($("#checkForm").data("module")!=$("#checkForm").data("type") && $("#checkForm").data("type")!='stared' && $("#checkForm").data("module")!='star'){
					for(var i=0;i<data.settings.length;i++){
						$('tr[data-id="'+data.settings[i]+'"]').remove();
					}
				}

				$.get(once.path+"/view.php?c=settings&o=installed&t="+$("#checkForm").data("type")+"&p="+$(".pagination").data("p"), function(data) {
					$("#content-body").html(data);
				})
				.error(function() { alert("couldnt"); });
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
	searchForm: function(obj){
		$("#searchForm").attr("action",once.path+"/view.php?c=settings&o="+$("#settings-data").data("o"));
		var options = {
			complete: function(data){
				$("#content-body").html(data.responseText);
			},
		};
		$("#searchForm").ajaxForm(options);
	},
	editForm: function(obj){
		$("#editForm").attr("action",once.path+"/ajax.php?c=settings&o=item_edit&id="+$("#setting-data").data("id"));
		var options = {
			dataType:  "json",
			success: function(data){
				// If response ok 
				if(data.status=='ok'){
					// Check for redirects then save
					if($("#setting-data").data('redirect')!==undefined){
						// Get source
						var source = rawurlencode($(".textarea").val());
						$.post(once.path+"/ajax.php?c=settings&o=save_content&id="+$("#setting-data").data("id"), { source: source }, function(data) {
							document.location.href=$("#setting-data").data('redirect');
						})
						.error(function() { console.log("Request Error: save_content"); });
					}
					
					if($("#setting-data").data('redirect')==undefined){
						// Update name & author on items list
						var name=$("#editForm input[name='name']");
						var author=$("#editForm input[name='author']");
						
						// Update DOM
						$("tr[data-id='"+data.item.id+"'] td[data-link='name']").html(name.val());
						$("tr[data-id='"+data.item.id+"'] td[data-link='author']").html(author.val());
					}
					
					console.log("Setting updated!");
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: editForm");
			}
		};
		$("#editForm").ajaxForm(options);
	},
	reportForm: function(obj){
		$("#reportForm").attr("action",once.path+"/ajax.php?c=settings&o=item_user_report&id="+$("#setting-data").data("id"));
		var options = {
			dataType:  "json",
			success: function(data){
				// If response ok 
				if(data.status=='ok'){
					$("#reportForm label[for='message']").html("Setting reported!");
					$("#reportForm textarea").attr("disabled",true);
					
					console.log("Setting reported!");
				}else{
					if(data.errors[0]=='user not logged'){
						document.location.href="/login";
					}else{
						alert(data.errors[0]);
					}
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: reportForm");
			}
		};
		$("#reportForm").ajaxForm(options);
	},
	uploadImage: function(obj){
		if(!$("#uploadImage").length){
			var str='';
			str+='<form id="uploadImage" method="post" enctype="multipart/form-data" class="hidden">';
				str+='<input type="file" size="60" name="myImage" id="myImage">';
				str+='<input type="submit" value="Ajax File Upload">';
			str+='</form>';
			// Append at end of the body
			$("body").append(str);
		}

		$("#uploadImage").attr("action",once.path+"/ajax.php?c=settings&o=upload_image&id="+$("#setting-data").data("id"));
		var options = { 
			dataType:  "json",
			success: function(data){
				// If response ok refresh logo
				if(data.status=='ok'){
					$("#item-logo").attr("src","/once/settings/"+data.item.id+"/thumbnail.png?"+Math.random());
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
		
		// Onclick event
		$("#uploadImage input[type='file']").change(function(e) {
			$("#uploadImage input[type='submit']").click();
		});
	},
	importForm: function(obj){
		var options = {
		dataType:  "json",
			success: function(data){
				// If response ok refresh logo
				if(data.status=='ok'){
					$("#import-data .item-close").click();
					console.log("plugin imported");
				}else{
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
		$("#importForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#importForm input[name=\"url\"]").focus();
		},700);
	},
	publishForm: function(obj){
		var options = {
		dataType:  "json",
			success: function(data){
				$("#publish-data .item-close").click();
				console.log("plugin published");
			},
			complete: function(data){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: publishForm");
			}
		}; 
		$("#publishForm").ajaxForm(options);
		
		setTimeout(function(){
			$("#publishForm input[name=\"name\"]").focus();
		},700);
	},
	publishSearch: function(obj){
		var options = {
			dataType:  "json",
			success: function(data){
				$("#content-header").html(data.header);
				$("#content-body").html(data.html);
			},
			complete: function(){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: publishSearch");
			}
		};
		$("#publishSearch").ajaxForm(options);
	},
}

$(document).ready(function () {
	// Initialize onclick action
	$(".item-new").click(function () {
		once.settings.itemNew($(this));
    });
	
	// Initialize publish dialog
	once.settings.dialogs.itemPublish(".item-publish");
	
	// Initialize import dialog
	once.settings.dialogs.itemImport(".item-import");

	// Initialize types & actions
	if($("#types-data").length){
		once.loadJSfile(once.path+'/js/once.types.js');
	}
	
	// Load libraries & modes
	if($("#setting-data").data("ajax") || $("#settings-data").data("ajax")){
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
		//once.loadJSfile('//oss.maxcdn.com/jquery.form/3.50/jquery.form.min.js');
	}
	
	// Initialize settings actions
	if($("#settings-data").length>0){
		// Sort actions
		$("#settings-data .sort-action").click(function () {
			once.settings.sortAction($(this));
		});
		
		// Initialize searchForm if its ajax only
		if($("#settings-data").data("ajax")){
			once.settings.forms.searchForm($(this));
		}
	}
	
	// Initialize setting actions
	if($("#setting-data").length){
		once.loadJSfile(once.path+'/lib/jquery-validation/dist/jquery.validate.js');
		//once.loadJSfile('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js');

		once.loadCSSfile(once.path+'/lib/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css');
		once.loadJSfile(once.path+'/lib/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js');
		
		once.settings.actions.editInit();
	}

	// Initialize / sandbox
	once.settings.initialized();
});