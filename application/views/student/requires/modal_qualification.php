<div class="modal fade" id="modal_qualification">
	<div class="modal-dialog">
		<form class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title">Education Details</h3>
			</div>
			<div class="modal-body">
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
			</div>
			<div class="modal-footer">
				<input type="hidden" name="action" value="update_qualification">
				<button type="button" name="more" value="more_edu" class="btn btn-info pull-left"><i class="fa fa-plus-circle fa-fw"></i> Add More Education</button>
				<button type="submit" class="btn btn-success pull-right">Save <i class="fa fa-check-circle fa-fw"></i></button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>