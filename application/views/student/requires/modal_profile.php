<div class="modal fade" id="modal_profile">
	<div class="modal-dialog">
		<form class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title">Edit Profile</h3>
			</div>
			<div class="modal-body">
				<div class="row">
		  			<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-user"></i> Full Name</label>
					    <input type="text" name="name" class="form-control" required="" placeholder="eg. John Doe" value="<?php echo $session->name;?>">
					  	</div>
			    	</div>
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-mobile"></i> Mobile</label>
					    <input type="tel" name="phone" class="form-control" required="" placeholder="eg. +91-99XXXXXXXX" value="<?php echo $user->phone;?>">
					  	</div>
			    	</div>
		    	</div>
				<div class="row">
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-wrench"></i> Skills</label>
					    <input type="text" name="skills" class="form-control" required="" placeholder="MS Office, C++, etc" value="<?php echo $student->skills;?>">
					  	</div>
			    	</div>
			    	<div class="col-xs-12 col-md-6">
				    	<div class="form-group">
					    <label for=""><i class="fa fa-map-marker"></i> City</label>
					    <input type="text" name="city" class="form-control" required="" placeholder="eg. Delhi, Mumbai" value="<?php echo $student->city;?>">
					  	</div>
			    	</div>
		    	</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" name="action" value="update_student_profile">
				<button type="submit" class="btn btn-success pull-right">Save <i class="fa fa-check-circle fa-fw"></i></button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>