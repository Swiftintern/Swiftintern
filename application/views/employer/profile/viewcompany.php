<?php require_once $dir_public.'requires/header.php';?>
<?php require_once $dir_public.'modal/experience.php';?>
<section class="container">
	<div class="row">
		<nav><ol class="breadcrumb">
				<li><a href="home">Home</a></li>
				<li><a href="companies">Companies</a></li>
				<li class="active"><?php echo $company->name;?></li>
		</ol></nav>
		
		<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
			<li class="active"><a href="#profile" role="tab" data-toggle="tab">Profile</a></li>
			<li><a href="#opportunity" role="tab" data-toggle="tab">Opportunity</a></li>
			<li><a href="#experience" role="tab" data-toggle="tab">Experience</a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="profile">
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="media">
							<span class="pull-left hidden-xs">
								<img src="<?php echo $photo->image_path();?>" class="media-object medium_image" align="right" alt="<?php echo $company->name;?>">
							</span>
							<div class="media-body">
								<h1 class="media-heading"><?php echo $company->name;?></h1>
								<span id="orgdetails">
									<p><i class="fa fa-globe fa-fw"></i> <?php echo $company->website;?></p>
									<p><i class="fa fa-facebook fa-fw"></i> <?php echo $company->fbpage;?></p>
								</span>
							</div>
						</div>
						<hr>
						<span class="pull-right">
							<button name="message" value="<?php echo $user_id;?>" class="btn btn-warning" role="button"><i class="fa fa-envelope"></i> Send Message</button> 
							<button name="follow" value="organization" id="<?php echo $company->id;?>" class="btn btn-info">Follow</button>
						</span>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">About</h3>
					</div>
					<div class="panel-body">
						<p><?php echo $company->about;?></p>
						<p>
						<?php
							$page_data = array('about', 'category', 'description', 'likes', 'website');
							if(strlen($company->fbpage) > 1){
								foreach ($facebook as $fb => $value) {
									if (in_array($fb, $page_data)) {
										echo ucfirst($fb)." : ".$value."<br>" ;
									}
								}
							}
						?>
						</p>
					</div>
				</div>

			</div>

			<div class="tab-pane" id="opportunity">
				<p class="page-header">Opportunity by <?php echo $company->name;?><small>(test, competition, internship)</small> <a href="login" class="btn btn-success pull-right">Post Internship</a></p>
				<table class="table table-striped">
					<tbody id="opportunity">
					<?php
						foreach ($opportunities as $opportunity) {
							$opportunity = Opportunity::find_by_id('id', $opportunity->id);
							$image 		 = Image::find_image('opportunity', $opportunity->id);
							if($image){
								$photo 	 = Photograph::find_by_id('id', $image->photo_id);
							}else {
								$organization = Organization::find_by_id('id', $opportunity->organization_id);
								$photo 	 = Photograph::find_by_id('id', $organization->photo_id);
							}
							echo '<tr>
									<td>
										<div class="media">
											<a class="pull-left" href="'.urlencode($opportunity->title).'/'.$opportunity->id.'">
												<img src="'.$photo->image_path_thumb().'" class="media-object small_image" alt="'.$opportunity->title.'">
											</a>
											<div class="media-body">
												<h4 class="media-heading"><a href="'.urlencode($opportunity->title).'/'.$opportunity->id.'">'.$opportunity->title.'</a></h4>
													'.$opportunity->eligibility.'
											</div>
										</div>
									</td>
									<td class="job-location">
										<p><i class="fa fa-calendar fa-fw"></i>'.only_date($opportunity->last_date).'</p>
										<p><i class="fa fa-map-marker"></i>'.$opportunity->location.'</p>
									</td>
								</tr>';
						}
					?>
				  </tbody>
				</table>
			</div>

			<div class="tab-pane" id="experience">
				<p class="page-header">Shared Experiences of <?php echo $company->name;?><small>(interview, internship)</small> <button class="btn btn-success pull-right" data-toggle="modal" data-target="#share_experience">Share Experience</button></p>
				<table class="table table-striped">
					<tbody id="opportunity">
					<?php
						foreach ($experiences as $experience) {
							$experience  = Experience::find_by_id('id', $experience->id);
							echo '<tr>
									<td>
										<div class="media">
											<div class="media-body">
												<h4 class="media-heading"><a href="experience/'.urlencode($experience->title).'/'.$experience->id.'">'.$experience->title.'</a></h4>
													'.substr(strip_tags($experience->details), 0, 350).'......<a href="experience/'.urlencode($experience->title).'/'.$experience->id.'">Read More</a>
											</div>
										</div>
									</td>
								</tr>';
						}
					?>
				  </tbody>
				</table>
			</div>
		</div>
		
			
	</div>
</section>
<script type="text/javascript">
	$(document).ready(function() {
		if(window.location.href.search("#")){
			var pieces = window.location.href.split("#");
			$('#myTab a[href="#'+pieces[1]+'"]').tab('show')
		}
	});
</script>
<?php require_once $dir_public.'requires/footer.php';?>