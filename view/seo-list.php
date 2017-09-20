<?php
/*
 * Version: 1.0, 31.05.2012
 * by Adam Wysocki, goldpaid777@gmail.com
 *
 * Copyright (c) 2012 Adam Wysocki
 *
 *	This is view template
 *
*/
# INITIALIZE DATA -------------------
if(!isset($_GET['page'])){
	$_GET['page']=1;
}

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
if(isset($_GET['option']) && $_GET['option']!=''){
	// Reset category_id
	$_GET['category_id']=0;

	// Clean category name
	$_GET['option'] = preg_replace('/-/i',' ', $_GET['option']);

	// Prepare statements to get selected data
	$stmt = $once->pdo->prepare("SELECT * FROM edit_settings_types WHERE LOWER(name) LIKE :category");
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
	"query_in" => array('name','description')
));

# GET DATA -------------------
$categories=$once->once_select_items('settings_types','project_id');

# GET DATA -------------------
$data=$once->once_select_items_page('settings');
?>

<div id="settings-data" class="box" data-ajax="true" data-c="<?php echo $_GET['c'];?>" data-o="<?php echo $_GET['o'];?>" data-type_id="<?php echo $_GET['type_id'];?>" data-category_id="<?php echo $once->url_slug($_GET['option']);?>" data-sort_by="<?php echo $_GET['sort_by'];?>" data-ids="<?php echo $_GET['idsxs'];?>" data-page="<?php echo $_GET['page'];?>" data-query="<?php echo $_GET['query'];?>">
	<div class="row box-body">
		<div class="mailbox">
			<form id="typeForm" method="post">
				<div class="col-md-6">
					<div class="form-group">
						<label for="name">Title</label>
						<input type="text" value="<?php echo $data['item']['name'];?>" class="form-control" name="name" placeholder="Enter name">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="ico">Description</label>
						<input type="text" value="<?php echo $data['item']['ico'];?>" class="form-control" name="ico" placeholder="Enter ico">
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label for="title">Keywords</label>
						<input type="text" value="<?php //echo $data['item']['title'];?>" class="form-control" name="title" placeholder="Enter title" disabled>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label for="keywords">Keywords</label>
						<input type="text" value="<?php //echo $data['item']['keywords'];?>" class="form-control" name="keywords" placeholder="Enter keywords" disabled>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label for="description">Description</label>
						<textarea class="form-control" name="description" placeholder="Enter description" cols="10" rows="10" disabled><?php //echo $data['item']['description'];?></textarea>
					</div>
				</div>
				<input type="submit" class="hidden">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		// Initialize actions
		once.settings.actions.mainInit($(this));
	});
</script>