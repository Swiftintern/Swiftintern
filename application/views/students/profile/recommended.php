<?php require_once $dir_student.'requires/header.php';?>
<section class="container">
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="home">Home</a></li>
			<li><a href="student">Profile</a></li>
			<li class="active">Internships Matching your Profile</li>
		</ol>
		<h4 class="page-header">Recommended Opportunity</h4>
		<p class="text-center">
			<b>Skills : </b> <span id="skills"><?php echo $student->skills;?></span>
			<b>City :</b> <span id="location"><?php echo $student->city;?></span>
		</p>
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
		<div class="text-center">
			<button class="btn btn-primary" onclick="loadmoreRecommended(++page, '10');" id="loadmorebtn">Load More</button>
		</div><hr>
	</div>
</section>
<script type="text/javascript">
var page = 1;
</script>
<?php require_once $dir_public.'requires/footer.php';?>