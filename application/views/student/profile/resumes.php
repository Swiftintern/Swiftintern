<?php require_once $dir_student.'requires/header.php';?>
<!-- the middle contents -->
<section class="container">
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="home">Home</a></li>
			<li><a href="student">Profile</a></li>
			<li class="active">Resume</li>
		</ol>
		<h3 class="page-header">All Resume and CV</h3>
		<form class="form-inline" role="form" id="fetch_resume">
		    <div class="form-group">
		        <div class="input-group">
		            <label class="sr-only">Resume</label>
		            <select id="resume_id" class="form-control">
		            <?php
		                foreach ($resumes as $resume) {
		                    $resume = Resume::find_by_id('id', $resume->id);
		                    echo '<option value="'.$resume->id.'">'.$session->name.' Resume Id:'.$resume->id.'</option>';
		                }
		            ?>
		            </select>
		        </div>
		    </div>
		    <button type="submit" class="btn btn-primary">View/Edit Resume</button>
		</form>
		<hr>
		<div id="result">
			<div class="row">
			<?php
				if (count($resumes) == '0') {
					echo '<p>You have not created any resume, use our awesome resume creator application.</p>';
				} else {
					echo '<div class="text-center">
							<p>Please Select a resume and click view resume to view or edit your resume live.</p>
						</div>';
				}
				
			?>
				<div class="panel panel-default col-md-6">
					<div class="panel-body">
						<label>Resume creator</label>
						<p>Use Our Awesome Resume Creator Application to Create Resume in minutes and apply to thousands of opportunities.</p>
						<p><a href="resume" class="btn btn-success">Resume Creator</a></p>
					</div>
				</div>

				<div class="panel panel-default col-md-6">
					<div class="panel-body">
						<label>Upload Resume</label>
						<form class="row" id="uploadResume">
							<div class="form-group">
								<input type="file" id="imageInput" name="file" class="form-control">
							</div>
							<button type="submit" class="btn btn-primary">Upload</button>
							<input type="hidden" name="action" value="upload_resume">
							<input type="hidden" name="student_id" value="<?php echo $student->id;?>">
						</form>
						<div id="output"></div>
					</div>
				</div>

			</div>
		</div>
		<div class="panel panel-default row hide">
		    <div class="panel-heading">
		        Resume (Update resume by direct editing.)
		        <div class="pull-right">
		        	<button name="save" value="resume" class="btn btn-success btn-xs"><i class="fa fa-upload fa-fw"></i> Save</button>
		        	<!--<button name="delete" value="resume" id="" class="btn btn-danger btn-xs"><i class="fa fa-trash fa-fw"></i> Delete</button> -->
		        	<a href="resume/success" class="btn btn-primary btn-xs" target="_blank"><i class="fa fa-check-circle fa-fw"></i> Create New</a>
		        </div>
		    </div>
		    <div class="panel-body" id="showresume" contenteditable="true"></div>
		</div>
	</div>
</section>
<script type="text/javascript" src="<?php echo $cdn;?>plugins/jquery/jquery.form.min.js"></script>
<?php require_once $dir_public.'requires/footer.php';?>