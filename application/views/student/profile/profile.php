<?php require_once $dir_student.'requires/header.php';?>
<?php require_once $dir_student.'requires/datalist.php';?>
<?php require_once $dir_student.'requires/modal_work.php';?>
<?php require_once $dir_student.'requires/modal_qualification.php';?>
<?php require_once $dir_student.'requires/modal_profile.php';?>
<section class="container">
	<div class="row">
		<div class="col-xs-12 col-md-9">
			<ol class="breadcrumb">
				<li><a href="home"><i class="fa fa-home fa-fw"></i> Home</a></li>
				<li class="active"><i class="fa fa-male fa-fw"></i> Profile</li>
			</ol>
			<?php
				if ($user->validity == 0) {
					echo '<div class="alert alert-warning">Hi '.$session->name.', your email account is not verified by us please verify by <button name="mail" value="verifyaccount" class="btn btn-warning btn-xs" id="'.$user->email.'">Resend Verification</button></div>';
				}
			?>
			<div class="panel panel-default table-responsive">
				<div class="panel-heading">
					<i class="fa fa-book fa-fw"></i> Education
					<div class="pull-right">
						<button type="button" name="addmore" value="qualification" class="btn btn-info btn-xs"><i class="fa fa-plus-square fa-fw"></i> Add More</button>
					</div>
				</div>
				<table class="table">
					<thead>
						<tr>
							<th>Qualification</th>
							<th>Major/Branch</th>
							<th>Institution/School</th>
							<th>Percentage or GPA</th>
							<th>Year of Passing</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody id="qualification">
					<?php
					if (count($qualifications) == '0') {
						echo '<p class="text-center">Please add all your Qualification and current education to be able to select in any internship. college training also counts.</p>';
					} else {
						foreach ($qualifications as $qualification) {
							$qualification = Qualification::find_by_id('id', $qualification->id);
							$organization  = Organization::find_by_id('id', $qualification->organization_id);
							echo '<tr>
									<td>'.$qualification->degree.'</td>
									<td>'.$qualification->major.'</td>
									<td><a href="organization/'.urlencode($organization->name).'/'.$organization->id.'" target="_blank">'.$organization->name.'</a></td>
									<td>'.$qualification->gpa.'</td>
									<td>'.$qualification->passing_year.'</td>
									<td>
										<button type="button" name="delete" value="qualification" id="'.$qualification->id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash fa-fw"></i> Delete</button>
									</td>
								</tr>';
								//$editbtn = '<button type="button" name="edit" value="qualification" id="'.$qualification->id.'" class="btn btn-primary btn-xs"><i class="fa fa-edit fa-fw"></i> Edit</button>'
						}
					}
					?>
					</tbody>
				</table>
			</div>
			
			<div class="panel panel-default table-responsive">
				<div class="panel-heading">
					<i class="fa fa-briefcase fa-fw"></i> Work Experience
					<div class="pull-right">
						<button type="button" name="addmore" value="work" class="btn btn-info btn-xs"><i class="fa fa-plus-square fa-fw"></i> Add More</button>
					</div>
				</div>
				<table class="table">
					<thead>
						<tr>
							<th>Organization</th>
							<th>Designation</th>
							<th>Responsibility</th>
							<th>Duration</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (count($works) == '0') {
						echo '<p class="text-center">You have not added any work Experience till now Please add any Experience to be able to select in any internship. small work as working fest counts.</p>';
					} else {
						foreach ($works as $work) {
							$work = Work::find_by_id('id', $work->id);
							$organization  = Organization::find_by_id('id', $work->organization_id);
							echo '<tr>
									<td><a href="organization/'.urlencode($organization->name).'/'.$organization->id.'" target="_blank">'.$organization->name.'</a></td>
									<td>'.$work->designation.'</td>
									<td>'.$work->responsibility.'</td>
									<td>'.$work->duration.'</td>
									<td>
										<button type="button" name="delete" value="work" id="'.$work->id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash fa-fw"></i> Delete</button>
									</td>
								</tr>';
						}
					}
					?>
					</tbody>
				</table>
			</div>
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-clock-o fa-fw"></i> Test Given
					<div class="pull-right">
						<div class="btn-group">
							<a href="test" class="btn btn-info btn-xs" target="_blank"><i class="fa fa-clock-o fa-fw"></i> See More Test</a>
						</div>
					</div>
				</div>
				<table class="table">
					<thead>
						<tr>
							<th>Test</th>
							<th>Score</th>
							<th>Given</th>
							<th>Result</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if ($participants) {
						foreach ($participants as $participant) {
							$participant = Participant::find_by_id('id', $participant->id);
							$test = Test::find_by_id('id', $participant->test_id);

							echo '<tr>
									<td><a href="test-details/'.urlencode($test->title).'/'.$test->id.'" target="_blank">'.$test->title.'</a></td>
									<td>'.$participant->score.'</td>
									<td><small>'.datetime_to_text($participant->created).'</small></td>';
									$certificate = Certificate::find_by_participant($participant->id);
									if ($certificate) {
										echo '<td><a href="certificate/'.$certificate->uniqid.'" class="btn btn-success btn-sm" target="_blank">View Certificate</a></td>';
									}else {
										echo '<td><a href="result/'.$participant->id.'" class="btn btn-warning btn-sm" target="_blank">View Result</a></td>';
									}
									
							echo '</tr>';
						}
					} else {
						echo '<p class="text-center">You have Not Attempted Any Test Till Now. See all test here.</p>';
					}
					?>
					</tbody>
				</table>
			</div>
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-lock fa-fw"></i> Security Details
					<div class="pull-right">
						<div class="btn-group">
							<button type="button" name="message" value="1" class="btn btn-danger btn-xs"><i class="fa fa-exclamation-circle"></i> Report</button>
						</div>
					</div>
				</div>
				<table class="table">
					<tbody>
						<tr>
							<td>Last IP</td>
							<td><?php echo $user->last_ip;?></td>
						</tr>
						<tr>
							<td>Last Login</td>
							<td><?php echo $user->last_login;?></td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>
		<div class="col-xs-12 col-md-3">
			<div class="thumbnail">
				<form id="user_image_upload">
					<div class="form-group">
						<label for="">Change Your photo</label>
						<input type="file" id="imageInput" name="photo" class="form-control">
						<input type="hidden" name="action" value="update_image">
						<input type="hidden" name="property" value="user">
						<input type="hidden" name="property_id" value="<?php echo $session->user_id;?>">
						<input type="hidden" name="photo_id" value="<?php echo $photo->id;?>">
					</div>
				</form>
				<div id="output" class="text-center">
					<img src="<?php echo $photo->image_path_thumb();?>" alt="<?php echo $user->name;?>">
				</div>
				<div class="caption">
					<h3><?php echo $user->name;?></h3>
					<p><b>E-mail :</b> <?php echo $user->email;?></p>
					<p><b>Phone :</b> <?php echo $user->phone;?></p>
					<p><b>Current City:</b> <?php echo $student->city;?></p>
					<p><b>Skills:</b> <?php echo $student->skills;?></p>
					<p>
						<button name="edit" value="profile" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</button> 
						<button id="chngpass" class="btn btn-warning btn-sm"><i class="fa fa-key"></i> Change Password</button>
					</p>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-bullhorn"></i> Sponsored
					<div class="btn-group pull-right">
						<button onclick="loadSponsored(--page, '1');" class="btn btn-default btn-xs"><i class="fa fa-chevron-left"></i></button>
						<button onclick="loadSponsored(++page, '1');" class="btn btn-default btn-xs"><i class="fa fa-chevron-right"></i></button>
					</div>
				</div>
				<div class="panel-body" id="sponsored"></div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript" async src="<?php echo $cdn;?>plugins/jquery/jquery.form.min.js"></script>
<script type="text/javascript">
var page = '1';
$(document).ready(function() {
	loadSponsored('1', '1');
	$('button[name="addmore"]').click(function(e) {
		var element = this.value;
		$('#modal_'+element).modal('show');
	});

	$("#imageInput").change(function(){
		$('#user_image_upload').ajaxSubmit({ 
			target: '#output',
			beforeSubmit: beforeSubmit,
			url: 'application/controllers/public/action.php',
			type: 'POST',
			uploadProgress: function(event, position, total, percentComplete) {
				var percentVal = percentComplete + '%';
				$('#output').html('<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="'+percentComplete+'" aria-valuemin="0" aria-valuemax="100" style="width: '+percentVal+'"><span class="sr-only">'+percentVal+' Complete</span></div></div>')
			},
			resetForm: true
		});
		return false;
	});
	
});
</script>
<?php require_once $dir_public.'requires/footer.php';?>