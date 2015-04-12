<?php require_once $dir_student.'requires/header.php';?>
<section class="container">
	<div class="row">
		
		<div class="col-xs-12 col-md-3">
			<div class="thumbnail">
				<div class="text-center">
					<img src="<?php echo $photo->image_path_thumb();?>" alt="<?php echo $user->name;?>">
				</div>
				<div class="caption">
					<h3><?php echo $user->name;?></h3>
					<p><b>E-mail :</b> <?php echo $user->email;?></p>
					<p><b>Phone :</b> <?php echo $user->phone;?></p>
					<p><b>Current City:</b> <?php echo $student->city;?></p>
					<p><b>Skills:</b> <?php echo $student->skills;?></p>
					<p>
						<button name="message" value="<?php echo $user->id;?>" class="btn btn-warning btn-sm"><i class="fa fa-envelope"></i> Message</button>
					</p>
				</div>
			</div>
		</div>
		
		<div class="col-xs-12 col-md-9">
			<div class="panel panel-default table-responsive">
				<div class="panel-heading">
					<i class="fa fa-book fa-fw"></i> Education
				</div>
				<table class="table">
					<thead>
						<tr>
							<th>Qualification</th>
							<th>Major/Branch</th>
							<th>Institution/School</th>
							<th>Percentage or GPA</th>
							<th>Year of Passing</th>
						</tr>
					</thead>
					<tbody id="qualification">
					<?php
					if (count($qualifications) == '0') {
						echo '<p class="text-center">No Qualification and current education added.</p>';
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
								</tr>';
						}
					}
					?>
					</tbody>
				</table>
			</div>
			
			<div class="panel panel-default table-responsive">
				<div class="panel-heading">
					<i class="fa fa-briefcase fa-fw"></i> Work Experience
				</div>
				<table class="table">
					<thead>
						<tr>
							<th>Organization</th>
							<th>Designation</th>
							<th>Responsibolity</th>
							<th>Duration</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (count($works) == '0') {
						echo '<p class="text-center">Not added any work Experience till now.</p>';
					} else {
						foreach ($works as $work) {
							$work = Work::find_by_id('id', $work->id);
							$organization  = Organization::find_by_id('id', $work->organization_id);
							echo '<tr>
									<td><a href="organization/'.urlencode($organization->name).'/'.$organization->id.'" target="_blank">'.$organization->name.'</a></td>
									<td>'.$work->designation.'</td>
									<td>'.$work->responsibility.'</td>
									<td>'.$work->duration.'</td>
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
									if ($participant->score > 60) {
										$certificate = Certificate::find_by_participant($participant->id);
										echo '<td><a href="certificate/'.$certificate->uniqid.'" class="btn btn-success btn-sm" target="_blank">View Certificate</a></td>';
									}else {
										echo '<td><a href="result/'.$participant->id.'" class="btn btn-warning btn-sm" target="_blank">View Result</a></td>';
									}
									
							echo '</tr>';
						}
					} else {
						echo '<p class="text-center">Not Attempted Any Test Till Now.</p>';
					}
					?>
					</tbody>
				</table>
			</div>
			
		</div>
		
	</div>
</section>
<?php require_once $dir_public.'requires/footer.php';?>