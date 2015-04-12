<?php require_once $dir_public.'requires/header.php';?>
<!-- the middle contents -->
<div class="container">
	<article class="row">
		<div class="panel panel-default hidden-print" id="finish">
			<div class="panel-body text-center">
				<label>Thanks for Creating Resume with us.</label>
				<p>You can use this resume to apply to varoius opportunities such as internship, competitions, freelance etc on our <a href="home" target="_blank">site</a>.</p>
				<p>We have registered your account with us and emailed you the details. Check your Email.</p>
				<p>
					<a href="student/resumes" id="editresume" class="btn btn-primary"><i class="fa fa-edit fa-fw"></i> Edit/Save Resume</a>
					<a href="resume/save" id="download" class="btn btn-success"><i class="fa fa-download fa-fw"></i> Download Resume</a>
					<button name="print" value="#resume" class="btn btn-info"><i class="fa fa-print fa-fw"></i> Print Resume</button>
					<button name="message" value="<?php echo $admin_id;?>" class="btn btn-warning"><i class="fa fa-comment fa-fw"></i> Feedback</button>
				</p>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-body" id="resume" style="padding: 60px;">
				<span style="text-align: center;">
					<h1><?php echo $user->name;?></h1>
					<p>
						<span><?php echo $user->email;?></span> | 
						<span><?php echo $user->phone;?></span> | 
						<span><?php echo $student->city;?></span>
					</p>
				</span>
				<h4 style="padding-top: 60px;">Objective</h4><hr style="margin-top: 0px;">
				<p><?php
					if(empty($student->about))
						echo 'Seeking a position as an accountant where extensive experience will be further developed and utilised. Extensive experience to the credit.';
					else
						echo $student->about;
				?></p>
				
				<h4 style="padding-top: 30px;">Education</h4><hr style="margin-top: 0px;">
				<?php
					foreach ($qualifications as $qualification) {
						$qualification = Qualification::find_by_id('id', $qualification->id);
						$organization  = Organization::find_by_id('id', $qualification->organization_id);
						echo '<p>
								'.$qualification->degree.', '.$qualification->major.' with <span>'.$qualification->gpa.'</span>
								<span class="pull-right">'.$qualification->passing_year.'</span><br>
								'.$organization->name.'
							</p>';
					}
				?>

				<h4 style="padding-top: 30px;">Work Experience</h4><hr style="margin-top: 0px;">
				<?php
					foreach ($works as $work) {
						$work = Work::find_by_id('id', $work->id);
						$organization  = Organization::find_by_id('id', $work->organization_id);
						echo '<p>
								'.$work->designation.', '.$organization->name.' 
								<span class="pull-right">'.$work->duration.'</span>
								<br><span>'.$work->responsibility.'</span>
							</p>';
					}
				?>

				<h4 style="padding-top: 30px;">Skills</h4><hr style="margin-top: 0px;">
				<p><?php echo $student->skills;?></p><br>

			</div>
		</div>
		
	</article>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$('#editresume').click(function(e) {
		e.preventDefault();
		var resume = $('#resume').html();
		var dataString 	= "action=save_resume";
		dataString 		= dataString.concat("&resume="+escape(resume));
		saveResume(dataString).success(function (data) {
			window.location.href = 'student/resume/'+data;
		});
	});
});
</script>
<?php require_once $dir_public.'requires/footer.php';?>