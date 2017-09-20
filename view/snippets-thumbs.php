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
	"where" => 'published=1'
));

# GET DATA -------------------
$categories=$once->once_select_items('snippets_categories');

# GET DATA -------------------
$data=$once->once_select_items_page('snippets');
?>
<div class="container">
	<div class="row">
		<div class="col-md-3">
			<ul id="categories-data" class="list-group nav nav-pills nav-stacked">
				<?php
				if($categories['items']){
					echo '
					<li class="list-group-item '.($_GET['category_id']==0?'current':'').'" data-id="'.$categories['items'][$key]['id'].'">
						<a href="/snippets"><div class="list-group-header"><i class="fa fa-folder-open"></i><span>All categories</span></div></a>
					</li>';
					foreach($categories['items'] as $key => $val){
						echo '
						<li class="list-group-item '.($_GET['category_id']==$categories['items'][$key]['id']?'current':'').'" data-id="'.$categories['items'][$key]['id'].'">
							<a href="/snippets/'.$once->url_slug($categories['items'][$key]['name']).'"><div class="list-group-header"><i class="'.$categories['items'][$key]['ico'].'"></i><span>'.$categories['items'][$key]['name'].'</span></div></a>
						</li>';
					}
				}
				?>
			</ul>
		</div><!-- /.col -->
		<div class="col-md-9">
			<div id="snippets-data" data-require="/once/js/once.snippets.js" data-ajax="true" data-c="snippets" data-o="thumbs" data-category="<?php echo $once->url_slug($_GET['option']);?>" data-category_id="<?php echo $_GET['category_id'];?>" data-sort_by="<?php echo $_GET['sort_by'];?>" data-ids="<?php echo $_GET['idsxs'];?>" data-page="<?php echo $_GET['page'];?>" data-query="<?php echo $_GET['query'];?>">
				<div class="row">
					<form id="searchForm" action="/snippets" method="get">
						<div class="col-sm-6">
							<!-- Action select button -->
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
				<div class="row">
					<div class="thumbnails">
						<?php
						if($data['items']){
							foreach($data['items'] as $key => $val){
								echo '
								<div class="col-md-3">
									<div class="thumbnail">
										<a href="/snippet/'.$data['items'][$key]['id'].'/'.$once->url_slug($data['items'][$key]['name']).'" rel="nofollow">
											<div class="snippet-image">
												<img src="/once/snippets/'.$data['items'][$key]['id'].'/logo.jpg"  alt="'.$data['items'][$key]['name'].'" class="img-responsive" onerror="this.src=\'/once/img/snippet.png\'"/>
											</div>
										</a>
										<div class="snippet-info">
											<div class="pull-left">
												<a href="/user/'.$data['items'][$key]['id'].'">
													<img src="/once/users/'.$data['items'][$key]['user_id'].'/thumbnail.png" onerror="this.src=\'/once/img/user.png\'">
													<span class="icon"></span>
												</a>
											</div>
											<div>
												<h2>
													<a href="/snippet/'.$data['items'][$key]['id'].'/'.$once->url_slug($data['items'][$key]['name']).'">
													'.$data['items'][$key]['name'].'
													</a>
												</h2>
												<p>
													by <a href="/user/'.$data['items'][$key]['id'].'">'.$data['items'][$key]['author'].'</a>
												</p>
											</div>
										</div>
										<div class="snippet-stats">
											<div class="pull-left">
												<a title="Visits" href="/snippet/'.$data['items'][$key]['id'].'" rel="nofollow">
													<i class="glyphicon glyphicon-eye-open"></i> '.$data['items'][$key]['visits'].'
												</a>
												<a title="Stars" href="/snippet/'.$data['items'][$key]['id'].'" rel="nofollow">
													<i class="glyphicon glyphicon-star"></i> '.$data['items'][$key]['votes'].'
												</a>
												<a title="Comments" href="/snippet/'.$data['items'][$key]['id'].'" rel="nofollow">
													<i class="glyphicon glyphicon-comment"></i> '.$data['items'][$key]['comments'].'
												</a>
											</div>
											<div class="pull-right">
											</div>
										</div>
									</div>
								</div>';
							}
						}else{
							echo '
							<div class="col-md-12">
								Not found any snippets here, be first and create it once!
							</div>';
						}
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="pull-right">
						<?php
						if($data['page']){
							$urlb='';
							if($_GET['query']!='' || $_GET['sort_by']){$urlb.='?';}
							if($_GET['query']!=''){$urlb.='&query='.$_GET['query'];}
							if($_GET['sort_by']!=''){$urlb.='&sort_by='.$_GET['sort_by'];}
									
							if($_GET['option']!=''){$urla='/'.$once->url_slug($_GET['option']);}
								
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
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		// Initialize actions
		once.snippets.actions.mainInit($(this));
	});
</script>