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
if(isset($_GET['option']) && $_GET['option']!=''){
	// Reset category_id
	$_GET['category_id']=0;

	// Clean category name
	$_GET['option'] = preg_replace('/-/i',' ', $_GET['option']);

	// Prepare statements to get selected data
	$stmt = $once->pdo->prepare("SELECT * FROM edit_langs_categories WHERE LOWER(name) LIKE :category");
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
	"query_in" => array('name')
));

# GET DATA -------------------
//$categories=$once->once_select_items('langs_types');

# GET DATA -------------------
$data=$once->once_select_items_page('langs','project_id');

# GET DATA -------------------
$project_langs=$once->once_lang_list();

# GET DATA -------------------
$langs=$once->once_select_items('langs');
if($langs['items']){
	foreach($langs['items'] as $key => $val){
		$langs_a[$langs['items'][$key]['lang_id']]=$langs['items'][$key]['name']."".$langs['items'][$key]['name_id'];
	}
}

?>
<div id="langs-data" class="box" data-ajax="true" data-c="<?php echo $_GET['c'];?>" data-o="<?php echo $_GET['o'];?>" data-type_id="<?php echo $_GET['type_id'];?>" data-category_id="<?php echo $_GET['category_id'];?>" data-category_name="<?php echo $once->url_slug($_GET['category_name']);?>" data-sort_by="<?php echo $_GET['sort_by'];?>" data-ids="<?php echo $_GET['idsxs'];?>" data-page="<?php echo $_GET['page'];?>" data-query="<?php echo $_GET['query'];?>">
	<div class="row box-header">
		<form id="searchForm" method="get">
			<div class="col-sm-6">
				<label>
					<input type="checkbox" id="check-all"/>
				</label>
				<div class="btn-group">
					<button type="button" class="btn btn-default btn-sm btn-flat dropdown-toggle" data-toggle="dropdown">
						Action <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a class="bulk-action" data-action="delete">Delete</a></li>
					</ul>
				</div>
			</div>
			<div class="col-sm-6 search-form">
				<div class="input-group">
					<input type="text" class="form-control input-sm" placeholder="Search by name" name="query" value="">
					<div class="input-group-btn">
						<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="row box-body">
		<?php
		if($data['items']){
			if($data['view_type']==''){ ?>
				<div class="mailbox">
					<form id="checkForm" method="post">
						<input type="hidden" name="action">
						<div class="col-md-12 col-sm-8">
							<div class="table-responsive">
								<table id="tablelist" class="table table-bordered table-striped table-mailbox">
									<thead>
										<th></th>
										<th>Lang variable</th>
										<?php
											foreach($project_langs['items'] as $key => $val){
												echo '<th>'.$project_langs['items'][$key]['desc'].'</th>';
											}
										?>
										<th></th>
									</thead>
									<tbody>
									<?php
										foreach($data['items'] as $key => $val){
											echo '
											<tr id="item_'.$data['items'][$key]['id'].'" data-id="'.$data['items'][$key]['id'].'">
												<td class="small-col"><input type="checkbox" name="ids['.$data['items'][$key]['id'].']"/></td>
												<td class="item-name">$_LANG['.$data['items'][$key]['name'].''.$data['items'][$key]['name_id'].']</td>';
													foreach($project_langs['items'] as $key2 => $val){
														echo '<td><input class="item-update" data-param="source_'.$project_langs['items'][$key2]['name'].'" type="text" value="'.$data['items'][$key]['source_'.$project_langs['items'][$key2]['name']].'"></td>';
													}
												echo '
												<td class="small-col">
													<a class="item-delete" title="route delete" style="cursor: pointer;"><i class="fa fa-trash-o"></i></a>
												</td>
											</tr>';
										}
									?>
									</tbody>
								</table>
							</div>
						</div>
					</form>
				</div>
			<?php 
			}
		}else{
			echo '
			<div class="col-md-12">
				Not found any langs here, be first and create it once!
			</div>';
		}
		?>
	</div>
	<div class="row box-footer">
		<div class="col-md-12">
			<div class="pull-right">
			<?php
			if($data['pages']){
				$urlb='';
				if($_GET['query']!='' || $_GET['sort_by']){$urlb.='?';}
				if($_GET['query']!=''){$urlb.='&query='.$_GET['query'];}
				if($_GET['sort_by']!=''){$urlb.='&sort_by='.$_GET['sort_by'];}
						
				if($_GET['option']!=''){$urla='/'.$once->url_slug($_GET['option']);}

				echo '
				<ul class="pagination">';
					for($i=1;$i<=$data['pages'];$i++){
						echo '
						<li><a href="/langs'.$urla.'/page/'.$i.''.$urlb.'" '.($_GET['page']==$i?'class="active"':'').'>'.$i.'</a></li>';
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
		// Initialize actions
		once.langs.actions.mainInit($(this));
	});
</script>