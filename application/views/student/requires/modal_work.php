<div class="modal fade" id="modal_work">
	<div class="modal-dialog">
		<form class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title">Experience Details <small>(small work even working in college fest counts)</small></h3>
			</div>
			<div class="modal-body">
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
			</div>
			<div class="modal-footer">
				<input type="hidden" name="action" value="update_work">
				<button type="button" name="more" value="more_work" class="btn btn-info pull-left"><i class="fa fa-plus-circle fa-fw"></i> Add More Work</button>
				<button type="submit" class="btn btn-success pull-right">Save <i class="fa fa-check-circle fa-fw"></i></button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>