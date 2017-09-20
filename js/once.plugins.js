/**
 * Version: 1.0, 31.03.2016
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Plugins module (once.plugins)
 *
*/

once.plugins = {
	loaded: false,
	keypress: false,
	initialized: function(){
		this.loaded=true;
	},
	
	// List function
	bulkAction: function(obj){//ok
		// Set action value
		$("#checkForm input[name='action']").val(obj.data("action"));
		// Set action o
		$("#checkForm").attr("action",once.path+"/ajax.php?c=plugins&o=bulk_action");
		// Submit form
		$("#checkForm").submit();
	},
	displayLimit: function(obj){//ok
		// Call for set limit
		$.getJSON(once.path+"/ajax.php?c=plugins&o=set_limit&limit="+$(obj).val(), function(data) {
			$(".pagination a:first").click();
		})
		.error(function() { console.log("Request Error: set_limit"); });
	},
	openPage: function(obj){//ok
		// Open selected page with params
		$.get(once.path+"/view.php?c=plugins&o="+$("#plugins-data").data("o")+"&type_id="+$("#plugins-data").data("type_id")+"&category_id="+$("#plugins-data").data("category_id")+"&sort_by="+$("#plugins-data").data("sort_by")+"&page="+obj.html()+"&ids="+$("#plugins-data").data("ids")+"&query="+$("#plugins-data").data("query"), function(data) {
			$("#content-body").html(data);
		})
		.error(function() { console.log("Request Error: "+$("#plugins-data").data("o")); });
	},
	sortAction: function(obj){//ok
		if($("#plugins-data").data("ajax")){
			$("#sort-label").html(obj.html());
			// Refresh items list by sort key
			$.get(once.path+"/view.php?c=plugins&o="+$("#plugins-data").data("o")+"&type_id="+$("#plugins-data").data("type_id")+"&category_id="+$("#plugins-data").data("category_id")+"&sort_by="+obj.data("sort")+"&page="+$("#plugins-data").data("page")+"&ids="+$("#plugins-data").data("ids")+"&query="+$("#plugins-data").data("query"), function(data) {
				$("#content-body").html(data);
			})
			.error(function() { alert("Couldn\'t load sort"); });
		}else{
			// Refresh items list by sort key
			$("#sort-label").html(obj.html());
			document.location.href='/plugins?category_name='+$("#plugins-data").data('category')+'&sort_by='+obj.data("sort")+'&p='+$("#plugins-data").data("page")+'&query='+$("#plugins-data").data('query');
		}
	},
	
	// View function
	itemDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var id=$(obj).parent().parent().data('id');
		var name=$("#item_"+id+" td.item-name").html();
		// We need to confirm to delete
		var r = confirm("Delete "+name+"?");
		if(r){
			// Call to item_delete with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=plugins&o=item_delete&id="+id,
				success: function(data) {
					// Refresh items list if response ok
					if(data.status=='ok'){
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=plugins&o="+$("#plugins-data").data("o")+"&type_id="+$("#plugins-data").data("type_id")+"&category_id="+$("#plugins-data").data("category_id")+"&sort_by="+$("#plugins-data").data("sort_by")+"&page="+$("#plugins-data").data("page")+"&ids="+$("#plugins-data").data("ids")+"&query="+$("#plugins-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#plugins-data").data("o")); });
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
	itemDownload: function(obj){//ok
		// Get item id
		var id=$(obj).parent().parent().data("id");
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=plugins&o=item_download&id="+id,
			beforeSend: function(data) {
				var button=$("#item_"+id+" .item-download");
				button.css("cursor","pointer");
				button.attr("disabled",true);
			},
			success: function(data) {
				// Refresh items list if response ok
				if(data.status=='ok'){

					var button=$("#item_"+id+" .item-download");

					button.toggleClass("btn-success");
					button.toggleClass("btn-default");
					button.attr("disabled",true);
					button.css("cursor","pointer");
					button.html("Downloaded");

					//item improt from file
					console.log(data);
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_download"); });
	},
	itemEditApprove: function(obj){//ok
		// Get varibles defined in rendering data-*
		var id=$("#plugin-data").data('id');
		var name=$("#editForm input[name='name']").val();
		// We need to confirm to delete
		var r = confirm("Approve "+name+"?");
		if(r){
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=plugins&o=item_approve&id="+id,
				success: function(data) {
					// Refresh items list if response ok
					if(data.status=='ok'){
						// Read and open edit dialog after approve
						$("#item-edit .modal").load("dialog.php?c=plugins&o=edit&id="+id+"&nomodal", function() {

						})
						.error(function() { console.log("Dialog Error: approve"); });
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: item_approve"); });
		}
	},
	itemEditDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var id=$("#plugin-data").data('id');
		var name=$("#editForm input[name='name']").val();
		// We need to confirm to delete
		var r = confirm("Delete "+name+"?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=plugins&o=item_delete&id="+id,
				success: function(data) {
					// Refresh items list if response ok
					if(data.status=='ok'){
						$("#plugin-data .item-close").click();
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=plugins&o="+$("#plugins-data").data("o")+"&type_id="+$("#plugins-data").data("type_id")+"&category_id="+$("#plugins-data").data("category_id")+"&sort_by="+$("#plugins-data").data("sort_by")+"&page="+$("#plugins-data").data("page")+"&ids="+$("#plugins-data").data("ids")+"&query="+$("#plugins-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#plugins-data").data("o")); });
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
	itemEditExport: function(){//ok
		document.location.href=once.path+"/ajax.php?c=plugins&o=item_export&id="+$("#plugin-data").data("id");
	},
	itemEditImageDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var image=$(obj).parent().parent().next().attr('src');
		
		// We need to confirm to delete
		var r = confirm("Delete "+image+"?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=plugins&o=delete_image&id="+$("#plugin-data").data("id")+"&currentImage="+$(obj).parent().parent().parent().parent().data('id'),
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						$(obj).parent().parent().parent().parent().toggleClass("hidden");
					}else{
						console.log("Action Error: "+data.error);
					}
				},
				contentType: "application/json",
				dataType: 'json'
			})
			.error(function() { console.log("Request Error: delete_image"); });
		}
	},
	itemEditPreview: function(obj){//ok
		var icon=obj.find(".fa-chevron-down");
		if(icon.length){
			icon.removeClass("fa-chevron-down");
			icon.addClass("fa-minus");

			$("#edit_preview").addClass("show");
			$("#edit_preview iframe").attr("src",once.path+"/plugins/"+$("#plugin-data").data("id")+"/index.php?"+Math.random());
		}else{
			var icon=obj.find(".fa-minus");
			if(icon.length){
				icon.removeClass("fa-minus");
				icon.addClass("fa-chevron-down");

				$("#edit_preview").removeClass("show");
			}
		}
	},
	itemEditSave: function(obj){//ok
		// Save selected source or just item edit
		if($("#plugin-data").data("tab")!==undefined){
			once.plugins.itemEditSaveSource();
		}else{
			$("#editForm").submit();
		}
	},
	itemEditSaveSource: function(obj){//ok
		// Save selected source
		if($("#plugin-data").data("tab")=='#edit_source'){
			var source = rawurlencode(once.editors[1].getValue());
			$.post(once.path+"/ajax.php?c=plugins&o=save_source&id="+$("#plugin-data").data("id")+"&file="+$("#plugin-data").data("file"), { source: source }, function(data) {
				// Check if preview is open then refresh
				var icon=$("#plugin-data .item-preview .fa-minus");
				if(icon.length || once.cms==false){
					$("#plugin-preview iframe").attr("src",once.path+"/plugins/"+$("#plugin-data").data("id")+"/index.php?"+Math.random());
					$("#edit_preview iframe").attr("src",once.path+"/plugins/"+$("#plugin-data").data("id")+"/index.php?"+Math.random());
				}
				console.log("Source saved");
			})
			.error(function() { console.log("Request Error: save_source"); });
		}
	},
	itemEditStar: function(obj){//ok
		// Call for star item
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=plugins&o=item_star&id="+$("#plugin-data").data("id"),
			success: function(data) {
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Read and open edit dialog with selected page
					$("#item-edit .modal").load("dialog.php?c=plugins&o=edit&id="+$("#plugin-data").data("id")+"&nomodal", function() {
						
					})
					.error(function() { console.log("Dialog Error: edit"); });
					
					// Check for fonts icos
					var obj=$("#item_"+$("#plugin-data").data("id")+" td i");
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
	itemImportSave : function(event){//ok
		$("#importForm").submit();
	},
	itemNew: function(obj){//ok
		// Call to item_new for new item
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=plugins&o=item_new&type_id="+$("#plugins-data").data('type_id')+"&category_id="+$("#plugins-data").data('category_id'),
			success: function(data) {
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Call for refresh
					$.get(once.path+"/view.php?c=plugins&o="+$("#plugins-data").data("o")+"&type_id="+$("#plugins-data").data("type_id")+"&category_id="+$("#plugins-data").data("category_id"), function(data) {
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
	itemPreviewDownload: function(obj){//ok
		// Get item id
		var id=$("#preview-data").data("id");
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=plugins&o=item_download&id="+id,
			beforeSend: function(data) {
				var button=$("#preview-data .item-download");
				button.css("cursor","pointer");
				button.attr("disabled",true);
			},
			success: function(data) {
				// Refresh items list if response ok
				if(data.status=='ok'){

					var button=$("#preview-data .item-download");

					button.toggleClass("btn-success");
					button.toggleClass("btn-default");
					button.attr("disabled",true);
					button.css("cursor","pointer");
					button.html("Downloaded");

					var button=$("#item_"+id+" .item-download");

					button.toggleClass("btn-success");
					button.toggleClass("btn-default");
					button.attr("disabled",true);
					button.css("cursor","pointer");
					button.html("Downloaded");

					//item improt from file
					console.log(data);
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_download"); });
	},
	itemPublishDisplayLimit: function(obj){//ok
		// Call for set limit
		$.getJSON("ajax.php?c=plugins&o=set_limit&limit="+$(obj).val(), function(data) {
			$(".modal .pagination a:first").click();
		})
		.error(function() { console.log("Request Error: set_limit"); });
	},
	itemPublishPage: function(obj){//ok
		// Read and open publish dialog with selected page
		$("#item-publish .modal").load("dialog.php?c=plugins&o=publish&page="+obj.html()+"&nomodal", function() {

		})
		.error(function() { console.log("Dialog Error: publish"); });
	},
	itemPublishSelect: function(obj){//ok
		// Read and open publish dialog with selected plugin
		$("#item-publish .modal").load("dialog.php?c=plugins&o=publish&id="+obj.parent().parent().data('id')+"&nomodal", function() {
			$("#publishForm input[name='id']").val(obj.parent().parent().data('id'));
		})
		.error(function() { console.log("Dialog Error: publish"); });
	},
	itemPublishSubmit: function(obj){//ok
		$("#publishForm").submit();
	},
	itemStar: function(obj){//ok
		var col=obj.parent();
		var row=col.parent();
		// Call for star item
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=plugins&o=item_star&id="+row.data("id"),
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

once.plugins.actions = {
	mainInit: function(obj){//ok
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
				once.plugins.bulkAction($(this));
			});
		}

		// Sort actions
		$(".sort-action").click(function () {
			once.plugins.sortAction($(this));
		});

		// Download item
		$(".item-download").click(function () {
			once.plugins.itemDownload($(this));
		});

		// Star item
		$(".item-star").click(function () {
			once.plugins.itemStar($(this));
		});
		
		// Initialize itemEdit dialog
		once.plugins.dialogs.itemEdit(".item-edit");

		// Delete item
		$(".item-delete").click(function () {
			once.plugins.itemDelete($(this));
		});

		// Set display limit
		$(".display-limit").change(function () {
			once.plugins.displayLimit($(this));
		});

		// Get selected page
		$(".pagination a").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.plugins.openPage($(this));
		});

		// Initialize checkForm
		once.plugins.forms.checkForm($(this));

		// Initialize itemPublish dialog
		once.plugins.dialogs.itemPreview(".item-preview");

		// Initialize searchForm
		once.plugins.forms.searchForm($(this));

		// Initialize / sandbox
		once.plugins.initialized();
	},
	editInit: function(obj){//ok
		once.editors = new Array()

		// Code mirror for sources
		var value = "// The bindings defined specifically in the Sublime Text mode\nvar bindings = {\n";
		var map = CodeMirror.keyMap.sublime, mapK = CodeMirror.keyMap["sublime-Ctrl-K"];
		for (var key in map) {
			if (key != "Ctrl-K" && key != "fallthrough" && (!/find/.test(map[key]) || /findUnder/.test(map[key]))) value += "  \"" + key + "\": \"" + map[key] + "\",\n";
		}
		for (var key in mapK) {
			if (key != "auto" && key != "nofallthrough") value += "  \"Ctrl-K " + key + "\": \"" + mapK[key] + "\",\n";
		}

		value += "}\n\n// The implementation of joinLines\n";
		value += CodeMirror.commands.joinLines.toString().replace(/^function\s*\(/, "function joinLines(").replace(/\n  /g, "\n") + "\n";

		once.editors.push(0);

		// Initialize on code window functions
		// Replace the <textarea id="code"> with an CodeMirror
		once.editors.push(CodeMirror.fromTextArea(document.getElementById("ajax-playground"), {
			value: value,
			styleActiveLine: false,
			lineNumbers: true,
			lineWrapping: true,
			extraKeys: [{"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }},{"Ctrl-J": "toMatchingTag"}],
			foldGutter: true,
			gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
			highlightSelectionMatches: {showToken: /\w/},
			matchTags: {bothTags: true},
			keyMap: "sublime",
			autoCloseBrackets: true,
			matchBrackets: true,
			showCursorWhenSelecting: true,
			theme: "monokai"
		}));

		// After show selected tab
		$('#plugin-data a[data-toggle="tab"]').on('show.bs.tab', function (e) {
			var tab = $(this); // current tab
			var previous = e.relatedTarget; // previous active tab
			var previous_id = $(e.relatedTarget).data('editor');

			$("#plugin-data").data("tab",tab.attr('href'));

			// Load tab content
			if(tab.attr('href')=='#edit_source'){
				// Set previus last content
				$("#source_"+previous_id).html(once.editors[1].getValue());

				// Set other settings
				$("#plugin-data").data("editor",tab.attr('data-editor'));
				$("#plugin-data").data("path",tab.attr('data-path'));
				$("#plugin-data").data("file",tab.attr('data-file'));
				$("#plugin-data").data("title",tab.html());

				// Refresh codemirror
				setTimeout("once.editors[1].refresh()", 1000);

				// Get codemirror mode
				var name=tab.html();
				var mode='';
				switch(tab.html()){
					case 'Plugin UI':
						mode= {
						name: "htmlmixed",
						scriptTypes: [{matches: /\/x-handlebars-template|\/x-mustache/i,
									   mode: null},
									  {matches: /(text|application)\/(x-)?vb(a|script)/i,
									   mode: "vbscript"}]
						};
					break;
					case 'PHP':
						mode= {
						name: "htmlmixed",
						scriptTypes: [{matches: /\/x-handlebars-template|\/x-mustache/i,
									   mode: null},
									  {matches: /(text|application)\/(x-)?vb(a|script)/i,
									   mode: "vbscript"}]
						};
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

				// Load file
				if(once.cms){
					// Load file
					$.getJSON(once.path+"/ajax.php?c=plugins&o=load_source&id="+$('#plugin-data').data("id")+"&file="+tab.attr('data-file'), function(data) {
						if(data.status=='ok'){
							// Fill editor
							once.editors[1].setValue(data.source);
							
							// Set editor mode
							once.editors[1].setOption('mode', mode);
						}else{
							console.log("Action Error: "+data.error);
						}
					})
					.error(function() { console.log("Request Error: load_source"); });
				}else{
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
			}
		});

		// Thumbnail hover to change logo
		$("#plugin-data .thumbnail").hover(
			function(){
				$(this).find(".caption").slideDown(250); //.fadeIn(250)
			},
			function(){
				$(this).find(".caption").slideUp(250,function(){$ (this).stop( true, true )}); //.fadeOut(205)
			}
		);

		// After close this modal
		$(document).on("hidden.bs.modal", "#item-edit", function (e) {
			$("#edit_preview").removeClass("show");
			$("#plugin-data").data("tab",false);
		});

		// Active keypress
		$("#plugin-data").keypress(function(e) {
			// Ctr + S to save
			if(e.ctrlKey && e.which==115){
				e.preventDefault();
				e.stopPropagation();
				once.plugins.itemEditSaveSource();
			}
		});

		// Aprove item
		$("#plugin-data .item-approve").click(function () {
			once.plugins.itemEditApprove($(this));
		});

		// Unaprove item
		$("#plugin-data .item-approved").click(function () {
			once.plugins.itemEditApprove($(this));
		});

		// Delete item
		$("#plugin-data .item-delete").click(function () {
			once.plugins.itemEditDelete($(this));
		});

		// Export item
		$("#plugin-data .item-export").click(function () {
			once.plugins.itemEditExport($(this));
		});

		// Preview file
		$("#plugin-data .item-preview").click(function () {
			once.plugins.itemEditPreview($(this));
		});

		// Save item
		$("#plugin-data .item-save").click(function () {
			once.plugins.itemEditSave($(this));
		});

		// Star item
		$("#plugin-data .item-star").click(function () {
			once.plugins.itemEditStar($(this));
		});

		// Unstar item
		$("#plugin-data .item-stared").click(function () {
			once.plugins.itemEditStar($(this));
		});

		// Change thumbnail
		$("#plugin-data .item-thumbnail").click(function () {
			$("#uploadThumbnail input[type='file']").click();
		});

		// Change image item
		$("#plugin-data .item-image").click(function () {
			$("#uploadImage input[name='currentImage']").val('');
			$("#uploadImage input[type='file']").click();
		});
		
		// Galery image change
		$("#plugin-data .item-image-change").click(function () {
			$("#uploadImage input[name='currentImage']").val($(this).parent().parent().parent().parent().data("id"));
			$("#uploadImage input[type='file']").click();
		});
		
		// Galery image delete
		$("#plugin-data .item-image-delete").click(function () {
			once.plugins.itemEditImageDelete($(this));
		});
		
		// Initialize editForm
		once.plugins.forms.editForm($(this));

		// Initialize uploadImage
		once.plugins.forms.uploadImage($(this));
		
		// Initialize uploadThumbnail
		once.plugins.forms.uploadThumbnail($(this));
	},
	importInit: function(obj){//ok
		// After tab show action
		$('#import-data a[data-toggle="tab"]').on('show.bs.tab', function (e) {
			e.target // activated tab
			e.relatedTarget // previous tab

			// Action for selected tab
			if($(e.target).attr('href')=='#import_url'){
				$("#importForm input[name='url']").val('');
				$("#importForm input[name='file']").val('');
			}else{
				$("#importForm input[name='url']").val('');
				$("#importForm input[name='file']").val('');
			}
		});

		// Import item from import mode
		$("#import-data .item-import-save").click(function () {
			once.plugins.itemImportSave($(this));
		});

		// Initialize importForm
		once.plugins.forms.importForm($(this));
	},
	previewInit: function(obj){//ok
		once.editors = new Array()

		// Code mirror for sources
		var value = "// The bindings defined specifically in the Sublime Text mode\nvar bindings = {\n";
		var map = CodeMirror.keyMap.sublime, mapK = CodeMirror.keyMap["sublime-Ctrl-K"];
		for (var key in map) {
			if (key != "Ctrl-K" && key != "fallthrough" && (!/find/.test(map[key]) || /findUnder/.test(map[key]))) value += "  \"" + key + "\": \"" + map[key] + "\",\n";
		}
		for (var key in mapK) {
			if (key != "auto" && key != "nofallthrough") value += "  \"Ctrl-K " + key + "\": \"" + mapK[key] + "\",\n";
		}

		value += "}\n\n// The implementation of joinLines\n";
		value += CodeMirror.commands.joinLines.toString().replace(/^function\s*\(/, "function joinLines(").replace(/\n  /g, "\n") + "\n";

		once.editors.push(0);

		// Initialize on code window functions
		// Replace the <textarea id="code"> with an CodeMirror
		once.editors.push(CodeMirror.fromTextArea(document.getElementById("ajax-playground"), {
			value: value,
			styleActiveLine: false,
			lineNumbers: true,
			lineWrapping: true,
			extraKeys: [{"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }},{"Ctrl-J": "toMatchingTag"}],
			foldGutter: true,
			gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
			highlightSelectionMatches: {showToken: /\w/},
			matchTags: {bothTags: true},
			keyMap: "sublime",
			autoCloseBrackets: true,
			matchBrackets: true,
			showCursorWhenSelecting: true,
			theme: "monokai"
		}));

		// Open selected tab
		$('#preview-data a[data-toggle="tab"]').on('show.bs.tab', function (e) {
			var tab = $(this); // current tab
			var previous = e.relatedTarget; // previous active tab
			var previous_id = $(e.relatedTarget).data('editor');

			$("#plugin-data").data("tab",tab.attr('href'));
			
			var tab = $(this); // current tab

			// Load tab content
			if(tab.attr('href')=='#preview_source'){
				// Set previus last content
				$("#source_"+previous_id).html(once.editors[1].getValue());
				
				// Refresh codemirror
				setTimeout("once.editors[1].refresh()", 1000);
				
				// Get codemirror mode
				var name=tab.html();
				var mode='';
				switch(tab.html()){
					case 'Plugin UI':
						mode= {
						name: "htmlmixed",
						scriptTypes: [{matches: /\/x-handlebars-template|\/x-mustache/i,
									   mode: null},
									  {matches: /(text|application)\/(x-)?vb(a|script)/i,
									   mode: "vbscript"}]
						};
					break;
					case 'Global CSS':
						mode='css';
					break;
					case 'Main JS':
						mode='javascript';
					break;
					case 'PHP':
						mode='application/x-httpd-php';
					break;
					case 'CSS':
						mode='css';
					break;
					case 'JS':
						mode='javascript';
					break;
					case 'AJAX':
						mode='application/x-httpd-php';
					break;
					case 'CLASS':
						mode='application/x-httpd-php';
					break;
				}
				
				// Focus editor
				once.editors[1].focus();
				
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

		// Download item
		$("#preview-data .item-download").click(function () {
			once.plugins.itemPreviewDownload($(this));
		});
	},
	publishInit: function(obj){//ok
		// Select item
		$("#plugin-data .item-select").click(function () {
			once.plugins.itemPublishSelect($(this));
		});
		
		// Set display limit
		$("#plugin-data .display-limit").change(function () {
			once.plugins.itemPublishDisplayLimit($(this));
		});
		
		// Get selected page
		$("#plugin-data .pagination a").click(function () {
			once.plugins.itemPublishPage($(this));
		});
		
		// Select item
		$("#plugin-data .item-submit").click(function () {
			once.plugins.itemPublishSubmit($(this));
		});
		
		// Initialize publishForm
		once.plugins.forms.publishForm($(this));
	},
}

once.plugins.dialogs = {
	itemEdit: function(obj){//ok
		// Append at end of the body
		$("body").append("<div id=\"item-edit\"></div>");

		// Read and open edit dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-edit").load(once.path+"/dialog.php?c=plugins&o=edit&id="+$(this).parent().parent().data("id"), function() {
				$('#item-edit .modal:first').modal({
					show: 'false'
				});
			})
			.error(function() { console.log("Dialog Error: edit"); });
		});
	},
	itemImport: function(obj){//ok
		// Append at end of the body
		$("body").append("<div id=\"item-import\"></div>");

		// Read and open import dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-import").load(once.path+"/dialog.php?c=plugins&o=import", function() {
				$('#item-import .modal:first').modal({
					show: 'false'
				});
			})
			.error(function() { console.log("Dialog Error: import"); });
		});
	},
	itemPreview: function(obj){//ok
		// Append at end of the body
		$("body").append("<div id=\"item-preview\"></div>");

		// Read and open preview dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-preview").load(once.path+"/dialog.php?c=plugins&o=preview&id="+$(this).parent().parent().data("id"), function() {
				$('#item-preview .modal:first').modal({
					show: 'false'
				});
			})
			.error(function() { console.log("Dialog Error: preview"); });
		});
	},
	itemPublish: function(obj){//ok
		// Append at end of the body
		$("body").append("<div id=\"item-publish\"></div>");

		// Read and open publish dialog
		$(obj).click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			$("#item-publish").load(once.path+"/dialog.php?c=plugins&o=publish", function() {
				$('#item-publish .modal:first').modal({
					show: 'false'
				});
			})
			.error(function() { console.log("Dialog Error: publish"); });
		});
	},
}

once.plugins.forms = {
	checkForm: function(obj){//ok
		var options = {
			dataType:  "json",
			success: function(data){
				
				console.log($("#checkForm").data("type"));
				console.log($("#checkForm").data("module"));
				
				// Call for refresh
				$.get(once.path+"/view.php?c=plugins&o="+$("#plugins-data").data("o")+"&type_id="+$("#plugins-data").data("type_id")+"&category_id="+$("#plugins-data").data("category_id")+"&sort_by="+$("#plugins-data").data("sort_by")+"&page="+$("#plugins-data").data("page")+"&ids="+$("#plugins-data").data("ids")+"&query="+$("#plugins-data").data("query"), function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("Request Error: "+$("#plugins-data").data("o")); });
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
	editForm: function(obj){//ok
		$("#editForm").attr("action",once.path+"/ajax.php?c=plugins&o=item_edit&id="+$("#plugin-data").data("id"));
		var options = {
			dataType:  "json",
			success: function(data){
				// Update name & version on items list
				if(data.status=='ok'){
					if($("#plugin-data").data('redirect')==undefined){
						// Get new data
						var name=$("#editForm input[name='name']");
						var version=$("#editForm input[name='version']");

						// Update DOM
						$("tr[data-id='"+data.item.id+"'] td[data-link='name']").html(name.val());
						$("tr[data-id='"+data.item.id+"'] td[data-link='version']").html(version.val());
					}
					
					console.log("Plugin updated!");
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
	importForm: function(obj){//ok
		$("#importForm").attr("action",once.path+"/ajax.php?c=plugins&o=item_import");
		var options = {
		dataType:  "json",
			success: function(data){
				// If response ok refresh logo
				if(data.status=='ok'){
					// Call for refresh
					$.get(once.path+"/view.php?c=plugins&o="+$("#plugins-data").data("o")+"&type_id="+$("#plugins-data").data("type_id")+"&category_id="+$("#plugins-data").data("category_id"), function(data) {
						$("#content-body").html(data);
					})
					.error(function() { alert("Couldnt load page"); });
					
					// Open edit dialog
					setTimeout(function(){
						$("#import-data .item-close").click();
						$("#item_"+data.item.id+" .item-edit").click();
					},500);
					console.log("Plugin imported");
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
	publishForm: function(obj){//ok
		$("#publishForm").attr("action",once.path+"/ajax.php?c=plugins&o=item_publish&id="+$("#plugin-data").data("id"));
		var options = {
		dataType:  "json",
			success: function(data){
				// If response ok refresh logo
				if(data.status=='ok'){
					$("#plugin-data .item-close").click();
					console.log("Plugin published");
				}else{
					console.log("Action Error: "+data.error);
				}
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
	searchForm: function(obj){//ok
		$("#searchForm").attr("action",once.path+"/view.php?c=plugins&o="+$("#plugins-data").data("o"));
		var options = {
			complete: function(data){
				$("#content-body").html(data.responseText);
			},
		};
		$("#searchForm").ajaxForm(options);
	},
	uploadImage: function(obj){//ok
		if(!$("#uploadImage").length){
			var str='';
			str+='<form id="uploadImage" method="post" enctype="multipart/form-data" class="hidden">';
				str+='<input type="hidden" size="60" name="currentImage">';
				str+='<input type="file" size="60" name="myImage" id="myImage">';
				str+='<input type="submit" value="Ajax File Upload">';
			str+='</form>';
			// Append at end of the body
			$("body").append(str);
			
			// Onclick event
			$("#uploadImage input[type='file']").change(function(e) {
				$("#uploadImage input[type='submit']").click();
			});
		}

		$("#uploadImage").attr("action",once.path+"/ajax.php?c=plugins&o=upload_image&id="+$("#plugin-data").data("id"));
		var options = { 
			dataType:  "json",
			success: function(data){
				// If response ok refresh image
				if(data.status=='ok'){
					// Image overwrite
					$("#image_"+data.item.currentImage+" img").attr("src",$("#image_"+data.item.currentImage+" img").data('src')+"?"+Math.random());
					// Show
					$("#image_"+data.item.currentImage).removeClass("hidden");
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
	uploadThumbnail: function(obj){//ok
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

		$("#uploadThumbnail").attr("action",once.path+"/ajax.php?c=plugins&o=upload_thumbnail&id="+$("#plugin-data").data("id"));
		var options = { 
			dataType:  "json",
			success: function(data){
				// If response ok refresh thumbnail
				if(data.status=='ok'){
					$("#item-thumbnail").attr("src","/once/plugins/"+data.item.id+"/thumbnail.png?"+Math.random());
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
	// Initialize onclick action
	$(".item-new").click(function () {
		once.plugins.itemNew($(this));
    });

	// Initialize import dialog
	once.plugins.dialogs.itemImport(".item-import");
	
	// Initialize publish dialog
	once.plugins.dialogs.itemPublish(".item-publish");

	// Initialize categories & actions
	if($("#categories-data").length){
		once.loadJSfile(once.path+'/js/once.categories.js');
	}

	// Load libraries & modes
	if($("#plugin-data").data("ajax") || $("#plugins-data").data("ajax")){
		once.loadJSfile(once.path+'/libs/jquery-form/jquery.form.js');
		//once.loadJSfile('//oss.maxcdn.com/jquery.form/3.50/jquery.form.min.js');
	}

	// Initialize plugins actions
	if($("#plugins-data").length>0){
		// Sort actions
		$("#plugins-data .sort-action").click(function () {
			once.plugins.sortAction($(this));
		});

		// Initialize searchForm if its ajax only
		if($("#plugins-data").data("ajax")){
			once.plugins.forms.searchForm($(this));
		}

		if(once.cms){
			setTimeout(function(){
				// Load code mirror library & modes
				once.loadJSfile(once.path+'/libs/codemirror/lib/codemirror.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/xml/xml.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/javascript/javascript.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/css/css.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/vbscript/vbscript.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/htmlmixed/htmlmixed.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/clike/clike.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/php/php.js');
				once.loadJSfile(once.path+'/libs/codemirror/mode/markdown/markdown.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/comment/comment.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/dialog/dialog.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/edit/matchtags.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/edit/matchbrackets.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/fold/foldcode.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/fold/foldgutter.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/fold/brace-fold.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/fold/xml-fold.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/fold/markdown-fold.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/fold/comment-fold.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/hint/show-hint.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon//tern/tern.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/search/match-highlighter.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/search/search.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/search/searchcursor.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/selection/active-line.js');
				once.loadJSfile(once.path+'/libs/codemirror/addon/wrap/hardwrap.js');
				once.loadJSfile(once.path+'/libs/codemirror/keymap/sublime.js');

				// Load code mirror styles
				once.loadCSSfile(once.path+'/libs/codemirror/lib/codemirror.css');
				once.loadCSSfile(once.path+'/libs/codemirror/addon/fold/foldgutter.css');
				once.loadCSSfile(once.path+'/libs/codemirror/addon/dialog/dialog.css');
				once.loadCSSfile(once.path+'/libs/codemirror/theme/monokai.css');
			}, 3000);
		}
	}

	// Initialize plugin actions
	if($("#plugin-data").length){
		once.loadJSfile(once.path+'/lib/jquery-validation/dist/jquery.validate.js');
		//once.loadJSfile('http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js');
		
		// Load code mirror library & modes
		once.loadJSfile(once.path+'/libs/codemirror/lib/codemirror.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/xml/xml.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/javascript/javascript.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/css/css.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/vbscript/vbscript.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/htmlmixed/htmlmixed.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/clike/clike.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/php/php.js');
		once.loadJSfile(once.path+'/libs/codemirror/mode/markdown/markdown.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/comment/comment.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/dialog/dialog.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/edit/matchtags.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/edit/matchbrackets.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/fold/foldcode.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/fold/foldgutter.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/fold/brace-fold.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/fold/xml-fold.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/fold/markdown-fold.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/fold/comment-fold.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/hint/show-hint.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon//tern/tern.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/search/match-highlighter.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/search/search.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/search/searchcursor.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/selection/active-line.js');
		once.loadJSfile(once.path+'/libs/codemirror/addon/wrap/hardwrap.js');
		once.loadJSfile(once.path+'/libs/codemirror/keymap/sublime.js');

		// Load code mirror styles
		once.loadCSSfile(once.path+'/libs/codemirror/lib/codemirror.css');
		once.loadCSSfile(once.path+'/libs/codemirror/addon/fold/foldgutter.css');
		once.loadCSSfile(once.path+'/libs/codemirror/addon/dialog/dialog.css');
		once.loadCSSfile(once.path+'/libs/codemirror/theme/monokai.css');

		once.plugins.actions.editInit();
	}

	// Initialize / sandbox
	once.plugins.initialized();
});
