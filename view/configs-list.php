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
$_GET['key'] = isset($_GET['key']) ? $_GET['key'] : '';
$_GET['query'] = isset($_GET['query']) ? $_GET['query'] : '';

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

# SET DATA -------------------
$once->set_data(array(
	"key" => $once->filter_string($_GET['key']),
	"query" => $once->filter_string($_GET['query'])
));

# GET DATA -------------------
$variables=$once->variables_get('configs');

?>
<div id="configs-data" class="box" data-ajax="true" data-c="<?php echo $_GET['c'];?>" data-o="<?php echo $_GET['o'];?>" data-query="<?php echo $_GET['query'];?>">
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
		if($variables['items']){
			if($variables['view_type']==''){ ?>
				<div class="mailbox">
					<form id="checkForm" method="post">
						<input type="hidden" name="action">
						<div class="col-md-12 col-sm-8">
							<div class="table-responsive">
								<table id="tablelist" class="table table-bordered table-striped table-mailbox">
									<thead>
										<th></th>
										<th>Config variable</th>
										<th>Current value</th>
										<th></th>
									</thead>
									<tbody>
									<?php
										foreach($variables['items'] as $key => $val){
											echo '
											<tr id="item_'.$key.'" data-key="'.$key.'">
												<td class="small-col"><input type="checkbox" name="ids['.$key.']"/></td>
												<td class="item-name">$_CONFIG['.$key.']</td>
												<td><input class="item-update" type="text" value="'.$val.'"></td>
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
				Not found any configs here, be first and create it once!
			</div>';
		}
		?>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		// Initialize actions
		once.configs.actions.mainInit($(this));
	});
</script>