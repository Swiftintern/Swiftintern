<div class="modal fade" id="add_options">
	<div class="modal-dialog">
		<form class="modal-content" id="add_options_form">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title">Add Options</h3>
			</div>
			<div class="modal-body" id="addmore_option">
				<div class="row">
					<div class="col-xs-8 col-md-8">
						<div class="form-group">
						<input type="text" name="ques_option[0]" class="form-control" required="">
						</div>
					</div>
					<div class="col-xs-4 col-md-4">
						<div class="form-group">
							<div class="form-control">
								<label class="radio-inline">
									<input type="radio" name="is_answer[0]" value="1"> Correct
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-8 col-md-8">
						<div class="form-group">
						<input type="text" name="ques_option[1]" class="form-control" required="">
						</div>
					</div>
					<div class="col-xs-4 col-md-4">
						<div class="form-group">
							<div class="form-control">
								<label class="radio-inline">
									<input type="radio" name="is_answer[1]" value="1"> Correct
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-8 col-md-8">
						<div class="form-group">
						<input type="text" name="ques_option[2]" class="form-control" required="">
						</div>
					</div>
					<div class="col-xs-4 col-md-4">
						<div class="form-group">
							<div class="form-control">
								<label class="radio-inline">
									<input type="radio" name="is_answer[2]" value="1"> Correct
								</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" name="ques_id" value="">
				<input type="hidden" name="action" value="add_options">
				<button id="addmoreOption" class="btn btn-primary pull-left" type="button">Add More Options</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save</button>
			</div>
		</form>
	</div>
</div>