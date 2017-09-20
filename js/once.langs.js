/**
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, support@oncebuilder.com
 *
 * Copyright (c) 2015 Adam Wysocki
 *
 *	This is OnceBuilder Langs plugin (once.langs)
 *
*/

once.langs = {
	loaded: false,
	initialized: function(){
		this.loaded=true;
	},
	
	// List function
	bulkAction: function(obj){//ok
		// Set action value
		$("#checkForm input[name='action']").val(obj.data("action"));
		// Set action o
		$("#checkForm").attr("action",once.path+"/ajax.php?c=langs&o=bulk_action");
		// Submit form
		$("#checkForm").submit();
	},
	displayLimit: function(obj){//ok
		// Call for set limit
		$.getJSON(once.path+"/ajax.php?c=langs&o=set_limit&limit="+$(obj).val(), function(data) {
			$(".pagination a:first").click();
		})
		.error(function() { console.log("Request Error: set_limit"); });
	},
	openPage: function(obj){//ok
		// Open selected page with params
		$.get(once.path+"/view.php?c=langs&o="+$("#langs-data").data("o")+"&type_id="+$("#langs-data").data("type_id")+"&category_id="+$("#langs-data").data("category_id")+"&sort_by="+$("#langs-data").data("sort_by")+"&page="+obj.html()+"&ids="+$("#langs-data").data("ids")+"&query="+$("#langs-data").data("query"), function(data) {
			$("#content-body").html(data);
		})
		.error(function() { console.log("Request Error: "+$("#langs-data").data("o")); });
	},
	sortAction: function(obj){//ok
		if($("#langs-data").data("ajax")){
			$("#sort-label").html(obj.html());
			// Refresh items list by sort key
			$.get(once.path+"/view.php?c=langs&o="+$("#langs-data").data("o")+"&type_id="+$("#langs-data").data("type_id")+"&category_id="+$("#langs-data").data("category_id")+"&sort_by="+obj.data("sort")+"&page="+$("#langs-data").data("page")+"&ids="+$("#langs-data").data("ids")+"&query="+$("#langs-data").data("query"), function(data) {
				$("#content-body").html(data);
			})
			.error(function() { alert("Couldn\'t load sort"); });
		}else{
			// Refresh items list by sort key
			$("#sort-label").html(obj.html());
			document.location.href='/langs?category_name='+$("#langs-data").data('category')+'&sort_by='+obj.data("sort")+'&p='+$("#langs-data").data("page")+'&query='+$("#langs-data").data('query');
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
			// Call to del_layer with parm id
			$.ajax({
				type: 'POST',
				url: once.path+"/ajax.php?c=langs&o=item_delete&id="+id,
				success: function(data) { 
					// Refresh items list if response ok
					if(data.status=='ok'){
						// Intermediatly delete from dom then refresh items list
						$("#item_"+id).remove();
						// Call for refresh
						$.get(once.path+"/view.php?c=langs&o="+$("#langs-data").data("o")+"&type_id="+$("#langs-data").data("type_id")+"&category_id="+$("#langs-data").data("category_id")+"&sort_by="+$("#langs-data").data("sort_by")+"&page="+$("#langs-data").data("page")+"&ids="+$("#langs-data").data("ids")+"&query="+$("#langs-data").data("query"), function(data) {
							$("#content-body").html(data);
						})
						.error(function() { console.log("Request Error: "+$("#langs-data").data("o")); });
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
		var id=$(obj).parent().parent().data('id');
		var value=$(obj).val();
		var param=$(obj).data('param');
		// Call to del_layer with parm id, value, config
		$.ajax({
			type: 'POST',
			url: once.path+"/ajax.php?c=langs&o=item_update&id="+id+"&param="+param+"&value="+value,
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

once.langs.actions = {
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
				once.langs.bulkAction($(this));
			});
		}
		
		// Sort actions
		$(".sort-action").click(function () {
			once.langs.sortAction($(this));
		});
		
		// Star item
		$(".item-star").click(function () {
			once.langs.itemStar($(this));
		});

		// Delete item
		$(".item-delete").click(function () {
			once.langs.itemDelete($(this));
		});
		
		// Set display limit
		$(".display-limit").change(function () {
			once.langs.displayLimit($(this));
		});
		
		// Get selected page
		$(".pagination a").click(function (e) {
			e.preventDefault();
			e.stopPropagation();
			once.langs.openPage($(this));
		});

		// Update langs
		$(".item-update").keyup(function () {
			once.langs.itemUpdate($(this));
		});
		
		// Set links
		$(".item-select").change(function () {
			once.langs.itemSelect($(this));
		});
		
		// Initialize addForm
		once.langs.forms.addForm($(this));
		
		// Initialize checkForm
		once.langs.forms.checkForm($(this));
				
		// Initialize searchForm
		once.langs.forms.searchForm($(this));

		// Initialize / sandbox
		once.langs.initialized();
	},
}

once.langs.forms = {
	addForm: function(obj){//ok
		$("#addForm").attr("action",once.path+"/ajax.php?c=langs&o=item_new&type_id="+$("#langs-data").data('type_id')+"&category_id="+$("#langs-data").data('category_id'));
		var options = {
			dataType:  "json",
			success: function(data){
				// Refresh items list if response ok
				if(data.status=='ok'){
					// Call for refresh
					$.get(once.path+"/view.php?c=langs&o="+$("#langs-data").data("o")+"&type_id="+$("#langs-data").data("type_id")+"&category_id="+$("#langs-data").data("category_id"), function(data) {
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
		$("#checkForm").attr("action",once.path+"/view.php?c=langs");
		var options = {
			dataType:  "json",
			success: function(data){
			
				console.log($("#checkForm").data("type"));
				console.log($("#checkForm").data("module"));
				
				// Call for refresh
				$.get(once.path+"/view.php?c=langs&o="+$("#langs-data").data("o")+"&type_id="+$("#langs-data").data("type_id")+"&category_id="+$("#langs-data").data("category_id")+"&sort_by="+$("#langs-data").data("sort_by")+"&page="+$("#langs-data").data("page")+"&ids="+$("#langs-data").data("ids")+"&query="+$("#langs-data").data("query"), function(data) {
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
		$("#searchForm").attr("action",once.path+"/view.php?c=langs&o="+$("#langs-data").data("o"));
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

	// Initialize categories & actions
	if($("#categories-data").length){
		once.loadJSfile(once.path+'/js/once.categories.js');
	}

	// Initialize / sandbox
	once.langs.initialized();
});