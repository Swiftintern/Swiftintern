<?php require_once $dir_public.'requires/header.php';?>
<!-- the middle contents -->
<div class="container">
	<div class="row">
		<h3 class="page-header">All Company with Placement Papers and Interview Experiences</h3>
		<article class="col-md-9 col-xs-12">
			<div class="row" id="search_results">
			<?php
				for ($i=0; $i < 24; $i++) { 
					echo '<div class="col-sm-4 col-md-3">
							<div class="thumbnail">
								<img src="'.$orgs[$i]['image'].'" alt="'.$orgs[$i]['name'].'">
								<div class="caption">
									<p><small>'.substr($orgs[$i]['name'], 0, 18).'</small></p>
									<p><a href="organization/'.url($orgs[$i]['name']).'/'.$orgs[$i]['id'].'#experience" class="btn btn-primary btn-sm" role="button">Papers</a> <a href="organization/'.url($orgs[$i]['name']).'/'.$orgs[$i]['id'].'" class="btn btn-info btn-sm">Profile</a></p>
								</div>
							</div>
						</div>';
				}
			?>
			</div>
			<div class="text-center">
				<button id="loadmorebtn" onclick="loadmoreExpComp(++page, '12');" class="btn btn-primary">Load More</button><hr>
			</div>
		</article>

		<aside class="col-md-3 col-xs-12">
			<!--<a href="#" class="btn btn-success btn-lg btn-block"><i class="fa fa-plus-square-o fa-lg"></i> Add Your Experience</a><br>-->
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-search"></i> Search Company
				</div>
				<div class="panel-body">
					<form id="ExpCompSearch">
				  		<div class="form-group">
						    <label>Select Company</label>
						    <select data-placeholder="Search Company" class="form-control chosen" name="company" required="">
						    	<option>Search Company</option>
						  	<?php
								foreach ($orgs as $org) {
									echo '<option value="'.$org['id'].'">'.$org['name'].'</option>';
								}
							?>
							</select>
						    <small class="help-block"></small>
					    </div>
					    <input type="submit" class="btn btn-primary btn-block" value="Go!">
					</form>
				</div>
			</div>
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-mobile"></i> Placement Updates App
				</div>
				<div class="panel-body text-center">
					<a href="https://play.google.com/store/apps/details?id=com.swiftintern.placement" target="_blank"><img src="<?php echo $cdn;?>img/others/google_play.png" title="Placement Updates"></a>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Follow us on Facebook</h3>
				</div>
				<div class="panel-body">
					<div class="fb-like-box" data-href="https://www.facebook.com/allplacement" data-width="230" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="false" data-show-border="true"></div>
				</div>
			</div>
		</aside>
	</div>
</div>
<script type="text/javascript">
var page = 2;
</script>
<?php require_once $dir_public.'requires/footer.php';?>