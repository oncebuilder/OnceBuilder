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
$_GET['path'] = isset($_GET['path']) ? $_GET['path'] : '';
$_GET['file'] = isset($_GET['file']) ? $_GET['file'] : '';
$_GET['query'] = isset($_GET['query']) ? $_GET['query'] : '';

if(!preg_match("/^[a-zA-Z0-9]+$/", $_GET['query'])) {
	$_GET['query']='';
}

# SET DATA -------------------
$once->set_data(array(
	"path" => $once->filter_string($_GET['path']),
	"query" => $once->filter_string($_GET['query'])
));

# GET DATA -------------------
$data=$once->get_dir_listing();
?>
<div id="libraries-data" class="box" data-ajax="true" data-c="<?php echo $_GET['c'];?>" data-o="<?php echo $_GET['o'];?>" data-path="<?php echo $data['path'];?>" data-query="<?php echo $_GET['query'];?>">
	<div class="row box-header">
		<div class="col-md-8">
			<ol class="breadcrumb">
				<li><a href="#" class="item-open" data-path=""><i class="fa fa-file-image-o"></i> &nbsp;Libraries</a></li>
				<?php
				if(isset($data['breadcrumb'])){
					foreach($data['breadcrumb'] as $key => $val){
						if($data['breadcrumb'][$key]!=''){
							echo '<li class="active"><a href="#" class="item-open" data-path="'.implode("/",array_slice($data['breadcrumb'], 0, $key)).'/'.$val.'">'.$val.'</a></li>';
						}
					}
				}
				?>
			</ol>
		</div>
		<form id="searchForm" method="get" style="margin-top: 3px;">
			<div class="col-sm-4 search-form">
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
										<tr>
											<th>Name</th>
											<th style="width: 30px;"></th>
										</tr>
									</thead>
									<tbody>
									<?php
										if(isset($data['items']['dirs'])){
											foreach($data['items']['dirs'] as $key => $val){
												echo '
												<tr id="item_'.$val.'">
													<td><a class="item-open" data-path="'.$data['path'].'/'.$val.'" title="item name" style="cursor: pointer;"><i class="fa fa-folder"></i> '.$val.'</a></td>
													<td>
														<a class="item-edit" data-path="'.$data['path'].'/'.$val.'" title="dir edit" style="cursor: pointer;"><i class="fa fa fa-edit"></i></a>
															&nbsp;&nbsp;
														<a class="item-delete" data-path="'.$data['path'].'/'.$val.'" title="dir delete" style="cursor: pointer;"><i class="fa fa-trash-o"></i></a>
													</td>
												</tr>';
											}
										}
										if(isset($data['items']['files'])){
											foreach($data['items']['files'] as $key => $val){
												echo '
												<tr id="item_'.$val.'">
													<td><a class="item-edit" data-path="'.$data['path'].'/'.$val.'" title="item name" style="cursor: pointer;"><i class="fa fa-picture-o"></i> '.$val.'</a></td>
													<td>
														<a class="item-edit" data-path="'.$data['path'].'/'.$val.'" title="file edit" style="cursor: pointer;"><i class="fa fa fa-edit"></i></a>
															&nbsp;&nbsp;
														<a class="item-delete" data-path="'.$data['path'].'/'.$val.'" title="file delete" style="cursor: pointer;"><i class="fa fa-trash-o"></i></a>
													</td>
												</tr>';
											}
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
				Not found any file here, be first and create it once!
			</div>';
		}
		?>
	</div>
	<div class="row box-footer">
		<div class="col-md-12">
			<!-- Will see -->
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		// Initialize actions
		once.libraries.actions.mainInit($(this));
	});
</script>