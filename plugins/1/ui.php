<?php
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is core connector class
 *
*/

# XAMPP fix without turning error info off -------------------
$_GET['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
$_GET['ids'] = isset($_GET['ids']) ? $_GET['ids'] : '';
$_GET['idsx'] = isset($_GET['idsx']) ? $_GET['idsx'] : '';
$_GET['idsxs'] = isset($_GET['idsxs']) ? $_GET['idsxs'] : '';
$_GET['option'] = isset($_GET['option']) ? $_GET['option'] : '';
$_GET['type_id'] = isset($_GET['type_id']) ? $_GET['type_id'] : '';
$_GET['category_id'] = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$_GET['sort_by'] = isset($_GET['sort_by']) ? $_GET['sort_by'] : 0;
$_GET['query'] = isset($_GET['query']) ? $_GET['query'] : '';

# DECLARE SORT ARRAY -------------------
$data_sort=array('','id DESC','id ASC','created DESC','created ASC','name DESC','name ASC');

# FIX ARRAY -------------------
if(gettype($_GET['ids'])=='array'){
	foreach ($_GET['ids'] as $position => $item){
		$_GET['idsx'][]=intval($position);
		$_GET['idsxs'].='&ids['.intval($position).']=on';
	}
}

# CHECK QUERIES -------------------
if(!preg_match("/^[a-zA-Z0-9-]+$/", $_GET['option'])) {
	$_GET['option']='';
}

if(!preg_match("/^[a-zA-Z0-9]+$/", $_GET['query'])) {
	$_GET['query']='';
}

# FIX CATEGORY -------------------
if($_GET['option']!=''){
	// Reset category_id
	$_GET['category_id']=0;

	// Clean category name
	$_GET['option'] = preg_replace('/-/i',' ', $_GET['option']);

	// Prepare statements to get selected data
	$stmt = $once->pdo->prepare("SELECT * FROM edit_contents_categories WHERE LOWER(name) LIKE :category");
	$stmt->bindParam(':category', $_GET['option'], PDO::PARAM_STR, 50);
	$stmt->execute();

	// Return result in table
	$row=$stmt->fetch(PDO::FETCH_ASSOC);

	// Check if item exist
	if($row['id']){
		$_GET['category_id']=$row['id'];
	}
}

# SET DATA -------------------
$once->set_data(array(
	"type_id" => intval($_GET['type_id']),
	"category_id" => intval($_GET['category_id']),
	"page" => intval($_GET['page']),
	"sort_by" => $data_sort[$_GET['sort_by']],
	"ids" => $_GET['idsx'],
	"query" => $once->filter_string($_GET['query']),
	"query_in" => array('name'),
	"where" => ''
));


# GET DATA -------------------
$data=$once->once_select_items_page('snippets');

# GET DATA -------------------
//$data=json_decode(file_get_contents('plugins/1/data/ui.json'), true);

?>
<div id="ui-data" data-plugin_id="<?php echo $_GET['plugin_id'];?>" data-layer_id="<?php echo $_GET['layer_id'];?>" data-page_id="<?php echo $_GET['page_id'];?>" data-grid_id="<?php echo $_GET['grid_id'];?>">
	<div class="row" style="margin-bottom: 20px">
		<form id="UIsearchForm">
			<div class="col-sm-12 search-form">
				<div class="input-group">
					<input type="text" class="form-control input-sm" placeholder="Search by name, description" name="query" value="">
					<div class="input-group-btn">
						<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
					</div>
				</div>
			</div>
		</form>
	</div><!-- /.row -->
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<?php
				if($data['items']){
					foreach($data['items'] as $key => $val){
						echo '
						<div class="col-md-3 item-select" id="item_'.$data['items'][$key]['id'].'" data-id="'.$data['items'][$key]['id'].'">
							<a href="#" class="thumbnail">
								<img src="/once/snippets/'.$data['items'][$key]['id'].'/thumbnail.png" class="img-responsive" onerror="this.src=\'img/snippet.png\'">
								<h5 style="text-align: center; height: 20px;">'.$data['items'][$key]['name'].'</h5>
							</a>
						</div>';
					}
				}
				?>
			</div><!-- /.col (RIGHT) -->
		</div><!-- /.col (RIGHT) -->
	</div><!-- /.col (RIGHT) -->
	<div class="row">
		<div class="col-md-12">
			<div class="pull-right">
			<?php
			if(isset($data['page'])){
				$urlb='';
				if($_GET['query']!='' || $_GET['sort_by']){$urlb.='?';}
				if($_GET['query']!=''){$urlb.='&query='.$_GET['query'];}
				if($_GET['sort_by']!=''){$urlb.='&sort_by='.$_GET['sort_by'];}
				if($_GET['option']!=''){$urla='/'.$once->url_slug($_GET['option']);}else{$urla='';}

				echo '
				<ul class="pagination">';
					for($i=1;$i<=$data['pages'];$i++){
						echo '
						<li><a href="#" '.($_GET['page']==$i?'class="active"':'').'>'.$i.'</a></li>';
					}
					echo '
				</ul>';
			}
			?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		
		"use strict";
		
		// Load & use form
		once.loadJSfile(once.path+'/js/jquery.form.js');
		$("#UIsearchForm").attr("action","plugins.php?c=ui&plugin_id="+$("#ui-data").data("plugin_id")+"&layer_id="+$("#ui-data").data("layer_id")+"&page_id="+$("#ui-data").data("page_id")+"&grid_id="+$("#ui-data").data("grid_id"));
		var options = {
			complete: function(data){
				$("#ui-data").parent().html(data.responseText);
			},
		};
		$("#UIsearchForm").ajaxForm(options);
		
		// Select item
		$("#ui-data .pagination a").click(function () {
			$("#ui-data").parent().load("plugins.php?c=ui&plugin_id="+$("#ui-data").data("plugin_id")+"&layer_id="+$("#ui-data").data("layer_id")+"&page_id="+$("#ui-data").data("page_id")+"&grid_id="+$("#ui-data").data("grid_id")+"&page="+$(this).html(), function() {
				//$(".modal-body").html(data);
			})
			.error(function() { console.log("Dialog Error: publish"); });
		});	

		// Change item
		$("#ui-data .item-select").click(function () {
			// We need to confirm to change this file
			var r = confirm("Do you want to change whole col for this snippet?");
			if(r){
				$.ajax({
					type: 'POST',
					url: once.path+"/plugins.php?c=ui&s=ajax&o=item_select&id="+$(this).data("id")+"&plugin_id="+$("#ui-data").data("plugin_id")+"&layer_id="+$("#ui-data").data("layer_id")+"&page_id="+$("#ui-data").data("page_id")+"&grid_id="+$("#ui-data").data("grid_id"),
					success: function(data) { 
						// Del col / row if response ok
						if(data.status=='ok'){
							console.log("Action OK");
						}else{
							console.log("Action Error: "+data.error);
						}
					},
					contentType: "application/json",
					dataType: 'json'
				})
				.error(function() { console.log("Request Error: item_change"); });
			}
		});
	});
</script>