<?php require_once $dir_public.'requires/header.php';?>
<section class="container">
	<div class="row">
		<div class="col-xs-12 col-md-9">
			<h2 class="page-header text-center">
				All Company
			</h2>
			<div class="row" id="search_results">
			<?php
				foreach ($companies as $company) {
					$company = Organization::find_by_id('id', $company->id);
					if ($company->photo_id) {
						$photo = Photograph::find_by_id('id', $company->photo_id);
					}else {
						$photo = Photograph::find_by_id('id', $noimage);
					}
					echo '<div class="col-sm-4 col-md-3">
							<div class="thumbnail">
								<img src="'.$photo->image_path_thumb().'" alt="'.$company->name.'">
								<div class="caption">
									<p><small>'.substr($company->name, 0, 20).'</small></p>
									<p>
										<a href="organization/'.url($company->name).'/'.$company->id.'" class="btn btn-primary btn-sm" role="button">Profile</a> 
										<button name="follow" value="organization" id="'.$company->id.'" class="btn btn-success brn-sm">Follow</button>
									</p>
								</div>
							</div>
						</div>';
				}
			?>
			</div>
			<div class="text-center">
				<button id="loadmorebtn" onclick="loadmoreComps(++page, '12');" class="btn btn-primary">Load More</button><hr>
			</div>
		</div>
		<aside class="col-xs-12 col-md-3">
			<a href="employer/register" class="btn btn-warning btn-lg btn-block"><i class="fa fa-briefcase fa-lg"></i> Register Company</a><br>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Search Company</h3>
				</div>
				<div class="panel-body">
					<form id="orgsearch">
				  		<div class="form-group">
						    <label>Company</label>
						    <input type="search" name="orgkeyword" id="orgkeyword" class="form-control" placeholder="Enter Name">
						    <small class="help-block"></small>
					    </div>
						<br>
					    <input type="submit" class="btn btn-primary input-lg btn-block" value="Search">
					</form>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Follow us on Facebook</h3>
				</div>
				<div class="panel-body">
					<div class="fb-like-box" data-href="https://www.facebook.com/pages/SwiftInterncom/268084876704495" data-width="230" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="false" data-show-border="true"></div>
				</div>
			</div>
		</aside>
	</div>
</section>
<script type="text/javascript">
var page = '2';
</script>
<?php require_once $dir_public.'requires/footer.php';?>