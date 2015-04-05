<?php require_once $dir_public.'requires/header.php';?>
<?php require_once $dir_student.'requires/datalist.php';?>
<div class="container">
	<article class="row">
		<h1 class="page-header text-center">Resume Builder</h1>
		<p id="showMessage" class="text-center">Note : If you already have an account login and create resume easily or continue here.</p>

		<form class="panel panel-default" id="basics">
			<div class="panel-body"><br>
				<legend>Basic Details</legend>
				<div class="row">
		  			<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-user"></i> Full Name</label>
					    <input type="text" name="name" class="form-control" required="" placeholder="eg. John Doe">
					  	</div>
			    	</div>
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-envelope"></i> E-mail</label>
					    <input type="email" name="email" class="form-control" required="" placeholder="eg. email@example.com">
					  	</div>
			    	</div>
		    	</div>
				<div class="row">
		  			<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-mobile"></i> Mobile</label>
					    <input type="tel" name="phone" class="form-control" required="" placeholder="eg. +91-99XXXXXXXX">
					  	</div>
			    	</div>
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-pencil"></i> Objective</label>
					    <input type="text" name="about" class="form-control" placeholder="resumes objective...">
					  	</div>
			    	</div>
		    	</div>
				<div class="row">
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-wrench"></i> Skills</label>
					    <input type="text" name="skills" class="form-control" required="" placeholder="MS Office, C++, etc">
					  	</div>
			    	</div>
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-map-marker"></i> Address/City</label>
						<input type="text" name="city" class="form-control" required="" placeholder="eg. New Delhi">
					  	</div>
			    	</div>
		    	</div>
		    	<div class="row col-md-12">
		    		<input type="hidden" name="action" value="create_resume_basics">
		    		<input type="hidden" name="about" value="Seeking a position as an accountant where extensive experience will be further developed and utilised. Extensive experience to the credit.">
		    		<button type="submit" class="btn btn-success pull-right">Next <i class="fa fa-arrow-circle-o-right fa-fw"></i></button>
		    	</div>
			</div>
		</form>


		<form class="panel panel-default hide" id="education">
			<div class="panel-body"><br>
				<legend>Education Details</legend>
				<div id="more_edu">
				<div class="row">
		  			<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-bank"></i> College/University/School</label>
					    <input type="text" list="college" name="name[]" class="form-control" required="" placeholder="start typing full name..." autocomplete="off">
					    <datalist id="college">
					    <?php
					    	$colleges = Organization::find_name_by_property('type', 'institute');
					    	foreach ($colleges as $college) {
					    		echo '<option value="'.$college->name.'">';
					    	}
					    ?>
					    </datalist>
					  	</div>
			    	</div>
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-graduation-cap"></i> Qualification/Degree</label>
					    <input type="text" list="degree" name="degree[]" class="form-control" required="" placeholder="eg. B.Tech, Intermediate, 10th" autocomplete="off">
					    <datalist id="degree">
					    <?php
					    	foreach ($alldegrees as $degree) {
					    		echo '<option value="'.$degree.'">';
					    	}
					    ?>
					    </datalist>
					  	</div>
			    	</div>
		    	</div>
				<div class="row">
			    	<div class="col-xs-12 col-md-6">
			    		<div class="form-group">
					    <label for=""><i class="fa fa-bookmark"></i> Major</label>
					    <input type="text" list="major" name="major[]" class="form-control" required="" placeholder="eg. Economics, Civil Engineering etc" autocomplete="off">
					    <datalist id="major">
					    <?php
					    	foreach ($allmajors as $major) {
					    		echo '<option value="'.$major.'">';
					    	}
					    ?>
					    </datalist>
					  	</div>
				    	<div class="form-group">
					    <label for=""><i class="fa fa-certificate"></i> Percentage or GPA<small>(till now for current)</small></label>
					    <input type="tel" name="gpa[]" class="form-control" placeholder="eg. 8.2, 73%, etc">
					  	</div>
			    	</div>
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-calendar"></i> Passing Year</label>
					    <input type="tel" name="passing_year[]" class="form-control" required="" placeholder="eg. 2016">
					  	</div>
			    	</div>
		    	</div>
		    	</div>
		    	<input type="hidden" name="action" value="update_qualification">
		    	<div class="row col-md-12">
		    		<button type="button" name="more" value="more_edu" class="btn btn-info pull-left"><i class="fa fa-plus-circle fa-fw"></i> Add More Education</button>
		    		<button type="submit" class="btn btn-success pull-right">Next <i class="fa fa-arrow-circle-o-right fa-fw"></i></button>
		    	</div>
			</div>
		</form>


		<form class="panel panel-default hide" id="work">
			<div class="panel-body"><br>
				<legend>Experience Details <small>(small work even working in college fest counts)</small></legend>
				<div id="more_work">
				<div class="row">
		  			<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-bank"></i> Company/Organization Name</label>
					    <input type="text" list="company" name="name[]" class="form-control" required="" placeholder="start typing..." autocomplete="off">
					    <datalist id="company">
					    <?php
					    	$companys = Organization::find_name_by_property('type', 'company');
					    	foreach ($companys as $comp) {
					    		echo '<option value="'.$comp->name.'">';
					    	}
					    ?>
					    </datalist>
					  	</div>
			    	</div>
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-calendar"></i> Duration</label>
					    <input type="text" name="duration[]" class="form-control" required="" placeholder="eg. May 2014 to July 2014">
					  	</div>
			    	</div>
		    	</div>
				<div class="row">
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-line-chart"></i> Responsibility</label>
					    <textarea name="responsibility[]" class="form-control" placeholder="explain what work you did."></textarea>
					  	</div>
			    	</div>
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-certificate"></i> Designation</label>
					    <input type="text" name="designation[]" class="form-control" required="" placeholder="eg. Intern, Sales Executive, etc">
					  	</div>
			    	</div>
		    	</div>
		    	</div>
		    	<div class="row col-md-12">
		    		<input type="hidden" name="action" value="update_work">
		    		<button type="button" name="more" value="more_work" class="btn btn-info pull-left"><i class="fa fa-plus-circle fa-fw"></i> Add More Work</button>
		    		<button type="submit" class="btn btn-success pull-right">Submit <i class="fa fa-check-circle"></i></button>
		    		<a href="resume/create" class="btn btn-primary pull-left">Skip <i class="fa fa-chevron-right"></i></a> 
		    	</div>
			</div>
		</form>
	</article>
</div>
<script type="text/javascript">
$(document).ready(function() {
	sessionStatus().success(function (data) {
		if (data != 'false') {
			user  =  data[0];
			switch(user.type){
				case 'student':
					bootbox.alert('hi '+user.name+' we have created your resume. click ok to edit your resume.', function () {
						window.location.href = "resume/success";
					});
					console.log(user);
					break;
				default:
					bootbox.alert('hi '+user.name+' you are not student. please register as student with different email to continue building your resume.', function () {
						window.location.href = user.type;
					});
					break;
			}
		};
	});
});
</script>
<?php require_once $dir_public.'requires/footer.php';?>