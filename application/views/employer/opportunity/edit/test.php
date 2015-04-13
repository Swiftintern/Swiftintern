<?php require_once $dir_employer.'requires/header.php';?>
<?php require_once $dir_employer.'modal/add_options.php';?>
<div id="page-wrapper">
    <div class="row col-lg-12"><br>	
		<div class="panel panel-default">
			<div class="panel-body">
				<h3 class="page-header text-center"><?php echo $test->title;?>, Test Questions</h3>
				<div id="test_id" class="hidden"><?php echo $test->id;?></div>
				<table class="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Question</th>
							<th>Answer Type</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody id="allquestions">
						<tr>
							<form class="save_question">
							<td>0</td>
							<td><input type="text" name="question" class="form-control" placeholder="New Question..." required=""></td>
							<td>
								<select name="type" class="form-control">
									<option value="">Select Answer Type</option>
									<option value="options">Options</option>
									<option value="text">Text Answer</option>
								</select>
							</td>
							<td>
								<button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add Question</button>
							</td>
							<input type="hidden" name="test_id" value="">
							<input type="hidden" name="action" value="save_question">
							</form>
						</tr>
						<?php
							$count = 0;
							foreach($questions as $question){
								echo '<tr>
										<td>'.++$count.'</td>
										<td>'.$question->question.'</td>
										<td>'.$question->type;
								switch($question->type){
									case 'options':
										$options = Option::find_by_property('ques_id', $question->id, '10', '0', 'ques_option,is_answer');
										foreach($options as $option){
											if ($option->is_answer == '1') {
												$is_answer = '<i class="fa fa-check"></i>';
											} else{
												$is_answer = '';
											}
											echo '<br>'.$is_answer.' '.$option->ques_option.'<button name="delete" value="option" id="'.$option->id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>';
										}
										echo '</td>
											<td><button name="delete" value="question" id="'.$question->id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Delete</button><button name="addoption" value="'.$question->id.'" class="btn btn-success btn-xs"><i class="fa fa-plus-circle"></i> Add Options</button></td>
										</tr>';
										break;
									case 'text':
										echo '</td>
												<td><button name="delete" value="question" id="'.$question->id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Delete</button></td>
											</tr>';
										break;
								}
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
        
    </div>
</div>
<?php require_once $dir_employer.'requires/footer.php';?>