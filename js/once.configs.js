/**
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Configs plugin (once.configs)
 *
*/

once.configs = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
	
	// List function
	bulkAction: function(obj){//ok
		// Set action value
		$("#checkForm input[name='action']").val(obj.data("action"));
		// Set action o
		$("#checkForm").attr("action",once.path+"/ajax.php?c=configs&o=bulk_action");
		// Submit form
		$("#checkForm").submit();
	},
	displayLimit: function(obj){//ok
		// Call for set limit
		$.getJSON(once.path+"/ajax.php?c=configs&o=set_limit&limit="+$(obj).val(), function(data) {
			$(".pagination a:first").click();
		})
		.error(function() { console.log("Request Error: set_limit"); });
	},
	openPage: function(obj){//ok
		// Open selected page with params
		$.get(once.path+"/view.php?c=configs&o="+$("#configs-data").data("o")+"&type_id="+$("#configs-data").data("type_id")+"&category_id="+$("#configs-data").data("category_id")+"&sort_by="+$("#configs-data").data("sort_by")+"&page="+obj.html()+"&ids="+$("#configs-data").data("ids")+"&query="+$("#configs-data").data("query"), function(data) {
			$("#content-body").html(data);
		})
		.error(function() { console.log("Request Error: "+$("#configs-data").data("o")); });
	},
	sortAction: function(obj){//ok
		if($("#configs-data").data("ajax")){
			$("#sort-label").html(obj.html());
			// Refresh items list by sort key
			$.get(once.path+"/view.php?c=configs&o="+$("#configs-data").data("o")+"&type_id="+$("#configs-data").data("type_id")+"&category_id="+$("#configs-data").data("category_id")+"&sort_by="+obj.data("sort")+"&page="+$("#configs-data").data("page")+"&ids="+$("#configs-data").data("ids")+"&query="+$("#configs-data").data("query"), function(data) {
				$("#content-body").html(data);
			})
			.error(function() { alert("Couldn\'t load sort"); });
		}else{
			// Refresh items list by sort key
			$("#sort-label").html(obj.html());
			document.location.href='/configs?category_name='+$("#configs-data").data('category')+'&sort_by='+obj.data("sort")+'&p='+$("#configs-data").data("page")+'&query='+$("#configs-data").data('query');
		}
	},
	// Load type
	typeLoad: function(obj){
		// Remove all highlights
		$(".list-group-item").removeClass("current");
		// Highlight selected object
		obj.addClass("current");
		var c=$("div[data-c]");
		// Call for items list with selected type
		$.get(once.path+"/view.php?c="+$(c).data("c")+"&o="+$(c).data("o")+"&key="+obj.data("key"), function(data) {
			$("#content-body").html(data);
		})
		.error(function() { console.log("Request Error: "+$(c).data("o")); });
	},
	
	// View function
	itemDelete: function(obj){//ok
		// Get varibles defined in rendering data-*
		var key=$(obj).parent().parent().data('key');
		// We need to confirm to delete
		var r = confirm("Delete $_CONFING["+key+"]?");
		if(r){
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=configs&o=item_delete&key="+key,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						// Intermediatly delete from dom then refresh items list
						$("#item_"+key).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=configs&o="+$("#configs-data").data("o")+"&query="+$("#configs-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#configs-data").data("o")); });
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
	itemUpdate: function(obj){//ok
		// Get varibles defined in rendering data-*
		var key=$(obj).parent().parent().data('key');
		var value=$(obj).val();
		// Call to del_layer with parm id, value, config
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=configs&o=item_update&key="+key+"&value="+value,
			success: function(data) { 
				if(data.status=='ok'){
					//ok
				}else{
					alert(data.error);
				}
			},
			contentType: "application/json",
			dataType: 'json'
		})
		.error(function() { console.log("Request Error: item_update"); });
	},
}


once.configs.actions = {
	mainInit: function(obj){//ok
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
				once.configs.bulkAction($(this));
			});
		}
		
		// Sort actions
		$(".sort-action").click(function () {
			once.configs.sortAction($(this));
		});
		
		// Star item
		$(".item-star").click(function () {
			once.configs.itemStar($(this));
		});

		// Delete item
		$(".item-delete").click(function () {
			once.configs.itemDelete($(this));
		});
		
		// Set display limit
		$(".display-limit").change(function () {
			once.configs.displayLimit($(this));
		});
		
		// Get selected page
		$(".pagination a").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.configs.openPage($(this));
		});

		// Update configs
		$(".item-update").keyup(function () {
			once.configs.itemUpdate($(this));
		});
		
		// Set links
		$(".item-select").change(function () {
			once.configs.itemSelect($(this));
		});
		
		// Initialize addForm
		once.configs.forms.addForm($(this));
		
		// Initialize checkForm
		once.configs.forms.checkForm($(this));
				
		// Initialize searchForm
		once.configs.forms.searchForm($(this));

		// Initialize / sandbox
		once.configs.initialized();
	},
}

once.configs.forms = {
	addForm: function(obj){//ok
		$("#addForm").attr("action",once.path+"/ajax.php?c=configs&o=item_new");
		var options = {
			dataType:  "json",
			success: function(data){
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Call for refresh
					$.get(once.path+"/view.php?c=configs&o="+$("#configs-data").data("o")+"&query="+$("#configs-data").data("query"), function(data) {
						$("#content-body").html(data);
					})
					.error(function() { alert("couldnt load selected page"); });
				}else{
					console.log("Action Error: "+data.error);
				}
			},
			complete: function(){
				//console.log(data.responseText);
			},
			error: function(){
				console.log("Form Error: addForm");
			}
		};
		$("#addForm").ajaxForm(options);
	},
	checkForm: function(obj){//ok
		$("#checkForm").attr("action",once.path+"/view.php?c=configs");
		var options = {
			dataType:  "json",
			success: function(data){
			
				console.log($("#checkForm").data("type"));
				console.log($("#checkForm").data("module"));
				
				// Call for refresh
				$.get(once.path+"/view.php?c=configs&o="+$("#configs-data").data("o")+"&type_id="+$("#configs-data").data("type_id")+"&category_id="+$("#configs-data").data("category_id")+"&sort_by="+$("#configs-data").data("sort_by")+"&page="+$("#configs-data").data("page")+"&ids="+$("#configs-data").data("ids")+"&query="+$("#configs-data").data("query"), function(data) {
					$("#content-body").html(data);
				})
				.error(function() { console.log("Request Error: "+$("#snippets-data").data("o")); });
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
	searchForm: function(obj){//ok
		$("#searchForm").attr("action",once.path+"/view.php?c=configs&o="+$("#configs-data").data("o"));
		var options = {
			complete: function(data){
				$("#content-body").html(data.responseText);
			},
		};
		$("#searchForm").ajaxForm(options);
	},
}

$(document).ready(function () {
	// Initialize onclick action
	$(".item-new").click(function () {
		$("#addForm").submit();
    });

	// Initialize variables & actions
	if($("#variables-data").length){
		// Initialize on click
		$("#variables-data li.list-group-item").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.configs.typeLoad($(this));
		});
	}

	// Initialize / sandbox
	once.configs.initialized();
});