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
if($_GET['option']!=''){
	// Reset category_id
	$_GET['category_id']=0;

	// Clean category name
	$_GET['option'] = preg_replace('/-/i',' ', $_GET['option']);

	// Prepare statements to get selected data
	$stmt = $once->pdo->prepare("SELECT * FROM edit_snippets_categories WHERE LOWER(name) LIKE :category");
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
	"query_in" => array('name','description'),
	"where" => ''
));

# GET DATA -------------------
$types=$once->category_get('snippets');
if(isset($types['items'])){
	foreach($types['items'] as $key => $val){
		$types_a[$types['items'][$key]['id']]=$types['items'][$key]['name'];
	}
}

# GET DATA -------------------
$data=$once->once_select_items_page('snippets');
?>
<div id="snippets-data" class="box" data-ajax="true" data-c="<?php echo $_GET['c'];?>" data-o="<?php echo $_GET['o'];?>" data-type_id="<?php echo $_GET['type_id'];?>" data-category_id="<?php echo $once->url_slug($_GET['option']);?>" data-sort_by="<?php echo $_GET['sort_by'];?>" data-ids="<?php echo $_GET['idsxs'];?>" data-page="<?php echo $_GET['page'];?>" data-query="<?php echo $_GET['query'];?>">
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
						<li><a class="bulk-action" data-action="star">Mark as stared</a></li>
						<li><a class="bulk-action" data-action="unstar">Mark as unstared</a></li>
						<li class="divider"></li>
						<li><a class="bulk-action" data-action="delete">Delete</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button type="button" class="btn btn-default btn-sm btn-flat dropdown-toggle" data-toggle="dropdown">
						<span id="sort-label">
						<?php
							if($_GET['sort_by']=='1'){
								echo 'Added DESC';
							}else if($_GET['sort_by']=='2'){
								echo 'Added ASC';
							}else if($_GET['sort_by']=='3'){
								echo 'Created DESC';
							}else if($_GET['sort_by']=='4'){
								echo 'Created ASC';
							}else if($_GET['sort_by']=='5'){
								echo 'Name DESC';
							}else if($_GET['sort_by']=='6'){
								echo 'Name ASC';
							}else{
								echo 'Sort by';
							}
						?>
						</span>
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a class="sort-action" data-sort="1">Added DESC</a></li>
						<li><a class="sort-action" data-sort="2">Added ASC</a></li>
						<li><a class="sort-action" data-sort="3">Created DESC</a></li>
						<li><a class="sort-action" data-sort="4">Created ASC</a></li>
						<li><a class="sort-action" data-sort="5">Name DESC</a></li>
						<li><a class="sort-action" data-sort="6">Name ASC</a></li>
					</ul>
				</div>
			</div>
			<div class="col-sm-6 search-form">
				<div class="input-group">
					<input type="text" class="form-control input-sm" placeholder="Search by name, description" name="query" value="">
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
										<tr>
											<th></th>
											<th></th>
											<th>Name</th>
											<th style="width: 200px;">Author</th>
											<th style="width: 60px;"></th>
										</tr>
									</thead>
									<tbody>
									<?php 
										foreach($data['items'] as $key => $val){
											echo '
											<tr id="item_'.$data['items'][$key]['id'].'" data-id="'.$data['items'][$key]['id'].'">
												<td class="small-col"><input type="checkbox" name="ids['.$data['items'][$key]['id'].']"/></td>
												<td class="small-col"><i class="fa fa-star'.($data['items'][$key]['stared']==0?'-o':'').' item-star"></i></td>
												<td data-link="name" class="item-name">'.$data['items'][$key]['name'].'</td>
												<td data-link="author">'.$data['items'][$key]['author'].'</td>
												<td>
													<a class="item-edit" title="snippet edit '.$data['items'][$key]['id'].'" style="cursor: pointer;"><i class="fa fa fa-edit"></i></a>
														&nbsp;&nbsp;
													<a class="item-delete" title="snippet delete '.$data['items'][$key]['id'].'" style="cursor: pointer;"><i class="fa fa-trash-o"></i></a>
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
				Not found any snippets here, be first and create it once!
			</div>';
		}
		?>
	</div>
	<div class="row box-footer">
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
						<li><a href="/snippets'.$urla.'/page/'.$i.''.$urlb.'" '.($_GET['page']==$i?'class="active"':'').'>'.$i.'</a></li>';
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
		once.snippets.actions.mainInit($(this));
	});
</script>