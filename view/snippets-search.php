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
$_GET['idsx'] = isset($_GET['idsx']) ? $_GET['idsx'] : array(0);
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
//$categories=$once->category_get('snippets');

# GET DATA -------------------
//$data=$once->once_select_items_page('snippets');

# GET REMOTE DATA -------------------
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://oncebuilder.com/once/ajax.php?c=snippets&o=api_search&type_id=".$_GET['type_id']."&category_id=".$_GET['category_id']."&sort_by=".$_GET['sort_by']."&page=".$_GET['page']."&ids=".$_GET['ids']."&query=".$_GET['query']."");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "api=".$once->data['api_key']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$page = curl_exec($ch);
curl_close($ch);
$response=json_decode($page, true); 

$categories=$response['categories'];

$data=$response['data'];
?>
<div id="snippets-data" class="box" data-ajax="true" data-c="<?php echo $_GET['c'];?>" data-o="<?php echo $_GET['o'];?>" data-type_id="<?php echo $_GET['type_id'];?>" data-category_id="<?php echo $once->url_slug($_GET['option']);?>" data-sort_by="<?php echo $_GET['sort_by'];?>" data-ids="<?php echo $_GET['idsxs'];?>" data-page="<?php echo $_GET['page'];?>" data-query="<?php echo $_GET['query'];?>">
	<div class="row box-header">
		<form id="searchForm" method="get">
			<div class="col-sm-6">
				<div class="btn-group">
					<?php
						if($categories['items']){
							$str ='<li class="list-group-item" value="0" data-id="0"><a href="#">All categories</a></li>';
							foreach($categories['items'] as $key => $val){
								$str.='<li class="list-group-item" value="'.$categories['items'][$key]['id'].'" data-id="'.$categories['items'][$key]['id'].'"><a href="#">'.$categories['items'][$key]['name'].'</a></li>';
								//echo '<li value="'.$categories['items'][$key]['id'].'"><a href="#"><input type="checkbox" name="ids['.$categories['items'][$key]['id'].']" '.(count($_GET['idsx'])>0?(in_array($categories['items'][$key]['id'],$_GET['idsx'])==1?"checked":" "):"").'><span>'.$categories['items'][$key]['name'].'</span></a></li>';
								if($categories['items'][$key]['id']==$_GET['category_id']){
									$strs='<button type="button" class="btn btn-default btn-sm btn-flat dropdown-toggle" data-toggle="dropdown"> '.$categories['items'][$key]['name'].' <span class="caret"></span></button>';
								}
							}
						}
						
						if(strlen($strs)>0){
							echo $strs;
						}else{
							if($data['item']['category_id']==0){
								echo '<button type="button" class="btn btn-default btn-sm btn-flat dropdown-toggle" data-toggle="dropdown"> All categories <span class="caret"></span></button>';
							}else{
								echo '<button type="button" class="btn btn-default btn-sm btn-flat dropdown-toggle" data-toggle="dropdown"> Unknown category <span class="caret"></span></button>';
							}
						}
						echo '<ul id="categories-remote" class="dropdown-menu dropdown-categories" role="menu">'.$str;
					?>
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
		if(isset($data['items'])){
			foreach($data['items'] as $key => $val){
				echo '
				<div class="col-md-3">
					<div class="snippet-item">
						<div class="row snippet-thumbnail">
							<div class="col-md-12">
								<img src="http://oncebuilder.com/once/snippets/'.$data['items'][$key]['id'].'/thumbnail.png?'.time().'" onerror="this.src=\'/once/img/snippet.png\'">
							</div>
						</div>
						<div class="row snippet-name">
							<div class="col-md-12">
								<h3>'.$data['items'][$key]['name'].'</h3>
							</div>
						</div>
						<div class="row snippet-footer" id="item_'.$data['items'][$key]['id'].'" data-id="'.$data['items'][$key]['id'].'">
							<div class="col-md-12">
								<button type="button" class="btn btn-sm item-preview"><i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp; Preview</button>
								<button type="button" class="btn btn-success btn-sm pull-right item-download"><i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp; Download</button>
							</div>
						</div>
					</div>
				</div>';
			}
		}else{
			if(isset($response['error'])){
				echo '
				<div class="col-md-12">
					You are not subscribed. More info at <a target="_blank" href="http://oncebuilder.com">oncebuilder.com</a>
				</div>';
			}else{
				echo '
				<div class="col-md-12">
					Not found any snippets here, be first and create it once!
				</div>';
			}
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