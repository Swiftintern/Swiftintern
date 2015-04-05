<?php require_once $dir_student.'requires/header.php';?>
<!-- the middle contents -->
<section class="container">
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="home">Home</a></li>
			<li><a href="student">Profile</a></li>
			<li class="active"><a href="student/resumes">Resume</a></li>
		</ol>
		<div class="panel panel-default">
		    <div class="panel-heading">
		        Resume (Update resume by direct editing.)
		        <div class="pull-right">
		        	<button name="save" value="resume" id="<?php echo $resume->id;?>" class="btn btn-success btn-xs"><i class="fa fa-upload fa-fw"></i> Save</button>
		        	<button name="delete" value="resume" id="<?php echo $resume->id;?>" class="btn btn-danger btn-xs"><i class="fa fa-trash fa-fw"></i> Delete</button> 
		        	<a href="resume/success" class="btn btn-primary btn-xs" target="_blank"><i class="fa fa-check-circle fa-fw"></i> Create New</a>
		        </div>
		    </div>
		    <div class="panel-body" id="showresume" contenteditable="true"><?php echo $resume->resume;?></div>
		</div>
	</div>
</section>
<script type="text/javascript">
$(document).ready(function() {
	$('#showresume').focus();
});
</script>
<?php require_once $dir_public.'requires/footer.php';?>